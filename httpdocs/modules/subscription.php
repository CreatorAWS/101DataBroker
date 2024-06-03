<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("subscription_FUNCTIONS_DEFINED"))
		{

			define("subscription_FUNCTIONS_DEFINED", 1);
		}

	sm_default_action('view');
	sm_add_body_class('product_page');
	if (sm_action('applycoupon'))
		{
			sm_use('tplan');
			sm_use('tcompany');
			sm_use('tcoupon');
			sm_use('formatter');
			$plan=new TPlan($_getvars['id']);
			if ($plan->Exists() && !$plan->isDeleted())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					$coupon=TCoupon::initWithCode($_postvars['coupon_code'], $plan->CompanyID());
					if ($coupon->Exists() && $coupon->isApplicableForPlan($plan))
						{
							if ($coupon->isDiscountFixed() && $plan->Price() < $coupon->DiscountFixed())
								{
									sm_notify('Unable to apply coupon', 'Error', 'error');
									sm_redirect('plan-'.$plan->ID().(!empty($id_employee)?'/member'.$id_employee:'').(!empty($_getvars['platform'])?'/'.$_getvars['platform']:''));
								}
							else
								{
									sm_notify('Coupon applied');
									sm_redirect('plan-'.$plan->ID().'/coupon-'.$coupon->ID().(!empty($id_employee)?'/member'.$id_employee:'').(!empty($_getvars['platform'])?'/'.$_getvars['platform']:''));
								}
						}
					else
						{
							if (!empty($_postvars['coupon_code']))
								sm_notify('Unable to apply coupon', 'Error', 'error');
							sm_redirect('plan-'.$plan->ID().(!empty($id_employee)?'/member'.$id_employee:'').(!empty($_getvars['platform'])?'/'.$_getvars['platform']:''));
						}
				}
		}

	if (sm_action('charge'))
		{
			$plan=new TPlan($_getvars['id']);
			if ($plan->Exists() && !$plan->isDeleted())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					if ($plan->isPLanTypePackage())
						$company = TCompany::SystemCompany();
					else
						$company = new TCompany($plan->CompanyID());

					if (intval($_postvars['subscription_quantity'])<$plan->MinAvailableQuantity() || intval($_postvars['subscription_quantity'])>$plan->MaxAvailableQuantity())
						$error_message='Wrong quantity';
					elseif ($plan->isAskForPasswordOnCheckout() && mb_strlen($_postvars['password'])==0)
						$error_message='The password cannot be empty';
					elseif ($_postvars['password']!=$_postvars['password2'])
						$error_message='The password are not equal';
					elseif (empty($_postvars['stripeToken']))
						$error_message='Wrong payment information';
					if (empty($error_message))
						{
							$coupon_code='';
							$stripe_source=$_postvars['stripeToken'];
							$qty=intval($_postvars['subscription_quantity']);
							if ($qty<=0)
								$qty=1;
							$total_in_cents = $plan->PriceInCents()*$qty;
							$error = false;
							\Stripe\Stripe::setApiKey($company->StripeSecretKey());
							try
								{
									$stripe_customer=\Stripe\Customer::create(Array(
										'source'=>$stripe_source,
										'description'=>$sm['p']['subscription_name'].', t:'.$sm['p']['subscription_phone'],
										'email'=>$sm['p']['subscription_email']
									));
									if ($plan->SetupFee()>0 && (!$plan->HasTrialPeriod() || $plan->isSetupFeeChargedBeforeTrialPeriod()))
										{
											\Stripe\InvoiceItem::create(Array(
												"customer" => $stripe_customer->id,
												"amount" => $plan->SetupFeeInCents(),
												"currency" => "usd",
												"description" => $plan->HasSetupFeeCustomTitle()?$plan->SetupFeeCustomTitle():'Setup Fee',
												// 'quantity'=>$qty - if you'll need to have setup fee related to quantity
											));
										}
									$params=Array(
												"customer" => $stripe_customer->id,
												"items" => Array(
													Array(
														"plan" => $plan->StripePlanID(),
														'quantity'=>$qty
													),
												)
											);
									if (!empty($_postvars['coupon_id']))
										{
											$coupon=new TCoupon($_postvars['coupon_id']);
											if ($coupon->Exists() && $coupon->CompanyID()==$plan->CompanyID() && $coupon->isApplicableForPlan($plan))
												{
													if ($coupon->isDiscountPercent() || ($coupon->isDiscountFixed() && $plan->Price() >= $coupon->DiscountFixed()))
														{
															$params['coupon'] = $coupon->StripeCouponID();
															$coupon_code=$coupon->CodeUppercased();
														}
												}
										}
									if ($plan->HasTrialPeriod())
										$params['trial_period_days']=$plan->TrialPeriodDays();
									$stripe_subscription=\Stripe\Subscription::create($params);
									if ($plan->SetupFee()>0 && $plan->HasTrialPeriod() && !$plan->isSetupFeeChargedBeforeTrialPeriod())
										{
											\Stripe\InvoiceItem::create(Array(
												"customer" => $stripe_customer->id,
												"amount" => $plan->SetupFeeInCents(),
												"currency" => "usd",
												"description" => $plan->HasSetupFeeCustomTitle()?$plan->SetupFeeCustomTitle():'Setup Fee',
												// 'quantity'=>$qty - if you'll need to have setup fee related to quantity
											));
										}
								}
							catch (\Stripe\Error\Card $e)
								{
									$error = true;
								}
							if (!$error)
								{
									if (!$company->isStripeTestMode() || $company->isStripeTestMode() && $company->isEnabledSaveToPurchasesTestMode())
										{
											$subscription=TSubscription::Create($company, $plan, $qty, $_postvars['subscription_email']);
											$name=explode(' ', $_postvars['subscription_name']);
											$first_name=$name[0];
											$last_name=$name[1];
											for ($i = 2; $i < count($name); $i++)
												{
													$last_name.=' '.$name[$i];
												}
											$subscription->SetFirstName($first_name);
											$subscription->SetLastName($last_name);
											$subscription->SetAddress1($_postvars['subscription_address_line1']);
											$subscription->SetAddress2($_postvars['subscription_address_line2']);
											$subscription->SetCity($_postvars['subscription_city']);
											$subscription->SetState($_postvars['subscription_state']);
											$subscription->SetZip($_postvars['subscription_zip']);
											$subscription->SetCountry('USA');
											$subscription->SetCellphone(Cleaner::USPhone($_postvars['subscription_phone']));
											$subscription->SetStripeCustomerID($stripe_customer->id);
											$subscription->SetStripeSubscriptionID($stripe_subscription->id);
											$subscription->SetStripeCardID($stripe_source, false);
											$subscription->CreateNewSubscriptionWebhook($_postvars);
											if (!empty($id_employee) && is_object($employee))
												{
													$subscription->SetEmployeeID($employee->ID());
													$subscription->AddPointsForEmployer();
												}
											if (!empty($coupon_code) && is_object($coupon))
												{
													$subscription->SetCouponCode($coupon_code);
													$coupon->IncreaseUsage();
												}
											sm_event('subscription-create', Array($subscription->ID()));
										}
									sm_redirect($plan->RedirectAfterCheckoutURL());
								}
							else
								{
									sm_title('Error');
									include_once('includes/admininterface.php');
									$ui = new TInterface();
									$ui->p('Unable to process the payment.');
									$ui->p('Please try again later.');
									$ui->Output(true);
								}
						}
					if (!empty($error_message))
						sm_set_action('view');
				}
		}
	if (sm_action('view'))
		{
			sm_use('ui.interface');
			sm_use('ui.form');
			sm_use('tcoupon');
			$ui = new TInterface();
			$plan = new TPlan($_getvars['id']);

			if (!$plan->HasSidebarText())
				sm_add_body_class('onecolumb-cart-page');
			if ($plan->Exists() && !$plan->isDeleted())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					sm_add_cssfile('subscription.css');
					sm_add_jsfile('subscription.js');
					if ($plan->isPLanTypePackage())
						$company = TCompany::SystemCompany();
					else
						$company = new TCompany($plan->CompanyID());

					$info=Array();
					sm_title($plan->Title());
					$info['discount']=0;
					$info['charge_url']='index.php?m=subscription&d=charge&id='.$plan->ID().(!empty($id_employee)?'&employee='.$id_employee:'').(!empty($_getvars['platform'])?'&platform='.$_getvars['platform']:'');
					$info['apply_coupon_url']='index.php?m=subscription&d=applycoupon&id='.$plan->ID().(!empty($id_employee)?'&employee='.$id_employee:'').(!empty($_getvars['platform'])?'&platform='.$_getvars['platform']:'');
					$info['years']=range(intval(date('Y')), intval(date('Y'))+35);
					$info['price']=$plan->Price();
					$info['interval_label']=$plan->IntervalType();
					$info['price_interval_separator']='/';
					$info['formatted']['price']='$'.Formatter::Money($plan->Price());
					$info['setup_fee']=$plan->SetupFee();
					$info['total_label']='Total';
					if ($plan->HasTrialPeriod())
						{
							$info['custom_payment_button_title']='Process Payment';
							$info['total_label']='Today\'s Total';
							if ($plan->SetupFee()>0)
								{
									$info['trial_box_enabled']=true;
									if ($plan->isSetupFeeChargedBeforeTrialPeriod())
										{
											$info['trial_type']=2;
											$info['trial_box']['first_line_title']='Payment Today';
										}
									else
										{
											$info['trial_type']=1;
											$info['trial_box']['first_line_title']='Setup Fee';
										}
									$info['trial_box']['first_line_amount']='$'.Formatter::Money($plan->SetupFee());
								}
							else
								$info['trial_type']=1;
						}
					else
						{
							$info['trial_type']=0;
						}
					$info['formatted']['setup_fee']='$'.Formatter::Money($plan->SetupFee());
					$info['setup_fee_label']=$plan->SetupFeeTitle();
					if (empty($info['setup_fee_label']))
						$info['setup_fee_label']='Setup Fee';
					$info['text']=$plan->Text();
					$info['sidebartext']=$plan->SidebarText();
					$info['coupon_form_visible']=TCoupon::isApplicableCouponForProductExists($plan, $plan->CompanyID());
					if ($info['coupon_form_visible'])
						{
							if (!empty($_getvars['discount']))
								{
									$coupon=new TCoupon($_getvars['discount']);
									if ($coupon->Exists() && $coupon->CompanyID()==$plan->CompanyID() && $coupon->isApplicableForPlan($plan))
										{
											if ($coupon->isDiscountPercent() && $plan->SetupFee()>0)
												{
													$info['setup_fee']=$plan->SetupFee() - ($plan->SetupFee()*$coupon->DiscountPercent()/100);
													$info['formatted']['setup_fee']='$'.Formatter::Money($plan->SetupFee() - ($plan->SetupFee()*$coupon->DiscountPercent()/100));
												}
											$info['coupon_id'] = $coupon->ID();
											$info['subscriptiondiscount']=$coupon->CalculateDiscount($plan->Price());
											$info['discount']=$coupon->CalculateDiscount($plan->Price()+$plan->SetupFee());
											$info['coupon_discount_text']='Coupon '.$coupon->CodeUppercased().'. Discount: <b>$'.Formatter::Money($info['discount']).'</b>';
											$info['charge_url'].='&coupon='.$coupon->ID();
											if ($plan->HasTrialPeriod() && $coupon->isDiscountPercent() && $plan->SetupFee()>0)
												{
													$info['trial_box']['first_line_amount']='$'.Formatter::Money($plan->SetupFee() - ($plan->SetupFee()*$coupon->DiscountPercent()/100));
												}
										}
								}
						}
					if ($plan->HasTrialPeriod())
						{
							$info['trial_period']=true;
							if ($plan->isSetupFeeChargedBeforeTrialPeriod())
								$info['trial_period_text']='Trial period will last '.$plan->TrialPeriodDays().' day'.($plan->TrialPeriodDays()==1?'':'s').'. You will not be charged a monthly subscription until your trial period is over.';
							else
								$info['trial_period_text']='Trial period will last '.$plan->TrialPeriodDays().' day'.($plan->TrialPeriodDays()==1?'':'s').'. You will not be charged until your trial period is over.';
						}
					if ($plan->MaxAvailableQuantity()!=$plan->MinAvailableQuantity())
						{
							$info['multiple_qty_allowed']=true;
							$info['form']['subscription_quantity']=$plan->MinAvailableQuantity();
							$info['qty_values']=array();
							$info['qty_labels']=array();
							for ($i = $plan->MinAvailableQuantity(); $i <= $plan->MaxAvailableQuantity(); $i+=$plan->StepForAvailableQuantity())
								{
									$info['qty_values'][]=$i;
									$info['qty_labels'][]=$i.' '.$plan->TitleForItemQuantity().' - $'.Formatter::Money($plan->Price()*$i);
								}
						}
					else
						{
							$info['multiple_qty_allowed']=false;
							$info['form']['subscription_quantity'] = 1;
						}


					$info['coupon_form_visible']=TCoupon::isApplicableCouponForPlanExists($plan, $plan->CompanyID());

					//--------------------------------------------------------------------------



					if (count($sm['p'])>0)
						{
							foreach ($sm['p'] as $key=>$val)
								$info['form'][$key]=$val;
						}
					if ($plan->isAskForPasswordOnCheckout())
						{
							$info['account_section']=true;
							$info['ask_for_password']=true;
							$info['form']['account_email']=$info['form']['subscription_email'];
						}
					else
						$info['account_section']=false;
					if (!empty($error_message))
						$info['error_message']=$error_message;
					//--------------------------------------------------------------------------
					$ui->AddTPL('subscription.tpl', 'view', $info);
					//--------------------------------------------------------------------------
					sm_html_headend('
							<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
					');
					sm_html_headend('
							<script type="text/javascript">
							
							function checkEmail(email)
								{
									var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
									if (!filter.test(email))
										return false;
									 return true;
								}
							// This identifies your website in the createToken call below
							Stripe.setPublishableKey(\''.$company->StripePublicKey().'\');
						
							var stripeResponseHandler = function(status, response) {
							  var $form = $(\'#payment-form\');
						
							  if (response.error) {
								// Show the errors on the form
								$form.find(\'.subscription-payment-errors\').text(response.error.message);
								$form.find(\'.subscription-payment-errors\').show();
								$form.find(\'button\').prop(\'disabled\', false);
							  } else {
								// token contains id, last4, and card type
								var token = response.id;
								// Insert the token into the form so it gets submitted to the server
								$form.append($(\'<input type="hidden" name="stripeToken" />\').val(token));
								// and re-submit
								$form.get(0).submit();
							  }
							};
							
							jQuery(function($) {
							    
							$("#subscription_email").keyup(function() {
							    
							    var email = $("#subscription_email").val();
							    $.get( \'index.php?m='.sm_current_module().'&d=checkemailexist&theonepage=1&id='.$_getvars['id'].'&email=\' + email, function( data ) {
									if (data == 1)
										{
											$(".subscription-text-payment-button").attr("disabled", true);
											$(\'#payment-form\').find(\'.subscription-payment-errors\').text("Email already exists");
											$(\'#payment-form\').find(\'.subscription-payment-errors\').show();
										}
									else
									    {
											$(\'#payment-form\').find(\'.subscription-payment-errors\').hide();
											$(\'#payment-form\').find(\'button\').prop(\'disabled\', false);
									    }
									});

							});
							    
							    
							  $(\'#payment-form\').submit(function(e) {
								var $form = $(this);
								$form.find(\'.subscription-payment-errors\').text("");
								$form.find(\'.subscription-payment-errors\').hide();
						
								// Disable the submit button to prevent repeated clicks
								$form.find(\'button\').prop(\'disabled\', true);
								
								if (!checkEmail($("#subscription_email").val()))
									{
										$form.find(\'.subscription-payment-errors\').text("Wrong email");
										$form.find(\'.subscription-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#subscription_name").val()=="")
									{
										$form.find(\'.subscription-payment-errors\').text("Wrong name");
										$form.find(\'.subscription-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()=="")
									{
										$form.find(\'.subscription-payment-errors\').text("Wrong password");
										$form.find(\'.subscription-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()!=$("#password2").val())
									{
										$form.find(\'.subscription-payment-errors\').text("Passwords did not match");
										$form.find(\'.subscription-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else
									Stripe.card.createToken($form, stripeResponseHandler);
						
								// Prevent the form from submitting with the default action
								return false;
							  });
							});
						  </script>
					');
					//--------------------------------------------------------------------------
					if ($plan->HasCSS())
						$ui->style($plan->CSS());

/*
					if ($plan->HasLogo())
						{
							$ui->style("
								.navbar-brand {
									margin: 0;
									background: url(".sm_homepage().$plan->LogoURL().") no-repeat left center;
									min-width: 300px;
									text-indent: -9999px;
								}
							");
						}
					elseif ($company->HasLogo())
						{
							$ui->style("
								.navbar-brand {
									margin: 0;
									background: url(".sm_homepage().$company->LogoURL().") no-repeat left center;
									min-width: 300px;
									text-indent: -9999px;
								}
							");
						}
*/
					//--------------------------------------------------------------------------
					$ui->Output(true);
				}
		}

	if(sm_action('checkemailexist'))
		{
			sm_use('ui.interface');
			$ui = new TInterface();
			$plan=new TPlan($_getvars['id']);
			if ($plan->Exists() && !$plan->isDeleted())
				{
					$subscriptions = new TSubscriptionList();
					$subscriptions->SetFilterCompany($plan->CompanyID());
					$subscriptions->SetFilterPlan($plan->ID());
					$subscriptions->SetFilterEmail($_getvars['email']);
					$subscriptions->SetFilterActive();
					$emailcount = $subscriptions->TotalCount();
					if($emailcount > 0)
						print('1');
					else
						print('0');
				}
			$ui->Output(true);
		}

	if (sm_action('thankyou'))
		{
			sm_use('TPlan');
			sm_use('tcompany');
			sm_use('formatter');
			$plan=new TPlan($_getvars['id']);
			if ($plan->Exists() && !$plan->isDeleted())
				{
					if ($plan->isPLanTypePackage())
						$company = TCompany::SystemCompany();
					else
						$company = new TCompany($plan->CompanyID());

					sm_title('Thank you');
					sm_use('ui.interface');
					$ui = new TInterface();
					if ($plan->HasThankYouText())
						{
							$ui->html($plan->ThankYouText());
						}
					else
						{
							$ui->p('You have successfully submitted your order.');
							if ($plan->isPLanTypePackage())
								$ui->html('<p>You can log in <a href="https://'.main_domain().'">here</a></p>');
						}
					$ui->Output(true);
				}
		}

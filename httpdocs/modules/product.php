<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("product_FUNCTIONS_DEFINED"))
		{

			define("product_FUNCTIONS_DEFINED", 1);
		}

	sm_default_action('view');

	if (sm_action('applycoupon'))
		{
			sm_use('tproduct');
			sm_use('tcompany');
			sm_use('tcoupon');
			sm_use('formatter');
			$product=new TProduct($_getvars['id']);
			if ($product->Exists())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					$coupon=TCoupon::initWithCode($_postvars['coupon_code'], $product->CompanyID());
					if ($coupon->Exists() && $coupon->isApplicableForProduct($product))
						{
							sm_notify('Coupon applied');
							if(!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto'].'&discount='.$coupon->ID());
							else
								sm_redirect('product-'.$product->ID().'/coupon-'.$coupon->ID().(!empty($id_employee)?'/member'.$id_employee:'').(!empty($_getvars['platform'])?'/'.$_getvars['platform']:''));
						}
					else
						{
							if (!empty($_postvars['coupon_code']))
								sm_notify('Unable to apply coupon', 'Error', 'error');
							if(!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto']);
							else
								{
									if (!empty($_getvars['employee']))
										{
											$employee = new TEmployee($_getvars['employee']);
											if ($employee->Exists())
												$id_employee = $employee->ID();
										}
									sm_redirect('product-'.$product->ID().(!empty($id_employee)?'/member'.$id_employee:'').(!empty($_getvars['platform'])?'/'.$_getvars['platform']:''));
								}
						}
				}
		}
	if (sm_action('charge'))
		{
			$m['module'] = sm_current_module();
			sm_use('tproduct');
			sm_use('tcompany');
			sm_use('tcoupon');
			sm_use('formatter');
			$product=new TProduct($_getvars['id']);
			if ($product->Exists())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					if ($product->isAskForPasswordOnCheckout() && mb_strlen($_postvars['password'])==0)
						$error_message='The password cannot be empty';
					elseif ($_postvars['password']!=$_postvars['password2'])
						$error_message='The password are not equal';

					if (empty($error_message))
						{
							$company=new TCompany($product->CompanyID());
							$coupon=new TCoupon($_getvars['coupon']);
							$discount=0;
							if (!$product->isMultipleQuantityAllowed() || intval($_postvars['product_quantity'])<1)
								$quantity=1;
							else
								$quantity=intval($_postvars['product_quantity']);
							$shipping=0;
							$total=0;
							$shipping_id=intval($_postvars['shipping_id']);
							$total_in_cents=0;
							$coupon_code='';
							$shipping_name='';
							if ($coupon->Exists() && $coupon->CompanyID()==$product->CompanyID() && $coupon->isApplicableForProduct($product))
								{
									$discount=$coupon->CalculateDiscount($product->Price());
									$coupon_code=$coupon->CodeUppercased();
								}
							if ($shipping_id>0)
								{
									$shipping_info = TQuery::ForTable('shipping')->Add('id_u', intval($company->ID()))->AddWhere('id', intval($shipping_id))->Get();
									if (!empty($shipping_info['id']))
										{
											$shipping = floatval($shipping_info['price']);
											$shipping_name = $shipping_info['title'];
										}
									else
										$shipping_id=0;
								}
							$total=round(($product->Price()-$discount)*$quantity+$shipping, 2);
							$total_in_cents=intval($total*100);
							$error = false;

							\Stripe\Stripe::setApiKey($company->StripeSecretKey());
							try
								{
									$charge = \Stripe\Charge::create(array(
											"amount" => $total_in_cents, // amount in cents, again
											"currency" => "usd",
											"card" => $_postvars['stripeToken'],
											"description" => "Payment for ".$_postvars['product_email'].' - Product ['.$product->ID().'] '.$product->Title().(!empty($coupon_code)?' (Coupon: '.$coupon_code.')':'').(!empty($shipping_name)?' (Shipping: '.$shipping_name.')':''),
											"receipt_email" => $_postvars['product_email']
										)
									);
								}
							catch (\Stripe\Error\Card $e)
								{
									$error = true;
								}
							if (!$error)
								{
									if (!$company->isStripeTestMode() || $company->isStripeTestMode() && $company->isEnabledSaveToPurchasesTestMode())
										{
											$product->IncPurchasesCountTotal();
											$q=new TQuery('purchases');
											$q->Add('id_u', intval($company->ID()));
											$q->Add('id_p', intval($product->ID()));
											$q->Add('title', dbescape($product->Title()));
											$q->Add('price', floatval($product->Price()));
											$q->Add('qty', intval($quantity));
											$q->Add('shipping', floatval($shipping));
											$q->Add('shipping_id', intval($shipping_id));
											$q->Add('shipping_name', dbescape($shipping_name));
											$q->Add('discount', floatval($discount));
											$q->Add('total', floatval($total));
											if (!empty($coupon_code))
												$q->Add('coupon_code', dbescape($coupon_code));
											$q->Add('customeremail', dbescape($_postvars['product_email']));
											$q->Add('stripetoken', dbescape($_postvars['stripeToken']));
											$q->Add('timebought', time());
											$q->Add('name', dbescape($_postvars['product_name']));
											$q->Add('email', dbescape($_postvars['product_email']));
											$q->Add('phone', dbescape($_postvars['product_phone']));
											$q->Add('address_line1', dbescape($_postvars['product_address_line1']));
											$q->Add('address_line2', dbescape($_postvars['product_address_line2']));
											$q->Add('city', dbescape($_postvars['product_city']));
											$q->Add('state', dbescape($_postvars['product_state']));
											$q->Add('zip', dbescape($_postvars['product_zip']));
											if (!empty($id_employee) && is_object($employee))
												$q->Add('id_employee', intval($employee->ID()));
											if ($product->isDownloadable())
												{
													$q->Add('downloadable', 1);
													$q->Add('downloaded_times', 0);
													$q->Add('max_downloads', 3);
													$q->Add('download_link_sent', 0);
													$q->Add('download_url_hash', time().md5($product->ID().'-'.rand(1, 9999)));
												}
											$id=$q->Insert();
											$purchase = new TPurchase($id);
											$purchase->CreateNewPurchaseWebhook($_postvars);
											if (!empty($coupon_code) && is_object($coupon))
												{
													$coupon->IncreaseUsage();
												}
										}
									if (!empty($product->RedirectAfterCheckout()))
										sm_redirect($product->RedirectAfterCheckoutURL());
									else
										sm_redirect('index.php?m=product&d=thankyou&id='.$product->ID().(!empty($id_employee)?'&employee='.$id_employee:'').(!empty($_getvars['platform'])?'&platform='.$_getvars['platform']:''));

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
						{
							if( $_getvars['type']=='prepared')
								sm_set_action('prepared');
							else
								sm_set_action('view');
						}

				}
		}
	if (sm_action('view'))
		{
			sm_use('ui.interface');
			sm_use('ui.form');
			sm_use('tproduct');
			sm_use('tcompany');
			sm_use('tcoupon');
			sm_use('formatter');
			$ui = new TInterface();
			$product = new TProduct($_getvars['id']);
			if ($product->Exists())
				{
					if (!empty($_getvars['employee']))
						{
							$employee = new TEmployee($_getvars['employee']);
							if ($employee->Exists())
								$id_employee = $employee->ID();
						}

					sm_add_cssfile('product.css');

					sm_add_jsfile('product.js');
					$company = new TCompany($product->CompanyID());

					$info=$product->info;
					if (!$product->HasSidebarText())
						sm_add_body_class('onecolumb-cart-page');
					else
						sm_add_body_class('twocolumn-cart-page');

					$info['discount']=0;
					$info['quantity']=intval($_postvars['quantity']);
					if ($info['quantity']<1 || !$product->isMultipleQuantityAllowed())
						$info['quantity']=1;
					$info['shipping']=0;
					$info['price']=floatval($product->Price());
					$info['years']=range(intval(date('Y')), intval(date('Y'))+35);
					$info['multiple_qty_allowed']=$product->isMultipleQuantityAllowed();

					if ($product->isShippable())
						{
							$info['shippable']=true;
							$q=new TQuery('shipping');
							$q->Add('id_u', intval($product->CompanyID()));
							$q->OrderBy('sort, title');
							$q->Select();
							for ($i = 0; $i < $q->Count(); $i++)
								{
									$info['shipping_methods'][$i]['id']=$q->items[$i]['id'];
									if ($i==0)
										{
											$info['shipping_methods'][$i]['selected'] = true;
											$info['shipping_id']=intval($q->items[$i]['id']);
										}
									$info['shipping_methods'][$i]['title']=$q->items[$i]['title'];
									$info['shipping_methods'][$i]['price']=$q->items[$i]['price'];
									if (floatval($q->items[$i]['price'])>0)
										$info['shipping_methods'][$i]['title'].=' - $'.Formatter::Money($q->items[$i]['price']);
								}
						}
					//--------------------------------------------------------------------------
					sm_title($product->Title());
					//--------------------------------------------------------------------------
					$info['charge_url']='index.php?m=product&d=charge&id='.$product->ID().(!empty($id_employee)?'&employee='.$id_employee:'').(!empty($_getvars['platform'])?'&platform='.$_getvars['platform']:'');
					$info['apply_coupon_url']='index.php?m=product&d=applycoupon&id='.$product->ID().(!empty($id_employee)?'&employee='.$id_employee:'').(!empty($_getvars['platform'])?'&platform='.$_getvars['platform']:'');
					$info['coupon_form_visible']=TCoupon::isApplicableCouponForProductExists($product, $product->CompanyID());
					if ($info['coupon_form_visible'])
						{
							if (!empty($_getvars['discount']))
								{
									$coupon=new TCoupon($_getvars['discount']);
									if ($coupon->Exists() && $coupon->CompanyID()==$product->CompanyID() && $coupon->isApplicableForProduct($product))
										{
											$info['discount']=$coupon->CalculateDiscount($product->Price());
											$info['coupon_discount_text']='Coupon '.$coupon->CodeUppercased().'. Discount: <b>$'.Formatter::Money($info['discount']).'</b>';
											$info['charge_url'].='&coupon='.$coupon->ID();
										}
								}
						}
					//--------------------------------------------------------------------------
					$info['total']=($product->Price()-floatval($info['discount']))*$info['quantity']+$info['shipping'];
					$info['total_text']='$'.Formatter::Money($info['total']);
					if (count($_postvars)>0)
						{
							foreach ($_postvars as $key=>$val)
								$info['form'][$key]=$val;
						}
					if ($product->isAskForPasswordOnCheckout())
						{
							$info['account_section']=true;
							$info['ask_for_password']=true;
							$info['form']['account_email']=$info['form']['product_email'];
						}
					else
						$info['account_section']=false;
					//--------------------------------------------------------------------------
					$ui->AddTPL('product.tpl', 'view', $info);
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
								$form.find(\'.product-payment-errors\').text(response.error.message);
								$form.find(\'.product-payment-errors\').show();
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
							  $(\'#payment-form\').submit(function(e) {
								var $form = $(this);
								$form.find(\'.product-payment-errors\').text("");
						
								// Disable the submit button to prevent repeated clicks
								$form.find(\'button\').prop(\'disabled\', true);
								
								if (!checkEmail($("#product_email").val()))
									{
										$form.find(\'.product-payment-errors\').text("Wrong email");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#product_name").val()=="")
									{
										$form.find(\'.product-payment-errors\').text("Wrong name");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()=="")
									{
										$form.find(\'.product-payment-errors\').text("Wrong password");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()!=$("#password2").val())
									{
										$form.find(\'.product-payment-errors\').text("Passwords did not match");
										$form.find(\'.product-payment-errors\').show();
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
					if ($product->HasCSS())
						$ui->style($product->CSS());

					//--------------------------------------------------------------------------
					$ui->Output(true);
				}
		}
	if (sm_action('prepared'))
		{
			$m['module'] = sm_current_module();
			sm_use('ui.interface');
			sm_use('ui.form');
			sm_use('tproduct');
			sm_use('tcompany');
			sm_use('tcoupon');
			sm_use('formatter');
			$ui = new TInterface();
			$product=new TProduct($_getvars['id']);
			if ($product->Exists())
				{
					sm_add_cssfile('product.css');
					sm_add_jsfile('product.js');
					$company=new TCompany($product->CompanyID());
					$info=$product->info;
					$info['discount']=0;
					$info['quantity']=intval($_getvars['qty']);
					if(!empty($_postvars['product_quantity']))
						$info['quantity']=intval($_postvars['product_quantity']);
					if (empty($info['quantity']))
						exit('Something went wrong :(');
					if ($info['quantity']<1 || !$product->isMultipleQuantityAllowed())
						$info['quantity']=1;
					$info['shipping']=0;
					$info['token']=$_getvars['token'];
					$info['price']=floatval($product->Price());
					$info['years']=range(intval(date('Y')), intval(date('Y'))+35);
					$info['product_type']='prepared';
					$info['multiple_qty_allowed']=0;
					$info['stripe_secret_key']=$company->StripeSecretKey();
					$info['stripe_public_key']=$company->StripePublicKey();
					$info['company_name']=$company->Name();
					$info['charge_url']='index.php?m=product&d=charge&id='.$product->ID().'&type=prepared';
					$info['apply_coupon_url']='index.php?m=product&d=applycoupon&id='.$product->ID().'&returnto='.urlencode(sm_this_url());
					if ($product->isShippable())
						{
							$info['shippable']=true;
							$q=new TQuery('shipping');
							$q->Add('id_u', intval($product->CompanyID()));
							$q->OrderBy('sort, title');
							$q->Select();
							for ($i = 0; $i < $q->Count(); $i++)
								{
									$info['shipping_methods'][$i]['id']=$q->items[$i]['id'];
									if ($i==0)
										{
											$info['shipping_methods'][$i]['selected'] = true;
											$info['shipping_id']=intval($q->items[$i]['id']);
										}
									$info['shipping_methods'][$i]['title']=$q->items[$i]['title'];
									$info['shipping_methods'][$i]['price']=$q->items[$i]['price'];
									if (floatval($q->items[$i]['price'])>0)
										$info['shipping_methods'][$i]['title'].=' - $'.Formatter::Money($q->items[$i]['price']);
								}
						}
					//--------------------------------------------------------------------------
					sm_title($product->Title());
					//--------------------------------------------------------------------------
					$info['coupon_form_visible']=TCoupon::isApplicableCouponForProductExists($product, $product->CompanyID());
					if ($info['coupon_form_visible'])
						{
							if (!empty($_getvars['discount']))
								{
									$coupon=new TCoupon($_getvars['discount']);
									if ($coupon->Exists() && $coupon->CompanyID()==$product->CompanyID() && $coupon->isApplicableForProduct($product))
										{
											$info['discount']=$coupon->CalculateDiscount($product->Price());
											$info['coupon_discount_text']='Coupon '.$coupon->CodeUppercased().'. Discount: <b>$'.Formatter::Money($info['discount']).'</b>';
											$info['charge_url'].='&coupon='.$coupon->ID();
										}
								}
						}
					//--------------------------------------------------------------------------
					$info['total']=($product->Price()-floatval($info['discount']))*$info['quantity']+$info['shipping'];
					$info['price_to_show']=$info['total'];
					$info['total_text']='$'.Formatter::Money($info['total']);
					if (count($_postvars)>0)
						{
							foreach ($_postvars as $key=>$val)
								$info['form'][$key]=$val;
						}
					if ($product->isAskForPasswordOnCheckout())
						{
							$info['account_section']=true;
							$info['ask_for_password']=true;
							$info['form']['account_email']=$info['form']['product_email'];
						}
					else
						$info['account_section']=false;
					if (!empty($error_message))
						$info['error_message']=$error_message;
					//--------------------------------------------------------------------------
					$ui->AddTPL('product.tpl', 'view', $info);
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
								$form.find(\'.product-payment-errors\').text(response.error.message);
								$form.find(\'.product-payment-errors\').show();
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
							  $(\'#payment-form\').submit(function(e) {
								var $form = $(this);
								$form.find(\'.product-payment-errors\').text("");
						
								// Disable the submit button to prevent repeated clicks
								$form.find(\'button\').prop(\'disabled\', true);
								
								if (!checkEmail($("#product_email").val()))
									{
										$form.find(\'.product-payment-errors\').text("Wrong email");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#product_name").val()=="")
									{
										$form.find(\'.product-payment-errors\').text("Wrong name");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()=="")
									{
										$form.find(\'.product-payment-errors\').text("Wrong password");
										$form.find(\'.product-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else if ($("#password").val()!=$("#password2").val())
									{
										$form.find(\'.product-payment-errors\').text("Passwords did not match");
										$form.find(\'.product-payment-errors\').show();
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
					if ($product->HasCSS())
						$ui->style($product->CSS());
//					if ($company->HasLogo())
//						{
//							$ui->style("
//								.navbar-brand {
//									margin: 0;
//									background: url(".sm_homepage().$company->Logo().") no-repeat left center;
//									min-width: 300px;
//									text-indent: -9999px;
//								}
//							");
//						}
					//--------------------------------------------------------------------------
					$ui->Output(true);
				}
		}


	if (sm_action('thankyou'))
		{
			sm_use('tproduct');
			sm_use('tcompany');
			sm_use('formatter');
			$product=new TProduct($_getvars['id']);
			if ($product->Exists())
				{
					$company=new TCompany($product->CompanyID());
					sm_title('Thank you');
					include_once('includes/admininterface.php');
					$ui = new TInterface();
					if ($product->HasThankYouText())
						{
							$ui->html($product->ThankYouText());
						}
					else
						{
							$ui->p('You have successfully submitted your order.');
						}
					$ui->Output(true);
				}
		}
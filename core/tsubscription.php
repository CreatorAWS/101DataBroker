<?php

	if (!defined("TSubscription_DEFINED"))
		{
			Class TSubscription extends TGenericObject
				{

					/**
					 * @var TPlan $plan
					 */
					protected $plan=NULL;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('subscriptions')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TSubscription'][$id]))
								{
									$object = new TSubscription($id);
									if ($object->Exists())
										$sm['cache']['TSubscription'][$id] = $object->GetRawData();
								}
							else
								$object = new TSubscription($sm['cache']['TSubscription'][$id]);
							return $object;
						}

					public static function InitWithStripeSubscriptionID($company_or_id, $stripe_id)
						{
							$subscription=new TSubscription(
								TQuery::ForTable('subscriptions')
									->Add('id_company', Cleaner::IntObjectID($company_or_id))
									->Add('subscription_stripe_id', dbescape($stripe_id))
									->Get()
							);
							return $subscription;
						}

				/**
				 * @param $company_or_id
				 * @param TPlan $plan
				 * @return TSubscription
				 */
					public static function Create($company_or_id, $plan, $qty, $email)
						{
							$subscription=new TSubscription(
								TQuery::ForTable('subscriptions')
									->Add('id_company', Cleaner::IntObjectID($company_or_id))
									->Add('added_time', time())
									->Insert()
							);
							$subscription->SetPlan($plan->ID(), $qty);
							$subscription->SetTrialPeriodDays($plan->TrialPeriodDays());
							$subscription->SetInitialSetupFee($plan->SetupFee());
							$subscription->SetInitialSubscriptionPrice($plan->Price());
							$subscription->SetEmail($email);
							if ($plan->HasTrialPeriod())
								$subscription->SetExpirationTimestamp(time()+$plan->TrialPeriodDays()*24*3600);
							else
								{
									$subscription->SetLastSuccessfulPaymentTimestamp(time());
									$subscription->SetBillingCyclesPaid(1);
									$subscription->SetExpirationTimestamp(time());
									$subscription->RenewExpirationTime();
								}
							return $subscription;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}


					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company' => intval($val)));
						}

					function PlanID()
						{
							return intval($this->info['id_plan']);
						}

					function Plan($owerwrite_cached=false)
						{
							if ($this->plan===NULL || $owerwrite_cached)
								{
									$this->plan=new TPlan($this->PlanID());
								}
							return $this->plan;
						}

					function SetPlan($plan_id, $qty)
						{
							$this->UpdateValues(Array(
								'id_plan' => intval($plan_id),
								'plan_quantity' => intval($qty)
							));
						}

					function PlanQuantity()
						{
							return intval($this->info['plan_quantity']);
						}

					function SetPlanQuantity($quantity)
						{
							if ($quantity!=$this->PlanQuantity())
								{
									$stripe_subscription=\Stripe\Subscription::retrieve($this->StripeSubscriptionID());
									for ($i=0; $i<count($stripe_subscription->items->data); $i++)
										{
											if ($this->Plan()->StripePlanID()==$stripe_subscription->items->data[$i]->plan->id)
												{
													$si=\Stripe\SubscriptionItem::retrieve($stripe_subscription->items->data[$i]->id);
													$si->quantity=$quantity;
													$si->save();
													$this->UpdateValues(Array('plan_quantity'=>intval($quantity)));
													break;
												}
										}
								}
						}

					function FirstName()
						{
							return $this->info['first_name'];
						}

					function ContactName()
						{
							return trim($this->FirstName().' '.$this->LastName());
						}

					function SetFirstName($val)
						{
							$this->UpdateValues(Array('first_name' => $val));
						}

					function LastName()
						{
							return $this->info['last_name'];
						}

					function SetLastName($val)
						{
							$this->UpdateValues(Array('last_name' => $val));
						}

					function Address1()
						{
							return $this->info['address1'];
						}

					function SetAddress1($val)
						{
							$this->UpdateValues(Array('address1' => $val));
						}

					function HasAddress1()
						{
							return !empty($this->info['address1']);
						}


					function Address2()
						{
							return $this->info['address2'];
						}

					function SetAddress2($val)
						{
							$this->UpdateValues(Array('address2' => $val));
						}

					function HasAddress2()
						{
							return !empty($this->info['address2']);
						}


					function City()
						{
							return $this->info['city'];
						}

					function SetCity($val)
						{
							$this->UpdateValues(Array('city' => $val));
						}

					function HasCity()
						{
							return !empty($this->info['city']);
						}

					function State()
						{
							return $this->info['state'];
						}

					function SetState($val)
						{
							$this->UpdateValues(Array('state' => $val));
						}

					function HasCouponCode()
						{
							return !empty($this->info['coupon_code']);
						}

					function CouponCode()
						{
							return $this->info['coupon_code'];
						}

					function SetCouponCode($val)
						{
							$this->UpdateValues(Array('coupon_code' => $val));
						}

					function HasState()
						{
							return !empty($this->info['state']);
						}

					function Zip()
						{
							return $this->info['zip'];
						}

					function SetZip($val)
						{
							$this->UpdateValues(Array('zip' => $val));
						}

					function HasZip()
						{
							return !empty($this->info['zip']);
						}


					function Country()
						{
							return $this->info['country'];
						}

					function SetCountry($val)
						{
							$this->UpdateValues(Array('country' => $val));
						}

					function Email()
						{
							return $this->info['email'];
						}

					function SetEmail($val)
						{
							$this->UpdateValues(Array('email' => $val));
						}

					function Cellphone()
						{
							return $this->info['cellphone'];
						}

					function SetCellphone($val)
						{
							$this->UpdateValues(Array('cellphone' => $val));
						}

					function HasCellphone()
						{
							return !empty($this->info['cellphone']);
						}


					function AddedTimestamp()
						{
							return intval($this->info['added_time']);
						}

					function SetAddedTimestamp($val)
						{
							$this->UpdateValues(Array('added_time' => intval($val)));
						}

					function StripeCustomerID()
						{
							return $this->info['stripe_id'];
						}

					function SetStripeCustomerID($val)
						{
							$this->UpdateValues(Array('stripe_id' => $val));
						}

					function StripeCardID()
						{
							return $this->info['card_stripe_id'];
						}

					function SetStripeCardID($stripe_source_id, $process_stripe)
						{
							if ($process_stripe)
								{
									$stripe_customer=\Stripe\Customer::retrieve($this->StripeCustomerID());
									$stripe_customer->source=$stripe_source_id;
									$stripe_customer->save();
								}
							$this->UpdateValues(Array('card_stripe_id' => $stripe_source_id));
						}

					function StripeSubscriptionID()
						{
							return $this->info['subscription_stripe_id'];
						}

					function SetStripeSubscriptionID($val)
						{
							$this->UpdateValues(Array('subscription_stripe_id' => $val));
						}

					function Discount()
						{
							return floatval($this->info['discount']);
						}

					function SetDiscount($amount)
						{
							if ($amount!=$this->Discount())
								{
									$amount_cents = intval($amount * 100);
									$stripe_subscription = \Stripe\Subscription::retrieve($this->StripeSubscriptionID());
									if ($this->HasCouponStripeID())
										{
											$stripe_subscription->deleteDiscount();
											$this->UpdateValues(Array('discount' => 0));
											$cpn = \Stripe\Coupon::retrieve($this->CouponStripeID());
											$cpn->delete();
											$this->SetCouponStripeID('');
										}
									if ($amount_cents>0)
										{
											$coupon = \Stripe\Coupon::create(array(
													"amount_off" => $amount_cents,
													"duration" => "forever",
													"currency" => $this->Plan()->CurrencyISOLowercased())
											);
											$this->SetCouponStripeID($coupon->id);
											$stripe_subscription->coupon = $this->CouponStripeID();
											$stripe_subscription->save();
											$this->UpdateValues(Array('discount' => floatval($amount)));
										}
								}
						}


					function HasDiscount()
						{
							return $this->Discount()>0;
						}


					function CouponStripeID()
						{
							return $this->info['coupon_stripe_id'];
						}

					function SetCouponStripeID($val)
						{
							$this->UpdateValues(Array('coupon_stripe_id' => $val));
						}

					function HasCouponStripeID()
						{
							return !empty($this->info['coupon_stripe_id']);
						}


					function TrialPeriodDays()
						{
							return intval($this->info['trial_period_days']);
						}

					function SetTrialPeriodDays($val)
						{
							$this->UpdateValues(Array('trial_period_days' => intval($val)));
						}

					function InitialSetupFee()
						{
							return floatval($this->info['initial_setup_fee']);
						}

					function SetInitialSetupFee($val)
						{
							$this->UpdateValues(Array('initial_setup_fee' => floatval($val)));
						}

					function HasInitialSetupFee()
						{
							return !empty($this->info['initial_setup_fee']);
						}


					function InitialSubscriptionPrice()
						{
							return floatval($this->info['initial_subscription_price']);
						}

					function SetInitialSubscriptionPrice($val)
						{
							$this->UpdateValues(Array('initial_subscription_price' => floatval($val)));
						}

					function BillingCyclesPaid()
						{
							return intval($this->info['billing_cycles_paid']);
						}

					function SetBillingCyclesPaid($val)
						{
							$this->UpdateValues(Array('billing_cycles_paid' => intval($val)));
						}

					function LastSuccessfulPaymentTimestamp()
						{
							return intval($this->info['last_successful_payment_time']);
						}

					function SetLastSuccessfulPaymentTimestamp($val)
						{
							$this->UpdateValues(Array('last_successful_payment_time' => intval($val)));
						}

					function ExpirationTimestamp()
						{
							return intval($this->info['expiration_time']);
						}

					function SetExpirationTimestamp($val)
						{
							$this->UpdateValues(Array('expiration_time' => intval($val)));
						}

					function RenewExpirationTime()
						{
							$plan=TPlan::UsingCache($this->PlanID());
							if ($plan->IntervalType()=='year')
								$period=365*24*3600;
							elseif ($plan->IntervalType()=='month')
								$period=31*24*3600;
							elseif ($plan->IntervalType()=='week')
								$period=7*24*3600;
							else
								$period=24*3600;
							$this->SetExpirationTimestamp(time()+$period);
						}

					function UnsuccessfulPaymentsCount()
						{
							return intval($this->info['unsuccessful_payments_count']);
						}

					function SetUnsuccessfulPaymentsCount($val)
						{
							$this->UpdateValues(Array('unsuccessful_payments_count' => intval($val)));
						}

					function CancelledTimestamp()
						{
							return intval($this->info['cancelled']);
						}

					function SetCancelledTimestamp($val)
						{
							$this->UpdateValues(Array('cancelled' => intval($val)));
						}

					function HasCancelledTimestamp()
						{
							return !empty($this->info['cancelled']);
						}

					function isCancelled()
						{
							return $this->HasCancelledTimestamp();
						}

					function Cancel($process_stripe=true)
						{
							if (!$this->isCancelled())
								{
									if ($process_stripe)
										{
											try
												{
													$stripe_subscription=\Stripe\Subscription::retrieve($this->StripeSubscriptionID());
													$stripe_subscription->cancel();
												}
											catch (\Stripe\Error\InvalidRequest $e)
												{
													//
												}
										}
									$this->SetCancelledTimestamp(time());
									$this->CreateCancelSubscriptionWebhook();
								}
							sm_event('subscription-cancel', Array($this->ID()));
						}

					function AddressFormatted()
						{
							$r=Array();
							if ($this->HasAddress1())
								$r[]=$this->Address1();
							if ($this->HasAddress2())
								$r[]=$this->Address2();
							if ($this->HasCity())
								$r[]=$this->City();
							if ($this->HasState())
								$r[]=$this->State();
							if ($this->HasZip())
								$r[]=$this->Zip();
							if (count($r)>0)
								$r[]=$this->Country();
							return implode(', ', $r);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('subscriptions');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							TQuery::ForTable('subscriptions')->AddWhere('id', intval($this->ID()))->Remove();
						}

					function PublicHash()
						{
							return md5($this->ID().'-'.$this->StripeSubscriptionID());
						}

					function CreateNewSubscriptionWebhook($posted_data=[])
						{
							if (!$this->Plan()->HasWebhookURL())
								return;
							$data=[
								'webhook'=>'subscription-create',
								'time'=>time()
							];
							$data['subscription']['id']=$this->ID();
							$data['subscription']['expiration']=$this->ExpirationTimestamp();
							if ($this->Plan()->isAskForPasswordOnCheckout())
								$data['subscription']['password']=$posted_data['password'];
							$data['plan']['id']=$this->PlanID();
							$data['plan']['quantity']=$this->PlanQuantity();
							$data['plan']['title']=$this->Plan()->Title();
							$data['contacts']['first_name']=$this->FirstName();
							$data['contacts']['last_name']=$this->LastName();
							$data['contacts']['cellphone']=$this->Cellphone();
							$data['contacts']['email']=$this->Email();
							$data['address']['line1']=$this->Address1();
							$data['address']['line2']=$this->Address2();
							$data['address']['city']=$this->City();
							$data['address']['state']=$this->State();
							$data['address']['zip']=$this->Zip();
							$data['address']['country']=$this->Country();
							TWebhookItem::Create(
								$this->Plan()->WebhookURL(),
								$data
							);
						}

					function CreateRenewSubscriptionWebhook()
						{
							if (!$this->Plan()->HasWebhookURL())
								return;
							$data=[
								'webhook'=>'subscription-renew',
								'time'=>time()
							];
							$data['subscription']['id']=$this->ID();
							$data['subscription']['expiration']=$this->ExpirationTimestamp();
							TWebhookItem::Create(
								$this->Plan()->WebhookURL(),
								$data
							);
						}

					function CreateCancelSubscriptionWebhook()
						{
							if (!$this->Plan()->HasWebhookURL())
								return;
							$data=[
								'webhook'=>'subscription-cancel',
								'time'=>time()
							];
							$data['subscription']['id']=$this->ID();
							TWebhookItem::Create(
								$this->Plan()->WebhookURL(),
								$data
							);
						}

					function HasEmployeeID()
						{
							return !empty($this->info['id_employee']);
						}

					function EmployeeID()
						{
							return intval($this->info['id_employee']);
						}

					function SetEmployeeID($val)
						{
							$this->UpdateValues(Array('id_employee' => intval($val)));
						}

					function wasPurchasePaymentMade($employee)
						{
							/** @var $employee TEmployee */
							return intval($employee->GetMetaData('subscription_payment_made_'.$this->PlanID()))>0;
						}
					function AddPointsForEmployer()
						{
							/** @var $employee TEmployee */
							if ($this->HasEmployeeID())
								{
									$employee = TEmployee::withID($this->EmployeeID());
									if ($employee->Exists() && $employee->isNotDeleted())
										{
											if (!$this->wasPurchasePaymentMade($employee))
												{
													$employee->SetMetaData('subscription_payment_made_'.$this->PlanID(), time());
													$company = TCompany::UsingCache($employee->CompanyID());

													$price = 0;
													$id_plan = 0;
													$plan = new TPlan($this->PlanID());
													if ($plan->Exists())
														{
															$price = $plan->BuyPrice();
															$id_plan = $plan->ID();
														}

													if (empty($price))
														{
															if ($company->isPaymentTypeLeadsMoney())
																{
																	$price=GlobalSettings::DefaultPurchasePaymentMoney();
																}
															elseif ($company->isPaymentTypeLeadsPoints())
																{
																	$price=GlobalSettings::DefaultPurchasePaymentPoints();
																}
														}
													if ($company->isPaymentTypeLeadsMoney())
														{
															$price_currency='USD';
															$employee->SchedulePaymentMoney(0, $price, GlobalSettings::DefaultLeadPaymentMoneyDelaySeconds(), 0, 0, $id_plan);
														}
													elseif ($company->isPaymentTypeLeadsPoints())
														{
															$price_currency='bonus points';
															$employee->SchedulePaymentPoints(0, $price, GlobalSettings::DefaultLeadPaymentPointsDelaySeconds(),  0, 0, $id_plan);
														}
													if ($price>0)
														$employee->SendSMS(
															sprintf('Congratulations, one of your connections made purchase a product and we\'re rewarding you %1$s %2$s! Thanks for sharing %3$s', $price, $price_currency, $company->Name()),
															$company->Cellphone()
														);
												}
										}
								}
						}
				}

			define("TSubscription_DEFINED", 1);
		}

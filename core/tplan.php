<?php

	if (!defined("TPlan_DEFINED"))
		{
			class TPlan extends TGenericObject
				{
					protected $metadata = NULL;

					function __construct($id_or_cahcedinfo = NULL)
						{
							if (is_array($id_or_cahcedinfo))
								{
									$this->info = $id_or_cahcedinfo;
								}
							else
								{
									$this->info = TQuery::ForTable('plans')->Add('id', intval($id_or_cahcedinfo))->Get();
								}
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TPlan'][$id]))
								{
									$object=new TPlan($id);
									if ($object->Exists())
										$sm['cache']['TPlan'][$id]=$object->GetRawData();
								}
							else
								$object=new TPlan($sm['cache']['TPlan'][$id]);
							return $object;
						}

					public static function Create($company_or_id)
						{
							$q=new TQuery('plans');
							$q->Add('id_company', Cleaner::IntObjectID($company_or_id));
							$object=new TPlan($q->Insert());
							return $object;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							if (strcmp($val, $this->Title())!=0)
								{
									$this->UpdateValues(Array('title'=>$val));
									if ($this->HasStripeProductID())
										{
											\Stripe\Product::update(
												$this->StripeProductID(),
												Array(
													'name'=>$this->Title()
												)
											);
										}
								}
						}

					function isDeleted()
						{
							return !empty($this->info['deleted']);
						}

					function HasStripePlanID()
						{
							return !empty($this->info['stripe_id']);
						}

					function StripePlanID()
						{
							return $this->info['stripe_id'];
						}

					function SetStripePlanID($val)
						{
							$this->UpdateValues(Array('stripe_id' => $val));
						}

					function isAskForPasswordOnCheckout()
						{
							return intval($this->info['ask_for_passwd'])==1;
						}

					function SetAskForPasswordOnCheckout($val_bool=true)
						{
							$this->UpdateValues(Array('ask_for_passwd' => $val_bool?1:0));
						}

					function StripeProductID()
						{
							return $this->info['stripe_product_id'];
						}

					function HasStripeProductID()
						{
							return !empty($this->info['stripe_product_id']);
						}

					function SetStripeProductID($val)
						{
							$this->UpdateValues(Array('stripe_product_id' => $val));
						}

					function StripeTitle()
						{
							return $this->info['title'];
						}

					function HasSetupFeeCustomTitle()
						{
							return !empty($this->info['setup_fee_title']);
						}

					function SetupFeeCustomTitle()
						{
							return $this->info['setup_fee_title'];
						}

					function SetupFeeTitle()
						{
							return $this->HasSetupFeeCustomTitle()?$this->SetupFeeCustomTitle():'Setup Fee';
						}

					function SetSetupFeeCustomTitle($val)
						{
							$this->UpdateValues(Array('setup_fee_title'=>$val));
						}

					function SetupFee()
						{
							return round(floatval($this->info['setup_fee']), 2);
						}

					function SetSetupFee($val)
						{
							$this->UpdateValues(Array('setup_fee'=>floatval($val)));
						}

					function SetupFeeInCents()
						{
							return intval($this->SetupFee() * 100);
						}

					function Price()
						{
							return round(floatval($this->info['price']), 2);
						}

					function SetPrice($val)
						{
							$this->UpdateValues(Array('price'=>floatval($val)));
						}

					function PriceInCents()
						{
							return intval($this->Price() * 100);
						}

					function CurrencyISOLowercased()
						{
							return 'usd';
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}

					function HasCSS()
						{
							return !empty($this->info['css']);
						}

					function SetThankYouText($val)
						{
							$this->UpdateValues(Array('thank_you_text'=>$val));
						}

					function HasThankYouText()
						{
							return !empty($this->info['thank_you_text']);
						}

					function ThankYouText()
						{
							return $this->info['thank_you_text'];
						}

					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text'=>$val));
						}

					function SidebarText()
						{
							return $this->info['sidebartext'];
						}

					function HasSidebarText()
						{
							return !empty($this->info['sidebartext']);
						}

					function SetSidebarText($val)
						{
							$this->UpdateValues(Array('sidebartext'=>$val));
						}

					function RedirectAfterCheckoutURL()
						{
							if (!empty($this->info['redirect_after_checkout']))
								return $this->info['redirect_after_checkout'];
							else
								return 'index.php?m=subscription&d=thankyou&id='.$this->ID();
						}

					function RedirectAfterCheckoutURLValue()
						{
							return $this->info['redirect_after_checkout'];
						}

					function SetRedirectAfterCheckoutURLValue($val)
						{
							$this->UpdateValues(Array('redirect_after_checkout'=>$val));
						}

					function CSS()
						{
							return $this->info['css'];
						}

					function SetCSS($val)
						{
							$this->UpdateValues(Array('css'=>$val));
						}

					function HasLogo()
						{
							return file_exists('files/img/logoplan'.$this->ID().'.png');
						}

					function LogoURL()
						{
							return 'files/img/logoplan'.$this->ID().'.png';
						}

					function AddLogo($filename)
						{
							if (file_exists($filename))
								{
									$logofile = 'files/img/logoplan'.$this->ID().'.png';
									if (file_exists($logofile))
										unlink($logofile);
									copy($filename, $logofile);
								}
						}

					function RemoveLogo()
						{
							$logofile = 'files/img/logoplan'.$this->ID().'.png';
							if (file_exists($logofile))
								unlink($logofile);
						}

					function FrontendURL()
						{
							return sm_homepage().'plan-'.$this->ID();
						}

					function TrialPeriodDays()
						{
							return intval($this->info['trial_period_days']);
						}

					function SetTrialPeriodDays($val)
						{
							$this->UpdateValues(Array('trial_period_days'=>intval($val)));
						}

					function HasTrialPeriod()
						{
							return !empty($this->info['trial_period_days']);
						}

					function isSetupFeeChargedBeforeTrialPeriod()
						{
							return !empty($this->info['setup_fee_trial_start']);
						}

					function SetSetupFeeChargedBeforeTrialPeriod($val_bool=true)
						{
							$this->UpdateValues(Array('setup_fee_trial_start' => $val_bool?1:0));
						}

					function IntervalType()
						{
							return $this->info['interval'];
						}

					function SetIntervalType($val)
						{
							$this->UpdateValues(Array('interval'=>$val));
						}

					function IntervalCount()
						{
							return intval($this->info['interval_count']);
						}

					function SetIntervalCount($val)
						{
							$this->UpdateValues(Array('interval_count'=>intval($val)));
						}

					function MinAvailableQuantity()
						{
							return intval($this->info['qty_min_available']);
						}

					function SetMinAvailableQuantity($val)
						{
							$this->UpdateValues(Array('qty_min_available'=>intval($val)));
						}

					function MaxAvailableQuantity()
						{
							return intval($this->info['qty_max_available']);
						}

					function SetMaxAvailableQuantity($val)
						{
							$this->UpdateValues(Array('qty_max_available'=>intval($val)));
						}

					function StepForAvailableQuantity()
						{
							return intval($this->info['qty_step']);
						}

					function SetStepForAvailableQuantity($val)
						{
							$this->UpdateValues(Array('qty_step'=>intval($val)));
						}

					function TitleForItemQuantity()
						{
							return $this->info['qty_title'];
						}

					function SetTitleForItemQuantity($val)
						{
							$this->UpdateValues(Array('qty_title'=>$val));
						}

					function UpdateValues($params)
						{
							if (!is_array($params))
								return;
							unset($params['id']);
							if (empty($params))
								return;
							$q = new TQuery('plans');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', $this->ID());
						}

					function Remove()
						{
							if ($this->HasStripePlanID())
								{
									$stripe_plan = \Stripe\Plan::retrieve($this->StripePlanID());
									$stripe_plan->delete();
								}
							if ($this->HasStripeProductID())
								{
									$product = \Stripe\Product::retrieve($this->StripeProductID());
									$product->delete();
								}
							$q = new TQuery('plans');
							$q->Add('deleted', time());
							$q->Update('id', intval($this->ID()));
							if (file_exists($this->HasLogo()))
								$this->RemoveLogo();
						}

					function CanStripePlanBeEdited()
						{
							if ($this->HasStripePlanID())
								return false;
							else
								return true;
						}

					function SubscribersCount()
						{
							return 0;
						}

					function CanBeDeleted()
						{
							return $this->SubscribersCount()==0;
						}

					function CreateStripePlan()
						{
							$stripe_product=\Stripe\Product::create(array(
							  "name" => $this->Title(),
							  "type" => "service"
							));
							$this->SetStripeProductID($stripe_product->id);
							$stripe_plan = \Stripe\Plan::create(Array(
							  'product' => $this->StripeProductID(),
							  'interval' => $this->IntervalType(),
							  'interval_count' => $this->IntervalCount(),
							  'currency' => $this->CurrencyISOLowercased(),
							  'amount' => $this->PriceInCents(),
							));
							$this->SetStripePlanID($stripe_plan->id);
						}

					function HasMultipleItems()
						{
							return $this->MaxAvailableQuantity()>1;
						}

					function HasWebhookURL()
						{
							return !empty($this->info['webhook_url']);
						}

					function WebhookURL()
						{
							return $this->info['webhook_url'];
						}

					function SetWebhookURL($val)
						{
							$this->UpdateValues(Array('webhook_url'=>$val));
						}

					function isPLanTypePackage()
						{
							return $this->info['type'] == 'package';
						}

					function PLanType()
						{
							return $this->info['type'];
						}

					function SetPLanType($val)
						{
							$this->UpdateValues(Array('type'=>$val));
						}


					function LoadMetaData()
						{
							if ($this->metadata === NULL)
								{
									$data = getsqlarray("SELECT * FROM products_metadata WHERE object_id=" . intval($this->ID()));
									for ($i = 0; $i < count($data); $i++)
										$this->metadata[$data[$i]['key_name']] = $data[$i]['val'];
								}
						}

					function GetMetaData($key)
						{
							$this->LoadMetaData();
							return $this->metadata[$key];
						}
					function SetMetaData($key, $value, $use_empty_as_null = false)
						{
							if (empty($value) && $use_empty_as_null) $value = NULL;
							$this->LoadMetaData();
							$q = new TQuery('products_metadata');
							$q->Add('object_id', $this->ID());
							$q->Add('key_name', dbescape($key));
							$info = $q->Get();
							if (!empty($info['id']))
								{
									if ($value === NULL)
										{
											$q = new TQuery('products_metadata');
											$q->Add('id', intval($info['id']));
											$q->Remove();
											unset($this->metadata[$key]);
										} else
										{
											$q = new TQuery('products_metadata');
											$q->Add('val', dbescape($value));
											$q->Update('id', intval($info['id']));
											$this->metadata[$key] = $value;
										}
								} elseif ($value !== NULL)
								{
									$q = new TQuery('products_metadata');
									$q->Add('object_id', $this->ID());
									$q->Add('key_name', dbescape($key));
									$q->Add('val', dbescape($value));
									$q->Insert();
									$this->metadata[$key] = $value;
								} else
								unset($this->metadata[$key]);
						}

				}

			define("TPlan_DEFINED", 1);
		}

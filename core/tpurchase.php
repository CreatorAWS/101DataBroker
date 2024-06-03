<?php

	if (!defined("TPurchase_DEFINED"))
		{
			Class TPurchase
				{
					/**
					 * @var TProduct $plan
					 */
					protected $product=NULL;

					protected $info;

					function ID()
						{
							return intval($this->info['id']);
						}

					function CompanyID()
						{
							return intval($this->info['id_u']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_u' => intval($val)));
						}

					function Product($owerwrite_cached=false)
						{
							if ($this->product===NULL || $owerwrite_cached)
								{
									$this->product=new TProduct($this->ProductID());
								}
							return $this->product;
						}

					function ProductID()
						{
							return intval($this->info['id_p']);
						}

					function SetProductID($val)
						{
							$this->UpdateValues(Array('id_p' => intval($val)));
						}


					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('title' => $val));
						}


					function Price()
						{
							return floatval($this->info['price']);
						}

					function SetPrice($val)
						{
							$this->UpdateValues(Array('price' => floatval($val)));
						}


					function Qty()
						{
							return intval($this->info['qty']);
						}

					function SetQty($val)
						{
							$this->UpdateValues(Array('qty' => intval($val)));
						}


					function Discount()
						{
							return floatval($this->info['discount']);
						}

					function SetDiscount($val)
						{
							$this->UpdateValues(Array('discount' => floatval($val)));
						}


					function HasDiscount()
						{
							return !empty($this->info['discount']);
						}


					function Shipping()
						{
							return floatval($this->info['shipping']);
						}

					function SetShipping($val)
						{
							$this->UpdateValues(Array('shipping' => floatval($val)));
						}


					function HasShipping()
						{
							return !empty($this->info['shipping']);
						}


					function ShippingID()
						{
							return intval($this->info['shipping_id']);
						}

					function SetShippingID($val)
						{
							$this->UpdateValues(Array('shipping_id' => intval($val)));
						}


					function HasShippingID()
						{
							return !empty($this->info['shipping_id']);
						}


					function ShippingName()
						{
							return $this->info['shipping_name'];
						}

					function SetShippingName($val)
						{
							$this->UpdateValues(Array('shipping_name' => $val));
						}


					function Total()
						{
							return floatval($this->info['total']);
						}

					function SetTotal($val)
						{
							$this->UpdateValues(Array('total' => floatval($val)));
						}


					function CouponCode()
						{
							return $this->info['coupon_code'];
						}

					function SetCouponCode($val)
						{
							$this->UpdateValues(Array('coupon_code' => $val));
						}


					function HasCouponCode()
						{
							return !empty($this->info['coupon_code']);
						}


					function TimeBought()
						{
							return intval($this->info['timebought']);
						}

					function SetTimeBought($val)
						{
							$this->UpdateValues(Array('timebought' => intval($val)));
						}


					function CustomerEmailLegacy()
						{
							return $this->info['customeremail'];
						}

					function SetCustomerEmailLegacy($val)
						{
							$this->UpdateValues(Array('customeremail' => $val));
						}


					function HasCustomerEmailLegacy()
						{
							return !empty($this->info['customeremail']);
						}


					function StripeToken()
						{
							return $this->info['stripetoken'];
						}

					function SetStripeToken($val)
						{
							$this->UpdateValues(Array('stripetoken' => $val));
						}


					function CustomerName()
						{
							return $this->info['name'];
						}

					function FirstName()
						{
							$name=explode(' ', $this->CustomerName());
							return $first_name=$name[0];
						}

					function LastName()
						{
							$name=explode(' ', $this->CustomerName());
							return $last_name=$name[1];
						}

					function SetCustomerName($val)
						{
							$this->UpdateValues(Array('name' => $val));
						}


					function CustomerEmail()
						{
							return $this->info['email'];
						}

					function SetCustomerEmail($val)
						{
							$this->UpdateValues(Array('email' => $val));
						}


					function HasCustomerEmail()
						{
							return !empty($this->info['email']);
						}


					function CustomerPhone()
						{
							return $this->info['phone'];
						}

					function SetCustomerPhone($val)
						{
							$this->UpdateValues(Array('phone' => $val));
						}


					function CustomerAddressLine1()
						{
							return $this->info['address_line1'];
						}

					function SetCustomerAddressLine1($val)
						{
							$this->UpdateValues(Array('address_line1' => $val));
						}


					function CustomerAddressLine2()
						{
							return $this->info['address_line2'];
						}

					function SetCustomerAddressLine2($val)
						{
							$this->UpdateValues(Array('address_line2' => $val));
						}


					function CustomerCity()
						{
							return $this->info['city'];
						}

					function SetCustomerCity($val)
						{
							$this->UpdateValues(Array('city' => $val));
						}


					function CustomerState()
						{
							return $this->info['state'];
						}

					function SetCustomerState($val)
						{
							$this->UpdateValues(Array('state' => $val));
						}


					function CustomerZip()
						{
							return $this->info['zip'];
						}

					function SetCustomerZip($val)
						{
							$this->UpdateValues(Array('zip' => $val));
						}


					function isDownloadable()
						{
							return !empty($this->info['downloadable']);
						}

					function SetDownloadable($val = true)
						{
							$this->UpdateValues(Array('downloadable' => $val ? 1 : 0));
						}

					function UnsetDownloadable()
						{
							$this->UpdateValues(Array('downloadable' => 0));
						}

					function isDownloadURLSent()
						{
							return !empty($this->info['download_link_sent']);
						}

					function SetDownloadURLSent($val = true)
						{
							$this->UpdateValues(Array('download_link_sent' => $val ? time() : 0));
						}


					function DownloadedTimes()
						{
							return intval($this->info['downloaded_times']);
						}

					function SetDownloadedTimes($val)
						{
							$this->UpdateValues(Array('downloaded_times' => intval($val)));
						}


					function MaxDownloads()
						{
							return intval($this->info['max_downloads']);
						}

					function SetMaxDownloads($val)
						{
							$this->UpdateValues(Array('max_downloads' => intval($val)));
						}

					function DownloadURLHash()
						{
							return $this->info['download_url_hash'];
						}

					function SetDownloadURLHash($val)
						{
							$this->UpdateValues(Array('download_url_hash' => $val));
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('purchases');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('purchases')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}

					public static function initWithHash($hash)
						{
							$data=TQuery::ForTable('purchases')->AddWhere('download_url_hash', dbescape($hash))->Get();
							$object=new TPurchase($data);
							return $object;
						}

					function CustomerDownloadURL()
						{
							return 'https://'.main_domain().'/index.php?m=purchasedownload&hash='.urlencode($this->DownloadURLHash());
						}

					function CreateNewPurchaseWebhook($posted_data=[])
						{
							if (!$this->Product()->HasWebhookURL())
								return;
							$data=[
								'webhook'=>'purchase-create',
								'time'=>time()
							];
							$data['purchase']['id']=$this->ID();
							if ($this->Product()->isAskForPasswordOnCheckout())
								$data['purchase']['password']=$posted_data['password'];
							$data['purchase']['quantity']=$this->Qty();
							$data['purchase']['product_id']=$this->ProductID();
							$data['contacts']['token']=$posted_data['product_token'];
							$data['contacts']['first_name']=$this->FirstName();
							$data['contacts']['last_name']=$this->LastName();
							$data['contacts']['cellphone']=$this->CustomerPhone();
							$data['contacts']['email']=$this->CustomerEmail();
							$data['address']['line1']=$this->CustomerAddressLine1();
							$data['address']['line2']=$this->CustomerAddressLine2();
							$data['address']['city']=$this->CustomerCity();
							$data['address']['state']=$this->CustomerState();
							$data['address']['zip']=$this->CustomerZip();
							TWebhookItem::Create(
								$this->Product()->WebhookURL(),
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
					function Remove()
						{
							TQuery::ForTable('purchases')->AddWhere('id', intval($this->ID()))->Remove();
						}
				}

			define("TPurchase_DEFINED", 1);
		}

<?php

	use GS\Common\Interfaces\ShreableInterface;

	if (!defined("TProduct_DEFINED"))
	{
		class TProduct
			{
				var $info;
				protected $metadata = NULL;
				function __construct($id_or_cahcedinfo=NULL)
					{
						global $sm;
						if (is_array($id_or_cahcedinfo))
							{
								$this->info=$id_or_cahcedinfo;
							}
						else
							{
								$this->info=TQuery::ForTable('products')->Add('id', intval($id_or_cahcedinfo))->Get();
							}
					}
				function ID()
					{
						return intval($this->info['id']);
					}
				function GetRawData()
					{
						return $this->info;
					}
				function CompanyID()
					{
						return intval($this->info['id_u']);
					}

				function Title()
					{
						return $this->info['title'];
					}
				function Text()
					{
						return $this->info['text'];
					}
				function SetTitle($var)
					{
						$this->UpdateValues(Array('title'=>$var));
					}
				function SetRedirectAfterCheckout($var)
					{
						$this->UpdateValues(Array('redirect_after_checkout'=>$var));
					}

				function RedirectAfterCheckout()
					{
						return $this->info['redirect_after_checkout'];
					}
				function SetMultipleQuantityAllowed($var)
					{
						$this->UpdateValues(Array('multiple_qty_allowed'=>intval($var)));
					}
				function SetShippable($var)
					{
						$this->UpdateValues(Array('shippable'=>intval($var)));
					}
				function SetAskForPassword($var)
					{
						$this->UpdateValues(Array('ask_for_passwd'=>intval($var)));
					}
				function SetCustomDownloadPath($var)
					{
						$this->UpdateValues(Array('download_path'=>$var));
					}
				function SetCSS($var)
					{
						$this->UpdateValues(Array('css'=>$var));
					}
				function SetText($var)
					{
						$this->UpdateValues(Array('text'=>$var));
					}
				function SetSidebarText($var)
					{
						$this->UpdateValues(Array('sidebartext'=>$var));
					}
				function SetDownloadable($var)
					{
						$this->UpdateValues(Array('downloadable'=>intval($var)));
					}
				function CSS()
					{
						return $this->info['css'];
					}
				function StripeTitle()
					{
						return $this->info['title'];
					}
				function PurchasesCountTotal()
					{
						return intval($this->info['purchases_count_total']);
					}
				function isShippable()
					{
						return intval($this->info['shippable'])==1;
					}
				function isDownloadable()
					{
						return intval($this->info['downloadable'])==1;
					}
				function isMultipleQuantityAllowed()
					{
						return intval($this->info['multiple_qty_allowed'])==1;
					}
				function Price()
					{
						return round(floatval($this->info['price']), 2);
					}
				function SetPrice($var)
					{
						$this->UpdateValues(Array('price'=>round(floatval($var), 2)));
					}
				function PriceInCents()
					{
						return intval($this->Price()*100);
					}
				function Points()
					{
						return intval($this->info['points']);
					}
				function SetPoints($var)
					{
						$this->UpdateValues(Array('points'=>intval($var)));
					}

				function Order()
					{
						return $this->info['order'];
					}

				function SetOrder($val)
					{
						$this->UpdateValues(Array('order'=>intval($val)));
					}
				function Exists()
					{
						return !empty($this->info['id']);
					}
				function HasCSS()
					{
						return !empty($this->info['css']);
					}
				function HasThankYouText()
					{
						return !empty($this->info['thank_you_text']);
					}
				function HasSidebarText()
					{
						return !empty($this->info['sidebartext']);
					}
				function ThankYouText()
					{
						return $this->info['thank_you_text'];
					}
				function RedirectAfterCheckoutURL()
					{
						if (!empty($this->info['redirect_after_checkout']))
							return $this->info['redirect_after_checkout'];
						else
							return 'index.php?m=product&d=thankyou&id='.$this->ID();
					}
				function IncPurchasesCountTotal()
					{
						TQuery::ForTable('products')
							->Add('purchases_count_total=purchases_count_total+1')
							->Update('id', intval($this->ID()));
					}
				function HasLogo()
					{
						return file_exists('files/img/logoproduct'.$this->ID().'.png');
					}
				function LogoURL()
					{
						return 'files/img/logoproduct'.$this->ID().'.png';
					}
				function AddLogo($filename)
					{
						if (file_exists($filename))
							{
								$logofile='files/img/logoproduct'.$this->ID().'.png';
								if (file_exists($logofile))
									unlink($logofile);
								copy($filename, $logofile);
							}
					}
				function RemoveLogo()
					{
						$logofile='files/img/logoproduct'.$this->ID().'.png';
						if (file_exists($logofile))
							unlink($logofile);
					}
				function FrontendURL()
					{
						return sm_homepage().'product-'.$this->ID();
					}
				function CustomDownloadPath()
					{
						return $this->info['download_path'];
					}
				function HasNonEmptyCustomDownloadPath()
					{
						return $this->info['download_path'];
					}
				function DefaultDownloadPath()
					{
						return dirname(dirname(__FILE__)).'/httpdocs/files/productdownloads/product'.$this->ID();
					}
				function FilePathToDownload()
					{
						if (file_exists($this->DefaultDownloadPath()))
							return $this->DefaultDownloadPath();
						else
							return $this->CustomDownloadPath();
					}
				function FileToDownloadBasename()
					{
						if (!empty($this->info['download_original_filename']))
							return basename($this->info['download_original_filename']);
						else
							return basename($this->CustomDownloadPath());
					}
				function HasFileToDownload()
					{
						if (file_exists($this->DefaultDownloadPath()))
							return true;
						else
							return $this->HasNonEmptyCustomDownloadPath();
					}
				function SetOriginalFileNameToDownload($filename)
					{
						$this->UpdateValues(Array('download_original_filename'=>$filename));
					}
				function UpdateValues($params)
					{
						if (!is_array($params))
							return;
						unset($params['id']);
						if (empty($params))
							return;
						$q=new TQuery('products');
						foreach ($params as $key=>$val)
							{
								$this->info[$key]=$val;
								$q->Add($key, dbescape($this->info[$key]));
							}
						$q->Update('id', $this->ID());
					}
				function Remove()
					{
						$q=new TQuery('products');
						$q->Add('id', intval($this->ID()));
						$q->Remove();
						if (file_exists($this->DefaultDownloadPath()))
							unlink($this->DefaultDownloadPath());
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

				function isAskForPasswordOnCheckout()
					{
						return intval($this->info['ask_for_passwd'])==1;
					}

				function SetAskForPasswordOnCheckout($val_bool=true)
					{
						$this->UpdateValues(Array('ask_for_passwd' => $val_bool?1:0));
					}

				/** @return TProduct */
				public static function Create($id_company)
					{
						$q = new TQuery('products');
						$q->Add('id_u', intval($id_company));
						$object = new TProduct($q->Insert());
						return $object;
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
									}
								else
									{
										$q = new TQuery('products_metadata');
										$q->Add('val', dbescape($value));
										$q->Update('id', intval($info['id']));
										$this->metadata[$key] = $value;
									}
							}
						elseif ($value !== NULL)
							{
								$q = new TQuery('products_metadata');
								$q->Add('object_id', $this->ID());
								$q->Add('key_name', dbescape($key));
								$q->Add('val', dbescape($value));
								$q->Insert();
								$this->metadata[$key] = $value;
							}
						else
							unset($this->metadata[$key]);
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

				function HasSicSearch()
					{
						return !empty(intval($this->info['sic_search_enabled']));
					}

				function SicSearch()
					{
						return intval($this->info['sic_search_enabled']);
					}

				function SetSicSearch($val)
					{
						$this->UpdateValues(Array('sic_search_enabled'=>intval($val)));
					}
				function HasGoogleSearch()
					{
						return !empty(intval($this->info['google_search_enabled']));
					}

				function GoogleSearch()
					{
						return intval($this->info['google_search_enabled']);
					}

				function SetGoogleSearch($val)
					{
						$this->UpdateValues(Array('google_search_enabled'=>intval($val)));
					}
				function HasBuiltWithSearch()
					{
						return !empty(intval($this->info['builtwith_search_enabled']));
					}

				function BuiltWithSearch()
					{
						return intval($this->info['builtwith_search_enabled']);
					}

				function SetBuiltWithSearch($val)
					{
						$this->UpdateValues(Array('builtwith_search_enabled'=>intval($val)));
					}

				function HasStateSearch()
					{
						return !empty(intval($this->info['state_search_enabled']));
					}

				function StateSearch()
					{
						return intval($this->info['state_search_enabled']);
					}

				function SetStateSearch($val)
					{
						$this->UpdateValues(Array('state_search_enabled'=>intval($val)));
					}

				function Interval()
					{
						return $this->info['interval'];
					}

				function SetInterval($val)
					{
						$this->UpdateValues(Array('interval'=>$val));
					}
			}
		define("TProduct_DEFINED", 1);
	}

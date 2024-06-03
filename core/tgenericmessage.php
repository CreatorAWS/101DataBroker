<?php

	if (!defined("TGenericMessage_DEFINED"))
		{
			Class TGenericMessage
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable(static::Table())->AddWhere('id', intval($id_or_cachedinfo))->Get();
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


					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer' => intval($val)));
						}


					function ReferralID()
						{
							return $this->info['id_referral'];
						}

					function SetReferralID($val)
						{
							$this->UpdateValues(Array('id_referral' => $val));
						}


					function TypeTag()
						{
							return $this->info['type'];
						}

					function SetTypeTag($val)
						{
							$this->UpdateValues(Array('type' => $val));
						}


					function isIncoming()
						{
							return !empty($this->info['is_incoming']);
						}

					function SetIncoming($val = true)
						{
							$this->UpdateValues(Array('is_incoming' => $val ? 1 : 0));
						}


					function UnsetIncoming()
						{
							$this->UpdateValues(Array('is_incoming' => 0));
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


					function EmployeeID()
						{
							return intval($this->info['id_employee']);
						}

					function CampaignID()
						{
							return intval($this->info['id_campaign']);
						}

					function ContactID()
						{
							return intval($this->info['id_campaign_item']);
						}

					function CampaignScheduleID()
						{
							return intval($this->info['id_campaign_schedule']);
						}

					function SetEmployeeID($val)
						{
							$this->UpdateValues(Array('id_employee' => intval($val)));
						}


					function AddedTimestamp()
						{
							return intval($this->info['timeadded']);
						}

					function SetAddedTimestamp($val)
						{
							$this->UpdateValues(Array('timeadded' => intval($val)));
						}


					function ReadTimestamp()
						{
							return intval($this->info['timeread']);
						}

					function SetReadTimestamp($val)
						{
							$this->UpdateValues(Array('timeread' => intval($val)));
						}


					function HasReadTimestamp()
						{
							return !empty($this->info['timeread']);
						}


					function SendAfterTimestamp()
						{
							return intval($this->info['sendafter']);
						}

					function SetSendAfterTimestamp($val)
						{
							$this->UpdateValues(Array('sendafter' => intval($val)));
						}


					function HasSendAfterTimestamp()
						{
							return !empty($this->info['sendafter']);
						}


					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text' => $val));
						}


					function ReadByClientTimestamp()
						{
							return intval($this->info['timeread_by_client']);
						}

					function SetReadByClientTimestamp($val)
						{
							$this->UpdateValues(Array('timeread_by_client' => intval($val)));
						}


					function HasReadByClientTimestamp()
						{
							return !empty($this->info['timeread_by_client']);
						}


					function AssetID()
						{
							return intval($this->info['id_asset']);
						}

					function SetAssetID($val)
						{
							$this->UpdateValues(Array('id_asset' => intval($val)));
						}


					function HasAssetID()
						{
							return !empty($this->info['id_asset']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery(static::Table());
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							$q = new TQuery(static::Table());
							$q->AddWhere('id', intval($this->ID()));
							$q->Remove();
						}
					function SendAsSMSNow()
						{
							$company=TCompany::UsingCache($this->CompanyID());
							$customer=TCustomer::UsingCache($this->CustomerID());
							$attachments=Array();
							if ($this->HasAssetID())
								{
									$asset=new TAsset($this->AssetID());
									if ($asset->Exists())
										{
											$attachments[]=sm_homepage().$asset->DownloadURL();
										}
								}
							queue_message($company, $customer, $customer->Cellphone(), $this->Text(), $company->Cellphone(), $this->SendAfterTimestamp(), $attachments);
						}
					public static function CreateOutgoing($text, $company, $customer, $logtype, $employee=0, $asset=0, $scheduletime=0, $contact_id=0, $campaign_id=0, $id_campaign_schedule=0)
						{
							$q = new TQuery(static::Table());
							$q->Add('type', dbescape($logtype));
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_customer', intval(Cleaner::IntObjectID($customer)));
							$q->Add('is_incoming', 0);
							$q->Add('id_employee', intval(Cleaner::IntObjectID($employee)));
							$q->Add('id_asset', intval(Cleaner::IntObjectID($asset)));
							$q->Add('id_campaign_item', intval(Cleaner::IntObjectID($contact_id)));
							$q->Add('id_campaign', intval(Cleaner::IntObjectID($campaign_id)));
							$q->Add('id_campaign_schedule', intval(Cleaner::IntObjectID($id_campaign_schedule)));
							if (intval($scheduletime) == 0)
								{
									$q->Add('timeadded', time());
									$q->Add('timeread', time());
								}
							else
								{
									$q->Add('timeadded', intval($scheduletime));
									$q->Add('timeread', intval($scheduletime));
								}
							$q->Add('text', dbescape($text));
							$id = $q->Insert();
							return $id;
						}

				}

			define("TGenericMessage_DEFINED", 1);
		}

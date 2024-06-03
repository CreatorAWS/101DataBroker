<?php

	if (!defined("TCampaignItemList_DEFINED"))
		{

			Class TCampaignItemList extends TGenericList
				{
					/** @var TCampaignItem[] $items */
					public $items;
					protected $tablename = 'campaigns_items';
					protected $idfield = 'id';
					protected $titlefield = 'last_name';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCustomerID($customer_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' (customer_id='.Cleaner::IntObjectID($customer_or_id).' OR customer_id=0)';
						}

					function SetFilterCampaign($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_campaign='.Cleaner::IntObjectID($object_or_id);
						}

					function SetFilterStatusInitialMessageSent()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' (status <> "pending1" AND status <> "none")';
						}

					function SetFilterStatus($status_tag)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status='".dbescape($status_tag)."'";
						}

					function SetFilterEmailStatusOpened()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " (email_status='open' OR email_status='click' OR email_status='spam' OR email_status='unsub')";
						}

					function SetFilterEmailStatusClicked()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " email_status='click'";
						}

					function SetFilterSMSStatusDelivered()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " sms_status='delivered'";
						}

					function SetFilterEmailStatusBlacklisted()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " (email_status='blocked' OR email_status='spam' OR email_status='unsub')";
						}

					function SetFilterStatuses($status_tag_array)
						{
							$this->SetFilterStrValues('status', $status_tag_array);
						}

					function SetFilterExcludeCustomerIDs($arrayids)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							if (!is_array($arrayids))
								$this->sql .= " 1=2 ";
							else
								$this->sql .= " customer_id NOT IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
						}

					function SetFilterNeedPhoneValidation()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone_number<>'' AND phone_number_type='notverified'";
						}

					function SetFilterHasVerifiedPhone()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone_number<>'' AND phone_number_type<>'notverified'";
						}

					function SetFilterPhone($cellphone)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone_number LIKE '".Cleaner::USPhone($cellphone)."'";
						}

					function SetFilterNextActionTimeBefore($timestamp)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " next_action<".intval($timestamp);
						}

					function SetFilterNoPhoneCallsYet()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " voicemail_call_time=0";
						}

					function SetFilterEmail($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " email='".$email."'";
						}


					function OrderByNextActionTime($asc=true)
						{
							$this->orderby='next_action';
							if (!$asc)
								$this->orderby.=' DESC';
							$this->orderby.=', id';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					function SetFilterCampaignsIDs($arrayids)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							if (!is_array($arrayids) || count($arrayids)==0)
								$this->sql .= " 1=2 ";
							else
								$this->sql .= " id_campaign IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
						}

					function ExtractCustomerIDsArray()
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->PartnerID();
								}
							return $r;
						}

					function ExtractCampaignsIDsArray()
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->CampaignID();
								}
							return $r;
						}

					protected function InitItem($index)
						{
							$item = new TCampaignItem($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCampaignItemList_DEFINED", 1);
		}

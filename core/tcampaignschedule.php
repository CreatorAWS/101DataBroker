<?php
	
	if (!defined("TCampaignSchedule_DEFINED"))
		{
			Class TCampaignSchedule
				{
					protected $info;
					
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('campaigns_schedule')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

				/**
				 * @param $sid
				 * @return TCampaignSchedule
				 */

					function GetRawData()
						{
							return $this->info;
						}

					public static function Create($company, $campaign)
						{
							$q = new TQuery('campaigns_schedule');
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_campaign', intval(Cleaner::IntObjectID($campaign)));
							$object=new TCampaignSchedule($q->Insert());
							return $object;
						}
					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCampaignSchedule'][$id]))
								{
									$object = new TCampaignSchedule($id);
									if ($object->Exists())
										$sm['cache']['TCampaignSchedule'][$id] = $object->GetRawData();
								}
							else
								$object = new TCampaignSchedule($sm['cache']['TCampaignSchedule'][$id]);
							return $object;
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
					
					function CampaignID()
						{
							return intval($this->info['id_campaign']);
						}
					function SequenceID()
						{
							return intval($this->info['id_sequence']);
						}
					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function EmailStatus()
						{
							return $this->info['email_status'];
						}

					function SMSStatus()
						{
							return $this->info['sms_status'];
						}

					function SetCustomerID($customer)
						{
							$this->UpdateValues(Array('id_customer' => intval($customer)));
						}
					function SetSequenceID($sequence)
						{
							$this->UpdateValues(Array('id_sequence' => intval($sequence)));
						}
					function SetCampaignID($val)
						{
							$this->UpdateValues(Array('id_campaign' => intval($val)));
						}
					
					function ScheduledTimestamp()
						{
							return intval($this->info['scheduledtime']);
						}
					
					function SetScheduledTimestamp($val)
						{
							$this->UpdateValues(Array('scheduledtime' => intval($val)));
						}

					function GetStatus()
						{
							return $this->info['status'];
						}

					function SetStatus($val)
						{
							$this->UpdateValues(Array('status' => $val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('campaigns_schedule');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							TQuery::ForTable('campaigns_schedule')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			
			define("TCampaignSchedule_DEFINED", 1);
		}

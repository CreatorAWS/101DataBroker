<?php
	
	if (!defined("TCampaignSequence_DEFINED"))
		{
			Class TCampaignSequence
				{
					protected $info;
					
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('campaigns_sequences')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

				/**
				 * @param $sid
				 * @return TCampaignSequence
				 */

					function GetRawData()
						{
							return $this->info;
						}

					public static function Create($company, $campaign)
						{
							$q = new TQuery('campaigns_sequences');
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_campaign', intval(Cleaner::IntObjectID($campaign)));
							$object=new TCampaignSequence($q->Insert());
							return $object;
						}
					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCampaignSequence'][$id]))
								{
									$object = new TCampaignSequence($id);
									if ($object->Exists())
										$sm['cache']['TCampaignSequence'][$id] = $object->GetRawData();
								}
							else
								$object = new TCampaignSequence($sm['cache']['TCampaignSequence'][$id]);
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

					function GetMode()
						{
							return $this->info['mode'];
						}

					function SetMode($val)
						{
							$this->UpdateValues(Array('mode' => $val));
						}

					function EmailTemplate()
						{
							return $this->info['email_template'];
						}

					function SetEmailTemplate($val)
						{
							$this->UpdateValues(Array('email_template' => intval($val)));
						}

					function EmailSubject()
						{
							return $this->info['email_subject'];
						}

					function SetEmailSubject($val)
						{
							$this->UpdateValues(Array('email_subject' => $val));
						}

					function EmailMessage()
						{
							return $this->info['email_message'];
						}

					function SetEmailMessage($val)
						{
							$this->UpdateValues(Array('email_message' => $val));
						}

					function IdAsset()
						{
							return $this->info['id_asset'];
						}

					function SetIdAsset($val)
						{
							$this->UpdateValues(Array('id_asset' => intval($val)));
						}

					function GetText()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text' => $val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('campaigns_sequences');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							TQuery::ForTable('campaigns_sequences')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			
			define("TCampaignSequence_DEFINED", 1);
		}

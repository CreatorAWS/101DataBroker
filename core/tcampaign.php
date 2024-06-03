<?php

	if (!defined("TCampaign_DEFINED"))
		{
			Class TCampaign
				{
					protected $info;
					public $customers=NULL;
					public $contacts=NULL;
					public $openers=NULL;
					public $clickers=NULL;
					public $unsubscribers=NULL;
					public $delivers=NULL;
					protected $metadata=NULL;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('campaigns')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCampaign'][$id]))
								{
									$object=new TCampaign($id);
									if ($object->Exists())
										$sm['cache']['TCampaign'][$id]=$object->GetRawData();
								}
							else
								$object=new TCampaign($sm['cache']['TCampaign'][$id]);
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

					function TotalCustomersCount()
						{
							return count($this->GetCustomerIDsArray());
						}

					function GetCustomerIDsArray()
						{
							$ids=$this->GetTaxonomy('customertocampaign', $this->ID(), true);
							return $ids;
						}

					function GetTagIDsArray()
						{
							$ids=$this->GetTaxonomy('tagstocampaign', $this->ID(), true);
							return $ids;
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function SetContactListID($val)
						{
							$this->UpdateValues(Array('id_contactlist'=>intval($val)));
						}

					function ContactListID()
						{
							return intval($this->info['id_contactlist']);
						}

					function AssetID()
						{
							return intval($this->info['id_asset']);
						}

					function CampaignType()
						{
							return $this->info['campaign_type'];
						}

					function CampaignSingleUser()
						{
							return $this->info['campaign_type']=='single_user';
						}

					function SetCampaignType($val)
						{
							$this->UpdateValues(Array('campaign_type'=>$val));
						}

					function SetAssetID($val)
						{
							$this->UpdateValues(Array('id_asset'=>intval($val)));
						}

					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('title'=>$val));
						}

					function Addedtime()
						{
							return intval($this->info['addedtime']);
						}

					function SetAddedtime($val)
						{
							$this->UpdateValues(Array('addedtime'=>intval($val)));
						}

					function Note()
						{
							return $this->info['note'];
						}

					function SetNote($val)
						{
							$this->UpdateValues(Array('note'=>$val));
						}

					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text'=>$val));
						}

					function Status()
						{
							return $this->info['status'];
						}

					function ContactsCount()
						{
							return $this->LoadContacts()->Count();
						}

					function ContactsCountInitialScheduled()
						{
							$campaignContacts = new TCampaignItemList();
							$campaignContacts->SetFilterCampaign($this->ID());
							$campaignContacts->SetFilterStatus('pending1');
							$campaignContacts->Load();

							return $campaignContacts->Count();
						}

					function ContactsCountInitialSent()
						{
							$campaignContacts = new TCampaignItemList();
							$campaignContacts->SetFilterCampaign($this->ID());
							$campaignContacts->SetFilterStatusInitialMessageSent();
							$campaignContacts->Load();

							return $campaignContacts->Count();
						}

					function LoadCustomers($rewritecache=false)
						{
							if ($rewritecache || $this->customers===NULL)
								{
									$this->customers=new TCustomerList();
									$this->customers->SetFilterCompany($this->CompanyID());
									$this->customers->SetFilterIDs($this->GetCustomerIDsArray());
									$this->customers->Load();
								}
							return $this->customers;
						}

					function LoadContacts($rewritecache=false)
						{
							if ($rewritecache || $this->contacts===NULL)
								{
									$this->contacts=new TCampaignItemList();
									$this->contacts->SetFilterCompany($this->CompanyID());
									$this->contacts->SetFilterCampaign($this);
									$this->contacts->Load();
								}
							return $this->contacts;
						}

					function LoadOpeners($rewritecache=false)
						{
							if ($rewritecache || $this->openers===NULL)
								{
									$this->openers=new TCampaignItemList();
									$this->openers->SetFilterCompany($this->CompanyID());
									$this->openers->SetFilterCampaign($this);
									$this->openers->SetFilterEmailStatusOpened();
									$this->openers->Load();
								}
							return $this->openers;
						}

					function OpenersCount()
						{
							return $this->LoadOpeners()->Count();
						}

					function LoadClicker($rewritecache=false)
						{
							if ($rewritecache || $this->clickers===NULL)
								{
									$this->clickers=new TCampaignItemList();
									$this->clickers->SetFilterCompany($this->CompanyID());
									$this->clickers->SetFilterCampaign($this);
									$this->clickers->SetFilterEmailStatusClicked();
									$this->clickers->Load();
								}
							return $this->clickers;
						}

					function LoadSMSDelivers($rewritecache=false)
						{
							if ($rewritecache || $this->delivers===NULL)
								{
									$this->delivers=new TCampaignItemList();
									$this->delivers->SetFilterCompany($this->CompanyID());
									$this->delivers->SetFilterCampaign($this);
									$this->delivers->SetFilterSMSStatusDelivered();
									$this->delivers->Load();
								}
							return $this->delivers;
						}

					function LoadUnsubscribers($rewritecache=false)
						{
							if ($rewritecache || $this->unsubscribers===NULL)
								{
									$this->unsubscribers=new TCampaignItemList();
									$this->unsubscribers->SetFilterCompany($this->CompanyID());
									$this->unsubscribers->SetFilterCampaign($this);
									$this->unsubscribers->SetFilterEmailStatusBlacklisted();
									$this->unsubscribers->Load();
								}
							return $this->unsubscribers;
						}
					function UnsubscribersCount()
						{
							return $this->LoadUnsubscribers()->Count();
						}

					function SMSDeliveredCount()
						{
							return $this->LoadSMSDelivers()->Count();
						}

					function ClickerCount()
						{
							return $this->LoadClicker()->Count();
						}


					function SetStatus($val)
						{
							$this->UpdateValues(Array('status'=>$val));
						}

					function Email1Template()
						{
							return intval($this->info['email1_template']);
						}

					function SetEmail1Template($val)
						{
							$this->UpdateValues(Array('email1_template'=>intval($val)));
						}

					function Email2Template()
						{
							return intval($this->info['email2_template']);
						}

					function SetEmail2Template($val)
						{
							$this->UpdateValues(Array('email2_template'=>intval($val)));
						}

					function Email3Template()
						{
							return intval($this->info['email3_template']);
						}

					function SetEmail3Template($val)
						{
							$this->UpdateValues(Array('email3_template'=>intval($val)));
						}

					function Email1Subject()
						{
							return $this->info['email1_subject'];
						}

					function SetEmail1Subject($val)
						{
							$this->UpdateValues(Array('email1_subject'=>$val));
						}

					function Email1Message()
						{
							return $this->info['email1_message'];
						}

					function SetEmail1Message($val)
						{
							$this->UpdateValues(Array('email1_message'=>$val));
						}

					function Email2Subject()
						{
							return $this->info['email2_subject'];
						}

					function SetEmail2Subject($val)
						{
							$this->UpdateValues(Array('email2_subject'=>$val));
						}

					function Email2Message()
						{
							return $this->info['email2_message'];
						}

					function SetEmail2Message($val)
						{
							$this->UpdateValues(Array('email2_message'=>$val));
						}

					function Email3Subject()
						{
							return $this->info['email3_subject'];
						}

					function SetEmail3Subject($val)
						{
							$this->UpdateValues(Array('email3_subject'=>$val));
						}

					function Email3Message()
						{
							return $this->info['email3_message'];
						}

					function SetEmail3Message($val)
						{
							$this->UpdateValues(Array('email3_message'=>$val));
						}

					function VoicemessageAsset()
						{
							return intval($this->info['voicemessage_asset']);
						}

					function HasVoiceMessageAsset()
						{
							return !empty($this->info['voicemessage_asset']);
						}

					function TextMessage()
						{
							return $this->info['text'];
						}

					function SetVoicemessageAsset($val)
						{
							$this->UpdateValues(Array('voicemessage_asset'=>intval($val)));
						}

					function UnsetVoiceMessageAsset()
						{
							$this->UpdateValues(Array('voicemessage_asset' => 0));
						}

					function Starttime()
						{
							return intval($this->info['starttime']);
						}

					function SetStarttime($val)
						{
							$this->UpdateValues(Array('starttime'=>intval($val)));
						}

					function SystemSequenceID()
						{
							return intval($this->info['id_system_sequence']);
						}

					function SetSystemSequenceID($val)
						{
							$this->UpdateValues(Array('id_system_sequence'=>intval($val)));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('campaigns');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}
					function SetEmailSubject($index, $val)
						{
							$this->UpdateValues(Array('email'.intval($index).'_subject'=>$val));
						}
					function SetEmailMessage($index, $val)
						{
							$this->UpdateValues(Array('email'.intval($index).'_message'=>$val));
						}
					function GetEmailSubject($index)
						{
							return $this->info['email'.intval($index).'_subject'];
						}
					function GetEmailMessage($index)
						{
							return $this->info['email'.intval($index).'_message'];
						}

					function GetEmailTemplateID($index)
						{
							return intval($this->info['email'.intval($index).'_template']);
						}

					function SetEmailTemplateID($index, $val)
						{
							$this->UpdateValues(Array('email'.intval($index).'_template'=>intval($val)));
						}

					public static function Create($company)
						{
							$q = new TQuery('campaigns');
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('addedtime', time());
							$object=new TCampaign($q->Insert());
							$object->SetEmailSubject(1, $object->GetEmailSubject(1));
							$object->SetEmailMessage(1, $object->GetEmailMessage(1));
							$object->SetEmailSubject(2, $object->GetEmailSubject(1));
							$object->SetEmailMessage(2, $object->GetEmailMessage(1));
							$object->SetEmailSubject(3, $object->GetEmailSubject(1));
							$object->SetEmailMessage(3, $object->GetEmailMessage(1));
							return $object;
						}
					function Remove()
						{
							TQuery::ForTable('campaigns')->AddWhere('id', intval($this->ID()))->Remove();
						}

					function GetTaxonomy($object_name, $object_id, $use_object_id_as_rel_id=false)
						{
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							if ($use_object_id_as_rel_id)
								{
									$q->Add('rel_id', dbescape($object_id));
									$q->SelectFields('object_id as taxonomyid');
								}
							else
								{
									$q->Add('object_id', dbescape($object_id));
									$q->SelectFields('rel_id as taxonomyid');
								}
							$q->Select();
							return $q->ColumnValues('taxonomyid');
						}
					function SetTaxonomy($object_name, $object_id, $rel_id)
						{
							if (is_array($rel_id))
								{
									for ($i = 0; $i<count($rel_id); $i++)
										{
											$this->GetTaxonomy($object_name, $object_id, $rel_id[$i]);
											return;
										}
								}
							if (in_array($rel_id, $this->GetTaxonomy($object_name, $object_id)))
								return;
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							$q->Add('object_id', intval($object_id));
							$q->Add('rel_id', intval($rel_id));
							$q->Insert();
						}
					function UnsetTaxonomy($object_name, $object_id, $rel_id)
						{
							if (is_array($rel_id))
								{
									for ($i = 0; $i<count($rel_id); $i++)
										{
											sm_unset_taxonomy($object_name, $object_id, $rel_id[$i]);
											return;
										}
								}
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							$q->Add('object_id', intval($object_id));
							$q->Add('rel_id', intval($rel_id));
							$q->Remove();
						}


				}
			define("TCampaign_DEFINED", 1);
		}

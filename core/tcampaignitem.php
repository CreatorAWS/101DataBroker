<?php
	
	if (!defined("TCampaignItem_DEFINED"))
		{
			Class TCampaignItem
				{
					var $info;
					var $tagids = NULL;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('campaigns_items')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

				/**
				 * @param $sid
				 * @return TCampaignItem
				 */
					public static function initWithTwilioSID($sid)
						{
							$info = TQuery::ForTable('campaigns_items')->AddWhere('twilio_call_sid', dbescape($sid))->Get();
							$object=new TCampaignItem($info);
							return $object;
						}

					function GetRawData()
						{
							return $this->info;
						}
					function LoadTags($rewritecache = false)
						{
							if ($rewritecache || $this->tagids === NULL)
								{
									$this->tagids = $this->GetTaxonomy('contacttotags', $this->ID());
								}
						}
					public static function Create($company, $campaign)
						{
							$q = new TQuery('campaigns_items');
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_campaign', intval(Cleaner::IntObjectID($campaign)));
							$q->Add('addedtime', time());
							$object=new TCampaignItem($q->Insert());
							return $object;
						}
					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCampaignItem'][$id]))
								{
									$object = new TCampaignItem($id);
									if ($object->Exists())
										$sm['cache']['TCampaignItem'][$id] = $object->GetRawData();
								}
							else
								$object = new TCampaignItem($sm['cache']['TCampaignItem'][$id]);
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
					
					function Company()
						{
							return $this->info['company'];
						}

					function SetCompany($val)
						{
							$this->UpdateValues(Array('company' => $val));
						}

					function CampaignID()
						{
							return intval($this->info['id_campaign']);
						}
					
					function SetCampaignID($val)
						{
							$this->UpdateValues(Array('id_campaign' => intval($val)));
						}
					
					function AddedTimestamp()
						{
							return intval($this->info['addedtime']);
						}
					
					function SetAddedTimestamp($val)
						{
							$this->UpdateValues(Array('addedtime' => intval($val)));
						}
					
					function VisitedTimestamp()
						{
							return intval($this->info['visitedtime']);
						}
					
					function SetVisitedTimestamp($val)
						{
							$this->UpdateValues(Array('visitedtime' => intval($val)));
						}
					
					function HasVisitedTimestamp()
						{
							return !empty($this->info['visitedtime']);
						}
					
					
					function RegisteredTimestamp()
						{
							return intval($this->info['registeredtime']);
						}
					
					function SetRegisteredTimestamp($val)
						{
							$this->UpdateValues(Array('registeredtime' => intval($val)));
						}
					
					function HasRegisteredTimestamp()
						{
							return !empty($this->info['registeredtime']);
						}

					function CustomerInitials()
						{
							return substr($this->info['first_name'], 0, 1).substr(trim($this->info['last_name'], ' '), 0, 1);
						}

					function Initials()
						{
							if (!empty($this->CustomerInitials()))
								return $this->CustomerInitials();
							else
								return $this->CompanyInitials();
						}

					function CompanyInitials()
						{
							return substr($this->info['company'], 0, 1);
						}

					function HasCustomerName()
						{
							return !empty($this->info['first_name']) || !empty($this->info['last_name']);
						}

					function CustomerName()
						{
							return $this->info['first_name'].' '.$this->info['last_name'];
						}

					function Name()
						{
							if (!empty($this->HasCustomerName()))
								return $this->CustomerName();
							else
								return $this->Company();
						}

					function HasName()
						{
							return !empty($this->Name());
						}

					function FirstName()
						{
							return $this->info['first_name'];
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
					
					function Email()
						{
							return $this->info['email'];
						}

					function HasEmail()
						{
							return !empty($this->info['email']);
						}

					function SetEmail($val)
						{
							$this->UpdateValues(Array('email' => $val));
						}
					
					function Phone()
						{
							return $this->info['phone_number'];
						}

					function HasPhone()
						{
							return !empty($this->info['phone_number']);
						}

					function SetPhone($val)
						{
							$this->UpdateValues(Array('phone_number' => $val));
						}

					function PhoneTypeTag()
						{
							return $this->info['phone_number_type'];
						}

					function SetPhoneTypeTag($val)
						{
							$this->UpdateValues(Array('phone_number_type' => $val));
						}

					function isPhoneTypeTagNotVerified()
						{
							return $this->PhoneTypeTag()=='notverified';
						}

					function isPhoneTypeTagLandline()
						{
							return $this->PhoneTypeTag()=='landline';
						}

					function isPhoneTypeTagCell()
						{
							return $this->PhoneTypeTag()=='mobile';
						}

					function isPhoneTypeTagVOIP()
						{
							return $this->PhoneTypeTag()=='voip';
						}

					function isPhoneTypeTagInvalid()
						{
							return $this->PhoneTypeTag()=='invalid';
						}

					function Tags()
						{
							return $this->info['tags'];
						}
					
					function GetTagsArray()
						{
							$this->LoadTags();
							return $this->tagids;
						}

					function SetTags($val)
						{
							$this->UpdateValues(Array('tags' => $val));
						}
					
					function Status()
						{
							return $this->info['status'];
						}
					function EmailStatus()
						{
							$status = $this->info['email_status'];
							if ($status == 'unsub')
								$status = 'unsubscribed';
							return $status;
						}

					function SMSStatus()
						{
							$status = $this->info['sms_status'];
							return $status;
						}

					function SetStatus($val)
						{
							$this->UpdateValues(Array('status' => $val));
						}
					
					function NextActionTimestamp()
						{
							return intval($this->info['next_action']);
						}
					
					function SetNextActionTimestamp($val)
						{
							$this->UpdateValues(Array('next_action' => intval($val)));
						}
					
					function SetStatusAndNextActionTimestamp($status, $time)
						{
							$this->UpdateValues(Array(
								'status' => $status,
								'next_action' => intval($time)
							));
						}

					function HasNextActionTimestamp()
						{
							return !empty($this->info['next_action']);
						}

					function VoicemailCallTime()
						{
							return intval($this->info['voicemail_call_time']);
						}

					function SetVoicemailCallTime($val)
						{
							$this->UpdateValues(Array('voicemail_call_time'=>intval($val)));
						}

					function HasVoicemailCallTime()
						{
							return !empty($this->info['voicemail_call_time']);
						}

					function TwilioCallSID()
						{
							return $this->info['twilio_call_sid'];
						}

					function SetTwilioCallSID($val)
						{
							$this->UpdateValues(Array('twilio_call_sid'=>$val));
						}

					function HasTwilioCallSID()
						{
							return !empty($this->info['twilio_call_sid']);
						}

					function VoicemailCallDuration()
						{
							return intval($this->info['voicemail_call_duration']);
						}

					function SetVoicemailCallDuration($val)
						{
							$this->UpdateValues(Array('voicemail_call_duration'=>intval($val)));
						}

					function VoicemailCallResultTag()
						{
							return $this->info['voicemail_call_result'];
						}

					function SetVoicemailCallResultTag($val)
						{
							$this->UpdateValues(Array('voicemail_call_result'=>$val));
						}
					function HasTagID($tag_id)
						{
							$this->LoadTags();
							return in_array($tag_id, $this->tagids);
						}

					function SetTagID($tag_id)
						{
							$this->SetTaxonomy('contacttotags', $this->ID(), intval($tag_id));
							$this->LoadTags(true);
						}

					function UnsetTagID($tag_id)
						{
							$this->UnsetTaxonomy('contacttotags', $this->ID(), intval($tag_id));
							$this->LoadTags(true);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('campaigns_items');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}
					
					function Remove()
						{
							TQuery::ForTable('campaigns_items')->AddWhere('id', intval($this->ID()))->Remove();
						}
					function IsPartner()
						{
							return !empty($this->info['customer_id']);
						}
					function PartnerID()
						{
							return $this->info['customer_id'];
						}
					function SetPartner($id)
						{
							$this->UpdateValues(Array('customer_id'=>intval($id)));
						}
					function SetStatusFirstMessageAfter($time)
						{
							$this->SetStatusAndNextActionTimestamp('pending1', $time);
						}

					function MarkRegistration()
						{
							$this->SetStatusAndNextActionTimestamp('registered', 0);
							$this->SetRegisteredTimestamp(time());
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

					function HasAddress()
						{
							return !empty($this->info['address']);
						}

					function Address()
						{
							return $this->info['address'];
						}

					function SetAddress($val)
						{
							$this->UpdateValues(Array('address' => $val));
						}

					function HasCity()
						{
							return !empty($this->info['city']);
						}

					function City()
						{
							return $this->info['city'];
						}

					function SetCity($val)
						{
							$this->UpdateValues(Array('city' => $val));
						}

					function HasState()
						{
							return !empty($this->info['state']);
						}

					function State()
						{
							return $this->info['state'];
						}

					function SetState($val)
						{
							$this->UpdateValues(Array('city' => $val));
						}

					function HasZip()
						{
							return !empty($this->info['zip']);
						}

					function Zip()
						{
							return $this->info['zip'];
						}

					function SetZip($val)
						{
							$this->UpdateValues(Array('zip' => $val));
						}
				}
			
			define("TCampaignItem_DEFINED", 1);
		}

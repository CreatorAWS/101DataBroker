<?php

	class TCustomer
		{
			var $info;
			var $tagids = NULL;
			var $listids = NULL;
			var $searchids = NULL;
			var $builtwithlistids = NULL;
			var $sequenceids = NULL;
			var $systemcampaignsds = NULL;
			var $emailsubject = NULL;
			var $emailmessage = NULL;

			function TCustomer($id_or_cahcedinfo)
				{
					global $sm;
					if (is_array($id_or_cahcedinfo))
						{
							$this->info = $id_or_cahcedinfo;
						}
					else
						{
							$this->info = TQuery::ForTable('customers')->Add('id', intval($id_or_cahcedinfo))->Get();
						}
				}

			function SetCompanyID($val)
				{
					$upd['id_company'] = $val;
					$this->UpdateValues($upd);
				}

			public static function StatusTagValues()
				{
					return Array('received', 'contact', 'appointment', 'sold', 'lost');
				}

			function LoadTags($rewritecache = false)
				{
					if ($rewritecache || $this->tagids === NULL)
						{
							$this->tagids = $this->GetTaxonomy('customertotags', $this->ID());
						}
				}
			function LoadLists($rewritecache = false)
				{
					if ($rewritecache || $this->listids === NULL)
						{
							$this->listids = $this->GetTaxonomy('customertolist', $this->ID());
						}
				}
			function LoadSequences($rewritecache = false)
				{
					if ($rewritecache || $this->sequenceids === NULL)
						{
							$this->sequenceids = $this->GetTaxonomy('customertosequence', $this->ID());
						}
				}
			function LoadSystemCampaigns($rewritecache = false)
				{
					if ($rewritecache || $this->systemcampaignsds === NULL)
						{
							$this->systemcampaignsds = $this->GetTaxonomy('customertocampaign', $this->ID());
						}
				}
			public static function initWithCustomerPhoneNotDeleted($phone, $id_company)
				{
					use_api('cleaner');
					$info = TQuery::ForTable('customers')->Add('id_company', intval($id_company))->Add('cellphone', dbescape(Cleaner::USPhone($phone)))->AddWhere('deleted=0')->OrderBy('id')->Get();
					$customer = new TCustomer($info);
					return $customer;
				}

			public static function initWithBusinessTitle($company_name, $id_company)
				{
					use_api('cleaner');
					$info = TQuery::ForTable('customers')->Add('id_company', Cleaner::IntObjectID($id_company))->Add('company', dbescape($company_name))->AddWhere('deleted=0')->OrderBy('id')->Get();
					$customer = new TCustomer($info);
					return $customer;
				}

			public static function initWithRawPhonesArray($phones, $id_company)
				{
					use_api('cleaner');

					foreach ($phones as $phone)
						{
							$customer = self::initWithCustomerPhoneNotDeleted(Cleaner::Phone($phone), Cleaner::IntObjectID($id_company));
							if (is_object($customer) && $customer->Exists())
								return $customer;
						}

					return '';
				}

			public static function initWithEmailsArray($emails, $id_company)
				{
					use_api('cleaner');

					foreach ($emails as $email)
						{
							$customer = TCustomer::initWithCustomerEmailNotDeleted($email, $id_company);
							if (is_object($customer) && $customer->Exists())
								return $customer;
						}

					return '';
				}

			public static function initWithCustomerEmailNotDeleted($email, $id_company)
				{
					use_api('cleaner');
					$info = TQuery::ForTable('customers')->Add('id_company', intval($id_company))->Add('email', dbescape($email))->AddWhere('deleted=0')->OrderBy('id')->Get();
					$customer = new TCustomer($info);
					return $customer;
				}

			function SendEmailFromCompany($subject, $message, $scheduletime=0, $writelog = false, $employee = 0, $campaign = 0, $campaign_item = 0, $id_campaign_schedule = 0, $email_id = '')
				{
					$id_reply = 0;
					$id_email = '';
					if (is_object($employee))
						$id_employee = $employee->ID();
					else
						$id_employee = $employee;
					$id = 0;
					if (empty($email_id))
						{
							$id_email = md5($this->ID().'-'.microtime().'-'.rand(1000, 9000));
							$is_reply = false;
						}
					else
						{
							$is_reply = true;
							$email = TEmail::initWithEmailID($email_id);
							if (is_object($email) && $email->Exists())
								{
									$id_email = $email->ID();
									$messagelog = TMessagesLog::initWithMessageIDAndType($email->ID(), 'email');
									if (is_object($messagelog) && $messagelog->Exists())
										$id_reply = $messagelog->ID();
								}
						}

					$company=TCompany::UsingCache($this->CompanyID());

					if ($is_reply)
						EmailMessages::QueueEmail($company, $this->Email(), $subject, $message, $scheduletime, '', $campaign_item, $id_campaign_schedule, $email_id);
					else
						EmailMessages::QueueEmail($company, $this->Email(), $subject, $message, $scheduletime, '', $campaign_item, $id_campaign_schedule, $id_email);

					if ($writelog)
						{
							$id = TEmail::CreateOutgoing(
								$subject,
								$message,
								$this->CompanyID(),
								$this,
								$id_employee,
								$scheduletime,
								$campaign,
								$id_campaign_schedule,
								$id_email
							);
							if (!empty($id))
								TMessagesLog::Create('email', $id, $this->CompanyID(), $this, $scheduletime, 0, $id_employee, $id_reply);
						}
					return true;
				}

			function GetTagIDsArray()
				{
					$this->LoadTags();
					return $this->tagids;
				}

			function TagsCount()
				{
					$this->LoadTags();
					return count($this->tagids);
				}

			function UnsetContactIDs()
				{
					$this->LoadLists();
					for ($i=0; $i<count($this->listids); $i++)
						{
							$this->UnsetTaxonomy('customertolist', $this->ID(), intval($this->listids[$i]));
						}
				}
			function UnsetCampaignContactIDs()
				{
					$this->LoadSystemCampaigns();
					for ($i=0; $i<count($this->systemcampaignsds); $i++)
						{
							$this->UnsetTaxonomy('customertosystemcampaign', $this->ID(), intval($this->listids[$i]));
						}
				}
			function HasTagID($tag_id)
				{
					$this->LoadTags();
					return in_array($tag_id, $this->tagids);
				}

			function SetTagID($tag_id)
				{
					$this->SetTaxonomy('customertotags', $this->ID(), intval($tag_id));
					$this->LoadTags(true);
				}

			function UnsetTagID($tag_id)
				{
					$this->UnsetTaxonomy('customertotags', $this->ID(), intval($tag_id));
					$this->LoadTags(true);
				}

			public static function initWithCustomerPhone($phone, $id_company)
				{
					use_api('cleaner');
					$info = TQuery::ForTable('customers')->Add('id_company', intval($id_company))->Add('cellphone', dbescape(Cleaner::USPhone($phone)))->OrderBy('deleted')->Get();
					$customer = new TCustomer($info);
					return $customer;
				}

			public static function initWithCustomerEmail($email, $id_company)
				{
					use_api('cleaner');
					$info = TQuery::ForTable('customers')->Add('id_company', intval($id_company))->Add('email', dbescape($email))->OrderBy('deleted')->Get();
					$customer = new TCustomer($info);
					return $customer;
				}

			public static function withID($id)
				{
					$customer = new TCustomer($id);
					return $customer;
				}

		/** @return TCustomer */
			public static function UsingCache($id)
				{
					global $sm;
					if (!is_object($sm['cache']['customer'][$id]))
						{
							$sm['cache']['customer'][$id] = new TCustomer($id);
						}
					return $sm['cache']['customer'][$id];
				}

			function Exists()
				{
					return !empty($this->info['id']);
				}

			function isDeleted()
				{
					return !empty($this->info['deleted']);
				}

			function isAllowedToReceiveSMSFrom()
				{
					return true;
				}

			function HasCellphone()
				{
					return !empty($this->info['cellphone']);
				}

			function Cellphone()
				{
					return $this->info['cellphone'];
				}

			function HasEmail()
				{
					return !empty($this->info['email']);
				}

			function Email()
				{
					return $this->info['email'];
				}

			function HasAddress1()
				{
					return !empty($this->info['address']);
				}

			function Address1()
				{
					return (string)$this->info['address'];
				}

			function City()
				{
					return (string)$this->info['city'];
				}

			function HasCity()
				{
					return !empty($this->info['city']);
				}

			function State()
				{
					return (string)$this->info['state'];
				}

			function HasState()
				{
					return !empty($this->info['state']);
				}

			function ZIP()
				{
					return (string)$this->info['zip'];
				}

			function HasZip()
				{
					return !empty($this->info['zip']);
				}

			function Country()
				{
					return (string)$this->info['country'];
				}

			function SetCountry($val)
				{
					$this->UpdateValues(Array('country'=>$val));
				}

			function HasCountry()
				{
					return !empty($this->info['country']);
				}

			function GetNextActivity()
				{
					$logs = new TCustomerLogsList();
					$logs->SetFilterCustomer($this->ID());
					$logs->SetFilterIsScheduled();
					$logs->OrderByAddedTime(false);
					$logs->Load();

					for ($i = 0; $i < $logs->Count(); $i++)
						{
							/** @var  $log TCustomerLog */
							$log = $logs->Item($i);
							if ($log->Exists())
								{
									if ($log->isCampaign())
										{
											$campaign = new TCampaign($log->ObjectID());
											if ($campaign->Exists() && $campaign->Status() == 'started')
												{
													$campaignitems = new TCampaignItemList();
													$campaignitems->SetFilterCompany($this->CompanyID());
													$campaignitems->SetFilterCampaign($campaign);
													$campaignitems->SetFilterCustomerID($this->ID());
													$campaignitems->Load();


													$items = new TCampaignScheduleList();
													$items->SetFilterStatus('scheduled');
													$items->SetFilterCompany($this->CompanyID());
													$items->SetFilterCampaign($campaign->ID());
													$items->SetFilterCustomer($campaignitems->Item(0)->ID());
													$items->OrderByActionTime();
													$items->Load();

													if ($items->Count() > 0)
														{
															return [
																'action' => $campaign->Title(),
																'time' => Formatter::DateTime($campaign->Starttime()),
																'url' => 'index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID(),
															];
														}
													else
														continue;
												}
										}
									else
										{
											return [
												'action' => $logs->Item($i)->Description(),
												'time' => Formatter::DateTime($logs->Item($i)->Scheduledtime()),
											];
										}
								}
						}
				}

			function GetActiveSequence()
				{
					$logs = new TCustomerLogsList();
					$logs->SetFilterCustomer($this->ID());
					$logs->SetFilterIsNotScheduled();
					$logs->SetFilterIsCampaign();
					$logs->OrderByAddedTime(false);
					$logs->Load();

					for ( $i = 0; $i < $logs->Count(); $i++)
						{
							$campaign = new TCampaign($logs->Item($i)->ObjectID());
							if (!$campaign->Exists() || $campaign->Status() != 'started')
								continue;
							else
								{
									return [
										'campaign' => $campaign->Title(),
										'campaign_url' => 'index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID(),
									];
								}
						}
				}

			function GetLastContact()
				{
					$logs = new TCustomerLogsList();
					$logs->SetFilterCustomer($this->ID());
					$logs->SetFilterIsNotScheduled();
					$logs->SetFilterIsContactAction();
					$logs->OrderByAddedTime(false);
					$logs->Limit(1);
					$logs->Load();

					if ($logs->Count() > 0 )
						{
							return [
								'action' => $logs->Item(0)->Description(),
								'time' => Formatter::DateTime($logs->Item(0)->Addedtime()),
							];
						}
				}

			function AppointmentAction($id_object = 0, $scheduledtime = 0)
				{
					/** @var $myaccount TEmployee */
					global $myaccount;
					TCustomerLog::Create($this->ID(),'appointment', 'User '.$myaccount->Name().' scheduled appointment with '.$this->Name(), $myaccount->ID(), $this->CompanyID(), $id_object, $scheduledtime);
				}

			function SendSMSAction($id_object = 0, $scheduledtime = 0, $bulk = false)
				{
					/** @var $myaccount TEmployee */
					global $myaccount;

					if (empty($id_employee))
						$employee = $myaccount;
					else
						{
							$employee = new TEmployee($id_employee);
						}

					if ($bulk)
						$action = 'bulk_sms';
					else
						$action = 'sms';

					TCustomerLog::Create($this->ID(), $action, 'User '.$employee->Name().' sent an SMS to '.$this->Name(), $employee->ID(), $this->CompanyID(), $id_object, $scheduledtime);
				}

			function IncomingSMSAction($id_object = 0, $id_employee = 0)
				{
					TCustomerLog::Create($this->ID(), 'incoming_sms', 'Received an SMS from '.$this->Name(), $id_employee, $this->CompanyID(), $id_object);
				}

			function SendEmailAction($id_object = 0, $scheduledtime = 0, $bulk = false, $id_employee = 0)
				{
					if (empty($id_employee))
						$employee = System::MyAccount();
					else
						$employee = new TEmployee($id_employee);

					if ($bulk)
						$action = 'bulk_email';
					else
						$action = 'email';
					TCustomerLog::Create($this->ID(),$action, 'User '.$employee->Name().' sent an email to '.$this->Name(), $employee->ID(), $this->CompanyID(), $id_object, $scheduledtime);
				}

			function IncomingEmailAction($id_object = 0, $id_employee = 0 )
				{
					TCustomerLog::Create($this->ID(), 'incoming_email', 'Received an email from '.$this->Name(), $id_employee, $this->CompanyID(), $id_object);
				}

			function StartCampaignAction($id_object, $scheduledtime = 0)
				{
					/** @var $myaccount TEmployee */
					global $myaccount;

					$campaign = new TCampaign($id_object);
					if ($campaign->Exists())
						{
							$txt = '';

							if (!empty($campaign->Title()))
								$txt = ' to '.$campaign->Title().' campaign';

							TCustomerLog::Create($this->ID(),'start_campaign', 'User '.$myaccount->Name().' added customer '.$this->Name().$txt, $myaccount->ID(), $this->CompanyID(), $id_object, $scheduledtime);
						}
				}

			function StopCampaignAction($id_object = 0, $scheduledtime = 0)
				{
					/** @var $myaccount TEmployee */
					global $myaccount;

					$campaign = new TCampaign($id_object);
					if ($campaign->Exists())
						{
							$txt = '';

							if (!empty($campaign->Title()))
								$txt = ' from '.$campaign->Title().' campaign';

							TCustomerLog::Create($this->ID(),'stop_campaign', 'User '.$myaccount->Name().' removed customer '.$this->Name().$txt, $myaccount->ID(), $this->CompanyID(), $id_object, $scheduledtime);
						}
				}

			function CallAction($id_object = 0, $id_employee = 0)
				{
					$hasEmployee = false;

					if (!empty($id_employee))
						{
							$employee = new TEmployee($id_employee);
							if ($employee->Exists())
								$hasEmployee = true;
						}

					if ($hasEmployee)
						TCustomerLog::Create($this->ID(),'call', 'User '.$employee->Name().' called '.$this->Name(), $employee->ID(), $this->CompanyID(), $id_object);
					else
						TCustomerLog::Create($this->ID(),'call', 'Phone call to '.$this->Name(), 0, $this->CompanyID(), $id_object);
				}

			function IncomingCallAction($id_object = 0)
				{
					TCustomerLog::Create($this->ID(),'call', 'Incoming Call from '.$this->Name(), 0, $this->CompanyID(), $id_object);
				}

			function AddressFormatted()
				{
					$arr = [];

					if ($this->HasAddress1())
						$arr[] = $this->Address1();
					if ($this->HasCity())
						$arr[] = $this->City();
					if ($this->HasState())
						$arr[] = $this->State();
					if ($this->HasZip())
						$arr[] = $this->ZIP();
					if ($this->HasCountry())
						$arr[] = $this->Country();

					return implode (", ", $arr);
				}

			function HasCustomerName()
				{
					return !empty($this->info['first_name']) || !empty($this->info['last_name']);
				}

			function CustomerName()
				{
					return $this->info['first_name'].' '.$this->info['last_name'];
				}

			function HasName()
				{
					return !empty( $this->Name() );
				}

			function Name()
				{
					if (!empty($this->HasCustomerName()))
						return $this->CustomerName();
					else
						return $this->GetBusinessName();
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

			function isRegisteredByStaff()
				{
					return $this->info['registeredtype'] == 'staff';
				}

			function isRegisteredOnline()
				{
					return $this->info['registeredtype'] == 'website';
				}

			function isRegisteredViaSMS()
				{
					return $this->info['registeredtype'] == 'sms';
				}

			function isRegisteredViaMobileApp()
				{
					return $this->info['registeredtype'] == 'mobileapp';
				}

			function FirstName()
				{
					return $this->info['first_name'];
				}

			function ContactName()
				{
					return $this->FirstName().' '.$this->LastName();
				}

			function HasNotEmptyContactName()
				{
					return !empty($this->info['first_name']) || !empty($this->info['last_name']);
				}

			function VehicleCondition()
				{
					return $this->info['vehicle_condition'];
				}

			function VehicleModel()
				{
					return $this->info['vehicle_model'];
				}

			function VehicleMake()
				{
					return $this->info['vehicle_make'];
				}

			function HasSocialURLS()
				{
					return !empty($this->info['facebookurl']) || !empty($this->info['twitterurl']) || !empty($this->info['instagramurl']) || !empty($this->info['linkedin']);
				}

			function HasFacebookURL()
				{
					return !empty($this->info['facebookurl']);
				}

			function FacebookURL()
				{
					return $this->info['facebookurl'];
				}

			function HasTwitterURL()
				{
					return !empty($this->info['twitterurl']);
				}

			function TwitterURL()
				{
					return $this->info['twitterurl'];
				}

			function HasInstagramURL()
				{
					return !empty($this->info['instagramurl']);
				}

			function InstagramURL()
				{
					return $this->info['instagramurl'];
				}

			function HasLinkedInURL()
				{
					return !empty($this->info['linkedin']);
				}

			function LinkedInURL()
				{
					return $this->info['linkedin'];
				}

			function VehicleFormatted()
				{
					$s = $this->VehicleMake();
					if (strlen($s) > 0)
						$s .= ' ';
					$s .= $this->VehicleModel();
					if (!empty($s))
						$s .= ' ('.$this->VehicleCondition().')';
					else
						$s .= $this->VehicleCondition();
					return $s;
				}

			function Note($nl2br = true)
				{
					if ($nl2br)
						return nl2br($this->info['note']);
					else
						return $this->info['note'];
				}

			function SetNote($val)
				{
					$breaks = array("<br />","<br>","<br/>", "&lt;br /&gt;");
					$upd['note'] = str_ireplace($breaks, "", $val);
					$upd['note'] = strip_tags($upd['note']);
					$this->UpdateValues($upd);
				}

			function LastName()
				{
					return $this->info['last_name'];
				}

			function Website()
				{
					return $this->info['website'];
				}

			function SetWebsite($val)
				{
					$this->UpdateValues(Array('website' => $val));
				}

			function HasWebsite()
				{
					return !empty($this->info['website']);
				}

			function SalesPersonID()
				{
					return intval($this->info['salesperson']);
				}

			function SalesPerson2ID()
				{
					return intval($this->info['salesperson2']);
				}

			function SalesManagerID()
				{
					return intval($this->info['salesmanager']);
				}

			function ID()
				{
					return intval($this->info['id']);
				}

			function SetLastUpdateTime()
				{
					$params['lastupdate'] = time();
					$this->UpdateValues($params);
				}

			function MarkAsConversationUnread()
				{
					$params['unread'] = time();
					$this->UpdateValues($params);
					$this->SetLastUpdateTime();
				}

			function MarkAsConversationRead()
				{
					$params['unread'] = 0;
					$this->UpdateValues($params);
					$this->SetLastUpdateTime();
				}

			function Delete()
				{
					$phones = new TPhoneList();
					$phones->SetFilterCustomer($this);
					$phones->Load();

					for ($i = 0; $i < $phones->Count(); $i++)
						{
							$phones->Item($i)->Remove();
						}

					$emails = new TEmailsList();
					$emails->SetFilterCustomer($this);
					$emails->Load();

					for ($i = 0; $i < $emails->Count(); $i++)
						{
							$emails->Item($i)->Remove();
						}

					TQuery::ForTable('customers')->AddWhere('id', intval($this->ID()))->Remove();
				}

			function Remove()
				{
					$params['deleted'] = time();
					$this->UpdateValues($params);
					$this->SetLastUpdateTime();
					//TODO disable user
				}

			function Disable()
				{
					$upd['is_enabled'] = 0;
					$this->UpdateValues($upd);
				}

			function Enable()
				{
					$upd['is_enabled'] = 1;
					$this->UpdateValues($upd);
				}

			function isEnabled()
				{
					return $this->info['is_enabled'] == 1;
				}

			function Type()
				{
					return $this->info['type'];
				}

			function isTypeCustomer()
				{
					return $this->Type() == 'customer';
				}

			function isTypeCompany()
				{
					return $this->Type() == 'company';
				}

			function SetType($val)
				{
					$upd['type'] = $val;
					$this->UpdateValues($upd);
				}

			function SetTypeCustomer()
				{
					$this->SetType('customer');
				}

			function SetTypeCompany()
				{
					$this->SetType('company');
				}

			function Log($text)
				{
					sm_log('customer', $this->ID(), $text);
				}

			function HasUnreadConversation()
				{
					return !empty($this->info['unread']);
				}

			function UnsetStoredPassword()
				{
					//Unset unencrypted password to protect hacking
					$this->SetMetaData('pwd', NULL);
				}

			protected function SendSMS($text, $messagetype = 'conversation', $scheduletime = 0, $attachments = Array(), $contact_id=0, $campaign_id=0)
				{
					use_api('sms');
					$company = TCompany::UsingCache($this->CompanyID());
					queue_sms($this->Cellphone(), $text, $company->Cellphone(), $scheduletime, $attachments, $contact_id, $campaign_id);
					$this->SetLastUpdateTime();
				}

			function SendInitialSMS()
				{
					global $userinfo;
					if ($this->isSendingMessagesUndefined())
						{
							$company = TCompany::UsingCache($this->CompanyID());
							$employee=new TEmployee(intval($userinfo['id']));
							if ($employee->Exists())
								{
									if($company->InitialComplianceTagFirstName())
										$contact = $employee->FirstName();
									elseif($company->InitialComplianceTagFirstLastName())
										$contact = $employee->Name();
									else
										$contact = 'Staff memeber';
								}
							else
								$contact='Staff memeber';

							if ($company->HasInitialComplianceMessageText())
								$initial_message_text = $company->InitialComplianceMessageText();
							else
								$initial_message_text = '{NAME} from {BUSINESS} would like to ask you a question.';

							$txt = str_replace('{NAME}', $contact, $initial_message_text);
							$txt=str_replace('{BUSINESS}', $company->Name(), $txt);

							$txt = $txt.' Reply with Yes to accept or No to decline.';
							
							$this->SetMetaData('sms_accept_message', $txt);
							$this->SendSMS(
								$txt,
								'initial'
							);
							$this->SetSMSAcceptedTag('pending1');
							$this->SetSMSPendingTimestamp();
						}
				}
			function ReSendInitialSMS()
				{
					$txt=$this->GetMetaData('sms_accept_message');
					if (!empty($txt))
						{
							$this->SendSMS(
								$txt,
								'initial'
							);
						}
				}

			function HasFullAddressAndName()
				{
					return $this->HasName() && $this->HasAddress1() && $this->HasCity() && $this->HasZip();
				}

			function SendVoice($scheduletime = 0, $asset_id = 0)
				{
					$company = TCompany::UsingCache($this->CompanyID());

					if($this->HasCellphone())
						queue_voice($this->Cellphone(),$company->Cellphone(), $scheduletime, $company->ID(), $this->ID(), $asset_id);
				}

			function SendMessage($text, $id_employee = 0, $writelog = true, $logtype = 'conversation', $scheduletime = 0, $attachments = Array(), $send_message_as_sms = true, $asset_id = 0, $contact_id=0, $campaign_id=0, $id_campaign_schedule = 0)
				{
					use_api('sms');
					if ($this->isSendingMessagesUndefined())
						{
							$company = TCompany::UsingCache($this->CompanyID());
							$employee=new TEmployee($id_employee);
							if ($employee->Exists())
								{
									if($company->InitialComplianceTagFirstName())
										$contact = $employee->FirstName();
									elseif($company->InitialComplianceTagFirstLastName())
										$contact = $employee->Name();
									else
										$contact = 'Staff memeber';
								}
							else
								$contact='Staff memeber';

							if ($company->HasInitialComplianceMessageText())
								$initial_message_text = $company->InitialComplianceMessageText();
							else
								$initial_message_text = '{NAME} from {BUSINESS} would like to ask you a question.';

							$txt = str_replace('{NAME}', $contact, $initial_message_text);
							$txt=str_replace('{BUSINESS}', $company->Name(), $txt);

							$txt = $txt.' Reply with Yes to accept or No to decline.';

/*							$txt=sprintf('%1$s from %2$s would like to ask you a question. Reply with Yes to accept or No to decline.',
																$contact,
																$company->Name()
															);
*/
							$this->SetMetaData('sms_accept_message', $txt);
							$this->SendSMS(
								$txt,
								'initial'
							);
							TPendingMessage::CreateOutgoing(
								$text,
								$this->CompanyID(),
								$this,
								$logtype,
								$id_employee,
								$asset_id,
								$scheduletime,
								$contact_id,
								$campaign_id,
								$id_campaign_schedule
							);
							$this->SetSMSAcceptedTag('pending1');
							$this->SetSMSPendingTimestamp();
						}
					elseif ($this->isSendingMessagesPending())//Put message to pending queue until customer replies YES
						{
							TPendingMessage::CreateOutgoing(
								$text,
								$this->CompanyID(),
								$this,
								$logtype,
								$id_employee,
								$asset_id,
								$scheduletime,
								$contact_id,
								$campaign_id,
								$id_campaign_schedule
							);
						}
					elseif ($this->isSendingMessagesEnabled())
						{
							$company = TCompany::UsingCache($this->CompanyID());
							if ($send_message_as_sms)
								$this->SendSMS($text, $logtype, $scheduletime, $attachments, $contact_id, $campaign_id);
							else
								queue_message($company, $this, $this->Cellphone(), $text, $company->Cellphone(), $scheduletime, $attachments, $contact_id, $campaign_id, $id_campaign_schedule);
							if ($writelog)
								{
									$id = TMessage::CreateOutgoing(
										$text,
										$this->CompanyID(),
										$this,
										$logtype,
										$id_employee,
										$asset_id,
										$scheduletime,
										$contact_id,
										$campaign_id,
										$id_campaign_schedule
									);
									if (!empty($id))
										TMessagesLog::Create('sms', $id, $this->CompanyID(), $this, $scheduletime);
								}
						}
					$this->SetLastUpdateTime();
				}

			function IncomingSMS($text)
				{
					$q = new TQuery('smslog');
					$q->Add('type', 'conversation');
					$q->Add('id_company', intval($this->CompanyID()));
					$q->Add('id_customer', intval($this->ID()));
					$q->Add('is_incoming', 1);
					$q->Add('id_employee', 0);
					$q->Add('timeadded', time());
					$q->Add('timeread_by_client', time());
					$q->Add('text', dbescape($text));
					$q->Insert();
					$this->SetLastUpdateTime();
					$this->MarkAsConversationUnread();
					$company = TCompany::UsingCache($this->CompanyID());
					$company->NewMessageFromCustomerNotification($this, $text);
				}

			function CompanyID()
				{
					return intval($this->info['id_company']);
				}

			function UpdateValues($params)
				{
					global $sm;
					if (empty($params) || !is_array($params))
						return;
					$q = new TQuery('customers');
					foreach ($params as $key => $val)
						{
							$this->info[$key] = $val;
							$q->Add($key, dbescape($this->info[$key]));
						}
					$q->Update('id', $this->ID());
				}

			public static function Create()
				{
					global $userinfo;
					$q = new TQuery('customers');
					$q->Add('approved', 1);
					$q->Add('timeadded', time());
					$q->Add('source', 'import');
					$q->Add('registeredtype', 'staff');
					$q->Add('addedby', intval($userinfo['id']));
					$id = $q->Insert();
					$customer = new TCustomer($id);
					$customer->ONCreateEvent();
					return $customer;
				}

			public static function CreateByStaff($company, $first_name, $last_name, $cellphone='', $email='')
				{
					global $userinfo;
					$q = new TQuery('customers');
					$q->Add('id_company', $company->ID());
					$q->Add('first_name', dbescape($first_name));
					$q->Add('last_name', dbescape($last_name));
					$q->Add('cellphone', Cleaner::USPhone($cellphone));
					if( !empty($email) && is_email($email) )
						$q->Add('email', dbescape($email));
					$q->Add('timeadded', time());
					$q->Add('source', 'manual');
					$q->Add('registeredtype', 'staff');
					$q->Add('addedby', intval($userinfo['id']));
					$id = $q->Insert();
					$customer = new TCustomer($id);
					$customer->ONCreateEvent();
					return $customer;
				}

			public static function CreateByImport($company, $first_name, $last_name, $cellphone='', $email='', $address='', $city='', $state='', $zip='')
				{
					global $userinfo;
					$q = new TQuery('customers');
					$q->Add('id_company', $company->ID());
					$q->Add('first_name', dbescape($first_name));
					$q->Add('last_name', dbescape($last_name));
					if (!empty($cellphone))
						$q->Add('cellphone', Cleaner::USPhone($cellphone));
					if (!empty($email))
						$q->Add('email', dbescape($email));
					if (!empty($address))
						$q->Add('address', dbescape($address));
					if (!empty($city))
						$q->Add('city', dbescape($city));
					if (!empty($state))
						$q->Add('state', dbescape($state));
					if (!empty($zip))
						$q->Add('zip', dbescape($zip));
					$q->Add('timeadded', time());
					$q->Add('source', 'import');
					$q->Add('registeredtype', 'staff');
					$q->Add('addedby', intval($userinfo['id']));
					$id = $q->Insert();
					$customer = new TCustomer($id);
					$customer->ONCreateEvent();
					return $customer;
				}

			function HasProfilePhoto()
				{
					return file_exists($this->ProfilePhotoPath());
				}

			function ProfilePhotoPath()
				{
					return 'files/img/customeravatar'.md5('g'.$this->ID().'s').'.jpg';
				}

			function ProfilePhotoURL()
				{
					return $this->ProfilePhotoPath();
				}

			function SetMetaData($key, $val)
				{
					sm_set_metadata('customer', $this->ID(), $key, $val);
				}

			function GetMetaData($key)
				{
					return sm_metadata('customer', $this->ID(), $key);
				}

			function MarketingMessagesCount()
				{
					$q = new TQuery('smslog');
					$q->INStrings('type', Array('multi', 'blast'));
					$q->AddWhere('id_company', intval($this->CompanyID()));
					$q->AddWhere('id_customer', intval($this->ID()));
					return $q->TotalCount();
				}

			function UnreadMessagesCount()
				{
					$q = new TQuery('smslog');
					$q->AddWhere('id_company', intval($this->CompanyID()));
					$q->AddWhere('id_customer', intval($this->ID()));
					$q->AddWhere('is_incoming=0');
					$q->AddWhere('timeread_by_client=0');
					return $q->TotalCount();
				}

			function SetLastName($val)
				{
					$upd['last_name'] = $val;
					$this->UpdateValues($upd);
				}

			function SetFirstName($val)
				{
					$upd['first_name'] = $val;
					$this->UpdateValues($upd);
				}

			function SetEmail($val)
				{
					$upd['email'] = $val;
					$this->UpdateValues($upd);
				}

			function SetBusinessName($val)
				{
					$upd['company'] = $val;
					$this->UpdateValues($upd);
				}

			function SetCompany($val)
				{
					$upd['company'] = $val;
					$this->UpdateValues($upd);
				}

			function GetEmailStatus()
				{
					return $this->info['valid_email'];
				}
			function GetBusinessName()
				{
					return $this->info['company'];
				}
			function SetEmailValidated()
				{
					$upd['valid_email'] = 1;
					$this->UpdateValues($upd);
				}

			function SetEmailNotValid()
				{
					$upd['valid_email'] = 2;
					$this->UpdateValues($upd);
				}

			function CreateMainCellPhone($val)
				{
					$phone = Cleaner::Phone($val);
					TPhone::Create($phone, $this->ID(), $this->CompanyID());
					$upd['cellphone'] = $phone;
					$this->UpdateValues($upd);
				}

			function SetCellPhone($val)
				{
					$upd['cellphone'] = $val;
					$this->UpdateValues($upd);
				}

			function SetAddress($val)
				{
					$upd['address'] = $val;
					$this->UpdateValues($upd);
				}

			function SetCity($val)
				{
					$upd['city'] = $val;
					$this->UpdateValues($upd);
				}

			function SetState($val)
				{
					$upd['state'] = $val;
					$this->UpdateValues($upd);
				}

			function SetZip($val)
				{
					$upd['zip'] = $val;
					$this->UpdateValues($upd);
				}

			function SetFacebookUrl($val)
				{
					$upd['facebookurl'] = $val;
					$this->UpdateValues($upd);
				}

			function SetTwitterUrl($val)
				{
					$upd['twitterurl'] = $val;
					$this->UpdateValues($upd);
				}

			function SetInstagramUrl($val)
				{
					$upd['instagramurl'] = $val;
					$this->UpdateValues($upd);
				}

			function SetLinkedin($val)
				{
					$upd['linkedin'] = $val;
					$this->UpdateValues($upd);
				}

			function SetBankName($val)
				{
					$upd['bank_name'] = $val;
					$this->UpdateValues($upd);
				}

			function SetRouting($val)
				{
					$upd['routing'] = $val;
					$this->UpdateValues($upd);
				}

			function SetAcctNumber($val)
				{
					$upd['acct_number'] = $val;
					$this->UpdateValues($upd);
				}

			function SetPaypalEmail($val)
				{
					$upd['paypal_email'] = $val;
					$this->UpdateValues($upd);
				}

			function GetBankName()
				{
					return $this->info['bank_name'];
				}

			function GetRouting()
				{
					return $this->info['routing'];
				}

			function GetSetAcctNumber()
				{
					return $this->info['acct_number'];
				}

			function GetSetPaypalEmail()
				{
					return $this->info['paypal_email'];
				}

			function ONCreateEvent()
				{
				}

			function HasAppointments()
				{
					return intval($this->info['has_appointment']) > 0;
				}

			function MarkAsHasAppointments()
				{
					$upd['has_appointment'] = 1;
					$this->UpdateValues($upd);
				}

			function GetSMSAcceptedTimestamp()
				{
					return $this->info['sms_accepted_time'];
				}

			function SetSMSAcceptedTimestamp($val=NULL)
				{
					$upd['sms_accepted_time'] = $val===NULL?time():$val;
					$this->UpdateValues($upd);
				}

			function GetSMSAcceptedTag()
				{
					return $this->info['sms_accepted'];
				}

			function SetSMSAcceptedTag($val)
				{
					$upd['sms_accepted'] = $val;
					$this->UpdateValues($upd);
				}

			function GetSMSPendingTimestamp()
				{
					return $this->info['sms_pending_time'];
				}

			function SetSMSPendingTimestamp($val=NULL)
				{
					$upd['sms_pending_time'] = $val===NULL?time():$val;
					$this->UpdateValues($upd);
				}

			function isSendingMessagesUndefined()
				{
					return $this->GetSMSAcceptedTag()=='undefined';
				}

			function isSendingMessagesRejected()
				{
					return $this->GetSMSAcceptedTag()=='no';
				}

			function isSendingMessagesNoResponse()
				{
					return $this->GetSMSAcceptedTag()=='noresponse';
				}

			function isSendingMessagesPending()
				{
					return $this->GetSMSAcceptedTag()=='pending1' || $this->GetSMSAcceptedTag()=='pending2';
				}

			function isSendingMessagesEnabled()
				{
					return $this->GetSMSAcceptedTag()=='yes' && $this->GetSMSAcceptedTimestamp()>time()-365*24*3600;
				}

			function isSendingMessagesAbilityExpired()
				{
					return $this->GetSMSAcceptedTag()=='yes' && $this->GetSMSAcceptedTimestamp()<=time()-365*24*3600;
				}

			function AddToCampaign($campaign)
				{
					$this->SetTaxonomy('customertocampaign', $this->ID(), Cleaner::IntObjectID($campaign));
				}

			function SetUnsubscribeStatus($val)
				{
					$this->SetMetaData('unsubscribed', $val);
				}

			function GetUnsubscribeStatus()
				{
					return $this->GetMetaData('unsubscribed');
				}

			function isUnsubscribeStatus()
				{
					return $this->GetMetaData('unsubscribed')==1;
				}

			function LoadOtherEmailsArray($exclude_email = '', $show_ids = false)
				{
					return $this->LoadOtherEmailsObject()->ExtractEmailsArray($exclude_email, $show_ids);
				}

			function LoadOtherEmailsObject()
				{
					$emails = new TEmailsList();
					$emails->SetFilterCompany($this->CompanyID());
					$emails->SetFilterCustomer($this);
					return $emails->Load();
				}

			function LoadOtherPhonesArray($show_ids = false)
				{
					return $this->LoadOtherPhonesObject()->ExtractPhonesArray($show_ids);
				}

			function LoadOtherPhonesObject()
				{
					$phones = new TPhoneList();
					$phones->SetFilterCompany($this->CompanyID());
					$phones->SetFilterCustomer($this);
					$phones->SetFilterAdditionalPhones(['other']);
					return $phones->Load();
				}

			function GetCustomerSequence()
				{
					$this->LoadSequences();
					return $this->sequenceids[0];
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

			function StatusTag()
				{
					return $this->info['status'];
				}
			function StatusName()
				{
					if ($this->isStatusPending())
						return 'Pending';
					elseif ($this->isStatusPendingVerification())
						return 'Pending Verification';
					elseif ($this->isStatusContact())
						return 'Contact';
					elseif ($this->isStatusAppointment())
						return 'Appointment';
					elseif ($this->isStatusSold())
						return 'Sold';
					elseif ($this->isStatusLost())
						return 'Lost';
					else
						return 'Received';
				}
			function StatusNamePublic()
				{
					//	Some of statuses from StatusName() shouldn't be shown for customer.
					if ($this->isStatusContact())
						return 'Contact';
					elseif ($this->isStatusAppointment())
						return 'Appointment';
					elseif ($this->isStatusSold())
						return 'Sold';
					elseif ($this->isStatusLost())
						return 'Lost';
					else
						return 'Received';
				}

			function isStatusReceived()
				{
					return $this->StatusTag()=='received';
				}

			function isStatusContact()
				{
					return $this->StatusTag()=='contact';
				}

			function isStatusAppointment()
				{
					return $this->StatusTag()=='appointment';
				}

			function isStatusSold()
				{
					return $this->StatusTag()=='sold';
				}

			function isStatusLost()
				{
					return $this->StatusTag()=='lost';
				}

			function isStatusPending()
				{
					return $this->StatusTag()=='pending';
				}

			function SetStatusPendingVerification()
				{
					$this->SetStatusTag('pending_verification');
				}

			function isStatusPendingVerification()
				{
					return $this->StatusTag()=='pending_verification';
				}

			function SetStatusTag($tagname)
				{
					$params['status']=$tagname;
					$this->UpdateValues($params);
					$this->SetLastUpdateTime();
				}

			function SortOrder()
				{
					return $this->info['sort_order'];
				}

			function SetSortOrder($val)
				{
					$this->UpdateValues(Array('sort_order'=>intval($val)));
				}

			function SetSearchID($search_id)
				{
					$this->SetTaxonomy('customertosearch', intval($search_id), $this->ID());
				}

			function UnsetSearchID($search_id)
				{
					$this->UnsetTaxonomy('customertosearch', intval($search_id), $this->ID());
					$this->LoadSearches(true);
				}

			function UnsetSearchIDs()
				{
					$this->LoadSearches();
					for ($i=0; $i<count($this->searchids); $i++)
						{
							$this->UnsetTaxonomy('customertosearch', $this->ID(), intval($this->searchids[$i]));
						}
				}

			function SearchCount()
				{
					$this->LoadSearches();
					return count($this->searchids);
				}

			function LoadSearches($rewritecache = false)
				{
					if ($rewritecache || $this->searchids === NULL)
						{
							$this->searchids = $this->GetTaxonomy('customertosearch', $this->ID(), true);
						}
				}

			function SetBuiltWithSearchID($search_id)
				{
					$this->SetTaxonomy('customertobuiltwithsearch', intval($search_id), $this->ID());
				}

			function UnsetBuiltWithSearchID($search_id)
				{
					$this->UnsetTaxonomy('customertobuiltwithsearch', intval($search_id), $this->ID());
					$this->LoadSearches(true);
				}

			function UnsetBuiltWithSearchIDs()
				{
					$this->LoadBuiltWithSearches();
					for ($i=0; $i<count($this->builtwithlistids); $i++)
						{
							$this->UnsetTaxonomy('customertobuiltwithsearch', $this->ID(), intval($this->builtwithlistids[$i]));
						}
				}

			function SearchBuiltWithCount()
				{
					$this->LoadBuiltWithSearches();
					return count($this->builtwithlistids);
				}

			function LoadBuiltWithSearches($rewritecache = false)
				{
					if ($rewritecache || $this->builtwithlistids === NULL)
						{
							$this->builtwithlistids = $this->GetTaxonomy('customertobuiltwithsearch', $this->ID());
						}
				}

			function SetMissedCall($val)
				{
					$this->SetMetaData('missed_call', $val);
				}

			function GetMissedCall()
				{
					return $this->GetMetaData('missed_call');
				}

			function HasMissedCall()
				{
					return $this->GetMissedCall() == 1;
				}

			function Source()
				{
					return $this->info['source'];
				}

			function SetSource($name)
				{
					$this->UpdateValues(Array('source'=>$name));
				}

		}

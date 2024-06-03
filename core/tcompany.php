<?php

if (!defined("tcompany_DEFINED"))
	{
		class TCompany
			{
				function TCompany($id_or_cahcedinfo=NULL)
					{
						global $sm;
						if (is_array($id_or_cahcedinfo))
							{
								$this->info=$id_or_cahcedinfo;
							}
						else
							{
								if (intval($id_or_cahcedinfo)==0)
									$id_or_cahcedinfo=$sm['u']['id_company'];
								$this->info=TQuery::ForTable('companies')->Add('id', intval($id_or_cahcedinfo))->Get();
							}
						if (!$this->Exists())
							{
								exit('E281 - Wrong Company Information');
							}
					}

				public static function initWithSubscriptionID($twiliophone)
					{
						use_api('cleaner');
						/** @var $company TCompany */
						$info=TQuery::ForTable('companies')->Add('id_subscription', intval($twiliophone))->Get();
						$company=new TCompany($info);
						return $company;
					}

				public static function checkIfExistWithPhone($twiliophone)
					{
						use_api('cleaner');
						/** @var $company TCompany */
						$info = TQuery::ForTable('companies')->Add('twiliophone', dbescape(Cleaner::USPhone($twiliophone)))->Get();
						if (!empty($info))
							{
								$company = new TCompany($info);
								return $company;
							}
						else
							return false;
					}

				function GetRawData()
					{
						return $this->info;
					}

				function ID()
					{
						return intval($this->info['id']);
					}

				function Status()
					{
						return intval($this->info['status']);
					}

				function IdBusinessType()
					{
						return intval($this->info['id_bt']);
					}

				function ExpirationTimestamp()
					{
						return intval($this->info['expiration']);
					}

				function Owner()
					{
						return '';
					}

				function Name()
					{
						return $this->info['name'];
					}

				function SetName($val)
					{
						$this->UpdateValues(Array('name'=>$val));
					}

				function Exists()
					{
						return !empty($this->info['id']);
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
						$this->UpdateValues(Array('address'=>$val));
					}

				function HasAddress2()
					{
						return !empty($this->info['address2']);
					}

				function Address2()
					{
						return $this->info['address2'];
					}

				function UnsubscribeMessageStatus()
					{
						return $this->info['sms_stop_message'];
					}

				function isUnsubscribeMessageSet()
					{
						return $this->info['sms_stop_message']==1;
					}

				function SetAddress2($val)
					{
						$this->UpdateValues(Array('address2'=>$val));
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
						$this->UpdateValues(Array('city'=>$val));
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
						$this->UpdateValues(Array('state'=>$val));
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
						$this->UpdateValues(Array('zip'=>$val));
					}
				function HasMailjetApiSecret()
					{
						return !empty($this->info['mailjet_api_secret']);
					}

				function MailjetApiSecret()
					{
						return $this->info['mailjet_api_secret'];
					}

				function SetMailjetApiSecret($val)
					{
						$this->UpdateValues(Array('mailjet_api_secret'=>$val));
					}

				function HasMailjetApiKey()
					{
						return !empty($this->info['mailjet_api_key']);
					}

				function MailjetApiKey()
					{
						return $this->info['mailjet_api_key'];
					}

				function SetMailjetApiKey($val)
					{
						$this->UpdateValues(Array('mailjet_api_key'=>$val));
					}

				function HasTwilioAccountSid()
					{
						return !empty($this->info['twilio_AccountSid']);
					}

				function TwilioAccountSid()
					{
						return $this->info['twilio_AccountSid'];
					}

				function SetTwilioAccountSid($val)
					{
						$this->UpdateValues(Array('twilio_AccountSid'=>$val));
					}

				function HasTwilioAuthToken()
					{
						return !empty($this->info['twilio_AuthToken']);
					}

				function TwilioAuthToken()
					{
						return $this->info['twilio_AuthToken'];
					}

				function SetTwilioAuthToken($val)
					{
						$this->UpdateValues(Array('twilio_AuthToken'=>$val));
					}

				function HasTwilioApiSid()
					{
						return !empty($this->info['twilio_ApiSid']);
					}

				function TwilioApiSid()
					{
						return $this->info['twilio_ApiSid'];
					}

				function SetTwilioApiSid($val)
					{
						$this->UpdateValues(Array('twilio_ApiSid'=>$val));
					}

				function TwilioTwiMLAppSID()
					{
						return $this->info['twilio_twiml_app_sid'];
					}

				function HasTwilioTwiMLAppSID()
					{
						return !empty($this->info['twilio_twiml_app_sid']);
					}

				function SetTwilioTwiMLAppSID($val)
					{
						$this->UpdateValues(Array('twilio_twiml_app_sid'=>$val));
					}

				function HasTwilioApiSecret()
					{
						return !empty($this->info['twilio_ApiSecret']);
					}

				function TwilioApiSecret()
					{
						return $this->info['twilio_ApiSecret'];
					}

				function SetTwilioApiSecret($val)
					{
						$this->UpdateValues(Array('twilio_ApiSecret'=>$val));
					}

				function Cellphone()
					{
						return $this->info['twiliophone'];
					}
				function SetCellphone($cellphone)
					{
						$this->UpdateValues(Array('twiliophone'=>Cleaner::USPhone($cellphone)));
					}
				function PhoneForConversation()
					{
						return $this->info['twiliophone'];
					}
				function SendNotificationsToCellphone()
					{
						return $this->info['cellphone_for_notifications'];
					}
				function HasSendNotificationsToCellphone()
					{
						return !empty($this->info['cellphone_for_notifications']);
					}
				function SetNotificationsToCellphone($cellphone)
					{
						$this->UpdateValues(Array('cellphone_for_notifications'=>Cleaner::USPhone($cellphone)));
					}
				function SMSNotification($text)
					{
						if ($this->HasSendNotificationsToCellphone())
							{
								use_api('sms');
								queue_sms($this->SendNotificationsToCellphone(), $text, $this->Cellphone());
							}
					}
				function UpdateValues($params)
					{
						global $sm;
						unset($params['id']);
						if (empty($params) || !is_array($params))
							return;
						$q=new TQuery('companies');
						foreach ($params as $key=>$val)
							{
								$this->info[$key]=$val;
								$q->Add($key, dbescape($this->info[$key]));
							}
						$q->Update('id', $this->ID());
					}
				/** @return TCompany */
				public static function UsingCache($id)
					{
						global $sm;
						if (!is_object($sm['cache']['company'][$id]))
							{
								$sm['cache']['company'][$id]=new TCompany($id);
							}
						return $sm['cache']['company'][$id];
					}
				public static function isSystemCompany()
					{
						return self::CurrentCompany()->ID() == 1;
					}

				public static function CurrentCompany()
					{
						global $currentcompany;
						/** @var $currentcompany TCompany */
						return $currentcompany;
					}

				public static function SystemCompany()
					{
						return TCompany::UsingCache(1);
					}

				function HasBuiltWithApiKey()
					{
						return !empty($this->info['builtwith_api_key']);
					}

				function isBuiltWithApiEnabled()
					{
						return !empty($this->info['builtwith_search']);
					}

				function isGoogleApiEnabled()
					{
						return !empty($this->info['google_search']);
					}

				function BuiltWithApiKey()
					{
						return $this->info['builtwith_api_key'];
					}

				public static function checkCompanyWithEmail($email)
					{
						$info=TQuery::ForTable('companies')->Add('email', dbescape($email))->Get();
						if ($info)
							{
								$company=new TCompany($info);
								return $company;
							}
						else
							return false;
					}

				public static function InitCurrentCompany($id)
					{
						global $currentcompany;
						/** @var $currentcompany TCompany */
						$currentcompany=new TCompany($id);
					}
				public static function initWithPhone($twiliophone)
					{
						use_api('cleaner');
						/** @var $company TCompany */
						$info=TQuery::ForTable('companies')->Add('twiliophone', dbescape(Cleaner::USPhone($twiliophone)))->Get();
						$company=new TCompany($info);
						return $company;
					}
				public static function initWithEmail($email)
					{
						use_api('cleaner');
						/** @var $company TCompany */
						$info=TQuery::ForTable('companies')->Add('email', dbescape($email))->Get();
						$company=new TCompany($info);
						return $company;
					}
				function SetSystemLogoImage($filename, $erase_source_after_copying=true)
					{
						if (!file_exists($filename))
							return false;
						use_api('path');
						$fd=Path::DealersRoot().'/files/img/systemlogo'.$this->ID().'.jpg';
						if (file_exists($fd))
							unlink($fd);
						copy($filename, $fd);
						if ($erase_source_after_copying)
							unlink($filename);
						return true;
					}
				function SystemLogoImageURL()
					{
						return 'https://'.image_domain().'/files/img/systemlogo'.$this->ID().'.jpg';
					}
				function HasSystemLogoImageURL()
					{
						use_api('path');
						return file_exists(Path::DealersRoot().'/files/img/systemlogo'.$this->ID().'.jpg');
					}
				function isCustomerFieldEnabled($field)
					{
						$fields=nllistToArray($this->info['customer_enabled_fields']);
						return in_array($field, $fields);
					}
				function EnableCustomerField($field)
					{
						$fields=nllistToArray($this->info['customer_enabled_fields']);
						if (!in_array($field, $fields))
							$fields[]=$field;
						$this->UpdateValues(Array('customer_enabled_fields'=>arrayToNllist($fields)));
					}
				function DisableCustomerField($field)
					{
						$fields=removefrom_nllist($this->info['customer_enabled_fields'], $field);
						$this->UpdateValues(Array('customer_enabled_fields'=>$fields));
					}
				function LabelForCustomer()
					{
						return $this->info['customer_label'];
					}
				function LabelForCustomers()
					{
						return $this->info['customers_label'];
					}
				function HasCustomerFormTemplate()
					{
						return !empty($this->info['id_template']);
					}
				function CustomerFormTemplate()
					{
						return $this->info['id_template'];
					}
				function SetCustomerFormTemplate($val)
					{
						$this->UpdateValues(Array('id_template'=>intval($val)));
					}
				function isExpired()
					{
						return false;
					}
				function MobileAppAuthorizationCode()
					{
						return 1000+$this->ID();
					}

				function HasEmailFrom()
					{
						return !empty($this->info['email']);
					}

				function EmailFrom()
					{
						return $this->info['email'];
					}

				function SetEmailFrom($val)
					{
						$this->UpdateValues(Array('email'=>$val));
					}

				public static function initWithMobileAppAuthorizationCode($code)
					{
						/** @var $company TCompany */
						$info=TQuery::ForTable('companies')->Add('id', intval($code)-1000)->Get();
						if (empty($info['id']))
							return false;
						$company=new TCompany($info);
						return $company;
					}

				function SetCompanyAppointmentReminder($val)
					{
						$this->SetMetaData('company_appointment_reminder', dbescape($val));
					}
				function HasCompanyAppointmentReminder()
					{
						return !empty($this->CompanyAppointmentReminder());
					}
				function CompanyAppointmentReminder()
					{
						return $this->GetMetaData('company_appointment_reminder');
					}
				function SetMetaData($key, $val)
					{
						sm_set_metadata('company', $this->ID(), $key, $val);
					}

				function InitialComplianceTagFirstName()
					{
						return (empty($this->InitialComplianceMessageName()) || $this->InitialComplianceMessageName()=='first_name');
					}
				function InitialComplianceTagFirstLastName()
					{
						return $this->InitialComplianceMessageName()=='first_and_last_name';
					}
				function InitialComplianceTagFirstAnonym()
					{
						return ($this->InitialComplianceMessageName()=='anonymous');
					}
				function HasInitialComplianceMessageName()
					{
						return !empty($this->InitialComplianceMessageName());
					}
				function InitialComplianceMessageName()
					{
						return $this->GetMetaData('initial_compliance');
					}
				function SetInitialComplianceMessageName($val)
					{
						$this->SetMetaData('initial_compliance', dbescape($val));
					}
				function HasInitialComplianceMessageText()
					{
						return !empty($this->InitialComplianceMessageText());
					}
				function InitialComplianceMessageText()
					{
						return $this->GetMetaData('initial_compliance_text');
					}
				function SetInitialComplianceMessageText($val)
					{
						$this->SetMetaData('initial_compliance_text', dbescape($val));
					}
				function InitialAssetID()
					{
						return intval($this->GetMetaData('initial_asset'));
					}
				function HasInitialAssetID()
					{
						return $this->InitialAssetID()>0;
					}
				function SetInitialAssetID($id)
					{
						$this->SetMetaData('initial_asset', intval($id));
					}

				function GetMetaData($key)
					{
						return sm_metadata('company', $this->ID(), $key);
					}
				function NewMessageFromCustomerNotification($customer, $message)
					{
						/** @var TCustomer $customer */
						$employees=new TEmployeeList($this->ID());
						$employees->SetFilterHaveNewMessageFromCustomerNotificationsEnabled();
						$employees->Load();
						for ($i = 0; $i < $employees->Count(); $i++)
							{
								if ($employees->items[$i]->NotificationAboutMessageFromMemberTag()=='email' && $employees->items[$i]->HasEmail())
									{
										$employees->items[$i]->SendEmail(frontend_domain().' - new message from '.$this->LabelForCustomer().' notification', sprintf('New message received from '.$this->LabelForCustomer().' %s at '.frontend_domain().'.<hr/>%s<hr/>Please check at https://'.main_domain().'/ccnv'.$customer->ID(), $customer->ContactName(), $message));
									}
								if ($employees->items[$i]->NotificationAboutMessageFromMemberTag()=='cellphone' && $employees->items[$i]->HasCellphone())
									{
										$employees->items[$i]->SendSMS(frontend_domain().' - message received from '.$this->LabelForCustomer().' - '.$customer->ContactName().' http://'.main_domain().'/ccnv'.$customer->ID(), $this->Cellphone());
									}
							}
					}
				public static function Create()
					{
						$sql=new TQuery('companies');
						$object = new TCompany($sql->Insert());
						return $object;
					}
				function Remove()
					{
						TQuery::ForTable('companies')->AddWhere('id', intval($this->ID()))->Remove();
					}

				function HasGoogleApiKey()
					{
						return !empty($this->info['google_places_api_key']);
					}

				function GoogleApiKey()
					{
						return $this->info['google_places_api_key'];
					}

				function SetGoogleApiKey($val)
					{
						$this->UpdateValues(Array('google_places_api_key'=>$val));
					}

				function HasCellphone()
					{
						return !empty($this->info['twiliophone']);
					}

				function SetCompanyEmailDnsID($val)
					{
						$this->SetMetaData('company_email_dns_id', $val);
					}

				function HasCompanyEmailDnsID()
					{
						return !empty($this->CompanyEmailDnsID());
					}

				function CompanyEmailDnsID()
					{
						return $this->GetMetaData('company_email_dns_id');
					}

				function SetCompanyEmailDomainID($val)
					{
						$this->SetMetaData('company_email_domain_id', $val);
					}

				function HasCompanyEmailDomainID()
					{
						return !empty($this->CompanyEmailDomainID());
					}

				function CompanyEmailDomainID()
					{
						return $this->GetMetaData('company_email_domain_id');
					}

				function SetCompanyEmailDomain($val)
					{
						$this->SetMetaData('company_email_domain', $val);
					}

				function HasCompanyEmailDomain()
					{
						return !empty($this->CompanyEmailDomain());
					}

				function CompanyEmailDomain()
					{
						return $this->GetMetaData('company_email_domain');
					}

				function SetCompanyEmailOwnerShipToken($val)
					{
						$this->SetMetaData('company_email_owner_ship_token', $val);
					}

				function HasCompanyEmailOwnerShipToken()
					{
						return !empty($this->CompanyEmailOwnerShipToken());
					}

				function CompanyEmailOwnerShipToken()
					{
						return $this->GetMetaData('company_email_owner_ship_token');
					}

				function SetCompanyEmailOwnerShipTokenRecordName($val)
					{
						$this->SetMetaData('company_email_owner_ship_token_record_name', $val);
					}

				function HasCompanyEmailOwnerShipTokenRecordName()
					{
						return !empty($this->CompanyEmailOwnerShipTokenRecordName());
					}

				function CompanyEmailOwnerShipTokenRecordName()
					{
						return $this->GetMetaData('company_email_owner_ship_token_record_name');
					}

				function SetCompanyEmailSPFRecordValue($val)
					{
						$this->SetMetaData('company_email_spf_record_value', $val);
					}

				function HasCompanyEmailSPFRecordValue()
					{
						return !empty($this->CompanyEmailSPFRecordValue());
					}

				function CompanyEmailSPFRecordValue()
					{
						return $this->GetMetaData('company_email_spf_record_value');
					}

				function SetCompanyEmailDKIMRecordName($val)
					{
						$this->SetMetaData('company_email_dkim_record_name', $val);
					}

				function HasCompanyEmailDKIMRecordName()
					{
						return !empty($this->CompanyEmailDKIMRecordName());
					}

				function CompanyEmailDKIMRecordName()
					{
						return $this->GetMetaData('company_email_dkim_record_name');
					}

				function SetCompanyEmailDKIMRecordValue($val)
					{
						$this->SetMetaData('company_email_dkim_record_value', $val);
					}

				function HasCompanyEmailDKIMRecordValue()
					{
						return !empty($this->CompanyEmailDKIMRecordValue());
					}

				function CompanyEmailDKIMRecordValue()
					{
						return $this->GetMetaData('company_email_dkim_record_value');
					}

				function SetCompanyEmailDKIMRecordStatus($val)
					{
						$this->SetMetaData('company_email_dkim_record_status', $val);
					}

				function HasCompanyEmailDKIMRecordStatus()
					{
						return !empty($this->CompanyEmailDKIMRecordStatus());
					}

				function CompanyEmailDKIMRecordStatus()
					{
						return $this->GetMetaData('company_email_dkim_record_status');
					}

				function SetCompanyEmailSPFRecordStatus($val)
					{
						$this->SetMetaData('company_email_spf_record_status', $val);
					}

				function HasCompanyEmailSPFRecordStatus()
					{
						return !empty($this->CompanyEmailSPFRecordStatus());
					}

				function CompanyEmailSPFRecordStatus()
					{
						return $this->GetMetaData('company_email_spf_record_status');
					}

				function SetCompanyEmailStatus($val)
					{
						$this->SetMetaData('company_email_status', $val);
					}

				function HasCompanyEmailStatus()
					{
						return !empty($this->CompanyEmailStatus());
					}

				function CompanyEmailStatus()
					{
						return $this->GetMetaData('company_email_status');
					}

				function isCompanyEmailActive()
					{
						return $this->CompanyEmailStatus() == 'Active';
					}

				function SetCompanyEmailParseRouteID($val)
					{
						$this->SetMetaData('company_email_parse_route_id', $val);
					}

				function HasCompanyEmailParseRouteURL()
					{
						return !empty($this->CompanyEmailParseRouteURL());
					}
				function CompanyEmailParseRouteURL()
					{
						return $this->GetMetaData('company_email_parse_route_id');
					}

				function HasCompanyEmailParseRouteID()
					{
						return !empty($this->CompanyEmailParseRouteID());
					}
				function CompanyEmailParseRouteID()
					{
						return $this->GetMetaData('company_email_parse_route_id');
					}

				function SetCompanyEmailParseRouteURL($val)
					{
						$this->SetMetaData('company_email_parse_route_url', $val);
					}

				function StripeSecretKey()
					{
						return $this->info['stripe_secret_key'];
					}

				function HasStripePublicKey()
					{
						return !empty($this->info['stripe_public_key']);
					}

				function HasStripeWebhooksEndpointSecret()
					{
						return !empty($this->info['stripe_endpoint_secret']);
					}

				function HasStripeSettings()
					{
						return ( $this->HasStripeWebhooksEndpointSecret() && $this->HasStripePublicKey() && $this->HasStripeSecretKey() );
					}

				function HasStripeSecretKey()
					{
						return !empty($this->info['stripe_secret_key']);
					}

				function StripePublicKey()
					{
						return $this->info['stripe_public_key'];
					}

				function StripeWebhooksEndpointSecret()
					{
						return $this->info['stripe_endpoint_secret'];
					}

				function SetStripeSecretKey( $val )
					{
						$this->UpdateValues(Array('stripe_secret_key'=>$val));
					}

				function SetStripePublicKey( $val )
					{
						$this->UpdateValues(Array('stripe_public_key'=>$val));
					}

				function SetStripeWebhooksEndpointSecret( $val )
					{
						$this->UpdateValues(Array('stripe_endpoint_secret'=>$val));
					}

				function isStripeTestMode()
					{
						return strpos($this->StripePublicKey(), '_test_')!==false;
					}

				function isEnabledSaveToPurchasesTestMode()
					{
						return intval($this->info['test_mode_add_to_purchases'])==1;
					}
				function EnableSaveToPurchasesTestMode()
					{
						$this->UpdateValues(Array('test_mode_add_to_purchases'=>1));
					}
				function DisableSaveToPurchasesTestMode()
					{
						$this->UpdateValues(Array('test_mode_add_to_purchases'=>0));
					}

				function HasSubscriptionID()
					{
						return !empty($this->info['id_subscription']);
					}

				function SubscriptionID()
					{
						return intval($this->info['id_subscription']);
					}

				function SetSubscriptionID($id_subscription)
					{
						$this->UpdateValues(Array('id_subscription'=>intval($id_subscription)));
					}

				function SetExpirationTimestamp($expiration)
					{
						$this->UpdateValues(Array('expiration'=>$expiration));
					}

				function SetPricingPlan($val)
					{
						$this->UpdateValues(Array('id_pricing_plan'=>intval($val)));
					}

				function BusinessWizardAvailable()
					{
						return $this->info['business_wizard_available']==1;
					}
				function EnableBusinessWizard()
					{
						$this->UpdateValues(Array('business_wizard_available'=>1));
					}
				function DisableBusinessWizard()
					{
						$this->UpdateValues(Array('business_wizard_available'=>0));
					}

				function SicCodesSearch()
					{
						return intval($this->info['sic_code_search']);
					}

				function SicCodesSearchEnabled()
					{
						return !empty($this->info['sic_code_search']);
					}

				function EnableSicCodesSearch()
					{
						$this->UpdateValues(Array('sic_code_search'=>1));
					}

				function DisableSicCodesSearch()
					{
						$this->UpdateValues(Array('sic_code_search'=>0));
					}

				function SetSicCodesSearch($val)
					{
						$this->UpdateValues(Array('sic_code_search'=> intval($val)));
					}

				function StatesSearch()
					{
						return intval($this->info['state_search']);
					}

				function StatesSearchEnabled()
					{
						return !empty($this->info['state_search']);
					}

				function EnableStatesSearch()
					{
						$this->UpdateValues(Array('state_search'=>1));
					}

				function DisableStatesSearch()
					{
						$this->UpdateValues(Array('state_search'=>0));
					}

				function SetStatesSearch($val)
					{
						$this->UpdateValues(Array('state_search'=>intval($val)));
					}

				function GoogleSearchEnabled()
					{
						return !empty($this->info['google_search']);
					}

				function GoogleSearch()
					{
						return intval($this->info['google_search']);
					}
				function EnableGoogleSearch()
					{
						$this->UpdateValues(Array('google_search'=>1));
					}

				function DisableGoogleSearch()
					{
						$this->UpdateValues(Array('google_search'=>0));
					}

				function SetGoogleSearch($val)
					{
						$this->UpdateValues(Array('google_search'=>intval($val)));
					}

				function BuiltWithSearch()
					{
						return intval($this->info['builtwith_search']);
					}

				function BuiltWithSearchEnabled()
					{
						return !empty($this->info['builtwith_search']);
					}

				function EnableBuiltWithSearch()
					{
						$this->UpdateValues(Array('builtwith_search'=>1));
					}

				function DisableBuiltWithSearch()
					{
						$this->UpdateValues(Array('builtwith_search'=>0));
					}

				function SetBuiltWithSearch($val)
					{
						$this->UpdateValues(Array('builtwith_search'=>intval($val)));
					}

			}
		define("tcompany_DEFINED", 1);
	}

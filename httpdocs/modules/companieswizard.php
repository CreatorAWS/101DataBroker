<?php

	sm_add_body_class('wizard companywizard');
	if ($userinfo['level']==3)
		{

			/** @var $company TCompany */

			sm_default_action('businessinfo');
			$m['wizardsteps']=['businessinfo', 'businesssettings', 'twiliosettings', 'twiliophone', 'twilioareacode', 'usercontact', 'summary'];

			if(!empty($_getvars['company_id']))
				{
					$company = new TCompany($_getvars['company_id']);
					if(!$company->Exists())
						exit('Access Denied');
				}
			else
				{
					$company = TCompany::CurrentCompany();
				}

			if (sm_action('setbusinessinfo'))
				{
					if (empty($_postvars['company_name']))
						$error_message = 'Wrong Company Name';
					elseif (!empty($_postvars['email']) && !is_email($_postvars['email']))
						$error_message = 'Wrong Email Address';
					else
						{
							if (!empty($_getvars['company_id']))
								{
									$company = new TCompany(intval($_getvars['company_id']));
									$company->SetName($_postvars['company_name']);
									$newid = intval($_getvars['company_id']);
								}
							else
								{
									$company = TCompany::Create();
									$company->SetName($_postvars['company_name']);
									$newid = $company->ID();
								}
							if ( !$company->HasEmailFrom() )
								{
									$need_validation = true;
									$j = 1;
									$generate_email = sm_getnicename(preg_replace("/[^a-zA-Z]/", "", $company->Name()));
									while ($need_validation)
										{
											$cur_email_check = TQuery::ForTable('companies')->Add('email', dbescape($generate_email.'@'.mail_domain()))->Get();
											if (!empty($cur_email_check['email']))
												{
													$generate_email = $generate_email.$j;
													$j++;
												}
											else
												{
													$company->SetEmailFrom($generate_email.'@'.mail_domain());
													$need_validation = false;
												}
										}
								}
							$company->SetAddress($_postvars['address']);
							$company->SetAddress2($_postvars['address2']);
							$company->SetCity($_postvars['city']);
							$company->SetState($_postvars['state']);
							$company->SetZip($_postvars['zip']);
						}
					if (!empty($error_message))
						{
							sm_set_action('businessinfo');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								{
									sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&company_id='.$newid);
								}
							else
								sm_redirect('index.php');
						}
				}

			if (sm_action('businessinfo'))
				{
					$m["module"] = sm_current_module();
					sm_title('Business Information');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}
					$m['states_list']['abb'] = USAStates::AbbreviationsArray();
					$m['states_list']['ab'] = USAStates::NamesArray();
					$m['states_list']['nm'] = USAStates::NamesArray();

					if(!empty($_getvars['company_id']))
						{
							$m['businessinfo']['company_email']=$company->EmailFrom();
							$m['businessinfo']['company_name']=$company->Name();
							$m['businessinfo']['address'] = $company->Address();
							$m['businessinfo']['address2'] = $company->Address2();
							$m['businessinfo']['city'] = $company->City();
							$m['businessinfo']['state'] = $company->State();
							$m['businessinfo']['zip'] = $company->Zip();
						}

					if(!empty($_postvars))
						$m['businessinfo']=$_postvars;

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=setbusinessinfo&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');

				}

			if (sm_action('posttwiliosettings'))
				{
					if ((!empty($_postvars['twilio_AccountSid']) && empty($_postvars['twilio_AuthToken'])) || (empty($_postvars['twilio_AccountSid']) && !empty($_postvars['twilio_AuthToken'])))
						{
							$error_message = 'Fill in Twilio Account Sid and Twilio Auth Token';
						}
					if ((!empty($_postvars['mailjet_api_key']) && empty($_postvars['mailjet_api_secret'])) || (empty($_postvars['mailjet_api_key']) && !empty($_postvars['mailjet_api_secret'])))
						{
							$error_message = 'Fill in MailJet API Key Sid and MailJet API Secret';
						}
					if (empty($error_message))
						{
							$company->SetTwilioAccountSid($_postvars['twilio_AccountSid']);
							$company->SetTwilioAuthToken($_postvars['twilio_AuthToken']);
							$company->SetMailjetApiKey($_postvars['mailjet_api_key']);
							$company->SetMailjetApiSecret($_postvars['mailjet_api_secret']);

							if ($company->HasMailjetApiKey() && $company->HasMailjetApiSecret())
								{
									require sm_cms_rootdir().'ext/vendor/autoload.php';
									$apikey = $company->MailjetApiKey();
									$apisecret = $company->MailjetApiSecret();
									$mj=new \Mailjet\Client($apikey, $apisecret);
									$body=[
										'EventType'=>"open",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=open",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"sent",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=sent",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"Click",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=click",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"Bounce",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=bounce",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"Spam",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=spam",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"Blocked",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=blocked",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
									unset ($body);
									$body=[
										'EventType'=>"Unsub",
										'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=unsub",
										'Version'=>"2"
									];
									$response=$mj->post(\Mailjet\Resources::$Eventcallbackurl, ['body'=>$body]);
								}

						}
					if (!empty($error_message))
						{
							sm_set_action('twiliosettings');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
							else
								sm_redirect('index.php');
						}
				}

			if (sm_action('twiliosettings'))
				{
					$m["module"] = sm_current_module();
					sm_title('Account Information');
					if (!empty($error_message))
						$m['error_message'] = $error_message;

					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}
					$m['titlehint'] = 'Hint';
					$m['hint'] = 'Leave empty for default';

					if(!empty($_getvars['company_id']))
						{
							$m['accountinfo']['twilio_AccountSid']=$company->TwilioAccountSid();
							$m['accountinfo']['twilio_AuthToken'] = $company->TwilioAuthToken();
							$m['accountinfo']['mailjet_api_key'] = $company->MailjetApiKey();
							$m['accountinfo']['mailjet_api_secret'] = $company->MailjetApiSecret();
						}

					if(!empty($_postvars))
						$m['accountinfo']=$_postvars;

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=posttwiliosettings&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['skip_url'] = 'index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
				}

			if (sm_action('settwilionumberlist'))
				{
					if(empty($_postvars['twilio_phone_number']))
						$error_message = 'Select a phone number';

					if(empty($error_message))
						{
							if (empty($company->Cellphone()))
								{
									if ($company->HasTwilioAccountSid() && $company->HasTwilioAuthToken() && $company->HasTwilioTwiMLAppSID())
										{
											$AccountSid = $company->TwilioAccountSid();
											$AuthToken = $company->TwilioAuthToken();
											$TwimlAppSid = $company->TwilioTwiMLAppSID();
										}
									else
										{
											$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
											$AuthToken = sm_settings('twilio_AuthToken');
											$TwimlAppSid = sm_settings('twilio_twiml_app_sid');
										}


									include_once('ext/Twilio/autoload.php');
									$client = new \Twilio\Rest\Client($AccountSid, $AuthToken);

									$firstNumber = $_postvars['twilio_phone_number'];

									if (!empty($firstNumber))
										{
											$twilioNumber = $client->incomingPhoneNumbers
												->create(array(
														"phoneNumber" => $firstNumber,
														//														"voiceUrl" => "https://demo.twilio.com/welcome/voice/",
														"voiceApplicationSid" => $TwimlAppSid,
														"smsUrl" => "https://".main_domain()."/index.php?m=receivesms"
													)
												);
											$company->SetCellphone($firstNumber);
										}

								}
						}

					if(!empty($error_message))
						{
							sm_set_action('gettwilionumberlist');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
							else
								sm_redirect('index.php');
						}

				}
			if (sm_action('gettwilionumberlist'))
				{
					$m["module"] = sm_current_module();
					sm_title('Select Phone Number');

					if (!empty($error_message))
						$m['error_message'] = $error_message;

					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]=='twilioareacode')
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}

					$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
					$AuthToken = sm_settings('twilio_AuthToken');

					include_once('ext/Twilio/autoload.php');
					$client = new \Twilio\Rest\Client($AccountSid, $AuthToken);

					$phonenumberparams = array(
						"smsEnabled" => true,
						"mmsEnabled" => true,
						"voiceEnabled" => true
					);

					$phonenumberparams['areaCode'] = $_postvars['twilio_phone_area'];
					$numbers = $client->availablePhoneNumbers("US")->local->read($phonenumberparams, 20);
					if (count($numbers) > 0)
						{
							$m['twilio_phone_area'] = $_postvars['twilio_phone_area'];

							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=settwilionumberlist&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
							$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');

							for($i=0; $i<count($numbers); $i++)
								{
									$m['businesssettings'][$i]['twilio_phones'] = $numbers[$i]->phoneNumber;
								}
						}
					else
						{
							$error_message = "No numbers found. Try another Area Code";
							sm_set_action('twilioareacode');
						}

				}

			if (sm_action('twilioareacode'))
				{
					$m["module"] = sm_current_module();
					sm_title('Phone Number Area Code');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}


					if($_getvars['action']=='back')
						sm_redirect('index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
					else
						{
							if(!empty($company->Cellphone()))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
						}

					if(!empty($_postvars['twilio_phone_area']))
						$m['businesssettings']['twilio_phone_area']=$_postvars['twilio_phone_area'];

					$m['titlehint'] = 'Hint';
					$m['hint'] = 'Leave empty for default';

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=gettwilionumberlist&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['skip_url'] = 'index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
				}

			if (sm_action('settwiliophone'))
				{

					if (!empty($error_message))
						{
							sm_set_action('twiliophone');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
							else
								sm_redirect('index.php');
						}
				}

			if (sm_action('twiliophone'))
				{
					$m["module"] = sm_current_module();
					sm_title('Twilio Phone Number');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}

					$m['businesssettings']['twilio_phone'] = $company->Cellphone();
					$m['titlehint'] = 'Hint';
					$m['hint'] = 'Leave empty to generate by Twilio';

					if(!empty($_postvars['twilio_phone']))
						$m['businesssettings']['twilio_phone']=$_postvars['twilio_phone'];

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=settwiliophone&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['skip_url'] = 'index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
				}

			if (sm_action('setbusinesssettings'))
				{

					if (!empty($_postvars['cellphone_for_notifications']) && strlen(Cleaner::Phone($_postvars['cellphone_for_notifications']))<10)
						{
							$error_message = 'Wrong Phone Number';
						}
					else
						{
							$company->SetNotificationsToCellphone($_postvars['cellphone_for_notifications']);
						}

					if (!empty($error_message))
						{
							sm_set_action('businesssettings');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
							else
								sm_redirect('index.php');
						}
				}
			if (sm_action('businesssettings'))
				{
					$m["module"] = sm_current_module();
					sm_title('Business Settings');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}

					$m['businesssettings']['cellphone_for_notifications'] = $company->SendNotificationsToCellphone();
					$m['titlehint'] = 'Hint';
					$m['hint'] = 'Leave empty for default';
					$m['hint2'] = 'Phone number to recive SMS notifications about new leads etc <span class="optional-label">(Optional)</span>';

					if(!empty($_postvars['cellphone_for_notifications']))
						$m['businesssettings']['cellphone_for_notifications']=$_postvars['cellphone_for_notifications'];


					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=setbusinesssettings&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['skip_url'] = 'index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
				}


			if (sm_action('setusercontact'))
				{
					sm_extcore();

					$_postvars['email'] = trim($_postvars['email']);
					if(!is_email($_postvars['email']))
						{
							$error_message='Not a valid Email';
						}
					if(empty($_postvars['first_name']) || empty($_postvars['last_name']))
						{
							$error_message='Please fill in all fields';
						}
					if(!empty($_getvars['company_id']))
						{
							if (!empty($_postvars['email'])) {
								$usr1=sm_userinfo($_postvars['email'], 'email');
							}
						}
					else
						{
							$currentuser = new TEmployee($userinfo['id']);
							if (!empty($_postvars['email']) && $currentuser->Email()!=$_postvars['email'])
								{
									$usr1=sm_userinfo($_postvars['email'], 'email');
								}
						}


					if (!empty ($_postvars['password']) && strlen($_postvars['password']) < 3)
						$error_message='Password length must be more than 3 characters';
					elseif (!empty($usr1['id']) && $usr1['info']['deleted']==0)
						$error_message='User with email "'.$_postvars['email'].'" exists';
					if (empty($error_message))
						{
							if(!empty($_getvars['company_id']))
								{
									$company = new TCompany(intval($_getvars['company_id']));
									$user_id = sm_add_user( $_postvars['email'], $_postvars['password'], $_postvars['email']);
									$employee=new TEmployee($user_id);
									$employee->SetCompanyID($company->ID());
									$employee->SetFirstName($_postvars['first_name']);
									$employee->SetLastName($_postvars['last_name']);
									$employee->SetNotifications('cellphone');
									//--------------------------------------------------
									$subject="Login details for ".$company->Name();
									$message="Hello ".$_postvars['first_name'].",<br><br>This is your login details for ".main_domain()."<br><a href='".main_domain()."'>Link to your dashboard</a><br>Login: ".$employee->Email()."<br>Password: ".$_postvars['password'];
									//--------------------------------------------------
									$employee->SendSystemEmail($subject, $message);
								}
							else
								{
									if (!empty ($_postvars['password']))
										$currentuser->SetPassword($_postvars['password']);
									if (!empty($_postvars['email']))
										$currentuser->SetEmail($_postvars['email']);
									$currentuser->SetCompanyID($company->ID());
									$currentuser->SetFirstName($_postvars['first_name']);
									$currentuser->SetLastName($_postvars['last_name']);
									$currentuser->SetNotifications('cellphone');
								}

						}

					if (!empty($error_message))
						{
							sm_set_action('usercontact');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:''));
							else
								sm_redirect('index.php');
						}

				}

			if (sm_action('usercontact'))
				{
					$m["module"] = sm_current_module();
					sm_title('Business Settings');
					if (!empty($error_message))
						{
							$m['error_message'] = $error_message;
						}
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}
					if(!empty($_getvars['company_id']))
						{
							if(empty($error_message))
								$m['password'] = substr(md5(microtime()), 0, 8);
							$usercount = TQuery::ForTable($tableusersprefix.'users')->AddWhere('id_company', $company->ID())->TotalCount();

							if($usercount>0)
								if($_getvars['action']=='back')
									sm_redirect('index.php?m='.sm_current_module().'&d='.$previous_step.'&company_id='.intval($_getvars['company_id']));
								else
									{
										if(!empty($next_step))
											sm_redirect('index.php?m='.sm_current_module().'&d='.$next_step.'&company_id='.intval($_getvars['company_id']));
										else
											sm_redirect('index.php');
									}
						}
					else
						{
							$currentuser = new TEmployee($userinfo['id']);
							$m['email'] = $currentuser->Email();
							$m['first_name'] = $currentuser->FirstName();
							$m['last_name'] = $currentuser->LastName();

						}

					if( !empty($_postvars) )
						{
							$m['email'] = $_postvars['email'];
							$m['password'] = $_postvars['password'];
							$m['first_name'] = $_postvars['first_name'];
							$m['last_name'] = $_postvars['last_name'];
							$m['primary_role'] = $_postvars['primary_role'];
						}

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=setusercontact&nextstep='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['skip_url'] = 'index.php?m='.sm_current_module().'&d='.$next_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&action=back&d='.$previous_step.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
				}


			if (sm_action('summary'))
				{
					$m["module"] = sm_current_module();
					sm_title('Summary');
					for ($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if ($m['wizardsteps'][$i]==sm_current_action())
								{
									$previous_step=$m['wizardsteps'][$i-1];
									$next_step=$m['wizardsteps'][$i+1];
									$m['active_step']=$i;
								}
						}
					$m['company_name'] = $company->Name();
					$m['company_email'] = $company->EmailFrom();
					$m['address']=$company->Address();
					$m['address2']=$company->Address2();
					$m['city']=$company->City();
					$m['state']=$company->State();
					$m['zip']=$company->Zip();

					$m['twilio_phone'] = $company->Cellphone();
					$m['cellphone_for_notifications'] = $company->SendNotificationsToCellphone();

					$m['customer_label'] = $company->LabelForCustomer();
					$m['customers_label'] = $company->LabelForCustomers();
					if ($company->HasSystemLogoImageURL())
						$m['logo_image']=$company->SystemLogoImageURL();

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=finish'.(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$previous_step.'&action=back&id='.$_getvars['id'].(!empty($_getvars['company_id'])?'&company_id='.$_getvars['company_id']:'');

				}

			if (sm_action('finish'))
				{
					$m["module"] = sm_current_module();
					sm_title('Company added');
					sm_notify('Company '.$company->Name().' has been successfully created');
					if(!empty($_getvars['company_id']))
						sm_redirect('index.php?m=companiesmgmt');
					else
						sm_redirect('index.php');
				}

		}
	else
		sm_redirect('index.php');
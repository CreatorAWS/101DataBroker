<?php
	/** @var $employee TEmployee */
	/*
	Module Name: Users Management
	*/

	use Twilio\Rest\Client;
	use Twilio\Jwt\ClientToken;

	if ($userinfo['level'] > 0)
		{
			sm_default_action('list');

			if (sm_action('setonlinetime'))
				{
					if (System::LoggedIn())
						{
							System::MyAccount()->SetOnlineTime();
							if (System::MyAccount()->isRefreshTwilioAccessTokenNeeded())
								{
									if (System::MyCompany()->HasCellphone() || System::MyAccount()->HasTwilioPhone())
										{
											if (System::MyCompany()->HasTwilioApiSecret() && System::MyCompany()->HasTwilioApiSid() && System::MyCompany()->HasTwilioTwiMLAppSID())
												{
													$AccountSid = System::MyCompany()->TwilioAccountSid();
													$AuthToken = System::MyCompany()->TwilioAuthToken();
													$TwiMLAppSID = System::MyCompany()->TwilioTwiMLAppSID();
												}
											else
												{
													$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
													$AuthToken = sm_settings('twilio_AuthToken');
													$TwiMLAppSID = sm_settings('twilio_twiml_app_sid');
												}

											$capability = new ClientToken($AccountSid, $AuthToken);
											$capability->allowClientOutgoing($TwiMLAppSID);
											$capability->allowClientIncoming(System::MyAccount()->TwilioClientID());
											$token = $capability->generateToken();

											System::MyAccount()->SetTwilioAccessToken($token);
										}
								}
						}
					exit();
				}

			if (sm_action('postdelete'))
				{
					$company=new TCompany(intval($_getvars['company']));
					/** @var $myaccount TEmployee */
					if( $company->ID()!= $myaccount->CompanyID() && !$myaccount->isSuperAdmin())
						exit('Access Denied');

					$q = new TEmployee(intval($_getvars['id']));
					if($q->CompanyID() != $company->ID())
						exit('Access Denied');
					$q->Remove();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('gettwilionumberlist'))
				{
					$m["module"] = sm_current_module();
					sm_title('Select Phone Number');

					$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
					$AuthToken = sm_settings('twilio_AuthToken');

					include_once('ext/Twilio/autoload.php');
					$client = new Client($AccountSid, $AuthToken);

					$phonenumberparams = array(
						"smsEnabled" => true,
						"mmsEnabled" => true,
						"voiceEnabled" => true
					);

					$phonenumberparams['areaCode'] = $_postvars['twilio_phone_area'];
					$numbers = $client->availablePhoneNumbers("US")->local->read($phonenumberparams, 20);

					for( $i = 0; $i < count($numbers); $i++ )
						{
							$m['phone_formatted'][$i] = Formatter::USPhone($numbers[$i]->phoneNumber);
							$m['phone_unformatted'][$i] = $numbers[$i]->phoneNumber;
						}

					$new_user = [
						'message' => 'success',
						'code' => $_postvars['twilio_phone_area'],
						'phonenumbers' => $m['phone_formatted'],
						'phonenumbers_unf' => $m['phone_unformatted'],
					];
					print(json_encode($new_user));
					exit();
				}

			if (sm_action('generatecellphone'))
				{
					sm_title('Select Phone Number');

					sm_use('ui.interface');
					sm_use('ui.form');

					$ui = new TInterface();

					if (!empty($error_message))
						$ui->NotificationError($error_message);


					$f = new TForm('index.php?m='.sm_current_module().'&d=gettwilionumberlist');
					$f->AddClassnameGlobal('addcustomerform');

					if ($_getvars['theonepage'] == '1')
						$f->InsertHTML('<div id="form-messages"></div>');
					$f->AddText('twilio_phone_area', 'Phone Number Area Code');
					$f->SaveButton('Search');
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					if ($_getvars['theonepage'] == '1')
						$ui->html('<script src="themes/default/usermgmttwiliophones.js"></script>');

					$ui->Output(true);
				}

			if (sm_action('postadd', 'postedit'))
				{
					$company=new TCompany(intval($_getvars['company']));
					/** @var $myaccount TEmployee */
					if( ($company->ID()!= $myaccount->CompanyID() && !$myaccount->isSuperAdmin()) || !$company->Exists())
						exit('Access Denied');

					use_api('cleaner');
					sm_extcore();
					$error = '';

					if (!empty($_postvars['email']))
						$email_check = TQuery::ForTable('sm_users')->AddWhere('email', $_postvars['email'])->AddWhere('deleted', '0')->Get();
					if (!empty($_postvars['twilio_phone']))
						$twilio_check = TQuery::ForTable('sm_users')->AddWhere('twilio_number', Cleaner::Phone($_postvars['twilio_phone']))->AddWhere('deleted', '0')->Get();
					if (empty($_postvars['first_name']))
						$error = 'Fill required fields';
					elseif (empty($_postvars['last_name']))
						$error = 'Fill required fields';
					elseif (!empty($_postvars['cellphone']) && strlen(Cleaner::Phone($_postvars['cellphone']))<10)
						$error = 'Wrong Phone Number';
					elseif (!empty($_postvars['twilio_phone']) && strlen(Cleaner::Phone($_postvars['twilio_phone']))<10)
						$error = 'Wrong Twilio Phone Number';
//					elseif (empty($_postvars['email']))
//						$error = 'Fill required fields';
					elseif (!empty($_postvars['email']) && !is_email($_postvars['email']))
						$error = 'Wrong Email Address';
					elseif ( sm_action('postadd') && empty($_postvars['password']))
						$error = 'Fill required fields';
					else
						{
							if ( sm_action('postadd') && !empty($twilio_check) )
								$error = 'This twilio number already exists';
							else
								{
									$cur_twilio_phone_check = TQuery::ForTable('sm_users')->Add('id_user', intval($_getvars['id']))->Get();
									if (!empty($twilio_check) && $cur_twilio_phone_check['twilio_number'] != $_postvars['twilio_phone'])
										$error = 'This twilio number already exists';
								}

							if( sm_action('postadd') && !empty($email_check) )
								$error='This email already exists';
							else
								{
									$cur_email_check = TQuery::ForTable('sm_users')->Add('id_user', intval($_getvars['id']))->Get();
									if (!empty($email_check) && $cur_email_check['email'] != $_postvars['email'])
										$error = 'This email already exists';
								}
						}
					unset($e);
					if (empty($error))
						{
							if (sm_action('postadd'))
								{
									if (empty($_postvars['login']))
										$_postvars['login'] = dbescape($_postvars['email']);
									if (empty($_postvars['password']))
										$_postvars['password'] = md5(rand(1111, 9999).microtime());
									$user_id = sm_add_user($_postvars['login'], $_postvars['password'], $_postvars['email']);
									$e = new TEmployee($user_id);
									$e->SetCompanyID($company->ID());
								}
							else
								{
									$e = new TEmployee(intval($_getvars['id']));
									if($e->CompanyID() != $company->ID())
										exit('Access Denied');
									$e->SetEmail($_postvars['email']);
									if (!empty($_postvars['password']))
										$e->SetPassword($_postvars['password']);
								}
							if ( $_postvars['twilio_phone_generated'] == 1 )
								{
									if (TCompany::CurrentCompany()->HasTwilioAccountSid() && TCompany::CurrentCompany()->HasTwilioAuthToken() && TCompany::CurrentCompany()->HasTwilioTwiMLAppSID())
										{
											$AccountSid = TCompany::CurrentCompany()->TwilioAccountSid();
											$AuthToken = TCompany::CurrentCompany()->TwilioAuthToken();
											$TwimlAppSid = TCompany::CurrentCompany()->TwilioTwiMLAppSID();
										}
									else
										{
											$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
											$AuthToken = sm_settings('twilio_AuthToken');
											$TwimlAppSid = sm_settings('twilio_twiml_app_sid');
										}

									include_once('ext/Twilio/autoload.php');
									$client = new Client($AccountSid, $AuthToken);

									$firstNumber = $_postvars['twilio_phone'];

									if (!empty($firstNumber))
										{
											$twilioNumber = $client->incomingPhoneNumbers
												->create(array(
														"phoneNumber" => $firstNumber,
														"voiceApplicationSid" => $TwimlAppSid,
														"smsUrl" => "https://".main_domain()."/index.php?m=receivesms"
													)
												);
											$e->SetTwilioSid($twilioNumber->sid);
											$e->SetTwilioPhone(Cleaner::USPhone($firstNumber));
										}
								}
							else
								$e->SetTwilioPhone(Cleaner::USPhone($_postvars['twilio_phone']));

							$e->SetFirstName($_postvars['first_name']);
							$e->SetLastName($_postvars['last_name']);
							$e->SetPrimaryRoleTag($_postvars['primary_role']);
							$e->SetEmail($_postvars['email']);
							$e->SetCellphone($_postvars['cellphone']);
							$e->SetNotifications(
								$_postvars['new_msg_from_member_notif']
							);

							$tags = new TTagList();
							$tags->Load();

							if ( sm_action('postedit'))
								{
									$e->UnsetAllUserTags();
								}

							for( $i = 0; $i < count($_postvars['tags_selected']); $i++ )
								{
									$tag_selected = new TTag(str_replace('tag_', '', $_postvars['tags_selected'][$i]));

									if(is_object($tag_selected) && $tag_selected->Exists())
										{
											$e->SetTagID($tag_selected->ID());
										}
									else
										{
											$newtag = TTag::Create(TCompany::CurrentCompany(), $_postvars['tags_selected'][$i]);
											$newtag->SetAddedBy($myaccount->ID());
											$e->SetTagID($newtag->ID());
										}
									unset($tag_selected);
								}
							if(sm_action('postadd'))
								{
									$subject="Login details for ".$company->Name();
									$message="Hello ".$_postvars['first_name'].",<br><br>This is your login details for ".$company->Name()."<br><a href='".main_domain()."'>Link to your dashboard</a><br>Login: ".$e->Email()."<br>Password: ".$_postvars['password'];
									if(!empty($company->EmailFrom()))
										$e->SendEmail($subject, $message);
								}

							sm_redirect($_getvars['returnto']);
						}
					else
						{
							sm_set_action(Array('postadd' => 'add', 'postedit' => 'edit'));
						}
				}

			if (sm_action('add', 'edit'))
				{
					
					$company=new TCompany(intval($_getvars['company']));
					/** @var $myaccount TEmployee */
					if( $company->ID()!= $myaccount->CompanyID() && !$myaccount->isSuperAdmin())
						exit('Access Denied');

					add_path_home();
					add_path('Users Management', 'index.php?m='.sm_current_module().'&d=list&id='.$company->ID());
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('edit'))
						{
							sm_title($lang['common']['edit']);
							$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&company='.$company->ID().'&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&company='.$company->ID().'&returnto='.urlencode($_getvars['returnto']));
						}
					$f->AddClassnameGlobal('usermgmt_form');
					$f->AddText('first_name', 'First Name', true);
					$f->AddText('last_name', 'Last Name', true);
					if (sm_action('edit'))
						$f->AddPassword('password', 'Password');
					else
						$f->AddPassword('password', 'Password', true);
					$f->AddText('email', 'Email');
					$f->AddText('cellphone', 'Cellphone');
					if (sm_action('edit'))
						{
							$employee = new TEmployee(intval($_getvars['id']));

							if($employee->CompanyID() != $company->ID())
								exit('Access Denied');

							$data['twilio_phone'] = $employee->TwilioPhone();

							if (!empty($_postvars))
								$data['twilio_phone'] = $_postvars['twilio_phone'];
						}
					$f->InsertTPL('usermgmt_phone.tpl', $data);
					$f->AddHidden('twilio_phone_generated', 0);
					unset($data);

					$f->AddText('primary_role', 'Primary Role');
					$f->AddSelectVL('new_msg_from_member_notif', 'New Message From Member Notification', Array('no', 'email', 'cellphone'), Array('No', 'To email', 'To cellphone'));
					
					
					$pre_selected_tags = [];
					if (sm_action('edit'))
						{
							$employee = new TEmployee(intval($_getvars['id']));
							if($employee->CompanyID() != $company->ID())
								exit('Access Denied');
							$f->SetValue('first_name', $employee->FirstName());
							$f->SetValue('last_name', $employee->LastName());
							$f->SetValue('login', $employee->Login());
							$f->SetValue('email', $employee->Email());
							$f->SetValue('primary_role', $employee->PrimaryRoleTag());
							$f->SetValue('cellphone', $employee->Cellphone());
							$f->SetValue('new_msg_from_member_notif', $employee->NotificationAboutMessageFromMemberTag());
							foreach ( $employee->GetTagIDsArray() as $get_tags)
								{
									if (!empty($get_tags))
										$selected_tags[] = 'tag_'.$get_tags;
								}
							unset($employee);
						}

					$f->LoadValuesArray($_postvars);

					if (!empty($_postvars))
						$selected_tags = $_postvars['tags_selected'];

					$tags = new TTagList();
					$tags->Load();

					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$data['tags'][$i]['title'] = $tags->items[$i]->Name();
							$data['tags'][$i]['value'] = 'tag_'.$tags->items[$i]->ID();

							if ( !empty($selected_tags))
								{
									for( $j = 0; $j < count($selected_tags); $j++ )
										{
											if ( $selected_tags[$j] == $data['tags'][$i]['value'] )
												$data['tags'][$i]['checked'] = '1';
										}
								}
						}

					$n = 0;
					$tagscount = $tags->Count()-1;
					if ( !empty($selected_tags) )
						{
							for($j=0; $j<count($selected_tags); $j++)
								{
									$exist=0;
									for ($i = 0; $i < $tags->Count(); $i++)
										{
											if($data['tags'][$i]['value'] == $selected_tags[$j])
												{
													$exist=1;
													continue;
												}
										}
									if($exist!=1)
										{
											$n++;
											$data['tags'][$tagscount+$n]['title']=$selected_tags[$j];
											$data['tags'][$tagscount+$n]['value']=$selected_tags[$j];
											$data['tags'][$tagscount+$n]['checked']=1;
										}
								}
						}

					$f->AddLabel('tagbar', 'Tags', '');
					$f->InsertTPL('tags_select.tpl', $data, '', 'Tags', 'tagbar');
					unset($data);
							
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('first_name');
				}

			if (sm_action('list'))
				{
					
					$company=new TCompany(intval($_getvars['id']));
					/** @var $myaccount TEmployee */
					if( $company->ID()!= $myaccount->CompanyID() && !$myaccount->isSuperAdmin())
						exit('Access Denied');
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					use_api('formatter');
					add_path_home();
					add_path('Users Management', 'index.php?m='.sm_current_module().'&d=list&id='.$company->ID());
					sm_title('Users Management - '.$company->Name());
					$extendedfilters=false;
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$b = new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m='.sm_current_module().'&d=add&company='.$company->ID().'&returnto='.urlencode(sm_this_url()));
					
					if (!$extendedfilters)
						$b->AddToggle('extsrch', 'Extended Search', 'ext-search');

					$ui->AddButtons($b);

					$tags = new TTagList();
					$tags->OrderByName();
					$tags->Load();
					
					$tagsfilter=Array();
					if(!empty($_getvars['tags_selected']))
						{
							$tags_array = explode(',', $_getvars['tags_selected']);
							$extendedfilters=true;

									for( $j = 0; $j < count($tags_array); $j++ )
										{
											$tag = new TTag($tags_array[$j]);
											if (is_object($tag) && $tag->Exists())
												{
													$tmp = $tag->GetEmployeeIDsArray();
													$tagsfilter = array_merge($tagsfilter, $tmp);
												}
										}
						}
					if(!empty($_getvars['name']))
						{
							$extendedfilters = true;
						}
					$ui->div_open('ext-search', '', $extendedfilters?'':'display:none');
					$ui->h(3, 'Extended Search');
					$f=new TForm('index.php', '', 'get');
					$f->AddHidden('m', 'usersmgmt');
					$f->AddHidden('d', 'list');
					$f->AddHidden('id', $company->ID());

					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$data['tags'][$i]['title'] = $tags->items[$i]->Name();
							$data['tags'][$i]['value'] = $tags->items[$i]->ID();
						}

					if ($tags_array)
						{
							for($j=0; $j<count($tags_array); $j++)
								{
									for ($i = 0; $i < $tags->Count(); $i++)
										{
											if($tags_array[$j]==$data['tags'][$i]['value'])
												{
													if($j==0)
														$data['values_selected'].= $data['tags'][$i]['value'];
													else
														$data['values_selected'].= ','.$data['tags'][$i]['value'];
												}
										}
								}
						}

					$f->AddText('name', 'Name');
					$f->AddLabel('tagbar', 'Tags', '');
					$f->InsertTPL('tags_filter.tpl', $data, '', 'Tags', 'tagbar');
					unset($data);
					$f->LoadValuesArray($_getvars);
					$f->SaveButton('Search');
					$ui->AddForm($f);
					$ui->div_close();

					$t = new TGrid();
					$t->AddCol('first_name', 'First Name');
					$t->AddCol('last_name', 'Last Name');
					//$t->AddCol('login', 'Login');
					$t->AddCol('email', 'Email');
					$t->AddCol('twiliophone', 'Twilio Phone');
					$t->AddCol('primary_role', 'Role');
					$t->AddCol('tags', 'Tags', '10%');
					$t->AddCol('status', 'Status', '5%');
					$t->AddEdit();
					$t->AddDelete();
					$companyusers = new TEmployeeList($company->ID());
					$companyusers->SetFilterNotDeleted();

					if(!empty($_getvars['tags_selected']))
						{
							$tagsfilter=array_values(array_unique($tagsfilter));
							$companyusers->SetFilterIDs($tagsfilter);
							$extendedfilters=true;
						}

					if(!empty($_getvars['name']))
						{
							$companyusers->SetFilterName($_getvars['name']);
						}

					$companyusers->Limit($limit);
					$companyusers->Offset($offset);
					$companyusers->Load();

					for ($i = 0; $i < count($companyusers->items); $i++)
						{
							$t->Label('first_name', $companyusers->items[$i]->FirstName());
							$t->Label('last_name', $companyusers->items[$i]->LastName());
							$t->Label('email', $companyusers->items[$i]->Email());

							if ($companyusers->items[$i]->HasTwilioPhone())
								$t->Label('twiliophone', Formatter::USPhone($companyusers->items[$i]->TwilioPhone()));

							$info=$companyusers->items[$i]->PrimaryRoleTag();
							if ($companyusers->items[$i]->NotificationAboutMessageFromMemberTag()!='no')
								$info.='<span class="label label-info">Message from member notif. - '.$companyusers->items[$i]->NotificationAboutMessageFromMemberTag().'</span>';
							$t->Label('primary_role', $info);
							$t->Label('login', $companyusers->items[$i]->Login());
							$t->Label('status', $companyusers->items[$i]->Status());

							$data['tags']=array();
							for ($j = 0; $j < $tags->Count(); $j++)
								{
									if ($companyusers->items[$i]->HasTagID($tags->items[$j]->ID()))
										{
											$data['tags'][] = Array(
												'title' => $tags->items[$j]->Name(),
												'url' => 'index.php?m='.sm_current_module().'&tags_selected='.$tags->items[$j]->ID().'&id='.$company->ID()
											);
										}
								}
							$tagsinline = '';
							for ($k = 0; $k < count($data['tags']); $k++)
								{
									$tagsinline .= '<a style="margin-left:5px;" class="label '.(empty($data['tags'][$k]['class']) ? 'label-info' : 'label-default').'" href="'.$data['tags'][$k]['url'].'">'.$data['tags'][$k]['title'].'</a>';
								}
							$t->Label('tags', '<span style="padding-left:0px;">'.$tagsinline.'<span>');
							if (!$companyusers->items[$i]->isSuperAdmin() || $myaccount->isSuperAdmin())
								{
									$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&company='.$company->ID().'&id='.$companyusers->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
									$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&company='.$company->ID().'&id='.$companyusers->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
								}

							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($companyusers->TotalCount(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

		}
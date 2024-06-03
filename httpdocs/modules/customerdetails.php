<?php

/*
 Module Name: Customer Details
 Description: Customer Details
 */

	if ($userinfo['level']>0 && !empty($_getvars['id']))
		{
			/** @var $customer TCustomer */

			function replace_tags_campaign($template, $customer)
				{
					$str=str_replace('{FIRST_NAME}', $customer->FirstName(), $template);
					$str=str_replace('{LAST_NAME}', $customer->LastName(), $str);
					$str=str_replace('{CONTACT_NAME}', $customer->FirstName().' '.$customer->LastName(), $str);
					$str=str_replace('{CONTACT_BUSINESS_NAME}', $customer->GetBusinessName(), $str);
					$str=str_replace('{EMAIL}', $customer->Email(), $str);
					$str=str_replace('{CELLPHONE}', $customer->Cellphone(), $str);
					$str=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $str);
					$str=str_replace('{BUSINESS_CELLPHONE}', TCompany::CurrentCompany()->Cellphone(), $str);
					$str=str_replace('{OWNER}', '', $str);
					return $str;
				}

			use_api('tcustomer');
			$customer=new TCustomer(intval($_getvars['id']));
			if ($customer->Exists() && TCompany::CurrentCompany()->ID()==$customer->CompanyID())
				{
					use_api('temployee');
					use_api('ttaglist');
					if (sm_action('info'))
						sm_set_action('newmsg');
					sm_default_action('newmsg');
					sm_add_jsfile('call.js');

					add_path_home();
					if ( !empty($_getvars['search']) )
						add_path('Search Leads', 'index.php?m=searchleads&id='.intval($_getvars['search']));
					elseif ( !empty($_getvars['buildwith_search']) )
						add_path('Search Leads', 'index.php?m=searchtechleads&d=details&id='.intval($_getvars['buildwith_search']));
					else
						add_path(TCompany::CurrentCompany()->LabelForCustomers(), 'index.php?m=customers&d=list');
					add_path_current();

					if (!empty($customer->FirstName()) || !empty($customer->LastName()))
						sm_title($customer->Name());
					else
						sm_title($customer->GetBusinessName());

					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/adminbuttons.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					use_api('smdatetime');
					$panel = new TPanel();
					if ($customer->isSendingMessagesAbilityExpired())
						$panel->NotificationError('Attention! User opted in for SMS notifications more than 365 days ago.');
					if (!$customer->isEnabled())
						$panel->NotificationWarning('Attention! This lead wasn\'t imported to the '.TCompany::CurrentCompany()->LabelForCustomers().' section. <a href="index.php?m='.sm_current_module().'&d=enable&id='.$customer->ID().'&returnto='.urlencode(sm_this_url()).'" style="margin-left: 15px;">Click Here to import</a>');

					$m['customer']['initials'] = $customer->Initials();
					$m['customer']['first_name'] = $customer->FirstName();
					$m['customer']['last_name'] = $customer->LastName();
					$m['customer']['company_name'] = $customer->GetBusinessName();
					if ($customer->HasProfilePhoto())
						{
							$m['customer']['photo']=$customer->ProfilePhotoURL();
							$m['customer']['photo_change_url']='index.php?m=customers&d=setphoto&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
						}
					if ($customer->HasCellphone())
						$m['customer']['cellphone']=Formatter::Phone($customer->Cellphone());

					$phones = $customer->LoadOtherPhonesArray( true);

					foreach ($phones as $phone)
						{
							$tmpdata[] = [
								'phone' => Formatter::Phone($phone['phone']),
								'primaryURL' => 'index.php?m=customerdetails&d=setprimaryphone&id='.$customer->ID().'&id_phone='.$phone['id'].'&returnto='.urlencode(sm_this_url()),
								'deleteURL' => 'index.php?m=customerdetails&d=deletephone&id='.$customer->ID().'&id_phone='.$phone['id'].'&returnto='.urlencode(sm_this_url()),
							];
						}
					unset($phones);

					$m['customer']['hasAdditionalPhones'] = count($tmpdata) > 0;
					$m['customer']['phones'] = $tmpdata;
					unset($tmpdata);

					$m['customer']['email'] = $customer->Email();
					$m['customer']['emailstatus']=$customer->GetEmailStatus();
					$m['customer']['businessname']=$customer->GetBusinessName();

					$emails = $customer->LoadOtherEmailsArray($customer->Email(), true);
					foreach ($emails as $email)
						{
							$tmpdata[] = [
								'email' => $email['email'],
								'primaryURL' => 'index.php?m=customerdetails&d=setprimaryemail&id='.$customer->ID().'&id_email='.$email['id'].'&returnto='.urlencode(sm_this_url()),
								'deleteURL' => 'index.php?m=customerdetails&d=deleteemail&id='.$customer->ID().'&id_email='.$email['id'].'&returnto='.urlencode(sm_this_url()),
							];
						}
					unset($emails);
					$m['customer']['hasAdditionalEmails'] = count($tmpdata) > 0;
					$m['customer']['emails'] = $tmpdata;
					unset($tmpdata);

					$m['tabs']['profile'] = sm_this_url(['d' => 'profile']);
					$m['tabs']['sms'] = sm_this_url(['d' => 'newmsg']);
					$m['tabs']['email'] = sm_this_url(['d' => 'newemail']);
					$m['tabs']['appointments'] = sm_this_url(['d' => 'appointments']);
					$m['tabs']['campaigns'] = sm_this_url(['d' => 'campaigns']);
					$m['tabs']['conversation'] = sm_this_url(['d' => 'conversation']);
					$m['tabs']['messages'] = sm_this_url(['d' => 'allmessages']);
					$m['tabs']['marketingmessages'] = sm_this_url(['d' => 'marketingmessages']);
					$m['tabs']['tags'] = sm_this_url(['d' => 'tags']);
					$m['tabs']['notes'] = sm_this_url(['d' => 'notes']);
					$m['tabs']['calls'] = sm_this_url(['d' => 'call']);

					$fieldslist_en = new TFieldsList();
					$fieldslist_en->SetFilterCompany(TCompany::CurrentCompany());
					$fieldslist_en->ExcludeCtg(0);
					$fieldslist_en->SetFilterEnabled();
					$fieldslist_en->Load();

					for ($i = 0; $i < $fieldslist_en->Count(); $i++)
						{
							$field = $fieldslist_en->items[$i];
							$m['customer']['customfields'][$i]['title'] = $field->Field();
							$m['customer']['customfields'][$i]['value'] = $customer->GetMetaData('customfield_'.$field->ID());
						}

					$fieldslist_en = new TFieldsList();
					$fieldslist_en->SetFilterCompany(TCompany::CurrentCompany());
					$fieldslist_en->SetFilterCategory(0);
					$fieldslist_en->SetFilterEnabled();
					$fieldslist_en->Load();

					for ($i = 0; $i < $fieldslist_en->Count(); $i++)
						{
							$field = $fieldslist_en->items[$i];
							$m['customer']['stafffields'][$i]['title'] = $field->Field();
							$m['customer']['stafffields'][$i]['value'] = TEmployee::withID($customer->GetMetaData('customfield_'.$field->ID()))->Name();
						}

					$m['customer']['address'] = $customer->AddressFormatted();
					$m['customer']['note'] = $customer->Note(true);
					$m['customer']['has_social_urls'] = $customer->HasSocialURLS();
					$m['customer']['returnto'] = urlencode(sm_this_url());
					$social_media = '';

					if ($customer->HasFacebookURL())
						$social_media .= '<a href="'.$customer->FacebookURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>';
					if ($customer->HasTwitterURL())
						$social_media .= '<a href="'.$customer->TwitterURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>';
					if ($customer->HasInstagramURL())
						$social_media .= '<a href="'.$customer->InstagramURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>';
					if ($customer->HasLinkedInURL())
						$social_media .= '<a href="'.$customer->LinkedInURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>';
					if ($customer->HasWebsite())
						$social_media .= '<a href="https://'.$customer->Website().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" fill="none" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 01-5.276 3.67m0 0a9 9 0 01-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25" /></svg></a>';

					$m['customer']['social_media'] = $social_media;


					$m['customer']['buttons'][]=Array(
						'title'=>'Edit Profile',
						'url'=>'index.php?m=customers&d=edit&id='.$customer->ID().'&returnto='.urlencode(sm_this_url()),
						'class'=>'btn-danger edit-profile-user'
					);
					if ($customer->HasCellphone())
						{
							$sendmessage_url = "index.php?m=contactcustomer&d=call&id=".$customer->ID()."&theonepage=1&returnto=".urlencode(sm_this_url());

							$m['customer']['buttons'][]=Array(
								'title' => Formatter::USPhone($customer->Cellphone()),
								'url' => 'index.php?m=appointments&d=add&customer='.$customer->ID().'&returnto='.urlencode(sm_this_url()),
								'class' => 'btn-primary call_customer_button',
								'onclick' => "startupClient(); make_a_call('".$sendmessage_url."', ".$customer->ID().")"
							);

							$panel->div_open('messagemodal', 'modal fade');
							$panel->div_open('', 'modal-dialog');
							$panel->div_open('', 'modal-content');
							$panel->div_open('messagemodal_content');
							$panel->div_close();
							$panel->div_close();
							$panel->div_close();
							$panel->div_close();
						}
						

					$m['customer']['buttons'][]=Array(
						'title'=>'Schedule an Appointment',
						'url'=>'index.php?m=appointments&d=add&customer='.$customer->ID().'&returnto='.urlencode(sm_this_url()),
						'class'=>'btn-primary schedule-appoint'
					);					

					if ($customer->TagsCount()>0)
						{
							$tags=new TTagList();
							$tags->SetFilterIDs($customer->GetTagIDsArray());
							$tags->OrderByName();
							$tags->Load();
							for ($i = 0; $i < $tags->Count(); $i++)
								$m['customer']['tags'][]=Array(
									'title'=>$tags->items[$i]->Name(),
									'url'=>'index.php?m=customers&d=list&tags_selected='.$tags->items[$i]->ID()
								);
						}
					$m['customer']['tagscount']=$customer->TagsCount();
					unset($tags);

					if (sm_action('setprimaryemail'))
						{
							if (empty($_getvars['id_email']))
								exit('Access Denied');

							$email = new TEmails($_getvars['id_email']);
							if (!$email->Exists() || $email->CustomerID() != $customer->ID())
								exit('Access Denied');

							$tmp_email = $customer->Email();

							if (!empty($email->Email()))
								$customer->SetEmail($email->Email());

							if (!empty($tmp_email))
								$email->SetEmail($tmp_email);

							sm_redirect($_getvars['returnto']);
						}

					if (sm_action('setprimaryphone'))
						{
							if (empty($_getvars['id_phone']))
								exit('Access Denied');

							$phone = new TPhone($_getvars['id_phone']);
							if (!$phone->Exists() || $phone->CustomerID() != $customer->ID() || $phone->PhoneType() != TPhone::OTHER_PHONE)
								exit('Access Denied');

							$tmp = $customer->Cellphone();
							if (!empty($tmp))
								{
									$phones = new TPhoneList();
									$phones->SetFilterCustomer($customer);
									$phones->SetFilterPhoneType();
									$phones->Load();

									for ($i = 0; $i < $phones->Count(); $i++)
										{
											$phones->Item($i)->SetPhoneType(TPhone::OTHER_PHONE);
										}
								}
							if (!empty($phone->Phone()))
								{
									$customer->SetCellPhone($phone->Phone());
									$phone->SetPhoneType(TPhone::MAIN_PHONE);
								}

							sm_redirect($_getvars['returnto']);
						}

					if (sm_action('deleteemail'))
						{
							if (empty($_getvars['id_email']))
								exit('Access Denied');

							$email = new TEmails($_getvars['id_email']);
							if (!$email->Exists() || $email->CustomerID() != $customer->ID())
								exit('Access Denied');

							$email->Remove();
							sm_redirect($_getvars['returnto']);
						}

					if (sm_action('deletephone'))
						{
							if (empty($_getvars['id_phone']))
								exit('Access Denied');

							$phone = new TPhone($_getvars['id_phone']);
							if (!$phone->Exists() || $phone->CustomerID() != $customer->ID())
								exit('Access Denied');

							$phone->Remove();
							sm_redirect($_getvars['returnto']);
						}

					if (sm_action('newmsg'))
						{
							if ($customer->isSendingMessagesUndefined() || $customer->isSendingMessagesEnabled())
								{
									if ($customer->isSendingMessagesUndefined())
										$panel->NotificationWarning(TCompany::CurrentCompany()->LabelForCustomer().' will be asked to accept or reject your message');
									$f = new TForm('index.php?m=customers&d=sendmessage&id='.$customer->ID().'&returnto='.urlencode(sm_this_url()));


									$templatectgs = new TTemplateCategoriesList();
									$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
									$templatectgs->Load();
									$templates_ctg = array();
									$j = 0;

									for ($i=0; $i<$templatectgs->Count(); $i++)
										{
											$templatectg = $templatectgs->items[$i];

											$messages = new TMessageTemplateList();
											$messages->SetFilterCompany(TCompany::CurrentCompany());
											$messages->SetFilterCategory($templatectg->ID());
											$messages->Load();

											if ( $templatectg->EmailTemplatesCount()>0 )
												{
													$templates_ctg[$j]['id'] = $templatectg->ID();
													$templates_ctg[$j]['title'] = $templatectg->Title();
													$templates[$j]['ctg_id'] = $templatectg->ID();
													$templates[$j]['message_ids'] = $messages->ExtractIDsArray();
													$templates[$j]['message_titles'] = $messages->ExtractTitlesArray();
													$j++;
													unset($messages);
												}
										}


									$data['categories'] = $templates_ctg;
									$data['templates'] = json_encode($templates);

									$f->AddLabel('ctg_select', 'Message Template', '');
									$f->InsertTPL('templates_select_sms.tpl', $data, '', 'Email Template', 'ctg_select');

									$f->InsertHTML('<div>OR</div>', 'a', 'divider');
									$f->AddTextarea('text', 'Custom Message<sup class="adminform-required">*</sup>')
										->SetFocus();

									$q = new TQuery('company_assets');
									$q->Add('id_company', intval(TCompany::CurrentCompany()->ID()));
									$q->OrderBy('comment, filename');
									$q->Select();
									if ($q->Count() > 0)
										{
											$v = Array('');
											$l = Array('- No attachment -');
											for ($i = 0; $i < $q->Count(); $i++)
												{
													$asset = new TAsset($q->items[$i]);
													if ($asset->isEligibleForMMS())
														{
															$v[] = $asset->ID();
															if ($asset->HasComment())
																$l[] = $asset->FileName().' - '.$asset->Comment();
															else
																$l[] = $asset->FileName();
														}
													unset($asset);
												}
											$f->AddSelectVL('attachment', 'Media Attachment', $v, $l);
										}
									$f->AddText('schedule', 'Schedule');
									$f->Calendar();
									$f->SetFieldEndText('schedule', ' at ');
									$f->HideEncloser();
									$f->AddSelect('hr', 'Hr', Array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12));
									$f->SetFieldClass('hr', 'hrs');
									$f->HideDefinition();
									$f->HideEncloser();
									$tmp = range(0, 59);
									for ($i = 0; $i < count($tmp); $i++)
										{
											if ($tmp[$i] < 10)
												$tmp[$i] = '0'.$tmp[$i];
										}
									$f->AddSelectVL('min', 'Min', range(0, 59), $tmp);
									$f->SetFieldClass('min', 'minutes');
									$f->HideDefinition();
									$f->HideEncloser();
									$f->AddSelectVL('ampm', 'ampm', Array('am', 'pm'), Array('AM', 'PM'));
									$f->SetFieldClass('ampm', 'ampm');
									$f->HideDefinition();
									$f->SaveButton('Send Message');
									$f->SetValue('schedule', SMDateTime::USDateFormat(SMDateTime::Now()));
									$f->SetValue('hr', SMDateTime::Hour12(SMDateTime::Now()));
									$f->SetValue('min', SMDateTime::Min(SMDateTime::Now()));
									$f->SetValue('ampm', SMDateTime::AMPMLowerCased(SMDateTime::Now()));
									$f->LoadValuesArray($_postvars);
									$panel->style('#schedule {width:120px;display:inline;}');
									$panel->style('#hr, #min, #ampm {width:90px;display:inline;}');
									$panel->AddForm($f);
								}
							elseif ($customer->isSendingMessagesAbilityExpired())
								{
									$panel->NotificationError(TCompany::CurrentCompany()->LabelForCustomer().' allowed to send the messages more than 365 days ago.');
								}
							elseif ($customer->isSendingMessagesRejected())
								{
									$panel->NotificationError(TCompany::CurrentCompany()->LabelForCustomer().' rejected to receive the messages.');
								}
							elseif ($customer->isSendingMessagesPending())
								{
									$panel->NotificationInfo('Pending permit to send the messages.');
								}
							elseif ($customer->isSendingMessagesNoResponse())
								{
									$panel->NotificationError('No response from '.strtolower(TCompany::CurrentCompany()->LabelForCustomer()).' to permit to send the messages. ');
								}
						}

					if (sm_action('sendemail'))
						{
							$error_message = '';
							$customer=new TCustomer(intval($_getvars['id']));
							if ($customer->Exists())
								{
									if ( (empty($_postvars['subject']) || empty($_postvars['text'])) && empty($_postvars['email_template']))
										{
											$error_message = 'Fill required fields';
										}
									else
										{
											$sendtime=0;
											$tmp=SMDateTime::TimestampFromUSDateAndTime($_postvars['schedule'], $_postvars['hr'].':'.$_postvars['min'].' '.$_postvars['ampm']);
											if ($tmp>SMDateTime::Now())
												$sendtime=$tmp;

											if(!empty($_postvars['email_template']))
												{
													$email = new TEmailTemplate(intval($_postvars['email_template']));
													if ($email->Exists() && !empty($email->Subject()) && !empty($email->Message()))
														{
															$_postvars['subject'] = $email->Subject();
															$_postvars['text'] = $email->Message();
														}
													else
														{
															$error_message = 'Template Error';
														}
												}
											if (empty($error_message))
												{
													$customer->SendEmailFromCompany(replace_tags_campaign($_postvars['subject'], $customer), replace_tags_campaign( $_postvars['text'], $customer ), $sendtime, true, System::MyAccount());
													$customer->SendEmailAction();
													sm_notify('Message sent');
												}
										}
									if (!empty($error_message))
										sm_set_action('newemail');
									else
										sm_redirect($_getvars['returnto']);
								}
						}


					if (sm_action('newemail'))
						{
							if(!empty($error_message))
								$panel->NotificationError($error_message);
							if ($customer->HasEmail())
								{
									$f = new TForm('index.php?m='.sm_current_module().'&d=sendemail&id=' . $customer->ID() . '&returnto=' . urlencode('index.php?m='.sm_current_module().'&d=newemail&id='.$customer->ID()));

									$templatectgs = new TTemplateCategoriesList();
									$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
									$templatectgs->Load();

									$templates_ctg = array();
									$j = 0;
									for ($i=0; $i<$templatectgs->Count(); $i++)
										{
											$templatectg = $templatectgs->items[$i];

											$emails = new TEmailTemplateList();
											$emails->SetFilterCompany(TCompany::CurrentCompany());
											$emails->SetFilterCategory($templatectg->ID());
											$emails->Load();

											if ( $templatectg->EmailTemplatesCount()>0 )
												{
													$templates_ctg[$j]['id'] = $templatectg->ID();
													$templates_ctg[$j]['title'] = $templatectg->Title();
													$templates[$j]['ctg_id'] = $templatectg->ID();
													$templates[$j]['email_ids'] = $emails->ExtractIDsArray();
													$templates[$j]['email_titles'] = $emails->ExtractTitlesArray();
													$j++;
													unset($emails);
												}
										}

									$data['categories'] = $templates_ctg;
									$data['templates'] = json_encode($templates);

									$f->AddLabel('ctg_select', 'Email Template', '');
									$f->InsertTPL('templates_select.tpl', $data, '', 'Email Template', 'ctg_select');

									$f->InsertHTML('<div class="field-caption-color">OR</div>', 'a', 'divider');
									$data['getimageslisturl'] = 'index.php?m=settings&d=getimageslistajax&theonepage=1';
									$f->InsertTPL('sendemailform.tpl', $data);
//									$f->AddText('subject', 'Subject', true);
//									$f->AddEditor('text', 'Custom Message<sup class="adminform-required">*</sup>')->SetFocus();
									$f->AddSeparator('schedules','Schedule');
									$f->AddText('schedule', 'Date');
									$f->Calendar();
									// $f->SetFieldEndText('schedule', ' at ');
									$f->SetFieldEndText('schedule', ' at ');
									$f->HideEncloser();
									$f->AddSelect('hr', 'Hr', Array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12));
									$f->SetFieldClass('hr', 'hrs');
									$f->HideDefinition();
									$f->HideEncloser();
									$tmp = range(0, 59);
									for ($i = 0; $i < count($tmp); $i++)
										{
											if ($tmp[$i] < 10) $tmp[$i] = '0' . $tmp[$i];
										}
									$f->AddSelectVL('min', 'Min', range(0, 59), $tmp);
									$f->SetFieldClass('min', 'minutes');
									$f->HideDefinition();
									$f->HideEncloser();
									$f->AddSelectVL('ampm', 'ampm', Array('am', 'pm'), Array('AM', 'PM'));
									$f->SetFieldClass('ampm', 'ampm');
									$f->HideDefinition();
									$f->SaveButton('Send Message');
									$f->SetValue('schedule', SMDateTime::USDateFormat(SMDateTime::Now()));
									$f->SetValue('hr', SMDateTime::Hour12(SMDateTime::Now()));
									$f->SetValue('min', SMDateTime::Min(SMDateTime::Now()));
									$f->SetValue('ampm', SMDateTime::AMPMLowerCased(SMDateTime::Now()));
									$f->LoadValuesArray($_postvars);
									if (!empty($_postvars))
										{
											$m['email']['subject'] = $_postvars['subject'];
											$m['email']['text'] = $_postvars['text'];
										}

									$panel->style('#schedule {width:120px;display:inline;}');
									$panel->style('#hr, #min, #ampm {width:90px;display:inline;}');
									$panel->AddForm($f);
								}
							else
								$panel->NotificationWarning('This '.TCompany::CurrentCompany()->LabelForCustomer().' doesn\'t have email address');

						}
					if (sm_action('appointments'))
						{
							sm_add_body_class('conversation-customer campaign-section');
							sm_use('ui.grid');
							$t = new TGrid();
							$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20 no-shadows');
							$t->AddCol('date', 'Date', '20%');
							$t->AddCol('note', 'Note', '70%');
							$t->AddCol('cancel', '', '10%');
						//	$t->AddEdit();
							$appointments=new TAppointmentList();
							$appointments->SetFilterCustomer($customer);
							$appointments->OrderByID(false);
							$appointments->Load();
							for ($i = 0; $i < $appointments->Count(); $i++)
								{
									$t->Label('date', '<p class="compaign-flex">Date</p>'.SMDateTime::USDateTimeFormat($appointments->items[$i]->ScheduledTimestamp()));
									$t->Label('note', '<p class="compaign-flex">Notes</p><span class="appoint-notes">'.$appointments->items[$i]->Note().'</span');
									// if ($appointments->items[$i]->ScheduledTimestamp()>SMDateTime::Now())
									// 	{
											$t->Label('cancel', 'Cancel');
											$t->ColumnAddClass('cancel','appoint-cancel');
											$t->URL('cancel', 'index.php?m=appointments&d=postdelete&id='.$appointments->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
											$t->CustomMessageBox('cancel', 'Are you sure?');
										// }
									$t->URL('edit', 'index.php?m=appointments&d=edit&customer='.$customer->ID().'&id='.$appointments->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
									$t->NewRow();
								}
							$panel->Add($t);
						}

					if (sm_action('postselectcampaign'))
						{
							$contact = new TCampaignItem(intval($_getvars['contact']));
							$campaign = new TCampaign(intval($_getvars['campaign']));
							$new_campaign = new TCampaign(intval($_postvars['campaign']));

							$customer->StopCampaignAction($campaign->ID());
							$customer->StartCampaignAction($new_campaign->ID());

							if (!$campaign->Exists() || !$contact->Exists() || !$new_campaign->Exists())
								exit('Access Denied!');

							$new_contact = TCampaignItem::Create(TCompany::CurrentCompany(), $new_campaign);
							$new_contact->SetFirstName($contact->FirstName());
							$new_contact->SetLastName($contact->LastName());
							$new_contact->SetCompany($contact->Company());
							if (!empty($customer->Email()))
								$new_contact->SetEmail($customer->Email());
							else
								$new_contact->SetEmail($contact->Email());

							if (!empty($customer->Cellphone()))
								$new_contact->SetPhone($customer->Cellphone());
							else
								$new_contact->SetPhone($contact->Phone());

							$new_contact->SetTags($contact->Tags());

							$schedulelist = new TCampaignScheduleList();
							$schedulelist->SetFilterCompany(TCompany::CurrentCompany());
							$schedulelist->SetFilterCampaign($campaign->ID());
							$schedulelist->SetFilterCustomer($contact->ID());
							$schedulelist->Load();

							for ( $i=0; $i<$schedulelist->Count(); $i++ )
								{
									$schedulelist->items[$i]->Remove();
								}

							$sequencelist= new TCampaignSequenceList();
							$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
							$sequencelist->SetFilterCampaign($campaign->ID());
							$sequencelist->SetFilterContacts($contact->ID());
							$sequencelist->Load();

							for ( $i=0; $i<$sequencelist->Count(); $i++ )
								{
									$sequencelist->items[$i]->Remove();
								}

							$contact->Remove();

							$sequencelist= new TCampaignSequenceList();
							$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
							$sequencelist->SetFilterCampaign($new_campaign->ID());
							$sequencelist->Load();

							for ( $i=0; $i<$sequencelist->Count(); $i++ )
								{
									$schedule = TCampaignSchedule::Create(TCompany::CurrentCompany(), $new_campaign->ID());
									$schedule->SetCustomerID($new_contact->ID());
									$schedule->SetSequenceID($sequencelist->items[$i]->ID());
									$schedule->SetScheduledTimestamp(time() + $sequencelist->items[$i]->ScheduledTimestamp());
									$schedule->SetStatus('scheduled');
								}

							$new_contact->SetStatus('pending1');
							$new_contact->SetNextActionTimestamp(time());
							$new_contact->SetPartner($contact->PartnerID());

							sm_redirect('index.php?m='.sm_current_module().'&d=campaigns&id='.$customer->ID());

						}

					if (sm_action('stopcampaign'))
						{
							$contact = new TCampaignItem(intval($_getvars['contact']));
							$contact->SetStatus('Removed');
							$contact->SetNextActionTimestamp(0);
							if ($contact->Exists())
								{
									$customer = new TCustomer($contact->PartnerID());
									if ( $customer->Exists() && $customer->CompanyID() == TCompany::CurrentCompany()->ID() )
										{
											$customer->StopCampaignAction($contact->CampaignID());
										}

									$schedulelist = new TCampaignScheduleList();
									$schedulelist->SetFilterCompany($contact->CompanyID());
									$schedulelist->SetFilterCustomer($contact->ID());
									$schedulelist->SetFilterCampaign($contact->CampaignID());
									$schedulelist->SetFilterStatus('scheduled');
									$schedulelist->Load();

									for ($j=0; $j<$schedulelist->Count(); $j++)
										{
											$schedulelist->items[$j]->SetStatus('Removed');
										}
								}
							sm_redirect('index.php?m='.sm_current_module().'&d=campaigns&id='.$customer->ID());

						}

					if (sm_action('changecampaign'))
						{
							$m["module"] = sm_current_module();
							sm_use('ui.form');
							$contact = new TCampaignItem(intval($_getvars['contact']));
							$campaign = new TCampaign(intval($_getvars['campaign']));
							if (!$campaign->Exists() || !$contact->Exists())
								exit('Access Denied!');

							sm_title('Select Sequence');

							if (!empty($error_message))
								$m['error_message'] = $error_message;
							$f = new TForm('index.php?m='.sm_current_module().'&d=postselectcampaign&id='.$customer->ID().'&campaign='.$campaign->ID().'&contact='.$contact->ID());

							$campaignitems=new TCampaignItemList();
							$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
							$campaignitems->SetFilterCustomerID($customer);
							$campaignitems->SetFilterPhone($customer->Cellphone());
							$campaignitems->Load();

							$campaigns = new TCampaignList();
							$campaigns->SetFilterCompany(TCompany::CurrentCompany()->ID());
							$campaigns->ExcludeStatusesArray(Array('notfinished'));
							$campaigns->SetFilterExcludeIDs($campaignitems->ExtractCampaignsIDsArray());
							$campaigns->OrderByID(false);
							$campaigns->Load();

							$f->AddSelectVL('campaign', 'Select Campaign', $campaigns->ExtractIDsArray(), $campaigns->ExtractTitlesArray());
							$f->SetValue('campaign', $campaign->ID());

							if (is_array($_postvars))
								$f->LoadValuesArray($_postvars);
							$panel->AddForm($f);
						}

					if (sm_action('campaigns'))
						{
							sm_add_body_class('conversation-customer campaign-section');
							sm_use('ui.grid');
							$t = new TGrid();
							$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20 underline_hrefs no-shadows');
							$t->AddCol('sequence', 'Sequence', '25%');
							$t->AddCol('sequence_status', 'Sequence Progress', '20%');
							$t->AddCol('status', 'Status', '20%');
							$t->AddCol('edit', 'Change Sequence', '20%');
							$t->AddCol('remove', 'Stop Sequence', '15%');
							$campaignitems=new TCampaignItemList();
							$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
							$campaignitems->SetFilterCustomerID($customer);
//							$campaignitems->SetFilterPhone($customer->Cellphone());
							$campaignitems->OrderByID(false);
							$campaignitems->Load();

							for ($i = 0; $i < $campaignitems->Count(); $i++)
								{
									$campaignitem = $campaignitems->items[$i];
									$campaign = new TCampaign($campaignitem->CampaignID());
									if (!$campaign->Exists() || $campaign->Status()=='notfinished')
										continue;

									$t->Label('sequence', '<p class="compaign-flex">Sequence</p><a href="index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID().'">'.$campaign->Title().'</a>');
									$t->Label('sequence_status', '<p class="compaign-flex">Sequence Progress</p>'.$campaignitem->Status());

									$t->Label('edit', '<p class="compaign-flex">Change Sequence</p><a href="index.php?m='.sm_current_module().'&d=changecampaign&id='.$customer->ID().'&campaign='.$campaign->ID().'&contact='.$campaignitem->ID().'">Change Sequence</a>');

									$schedulelist = new TCampaignScheduleList();
									$schedulelist->SetFilterCompany($campaignitem->CompanyID());
									$schedulelist->SetFilterCustomer($campaignitem->ID());
									$schedulelist->SetFilterCampaign($campaignitem->CampaignID());
									$schedulelist->SetFilterStatus('scheduled');

									if($schedulelist->TotalCount()>0)
										{
											$t->Label('status', '<p class="compaign-flex">Status</p>'.$campaign->Status());
											$t->Label('remove', '<p class="compaign-flex">Stop Sequence</p><a href="index.php?m='.sm_current_module().'&d=stopcampaign&id='.$customer->ID().'&campaign='.$campaign->ID().'&contact='.$campaignitem->ID().'">Stop Sequence</a>');
										}
									else
										$t->Label('status', 'Finished');
									$t->NewRow();

									unset($schedulelist);
									unset($scheduled);
									unset($campaign);
								}
							if ($t->RowCount() == 0)
								$t->SingleLineLabel('No sequences yet');
							$panel->Add($t);
						}

					if (sm_action('tags'))
						{
							$b_on = new TButtons();
							$b_on->AddClassnameGlobal('tags-violet-text');
							$b_off = new TButtons();
							$b_off->AddClassnameGlobal('tags-violet-text');
							$q=new TQuery('company_tags');
							$q->Add('id_company', intval(TCompany::CurrentCompany()->ID()));
							$q->OrderBy('tag');
							$q->Select();
							for ($i = 0; $i < $q->Count(); $i++)
								{
									if ($customer->HasTagID($q->items[$i]['id']))
										{
											$b_on->AddButton('b'.$q->items[$i]['id'], $q->items[$i]['tag'].' X', 'index.php?m=customers&d=tag&id='.$customer->ID().'&unset='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
											$b_on->AddClassname('btn-default button-tags-violet');
										}
									else
										{
											$b_off->AddButton('b'.$q->items[$i]['id'], $q->items[$i]['tag'], 'index.php?m=customers&d=tag&id='.$customer->ID().'&set='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
											$b_off->AddClassname('btn-danger button-untags-violet');
										}
								}
							if ($b_on->Count()>0)
								{
									$panel->h(3, 'Assigned Tags', 'assign-text' );
									$panel->Add($b_on);
								}
							if ($b_off->Count()>0)
								{
									$panel->h(3, 'Unassigned Tags', 'assign-text');
									$panel->Add($b_off);
								}
						}
					if (sm_action('conversation'))
						{
							sm_add_body_class('conversation-customer');
							$m['initjs']="var dash_url='';dash_show_conversation(".$customer->ID().");";
							$special['document']['bodyend']="<script type='text/javascript'>dash_show_conversation(".$customer->ID().");</script>";
							$m['show_conversation_send_message']=$customer->isSendingMessagesEnabled();
							sm_add_cssfile('conversation.css');
							sm_add_jsfile('conversation.js');
							if ($customer->HasUnreadConversation())
								$customer->MarkAsConversationRead();
						}
					if (sm_action('allmessages'))
						{
							sm_add_body_class('conversation-customer');
							$special['document']['bodyend']="<script type='text/javascript'>dash_show_conversation_all(".$customer->ID().");</script>";
							sm_add_cssfile('conversation.css');
							sm_add_jsfile('allmessages.js');
						}

					if (sm_action('marketingmessages'))
						{
							sm_add_body_class('conversation-customer');
							$special['document']['bodyend']="<script type='text/javascript'>dash_show_conversation_mm(".$customer->ID().");</script>";
							sm_add_cssfile('marketingmessages.css');
							sm_add_jsfile('marketingmessages.js');
						}
					if (sm_action('notes'))
						{
							sm_add_body_class('notes_section');
							$m['show_conversation_send_message'] = true;
							$m['initjs']="var dash_url='';dash_show_note(".$customer->ID().");";
							$special['document']['bodyend']="<script type='text/javascript'>dash_show_note(".$customer->ID().");</script>";
							sm_add_cssfile('conversation.css');
							sm_add_jsfile('notes.js');
						}

					if (sm_action('call'))
						{
							sm_add_body_class('conversation-customer campaign-section');
							sm_use('ui.interface');
							sm_use('ui.buttons');
							sm_use('ui.grid');
							sm_use('ui.modal');
							sm_use('ui.fa');

							$b = new TButtons();
							$customer->SetMissedCall(0);

							$customercalls = new TCustomerCallList();
							$customercalls->SetFilterCustomer($customer);
							$customercalls->OrderByID(false);
							$customercalls->Load();
							if ($customercalls->Count() > 0)
								{
									$panel->html('<div class="col-md-12 table-responsive" style="margin-top:10px;">');
									$panel->html('<h4>Previous Calls</h4>');
									$panel->html('</div>');
									$t = new TGrid();
									$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20 no-shadows');
									$t->AddCol('time', 'Time');
									$t->AddCol('phone', 'Phone');
									$t->AddCol('employee', 'Staff Member');
									$t->AddCol('duration', 'Duration');
									$t->AddCol('status', 'Status');
									$t->AddCol('listen', 'Listen Recoding');
									for ($i = 0; $i < $customercalls->Count(); $i++)
										{
											$t->Label('time', '<p class="compaign-flex">Time</p>'.Formatter::DateTime($customercalls->items[$i]->Timemade()));
											$t->Label('phone', '<p class="compaign-flex">Phone</p>'.$customercalls->items[$i]->Phone());
											$t->Label('employee', '<p class="compaign-flex">Staff Member</p>'.TEmployee::UsingCache($customercalls->items[$i]->EmloyeeID())->Name());
											$t->Label('duration', '<p class="compaign-flex">Duration</p>'.$customercalls->items[$i]->DurationSec());
											$t->Label('status', '<p class="compaign-flex">Listen Recoding</p>'.$customercalls->items[$i]->Status());
											if ($customercalls->items[$i]->HasRecordingUrl())
												{
													$t->Label('listen', 'Listen Recoding');
													$modal = new TModalHelper();
													$modal->SetAJAXSource('index.php?m=playcallrecording&theonepage=1&id='.$customercalls->items[$i]->ID());
													$t->URL('listen', 'javascript:;');
													$t->OnClick('listen', $modal->GetJSCode());
													unset($modal);
												}
											$t->NewRow();
										}
									if ($t->RowCount() == 0)
										$t->SingleLineLabel('No calls yet');

									$panel->Add($t);

								}
						}

					$m['uipanel'] = $panel->Output();
					$m['id']=$customer->ID();
					$m['module']='customerdetails';
				}

		}
	elseif ($userinfo['level']==0)
		sm_redirect('index.php?m=account');


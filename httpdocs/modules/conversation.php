<?php

	function replace_tags_sms($txt, $customer)
		{
			/** @var $customer TCustomer */
			$str=str_replace('{FIRST_NAME}', $customer->FirstName(), $txt);
			$str=str_replace('{LAST_NAME}', $customer->LastName(), $str);
			$str=str_replace('{CONTACT_NAME}', $customer->FirstName().' '.$customer->LastName(), $str);
			$str=str_replace('{EMAIL}', $customer->Email(), $str);
			$str=str_replace('{CELLPHONE}', $customer->Cellphone(), $str);
			$str=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $str);
			$str=str_replace('{BUSINESS-CELLPHONE}', Formatter::USPhone(TCompany::CurrentCompany()->Cellphone()), $str);
			$str=str_replace('{OWNER}', '', $str);

			return $str;
		}

	function replace_tags_campaign($template, $customer)
		{
			/** @var $customer TCustomer */
			$str=str_replace('{FIRST_NAME}', $customer->FirstName(), $template);
			$str=str_replace('{LAST_NAME}', $customer->LastName(), $str);
			$str=str_replace('{CONTACT_NAME}', $customer->FirstName().' '.$customer->LastName(), $str);
			$str=str_replace('{EMAIL}', $customer->Email(), $str);
			$str=str_replace('{CELLPHONE}', $customer->Cellphone(), $str);
			$str=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $str);
			$str=str_replace('{BUSINESS-CELLPHONE}', TCompany::CurrentCompany()->Cellphone(), $str);
			$str=str_replace('{OWNER}', '', $str);
			return $str;
		}

	if ($userinfo['level']>0)
		{
			/** @var $currentcompany TCompany */

			use_api('temployee');
			use_api('tcustomer');

			sm_default_action('view');

			if (sm_action('view'))
				{
					$m['module']='conversation';
					sm_title('conversation');
					if (!empty($_getvars['cid']))
						$m['initjs']=("var dash_url='index.php?m=conversation&d=sidebar&cid=".intval($_getvars['cid'])."&rid=".intval($_getvars['rid'])."';");
					else
						$m['initjs']=("var dash_url='';");
					sm_add_cssfile('conversation.css');
					sm_add_cssfile('all_conversation.css');
					sm_add_jsfile('conversation.js');
				}
			if (sm_action('incomingemails'))
				{
					$m['module']='conversation';
					sm_title('Emails');
					$sm['inbox'] = 'email';
					if (!empty($_getvars['cid']))
						$m['initjs']=("var dash_url='index.php?m=conversation&d=emailsidebar".(!empty($_getvars['type'])?'&type='.$_getvars['type']:'')."&cid=".intval($_getvars['cid'])."&rid=".intval($_getvars['rid'])."';");
					else
						$m['initjs']=("var dash_url='index.php?m=conversation&d=emailsidebar".(!empty($_getvars['type'])?'&type='.$_getvars['type']:'')."';");

					if (isset($_getvars['type']) && $_getvars['type'] == 'incoming')
						{
							$sm['activetab'] = 'incoming';
						}

					sm_add_cssfile('conversation.css');
					sm_add_cssfile('all_conversation.css');
					sm_add_jsfile('emailconversation.js');
				}
			if (sm_action('sidebar'))
				{
					$m['module']='conversation';
					sm_use_template('conversationsidebar');
					$special['no_blocks'] = true;
					$limit = 20;
					$offset = intval($_getvars['from']);
					$sm['nextoffset'] = $limit+$offset;
					$q = new TQuery('smslog');
					$q->INStrings('type', Array('conversation'));
					$q->SelectFields('type, id_customer, max(timeadded) as lasttime, min(timeread) as unread');
					$q->AddWhere('id_company', TCompany::CurrentCompany()->ID());
					if (!empty($_getvars['cid']))
						$q->AddWhere('id_customer', intval($_getvars['cid']));
					$q->GroupBy('type, id_customer');
					$q->OrderBy('lasttime desc');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();

					if ($q->Count()>0)
						$sm['nextoffset']=$q->Count()+$offset;
					else
						$sm['nextoffset']=$offset;
					for ($i = 0; $i<$q->Count(); $i++)
						{
							$customer=new TCustomer($q->items[$i]['id_customer']);
							if (!$customer->Exists() || $customer->isDeleted() || !$customer->isSendingMessagesEnabled())
								continue;
							$j=count($sm['items']);

							$sm['items'][$j]['initials'] = $customer->Initials();
							$sm['items'][$j]['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();

							$sm['items'][$j]['id_customer']=$q->items[$i]['id_customer'];
							$sm['items'][$j]['primary_title']=TCompany::CurrentCompany()->LabelForCustomer();
							$sm['items'][$j]['primary_label']=$customer->Name();
							if (intval($q->items[$i]['unread'])==0)
								$sm['items'][$j]['unread']=true;
						}
				}
			if (sm_action('emailsidebar'))
				{
					$m['module']='conversation';
					sm_use_template('conversationsidebar');
					$special['no_blocks'] = true;
					$limit=20;
					$offset=intval($_getvars['from']);
					$sm['nextoffset']=$limit+$offset;
					$sm['inbox'] = 'email';

					$conversations = new TMessagesLogList();
					$conversations->SetFilterCompany(TCompany::CurrentCompany());
					$conversations->SetFilterEmail();

					if (isset($_getvars['type']) && $_getvars['type'] == 'incoming')
						{
							$sm['activetab'] = 'incoming';
							$conversations->SetFilterIncoming();
						}

					$conversations->OrderByTimestamp(false);
					if (!empty($_getvars['cid']))
						{
							$customer = new TCustomer($_getvars['cid']);
							if ($customer->Exists())
								$conversations->SetFilterCustomer($customer);
						}
					$conversations->GroupBy('id_customer');
					$conversations->Limit($limit);
					$conversations->Offset($offset);
					$conversations->Load();

					if ($conversations->Count() > 0)
						$sm['nextoffset'] = $conversations->Count()+$offset;
					else
						$sm['nextoffset'] = $offset;

					$j=0;
					for ($i = 0; $i < $conversations->Count(); $i++)
						{
							/** @var  $message TMessagesLog */
							$message = $conversations->Item($i);
							if (!$message->Exists())
								continue;
							$customer = new TCustomer($message->CustomerID());
							if ( !$customer->Exists() )
								continue;

							$sm['items'][$j]['initials'] = $customer->Initials();
							$sm['items'][$j]['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();

							$sm['items'][$j]['id_customer'] = $customer->ID();
							$sm['items'][$j]['primary_title'] = TCompany::CurrentCompany()->LabelForCustomer();
							$sm['items'][$j]['primary_label'] = $customer->Name();
							$sm['items'][$j]['id_referral']=0;
							unset($customer);

							if ( $message->isUnread() && $message->isIncoming())
								$sm['items'][$i]['unread'] = true;
							$j++;
						}
				}

			if (sm_action('userinfo'))
				{
					$m['module']='conversation';
					sm_use_template('conversationcustomerinfo');
					$special['no_blocks'] = true;
					$current_customer=new TCustomer(intval($_getvars['customer']));
					if ($current_customer->Exists() && !$current_customer->isDeleted())
						{
							$sm['currentuser']['exist']=1;
							$sm['currentuser']['initials'] = $current_customer->Initials();
							$sm['currentuser']['name'] = $current_customer->Name();
							$sm['currentuser']['url'] = 'index.php?m=customerdetails&d=info&id='.$current_customer->ID();
						}
				}
			if (sm_action('conversation'))
				{
					$m['module']='conversation';
					sm_use_template('conversationconversation');
					$special['no_blocks'] = true;
					$q = new TQuery('smslog');
					$q->AddWhere('id_company', TCompany::CurrentCompany()->ID());
					$q->INStrings('type', Array('conversation', 'startmessage'));
					$q->AddWhere('id_customer', intval($_getvars['customer']));
					$q->OrderBy('id');
					$q->Select();

					$customer=new TCustomer(intval($_getvars['customer']));
					for ($i = 0; $i<$q->Count(); $i++)
						{
							$employee=new TEmployee($q->items[$i]['id_employee']);
							$sm['items'][$i]['id_customer']=$q->items[$i]['id_customer'];
							if (intval($q->items[$i]['timeread'])==0)
								$sm['items'][$i]['unread']=true;
							if ($customer->Exists())
								$sm['items'][$i]['customer']=$customer->Name();
							if (empty($q->items[$i]['is_incoming']))
								{
									if (!$employee->Exists())
										{
											$sm['items'][$i]['employee'] = '&nbsp;';
											$sm['items'][$i]['employee_label']=TCompany::CurrentCompany()->Name();
										}
									else
										{
											$sm['items'][$i]['employee'] = $employee->Name();
											$sm['items'][$i]['employee_label']='Employee:';
										}
								}
							$sm['items'][$i]['text']=nl2br($q->items[$i]['text']);
							if ($i>1 && (abs($q->items[$i]['timeadded']-$q->items[$i-1]['timeadded'])<600))
								{
									$sm['items'][$i]['time']=strftime("%I:%M %p", $q->items[$i]['timeadded']);
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-short';
								}
							else
								{
									$sm['items'][$i]['time']=strftime("%m/%d/%Y %I:%M %p", $q->items[$i]['timeadded']);
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-full';
								}
							unset($employee);
							if ($q->items[$i]['timeread']==0)
								TQuery::ForTable('smslog')->Add('timeread', time())->Update('id', intval($q->items[$i]['id']));
						}
				}

			if (sm_action('setmessageread'))
				{
					if (!empty($_getvars['id']))
						{
							$conversations = new TMessagesLog($_getvars['id']);
							if ($conversations->Exists())
								$conversations->SetTimeRead(time());

							if ($conversations->Type() == 'email')
								{
									$replies = new TMessagesLogList();
									$replies->SetFilterCompany(TCompany::CurrentCompany());
									$replies->SetFilterIncoming();
									$replies->SetFilterUnread();
									$replies->SetFilterEmail();
									$replies->SetFilterReply($conversations->ID());
									$replies->Load();

									if ($replies->Count() > 0)
										{
											for ($l = 0; $l < $replies->Count(); $l++)
												{
													/** @var  $reply TMessagesLog */
													$reply = $replies->Item($l);
													$reply->SetTimeRead(time());
												}
										}
								}
						}

					exit();
				}

			if (sm_action('sendemail'))
				{
					if(empty($_getvars['id']))
						exit('Access Denied');

					$customer = new TCustomer(intval($_getvars['id']));
					if ($customer->Exists() && !$customer->isUnsubscribeStatus())
						{
							if ( (empty($_postvars['subject']) || empty($_postvars['text'])))
								$error_message = 'Fill required fields';

							if (empty($error_message))
								{
									$customer->SendEmailFromCompany(replace_tags_campaign($_postvars['subject'], $customer), replace_tags_campaign( $_postvars['text'], $customer ), 0, true, System::MyAccount(), 0, 0, 0, $_postvars['id_email']);
									sm_notify('Message sent');
								}
						}

					if (!empty($error_message))
						exit($error_message);
					else
						{
							exit('success');
						}
				}

			if (sm_action('loademaileditor'))
				{
					$m['module']='conversation';
					sm_use('ui.interface');
					sm_use('ui.form');

					if (empty($_getvars['id']))
						exit('Access Denied!');

					if (empty($_getvars['id_message']))
						exit('Access Denied!!');

					$message = new TMessagesLog($_getvars['id_message']);
					if (!$message->Exists() || $message->Type() != 'email')
						exit('Access Denied!!!');

					$email = new TEmail($message->MessageID());
//					if (!$email->Exists() || !$email->HasEmailID())
					if (!$email->Exists())
						exit('Access Denied!!!!');

					$customer = new TCustomer($_getvars['id']);
					if (!$customer->Exists() || $customer->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied!!!!!');

					$ui = new TInterface();

					if ($customer->isUnsubscribeStatus())
						$ui->NotificationError('Attention! User opted out from email messages.');

					if(!empty($error_message))
						$ui->NotificationError($error_message);

						if (!$customer->isUnsubscribeStatus())
							{
								$f = new TForm('index.php?m='.sm_current_module().'&d=sendemail&theonepage=1&id=' . $customer->ID());
								$f->AddClassnameGlobal('send_custom_email_form global_email_inbox');
								$f->InsertHTML('<div class="error-messages reply-'.$_getvars['id_message'].'"></div>');
								$f->AddHidden('id_email', $email->EmailID());
								$m['email']['subject'] = nl2br($email->Subject());
								$data['getimageslisturl'] = 'index.php?m=settings&d=getimageslistajax&theonepage=1';
								$f->InsertTPL('sendemailform.tpl', $data);
								$f->SaveButton('Send');

								$data['id_customer'] = $customer->ID();
								$ui->AddForm($f);
								$ui->html("
								
								<script>
								$(function() {
									var form = $('#reply-".$_getvars['id_message']." .send_custom_email_form');
								
									$(form).submit(function(event) {
										event.preventDefault();
										var formMessages = $('.error-messages.reply-".$_getvars['id_message']."');
										var formData = new FormData(form[0]);
							
								
										$('#reply-".$_getvars['id_message']." .adminform_savebutton input').prop('disabled', true);
										$.ajax({
											type: 'POST',
											enctype: 'multipart/form-data',
											processData: false,
											contentType: false,
											url: $(form).attr('action'),
											data: formData
										})
										.done(function(response) {
											if(response.includes(\"success\"))
												{
													 location.reload();
												}
											else
												{
													$(formMessages).removeClass('success');
													$(formMessages).addClass('aui-message aui-message-error');
													$(formMessages).text(response);
													$('.adminform_savebutton input').prop('disabled', false);
												}
										})
										.fail(function(data) {
											$(formMessages).removeClass('success');
											$(formMessages).addClass('aui-message aui-message-error');
								
											// Set the message text.
											if (data.responseText !== '') {
												$(formMessages).text(data.responseText);
											} else {
												$(formMessages).text('Oops! An error occured and your message could not be sent.');
											}
										});
									});
								});	
								</script>
								
								
								");

							}

					$ui->Output(true);
				}


			if (sm_action('emailconversation'))
				{
					$m['module']='conversation';
					sm_use_template('emailconversation');
					//sm_use_template('customermessages');
					$special['no_blocks'] = true;

					$customer = new TCustomer(intval($_getvars['customer']));
					if ( !$customer->Exists() )
						exit('Access Denied');
					$conversations = new TMessagesLogList();
					$conversations->SetFilterCompany(TCompany::CurrentCompany());
					$conversations->SetFilterEmail();
					$conversations->SetFilterNotReply();
					$conversations->OrderByTimestamp();
					$conversations->SetFilterCustomer($customer);
					if (!empty(intval($_getvars['lastid'])))
						$conversations->SetFilterGreaterThenID($_getvars['lastid']);
					$conversations->Load();
					$sm['replies_count'] = '';
					if ($conversations->Count() == 0 && !empty($_getvars['lastid']))
						$sm['lastitems_id'] = intval($_getvars['lastid']);
					$j = 0;
					$action_time = 0;
					for ($i = 0; $i < $conversations->Count(); $i++)
						{
							/** @var  $message TMessagesLog */
							$message = $conversations->Item($i);

							if ($message->TimeSent() > $action_time)
								{
									$action_time = $message->TimeSent();
									$sm['lastitems_id'] = $message->TimeSent();
								}
							$email = new TEmail($message->MessageID());
							if (!$email->Exists())
								continue;

							$sm['items'][$j]['unread'] = 0;
							if (!empty($message->EmployeeID()))
								$employee = new TEmployee($message->EmployeeID());
							$sm['items'][$j]['id'] = $message->ID();
							$sm['items'][$j]['id_customer'] = $customer->ID();
							$sm['items'][$j]['has_email_id'] = $email->HasEmailID();
							$sm['items'][$i]['isincoming']=$message->isIncoming();
							if ( $message->isUnread() && $message->isIncoming() )
								$sm['items'][$j]['unread'] = true;
							if ($customer->Exists())
								$sm['items'][$j]['customer'] = $customer->Name();

							if (!$message->isIncoming())
								{
									if (!is_object($employee) || !$employee->Exists())
										{
											$sm['items'][$j]['employee'] = '&nbsp;';
											$sm['items'][$j]['employee_label'] = TCompany::CurrentCompany()->Name();
										}
									else
										{
											$sm['items'][$j]['employee'] = $employee->Name();
											$sm['items'][$j]['employee_label'] = 'Employee:';
										}
								}

							$sm['items'][$j]['subject'] = nl2br($email->Subject());
							$sm['items'][$j]['text'] = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $email->Message());

							if($email->HasAttachments())
								{
									$files = $email->Attachments();
									$sm['items'][$j]['hasattachments'] = true;
									for ($k = 0; $k < count($files); $k++)
										{
											$ext = pathinfo($files[$k]['filename'], PATHINFO_EXTENSION);
											if ($email->HasAttachment($files[$k]['id'], $ext))
												$url = 'index.php?m='.sm_current_module().'&d=showattachment&id='.$files[$k]['id'].'&extension='.$ext;
											else
												$url = 'index.php?m='.sm_current_module().'&d=downloadattachment&id='.$files[$k]['id'].'&extension='.$ext.'&employee='.$email->EmployeeID();
											$sm['items'][$j]['files'][] = array(
												'title' => $files[$k]['filename'],
												'id' =>  $files[$k]['id'],
												'url' => $url
											);
										}
								}

							$sm['items'][$j]['time']=strftime("%m/%d/%Y %I:%M %p", $message->TimeSent());
							$sm['items'][$j]['time_class']='rd-dash-conversation-time-full';

							// =============== Replies ======================= //

							$replies = new TMessagesLogList();
							$replies->SetFilterCompany(TCompany::CurrentCompany());
							$replies->SetFilterEmail();
							$replies->SetFilterReply($message->ID());
							$replies->OrderByTimestamp();
							$replies->SetFilterCustomer($customer);
							$replies->Load();

							if ($replies->Count() > 0)
								{
									$sm['items'][$j]['replies_count'] = $replies->Count() + 1;

									$n = 0;
									for ($l = 0; $l < $replies->Count(); $l++)
										{
											/** @var  $reply TMessagesLog */
											$reply = $replies->Item($l);

											$email = new TEmail($reply->MessageID());
											if (!$email->Exists())
												continue;

											if (!empty($reply->EmployeeID()))
												$employee = new TEmployee($reply->EmployeeID());
											$sm['items'][$j]['replies'][$n]['id'] = $reply->ID();
											$sm['items'][$j]['replies'][$n]['id_email'] = $email->EmailID();
											$sm['items'][$j]['replies'][$n]['id_customer']=$customer->ID();
											if ($reply->isUnread() && $reply->isIncoming())
												{
													$sm['items'][$j]['unread'] = true;
													$sm['items'][$j]['replies'][$n]['unread'] = true;
												}
											if ($customer->Exists())
												{
													$sm['items'][$j]['replies'][$n]['customer'] = $customer->Name();
													$sm['items'][$j]['replies'][$n]['customer_email'] = $customer->Email();
													$sm['items'][$j]['replies'][$n]['customer_initials'] = $customer->Initials();
													$sm['items'][$j]['replies'][$n]['customer_url'] = 'index.php?m=customerdetails&id='.$customer->ID();
												}

											if (!$reply->isIncoming())
												{
													if (!is_object($employee) || !$employee->Exists())
														{
															$sm['items'][$j]['replies'][$n]['employee'] = TCompany::CurrentCompany()->Name();
															$sm['items'][$j]['replies'][$n]['email'] = TCompany::CurrentCompany()->EmailFrom();
														}
													else
														{
															$sm['items'][$j]['replies'][$n]['employee'] = $employee->Name();
															$sm['items'][$j]['replies'][$n]['email'] = $employee->Email();
														}
												}
											$sm['items'][$j]['replies'][$n]['subject'] = nl2br($email->Subject());
											$sm['items'][$j]['replies'][$n]['text'] = $email->Message();

											if($email->HasAttachments())
												{
													$files = $email->Attachments();
													$sm['items'][$j]['replies'][$n]['hasattachments'] = true;
													for ($k = 0; $k < count($files); $k++)
														{
															$ext = pathinfo($files[$k]['filename'], PATHINFO_EXTENSION);
															if ($email->HasAttachment($files[$k]['id'], $ext))
																$url = 'index.php?m='.sm_current_module().'&d=showattachment&id='.$files[$k]['id'].'&extension='.$ext;
															else
																$url = 'index.php?m='.sm_current_module().'&d=downloadattachment&id='.$files[$k]['id'].'&extension='.$ext.'&employee='.$email->EmployeeID();
															$sm['items'][$j]['replies'][$n]['files'][] = array(
																'title' => $files[$k]['filename'],
																'id' =>  $files[$k]['id'],
																'url' => $url
															);
														}
												}
											$sm['items'][$j]['replies'][$n]['time']=strftime("%m/%d/%Y %I:%M %p", $reply->TimeSent());
											$sm['items'][$j]['replies'][$n]['time_class']='rd-dash-conversation-time-full';

											$n++;
										}
								}
							unset($message);
							unset($replies);
							unset($employee);
							$j++;
						}
				}

			if (sm_action('marketingmessages'))
				{
					$m['module']='conversation';
					sm_use_template('marketingmessages');
					$special['no_blocks'] = true;
					$q = new TQuery('smslog');
					$q->INStrings('type', Array('multi', 'blast', 'notification'));
					$q->AddWhere('id_company', TCompany::CurrentCompany()->ID());
					$q->AddWhere('id_customer', intval($_getvars['customer']));
					$q->OrderBy('id');
					$q->Select();
					$customer=new TCustomer(intval($_getvars['customer']));
					for ($i = 0; $i<$q->Count(); $i++)
						{
							$sm['items'][$i]['id_customer']=$q->items[$i]['id_customer'];
							if (intval($q->items[$i]['timeread'])==0)
								$sm['items'][$i]['unread']=true;
							$sm['items'][$i]['customer']=$customer->Name();
							$sm['items'][$i]['text']=nl2br($q->items[$i]['text']);
							if ($q->items[$i]['type']=='notification')
								$sm['items'][$i]['type_title']='Notification';
							elseif ($q->items[$i]['type']=='blast')
								$sm['items'][$i]['type_title']='Blast Message';
							else
								$sm['items'][$i]['type_title']='Custom Selection Message';
							if ($i>1 && (abs($q->items[$i]['timeadded']-$q->items[$i-1]['timeadded'])<600))
								{
									$sm['items'][$i]['time']=strftime("%I:%M %p", $q->items[$i]['timeadded']);
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-short';
								}
							else
								{
									$sm['items'][$i]['time']=strftime("%m/%d/%Y %I:%M %p", $q->items[$i]['timeadded']);
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-full';
								}
						}
				}
			if (sm_action('allmessages'))
				{
					$m['module']='conversation';
					sm_use_template('customermessages');
					$special['no_blocks'] = true;
					$customer=new TCustomer(intval($_getvars['customer']));
					if(!$customer->Exists())
						exit('Access Denied');

					$messageslogs = new TMessagesLogList();
					$messageslogs->SetFilterMessages();
					$messageslogs->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$messageslogs->SetFilterCustomer($customer->ID());
					if (!empty(intval($_getvars['lastid'])))
						$messageslogs->SetFilterGreaterThenID($_getvars['lastid']);
					$messageslogs->OrderByID(true);
					$messageslogs->Load();

					if ($messageslogs->Count() == 0 && !empty(intval($_getvars['lastid'])))
						$sm['lastitems_id'] = intval($_getvars['lastid']);


					for ($i = 0; $i<$messageslogs->Count(); $i++)
						{
							$messageslog = $messageslogs->items[$i];
							$sm['lastitems_id'] = $messageslog->ID();
							$sm['items'][$i]['isincoming']=$messageslog->isIncoming();
							$sm['items'][$i]['id_customer']=$messageslog->ID();
							$sm['items'][$i]['customer']=$customer->Name();
							$sm['items'][$i]['preview']=false;

							if($messageslog->Type()=='email')
								{
									$email = new TEmail($messageslog->MessageID());
									if (!$email->Exists())
										continue;

									$sm['items'][$i]['preview']=true;
									$sm['items'][$i]['subject']=$email->Subject();
									$sm['items'][$i]['text']=$email->Message();
									if (is_dir('files/img/email_attachments_'.$email->ID()))
										{
											$dir_path = 'files/img/email_attachments_'.$email->ID();

											$dir = new DirectoryIterator($dir_path);
											$k = 0;
											foreach ($dir as $fileinfo)
												{
													if (!$fileinfo->isDot())
														{
															$sm['items'][$i]['files'][$k]['title'] = $fileinfo->getFilename();
															$sm['items'][$i]['files'][$k]['url'] = 'index.php?m='.sm_current_module().'&d=downloadattachment&id='.$email->ID().'&file='.$k;
															$k ++;
														}
												}
											if ($k>0)
												$sm['items'][$i]['hasattachments'] = true;
										}
									if ($email->CampaignID()!=0)
										{
											$title = '';
											$campaign = new TCampaign($email->CampaignID());
											if ($campaign->Exists())
												$title = '<a href="index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID().'">'.$campaign->Title().'</a> ';
											$sm['items'][$i]['type_title']=$title.'Campaign Email';
										}
									else
										{
											if ($messageslog->isIncoming())
												$sm['items'][$i]['type_title']='Incoming Email';
											else
												$sm['items'][$i]['type_title']='Direct Email';
										}
								}
							elseif($messageslog->Type()=='sms')
								{
									$sms = new TMessage($messageslog->MessageID());
									if (!$sms->Exists())
										continue;

									$sm['items'][$i]['text']=nl2br($sms->Text());
									if ($sms->TypeTag()=='notification')
										$sm['items'][$i]['type_title']='SMS Notification';
									elseif ($sms->TypeTag()=='blast')
										$sm['items'][$i]['type_title']='Blast SMS';
									elseif ($sms->TypeTag()=='conversation')
										{
											if ($messageslog->isIncoming())
												$sm['items'][$i]['type_title']='Incoming Message';
											else
												$sm['items'][$i]['type_title']='Direct Message';
										}
									elseif ($sms->CampaignID()!=0)
										{
											$title = '';
											$campaign = new TCampaign($sms->CampaignID());
											if ($campaign->Exists())
												$title = '<a href="index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID().'">'.$campaign->Title().'</a> ';
											$sm['items'][$i]['type_title']=$title.'Campaign SMS';
										}
									else
										$sm['items'][$i]['type_title']='Customer Selection SMS';

								}
							if ($messageslog->isUnread())
								$messageslog->SetTimeRead(time());

							if ($i>1 && (abs($messageslogs->items[$i]->TimeSent() - $messageslogs->items[$i-1]->TimeSent())<600))
								{
									$sm['items'][$i]['time']=strftime("%I:%M %p", $messageslog->TimeSent());
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-short';
								}
							else
								{
									$sm['items'][$i]['time']=strftime("%m/%d/%Y %I:%M %p", $messageslog->TimeSent());
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-full';
								}
							unset($messageslog);
							unset($email);
							unset($sms);
						}
				}

			if (sm_action('downloadattachment'))
				{
					$m['module']='conversation';

					if (empty($_getvars['id']))
						exit('Access Denied!');

					$email = new TEmail($_getvars['id']);
					if (!$email->Exists())
						exit('Access Denied!');

					if (is_dir('files/img/email_attachments_'.$email->ID()))
						{
							$dir_path = 'files/img/email_attachments_'.$email->ID();

							$dir = new DirectoryIterator($dir_path);
							$k = 0;
							foreach ($dir as $fileinfo)
								{
									if (!$fileinfo->isDot())
										{
											if($k == intval($_getvars['file']))
												{
													header("Content-type: ".$fileinfo->getType());
													header("Content-Disposition: attachment; filename=".$fileinfo->getFilename());
													$fp = fopen($dir_path.'/'.$fileinfo->getFilename(), 'rb');
													fpassthru($fp);
													fclose($fp);
													exit;
												}
											$k++;
										}
								}
						}
				}

			if (sm_action('sendmessage'))
				{
					$customer=new TCustomer(intval($_getvars['customer']));
					if ($customer->Exists() && $customer->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							$customer->SendMessage($_postvars['text'], $userinfo['id']);
							$customer->SendSMSAction();
						}
					exit($customer->ID());
				}
		}
	else
		sm_redirect('index.php?m=account');
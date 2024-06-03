<?php

	if ($userinfo['level']>0)
		{
			/** @var $currentcompany TCompany */
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

			if (sm_action('done'))
				{
					add_path_home();
					sm_title('Blast');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();
					$ui->p('Messages are in the queue.');
					$ui->Output(true);
				}
			if (sm_action('queue'))
				{
					if (intval($_postvars['ok'])!=1)
						{
							$error='Please check the box to start';
						}
					elseif ($_postvars['type']=='voice' && empty($_postvars['attachment_voice']))
						{
							$error='Please select a voice message';
						}
					elseif ( (empty($_postvars['subject']) || empty($_postvars['message'])) && $_postvars['type']=='email' )
						{
							$error='Subject and Message are required';
						}
					elseif (empty($_postvars['text']) && $_postvars['type']=='sms')
						{
							$error='Please enter the message';
						}
					elseif (empty($_postvars['type']))
						$error='Please select the Bulk send type';
					else
						{
							use_api('tcustomer');
							use_api('smdatetime');
							$sendtime = 0;
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_postvars['schedule'], $_postvars['hr'].':'.$_postvars['min'].' '.$_postvars['ampm']);
							if ( $tmp > SMDateTime::Now() )
								$sendtime = $tmp;

							if (!empty($_postvars['tag_filter']))
								{
									use_api('ttaglist');
									$tagids=array();
									$tags=$_postvars['tags_selected'];
									for ($i = 0; $i < count($tags); $i++)
										{
											if (!empty($tags[$i]))
												{
													$tmpid=str_replace('tag_','', $tags[$i]);
													$tag = new TTag($tmpid);
													if($tag->Exists())
														{
															$tagids[]=$tag->ID();
														}
												}
										}

									if (count($tagids)>0)
										{
											$tags=new TTagList();
											$tags->SetFilterIDs($tagids);
											$tags->OrderByName();
											$tags->Load();
										}
									else
										{
											$error='Please select tags';
										}
								}
							$customers = new TCustomerList();
							$customers->SetFilterEnabled();
							$customers->SetFilterCompany(TCompany::CurrentCompany());
							$customers->OrderByName();
							$customers->SetFilterNotDeleted();
							if (!empty($_postvars['tag_filter']) && empty($error))
								$customers->SetFilterIDs($tags->GetCustomerIDsArray());
							$customers->Open();

							if($customers->TotalCount()==0)
								{
									$error = 'Sequence Does Not Have Any '.$currentcompany->LabelForCustomers();
									sm_set_action('start');
								}
							if(empty($error))
								{
									while ($row = $customers->Fetch())
										{
											$customer = new TCustomer($row->ID());
											if ($_postvars['type']=='voice')
												{
													$customer->SendVoice($sendtime, $_postvars['attachment_voice']);
												}
											elseif($_postvars['type']=='sms')
												{
													if ($customer->HasCellphone())
														{
															if (!empty($_postvars['attachment']))
																$attachment = Array(sm_homepage().'index.php?m=companyassets&d=twiliomms&id='.intval($_postvars['attachment']));
															else
																$attachment = Array();

															$customer->SendMessage(replace_tags_campaign($_postvars['text'], $customer), 0, true, 'blast', $sendtime, $attachment);
															$customer->SendSMSAction(0, $sendtime, true);
														}
												}
											elseif($_postvars['type']=='email')
												{
													if ($customer->HasEmail())
														{
															$customer->SendEmailFromCompany(replace_tags_campaign($_postvars['subject'], $customer), replace_tags_campaign( $_postvars['message'], $customer ), $sendtime, true, $myaccount);
															$customer->SendEmailAction(0, $sendtime, true);
														}
												}
											unset($customer);
										}
								}
							sm_redirect('index.php?m=smsblast&d=done');
						}
					if (!empty($error))
						sm_set_action('start');
				}
			if (sm_action('queuemulti'))
				{
					if (intval($_postvars['ok'])!=1)
						{
							$error='Please check the box to start';
						}
					elseif (empty($_postvars['text']))
						{
							$error='Please enter the message';
						}
					else
						{
							use_api('tcustomer');
							use_api('smdatetime');
							$sendtime=0;
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_postvars['schedule'], $_postvars['hr'].':'.$_postvars['min'].' '.$_postvars['ampm']);
							if ($tmp>SMDateTime::Now())
								$sendtime=$tmp;
							$customers=new TCustomerList();
							$customers->SetFilterEnabled();
							$customers->SetFilterCompany(TCompany::CurrentCompany()->ID());
							$customers->SetFilterIDs(explode('|', $_postvars['list']));
							$customers->Load();
							for ($i = 0; $i < $customers->Count(); $i++)
								{
									$customer = $customers->items[$i];
									if (!empty($_postvars['attachment']))
										{
											$attachment = Array(sm_homepage().'index.php?m=companyassets&d=twiliomms&id='.intval($_postvars['attachment']));
										}
									else
										$attachment = Array();
									$customer->SendMessage($_postvars['text'], 0, true, 'multi', $sendtime, $attachment);
									unset($customer);
								}
							if (!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto']);
							else
								sm_redirect('index.php?m=smsblast&d=done');
						}
					if (!empty($error))
						sm_set_action('startmulti');
				}
			if (sm_action('startmulti'))
				{
					add_path_home();
					add_path_current();
					use_api('tcustomer');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					sm_add_jsfile('ext/datepicker/js/bootstrap-datepicker.js', true);
					sm_add_cssfile('ext/datepicker/css/datepicker.css', true);
					use_api('smdatetime');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (empty($_postvars['list']) && is_array($_postvars['ids']))
						$_postvars['list']=implode('|', $_postvars['ids']);
					$list=explode('|', $_postvars['list']);
					$html='';
					sm_title('Blast - Message to '.TCompany::CurrentCompany()->LabelForCustomers());
					$ui->html(TCompany::CurrentCompany()->LabelForCustomers().': ');
					for ($i = 0; $i < count($list); $i++)
						{
							if ($i > 0)
								$html .= (', ');
							$customer = new TCustomer($list[$i]);
							if ($customer->Exists() && $customer->CompanyID()==TCompany::CurrentCompany()->ID())
								$html .= ($customer->Name());
							unset($customer);
						}
					$ui->html($html);
					$f = new TForm('index.php?m=smsblast&d=queuemulti&returnto='.urlencode($_getvars['returnto']));
					$f->AddHidden('list', $_postvars['list']);
					$f->AddTextarea('text', 'Message');
					$q = new TQuery('company_assets');
					$q->Add('id_company', intval($currentcompany->ID()));
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
											$l[] = $asset->FileName().' - '.$asset->Comment();
										}
									unset($asset);
								}
							$f->AddSelectVL('attachment', 'Media Attachment', $v, $l);
						}
					$f->AddText('schedule', 'Schedule');
					$f->Calendar();
					$f->SetFieldEndText('schedule', ' at ');
					$f->HideEncloser();
					$f->AddSelect('hr', 'Hr', Array(1,2,3,4,5,6,7,8,9,10,11,12));
					$f->SetFieldClass('hr', 'hrs');
					$f->HideDefinition();
					$f->HideEncloser();
					$tmp=range(0, 59);
					for ($i = 0; $i < count($tmp); $i++)
						if ($tmp[$i]<10)
							$tmp[$i]='0'.$tmp[$i];
					$f->AddSelectVL('min', 'Min', range(0, 59), $tmp);
					$f->SetFieldClass('min', 'minutes');
					$f->HideDefinition();
					$f->HideEncloser();
					$f->AddSelectVL('ampm', 'ampm', Array('am', 'pm'), Array('AM', 'PM'));
					$f->SetFieldClass('ampm', 'ampm');
					$f->HideDefinition();
					//$f->AddCheckbox('ok', 'I understand that this action cannot be undone. Send messages to all selected '.strtolower($currentcompany->LabelForCustomers()));
					$f->AddCheckbox('ok', 'I understand this action can not be undone and want to send messages.');
					$f->LabelAfterControl();
					$f->SaveButton('Send Messages');
					$f->SetValue('schedule', SMDateTime::USDateFormat(SMDateTime::Now()));
					$f->SetValue('hr', SMDateTime::Hour12(SMDateTime::Now()));
					$f->SetValue('min', SMDateTime::Min(SMDateTime::Now()));
					$f->SetValue('ampm', SMDateTime::AMPMLowerCased(SMDateTime::Now()));
					$f->LoadValuesArray($_postvars);
					$ui->style('#schedule {width:120px;display:inline;}');
					$ui->style('#hr, #min, #ampm {width:70px;display:inline;}');
					$ui->AddForm($f);
					$ui->Output(true);
				}
			if (sm_action('start'))
				{
					sm_add_jsfile('ext/datepicker/js/bootstrap-datepicker.js', true);
					sm_add_cssfile('ext/datepicker/css/datepicker.css', true);
					use_api('smdatetime');
					add_path_home();
					add_path_current();
					use_api('ttaglist');
					if (!empty($_getvars['tag']) && empty($_postvars['tag']))
						$_postvars['tags_selected']=array('tag_'.$_getvars['tag']);
					$tags=new TTagList();
					$tags->OrderByName();
					$tags->Load();
					sm_title('Blast');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					$f = new TForm('index.php?m=smsblast&d=queue');
					$f->AddSelectVL('type', 'Blast Type', Array('sms', 'voice', 'email'), Array('SMS to '.TCompany::CurrentCompany()->LabelForCustomers(), 'Voice message to '.TCompany::CurrentCompany()->LabelForCustomers(), 'Email to '.TCompany::CurrentCompany()->LabelForCustomers()));
					$f->ValueToggleFor('text', 'sms');
					$f->ValueToggleFor('attachment', 'sms');
					$f->ValueToggleFor('attachment_voice', 'voice');
					$f->ValueToggleFor('subject', 'email');
					$f->ValueToggleFor('message', 'email');
					$f->AddTextarea('text', 'Message')->SetFocus();
					$data['var'] = 'message';
					$data['getimageslisturl'] = 'index.php?m=settings&d=getimageslistajax&theonepage=1';
					$f->InsertTPL('sendemailform.tpl', $data, '', '', 'subject');

					$f->AddSelectVL('tag_filter', 'Filter', Array(0, 1), Array('-- Tag filering disabled --', '-- Tag filering enabled --'));
					if(!empty($_getvars['tag']) && in_array($_getvars['tag'], $tags->ExtractIDsArray()))
						$f->SetValue('tag_filter', 1);
					$f->ValueToggleFor('tags', '1');

					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$data['tags'][$i]['title'] = $tags->items[$i]->Name();
							$data['tags'][$i]['value'] = 'tag_'.$tags->items[$i]->ID();

							if (!empty($_postvars['tags_selected']))
								{
									for($j=0; $j<count($_postvars['tags_selected']); $j++)
										{
											if($_postvars['tags_selected'][$j]==$data['tags'][$i]['value'])
												$data['tags'][$i]['checked'] = '1';
										}
								}
						}
					$n=0;
					$tagscount=$tags->Count()-1;
					if (!empty($_postvars['tags_selected']))
						{
							for($j=0; $j<count($_postvars['tags_selected']); $j++)
								{
									$exist=0;
									for ($i = 0; $i < $tags->Count(); $i++)
										{
											if($data['tags'][$i]['value']==$_postvars['tags_selected'][$j])
												{
													$exist=1;
													continue;
												}
										}
									if($exist!=1)
										{
											$n++;
											$data['tags'][$tagscount+$n]['title']=$_postvars['tags_selected'][$j];
											$data['tags'][$tagscount+$n]['value']=$_postvars['tags_selected'][$j];
											$data['tags'][$tagscount+$n]['checked']=1;
										}
								}
						}
					$f->InsertTPL('tags_select.tpl', $data, '', 'Tags', 'tags');
					$f->SetTitleText('tags', 'Tags');
					unset($data);

					$q=new TQuery('company_assets');
					$q->Add('id_company', intval($currentcompany->ID()));
					$q->OrderBy('comment, filename');
					$q->Select();
					if ($q->Count()>0)
						{
							$v=Array('');
							$l=Array('- No attachment -');
							for ($i = 0; $i < $q->Count(); $i++)
								{
									$asset=new TAsset($q->items[$i]);
									if ($asset->isEligibleForMMS())
										{
											$v[]=$asset->ID();
											$l[]=$asset->FileName().' - '.$asset->Comment();
										}
									unset($asset);
								}
							$f->AddSelectVL('attachment', 'Media Attachment', $v, $l);
						}

					$assets = new TAssetList();
					$assets->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$assets->SetFilterAudio();
					$assets->Load();
					$f->AddSelectVL('attachment_voice', 'Media Attachment', $assets->ExtractIDsArray(), $assets->ExtractTitlesArray());
					$f->SelectAddBeginVL('attachment_voice', '0', '- Select a voice message -');
					$f->AddText('schedule', 'Schedule');
					$f->Calendar();
					$f->SetFieldEndText('schedule', ' at ');
					$f->HideEncloser();
					$f->AddSelect('hr', 'Hr', Array(1,2,3,4,5,6,7,8,9,10,11,12));
					$f->SetFieldClass('hr', 'hrs');
					$f->HideDefinition();
					$f->HideEncloser();
					$tmp=range(0, 59);
					for ($i = 0; $i < count($tmp); $i++)
						if ($tmp[$i]<10)
							$tmp[$i]='0'.$tmp[$i];
					$f->AddSelectVL('min', 'Min', range(0, 59), $tmp);
					$f->SetFieldClass('min', 'minutes');
					$f->HideDefinition();
					$f->HideEncloser();
					$f->AddSelectVL('ampm', 'ampm', Array('am', 'pm'), Array('AM', 'PM'));
					$f->SetFieldClass('ampm', 'ampm');
					$f->HideDefinition();
					$f->AddCheckbox('ok', 'I understand this action can not be undone and want to send messages.');
					$f->LabelAfterControl();
					$f->SaveButton('Send Messages');
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
					$ui->style('#schedule {width:120px;display:inline;}');
					$ui->style('#hr, #min, #ampm {width:70px;display:inline;}');
					$ui->AddForm($f);
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');
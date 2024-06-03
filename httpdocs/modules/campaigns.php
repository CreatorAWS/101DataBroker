<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if (sm_action('contacts'))
				{
					$campaign=new TCampaign($_getvars['campaign']);
					if ($campaign->Exists() && $campaign->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							$statuses=Array(
								'pending1'=>'Queued',
								'pending2'=>'Sent',
								'pending3'=>'Sent',
								'pendingfinish'=>'Sent'
							);
							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.fa');
							sm_title('Drip');
							$limit = 30;
							$offset = intval($_getvars['from']);
							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign);
							if ( $_getvars['type'] == 'unsubscribers')
								$contacts->SetFilterEmailStatusBlacklisted();
							elseif ( $_getvars['type'] == 'clickers')
								$contacts->SetFilterEmailStatusClicked();
							elseif ( $_getvars['type'] == 'openers')
								$contacts->SetFilterEmailStatusOpened();
							elseif ( $_getvars['type'] == 'smsdelivered')
								$contacts->SetFilterSMSStatusDelivered();
							$contacts->Limit($limit);
							$contacts->Offset($offset);
							$contacts->OrderByID();
							$contacts->Load();
							$ui = new TInterface();
							$t = new TGrid();
							$t->AddCol('name', 'Name');
							$t->AddCol('email', 'Email');
							$t->AddCol('phone', 'Phone');
							$t->AddCol('status', 'Status');
							$t->AddCol('email_status', 'Email Status');
							$t->AddCol('sms_status', 'SMS Status');
							for ($i = 0; $i < $contacts->Count(); $i++)
								{
									$t->Label('name', '<p class="compaign-flex">Name</p>'.$contacts->items[$i]->Name());
									if($contacts->items[$i]->PartnerID()!=0)
										$t->URL('name', 'index.php?m=customerdetails&d=info&id='.$contacts->items[$i]->PartnerID());
									$t->Label('email', '<p class="compaign-flex">Email</p>'.$contacts->items[$i]->Email());

									if ($contacts->items[$i]->HasPhone())
										{
											$t->Label('phone', '<p class="compaign-flex">Phone</p>'.Formatter::USPhone($contacts->items[$i]->Phone()));
											if ($contacts->items[$i]->isPhoneTypeTagNotVerified())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('hourglass-half'));
											if ($contacts->items[$i]->isPhoneTypeTagLandline())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('phone'));
											if ($contacts->items[$i]->isPhoneTypeTagCell())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('mobile'));
											if ($contacts->items[$i]->isPhoneTypeTagVOIP())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('headphones'));
											if ($contacts->items[$i]->HasVoicemailCallTime())
												{
													if ($contacts->items[$i]->VoicemailCallResultTag()!='none')
														{
															$lbl = 'Call status: '.$contacts->items[$i]->VoicemailCallResultTag();
															$lbl .= ', '.$contacts->items[$i]->VoicemailCallDuration().'s';
														}
													else
														$lbl='Pending call';
													$t->LabelAppend('phone', '<br /><div class="label label-info">'.$lbl.'</div>');
												}
										}
									else
										$t->Label('phone', '<p class="compaign-flex">Phone</p>'.'-');

									$t->Label('status', '<p class="compaign-flex">Status</p>'.$contacts->items[$i]->Status());
									$t->Label('email_status', '<p class="compaign-flex">Email Status</p>'.$contacts->items[$i]->EmailStatus());
									$t->Label('sms_status', '<p class="compaign-flex">SMS Status</p>'.$contacts->items[$i]->SMSStatus());
									$t->NewRow();
								}
							if ($t->RowCount() == 0)
								$t->SingleLineLabel('Nothing found');
							$ui->Add($t);
							$ui->AddPagebarParams($contacts->TotalCount(), $limit, $offset);
							$ui->Output(true);
						}
				}

			if (sm_action('initialmessagedetails'))
				{
					$campaign = new TCampaign($_getvars['campaign']);
					if ($campaign->Exists() && $campaign->CompanyID() == TCompany::CurrentCompany()->ID())
						{
							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.fa');

							add_path_home();
							add_path($campaign->Title(), 'index.php?m='.sm_current_module().'&d=campaigndetails&id='.$campaign->ID());
							add_path_current();
							sm_title('Initial Message');

							$limit = 30;
							$offset = intval($_getvars['from']);

							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign->ID());
							$contacts->Limit($limit);
							$contacts->Offset($offset);
							$contacts->OrderByID();
							$contacts->Load();

							$ui = new TInterface();

							$t = new TGrid();
							$t->AddCol('name', 'Name');
							$t->AddCol('email', 'Email');
							$t->AddCol('phone', 'Phone');
							$t->AddCol('status', 'Status');

							for ($i = 0; $i < $contacts->Count(); $i++)
								{
									/** @var  $contact TCampaignItem */
									$contact = $contacts->Item($i);
									$t->Label('name', '<p class="compaign-flex">Name</p>'.$contact->Name());

									$customer = new TCustomer($contact->PartnerID());
									if($customer->Exists())
										$t->URL('name', 'index.php?m=customerdetails&d=info&id='.$customer->ID());

									$t->Label('email', '<p class="compaign-flex">Email</p>'.$contact->Email());

									if ($contact->HasPhone())
										{
											$t->Label('phone', '<p class="compaign-flex">Phone</p>'.Formatter::USPhone($contact->Phone()));
											if ($contact->isPhoneTypeTagNotVerified())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('hourglass-half'));
											if ($contact->isPhoneTypeTagLandline())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('phone'));
											if ($contact->isPhoneTypeTagCell())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('mobile'));
											if ($contact->isPhoneTypeTagVOIP())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('headphones'));
											if ($contact->HasVoicemailCallTime())
												{
													if ($contact->VoicemailCallResultTag()!='none')
														{
															$lbl = 'Call status: '.$contact->VoicemailCallResultTag();
															$lbl .= ', '.$contact->VoicemailCallDuration().'s';
														}
													else
														$lbl='Pending call';
													$t->LabelAppend('phone', '<br /><div class="label label-info">'.$lbl.'</div>');
												}
										}
									else
										$t->Label('phone', '<p class="compaign-flex">Phone</p>'.'-');

									if ($contact->Status() == 'pending1')
										$initial_message_status = 'Scheduled';
									elseif ($contact->Status() != 'pending1' && $contact->Status() != 'none')
										$initial_message_status = 'Sent';
									else
										$initial_message_status = '';

									if ( !empty($contact->EmailStatus()) )
										$initial_message_status = $contact->EmailStatus();

									$t->Label('status', '<p class="compaign-flex">Status</p>'.$initial_message_status);

									if ( !empty($contact->EmailStatus()) )
										$t->Label('status', '<p class="compaign-flex">Status</p>'.$contact->EmailStatus());

									$t->NewRow();
								}
							if ($t->RowCount() == 0)
								$t->SingleLineLabel('Nothing found');
							$ui->Add($t);
							$ui->AddPagebarParams($contacts->TotalCount(), $limit, $offset);
							$ui->Output(true);
						}
				}

			if (sm_action('sequencedetails'))
				{
					$campaign=new TCampaign($_getvars['campaign']);
					if ($campaign->Exists() && $campaign->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							$sequence = new TCampaignSequence($_getvars['id']);
							if(!$sequence->Exists())
								exit('Access Denied');

							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.fa');

							add_path_home();
							add_path($campaign->Title(), 'index.php?m='.sm_current_module().'&d=campaigndetails&id='.$campaign->ID());
							add_path_current();
							sm_title($sequence->GetMode());

							$limit = 30;
							$offset = intval($_getvars['from']);

							$schedulelist = new TCampaignScheduleList();
							$schedulelist->SetFilterCompany(TCompany::CurrentCompany());
							$schedulelist->SetFilterCampaign($campaign->ID());
							$schedulelist->SetFilterSequence($sequence->ID());
							$schedulelist->Limit($limit);
							$schedulelist->Offset($offset);
							$schedulelist->OrderByID();
							$schedulelist->Load();
							$ui = new TInterface();
							$t = new TGrid();
							$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20 underline_hrefs');
							$t->AddCol('name', 'Name');
							$t->AddCol('email', 'Email');
							$t->AddCol('phone', 'Phone');
							$t->AddCol('status', 'Status');
							for ($i = 0; $i < $schedulelist->Count(); $i++)
								{
									$schedule = $schedulelist->items[$i];
									$contact = new TCampaignItem($schedule->CustomerID());

									$customer = new TCustomer($contact->PartnerID());
									if($customer->Exists())
										$t->Label('name', '<p class="compaign-flex">Name</p><a href="index.php?m=customerdetails&d=info&id='.$customer->ID().'">'.$contact->Name().'</a>');
									else
										$t->Label('name', '<p class="compaign-flex">Name</p>'.$contact->Name());

									$t->Label('email', '<p class="compaign-flex">Email</p>'.$contact->Email());

									if ($contact->HasPhone())
										{
											$t->Label('phone', '<p class="compaign-flex">Phone</p>'.Formatter::USPhone($contact->Phone()));
											if ($contact->isPhoneTypeTagNotVerified())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('hourglass-half'));
											if ($contact->isPhoneTypeTagLandline())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('phone'));
											if ($contact->isPhoneTypeTagCell())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('mobile'));
											if ($contact->isPhoneTypeTagVOIP())
												$t->LabelAppend('phone', ' '.FA::EmbedCodeFor('headphones'));
											if ($contact->HasVoicemailCallTime())
												{
													if ($contact->VoicemailCallResultTag()!='none')
														{
															$lbl = 'Call status: '.$contact->VoicemailCallResultTag();
															$lbl .= ', '.$contact->VoicemailCallDuration().'s';
														}
													else
														$lbl='Pending call';
													$t->LabelAppend('phone', '<br /><div class="label label-info">'.$lbl.'</div>');
												}
										}
									else
										$t->Label('phone', '<p class="compaign-flex">Phone</p>'.'-');

									$t->Label('status', '<p class="compaign-flex">Status</p>'.$schedule->GetStatus());

									if ($sequence->GetMode() == 'email')
										{
											if ( !empty($schedule->EmailStatus()) )
												$t->Label('status', '<p class="compaign-flex">Status</p>'.$schedule->EmailStatus());
										}
									elseif ($sequence->GetMode() == 'sms')
										{
											if ( !empty($schedule->SMSStatus()) )
												$t->Label('status', '<p class="compaign-flex">Status</p>'.$schedule->SMSStatus());
										}
									$t->NewRow();
								}
							if ($t->RowCount() == 0)
								$t->SingleLineLabel('Nothing found');
							$ui->Add($t);
							$ui->AddPagebarParams($schedulelist->TotalCount(), $limit, $offset);
							$ui->Output(true);
						}
				}

			if (sm_action('campaigndetails'))
				{
					add_path_home();
					add_path('Drips', 'index.php?m=campaigns');
					add_path_current();

					sm_add_body_class('campaign_stats_details');
					$campaign = new TCampaign($_getvars['id']);
					if ($campaign->Exists() && $campaign->CompanyID() == TCompany::CurrentCompany()->ID())
						{
							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.fa');
							sm_title($campaign->Title());
							$ui = new TInterface();

							$campaign=new TCampaign(intval($_getvars['id']));
							if (!$campaign->Exists())
								exit('Error 463356-23464');

							$campaign_type = 'Unset';
							$data['sequence'] = [];

							$campaign_type = 'Initial Email';
							$campaign_template = '';
							$template = new TEmailTemplate($campaign->Email1Template());
							if($template->Exists())
								{
									$campaign_template = $template->Title();
									$campaign_template_url = 'index.php?m=templates&d=emailtemplates&id_ctg='.$template->CategoryID();
								}

							if($campaign->Starttime() > 0)
								$campaign_date = Formatter::DateTime($campaign->Starttime());
							else
								$campaign_date = 'Not Started';

							$data['sequence'][] = [
								'date' => $campaign_date,
								'mode' => $campaign_type,
								'template' => $campaign_template,
								'template_url' => $campaign_template_url,
								'customers' => $campaign->ContactsCount(),
								'scheduled' => $campaign->ContactsCountInitialScheduled(),
								'sent' => $campaign->ContactsCountInitialSent(),
								'details' => 'index.php?m='.sm_current_module().'&d=initialmessagedetails&campaign='.$campaign->ID()
							];

							$sequencelist = new TCampaignSequenceList();
							$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
							$sequencelist->SetFilterCampaign($campaign->ID());
							$sequencelist->Load();

							$sequence_steps = [];
							for ( $i = 0; $i < $sequencelist->Count(); $i++ )
								{
									$sequence = $sequencelist->items[$i];
									if($campaign->Starttime() > 0)
										$sequence_steps['date'] = Formatter::DateTime($campaign->Starttime() + $sequence->ScheduledTimestamp());
									else
										$sequence_steps['date'] = 'Not Started';
									$sequence_steps['mode'] = $sequence->GetMode();

									$sequence_steps['template']='';
									$sequence_steps['template_url']='';
									if ($sequence->GetMode() == 'sms')
										{
											$template = TMessageTemplate::initWithText($sequence->GetText());
											if($template->Exists())
												{
													$sequence_steps['template'] = $template->Title();
													$sequence_steps['template_url'] = 'index.php?m=templates&d=messagetemplates&id_ctg='.$template->CategoryID();
												}
											else
												{
													$sequence_steps['template'] = $sequence->GetText();
												}

										}
									elseif($sequence->GetMode() == 'email')
										{
											$template = new TEmailTemplate($sequence->EmailTemplate());
											if($template->Exists())
												{
													$sequence_steps['template'] = $template->Title();
													$sequence_steps['template_url'] = 'index.php?m=templates&d=emailtemplates&id_ctg='.$template->CategoryID();
												}
										}
									elseif($sequence->GetMode() == 'voice')
										{
											$asset = new TAsset($sequence->IdAsset());
											if($asset->Exists())
												{
													$sequence_steps['template'] = $asset->FileNameWithComment();
													$sequence_steps['template_url'] = 'index.php?m=companyassets&mode=voice';
												}
										}

									$schedulelist = new TCampaignScheduleList();
									$schedulelist->SetFilterCompany(TCompany::CurrentCompany());
									$schedulelist->SetFilterCampaign($campaign->ID());
									$schedulelist->SetFilterSequence($sequence->ID());

									$sequence_steps['customers'] = $schedulelist->TotalCount();

									$schedulelist_scheduled = new TCampaignScheduleList();
									$schedulelist_scheduled->SetFilterCompany(TCompany::CurrentCompany());
									$schedulelist_scheduled->SetFilterCampaign($campaign->ID());
									$schedulelist_scheduled->SetFilterSequence($sequence->ID());
									$schedulelist_scheduled->SetFilterStatus('scheduled');

									$sequence_steps['scheduled'] = $schedulelist_scheduled->TotalCount();
									$sequence_steps['sent'] = $schedulelist->TotalCount() - $schedulelist_scheduled->TotalCount();
									$sequence_steps['details'] = 'index.php?m='.sm_current_module().'&d=sequencedetails&campaign='.$campaign->ID().'&id='.$sequence->ID();

									$data['sequence'][] = $sequence_steps;
								}

							$data['campaign']['id'] = $campaign->ID();
							$data['campaign']['title'] = $campaign->Title();
							$data['campaign']['recipients'] = $campaign->ContactsCount();
							$data['campaign']['openers'] = $campaign->OpenersCount();
							$data['campaign']['clickers'] = $campaign->ClickerCount();
							$data['campaign']['unsubscribers'] = $campaign->UnsubscribersCount();
							$data['campaign']['smsdelivered'] = $campaign->SMSDeliveredCount();
							if ($campaign->Starttime()!=0)
								$data['campaign']['time'] = Formatter::DateTime($campaign->Starttime());
							else
								$data['campaign']['time'] = Formatter::DateTime($campaign->Addedtime());
							$data['campaignstats']['customerscount'] = $campaign->ContactsCount();
							$data['campaignstats']['openedcount'] = $campaign->OpenersCount();
							if($data['campaignstats']['customerscount']!=0)
								$data['campaignstats']['openedpercent'] = round($data['campaignstats']['openedcount']*100/$data['campaignstats']['customerscount'], 2);
							$data['campaignstats']['clicked'] = $campaign->ClickerCount();
							if($data['campaignstats']['customerscount']!=0)
								$data['campaignstats']['clickedpercent'] = round($data['campaignstats']['clicked']*100/$data['campaignstats']['customerscount'], 2);
							$data['campaignstats']['blacklisted'] = $campaign->UnsubscribersCount();
							if($data['campaignstats']['customerscount']!=0)
								$data['campaignstats']['blacklistedpercent'] = round($data['campaignstats']['blacklisted']*100/$data['campaignstats']['customerscount'], 2);
							$data['campaignstats']['smsdelivered'] = $campaign->SMSDeliveredCount();
							if($data['campaignstats']['customerscount']!=0)
								$data['campaignstats']['smsdeliveredpercent'] = round($data['campaignstats']['smsdelivered']*100/$data['campaignstats']['customerscount'], 2);

							$data['customer']['url'] = 'index.php?m='.sm_current_module().'&d=contacts&campaign='.$campaign->ID();
							$data['openers']['url']='index.php?m='.sm_current_module().'&d=contacts&campaign='.$campaign->ID().'&type=openers';
							$data['clickers']['url']='index.php?m='.sm_current_module().'&d=contacts&campaign='.$campaign->ID().'&type=clickers';
							$data['unsubscribers']['url']='index.php?m='.sm_current_module().'&d=contacts&campaign='.$campaign->ID().'&type=unsubscribers';
							$data['smsdelivered']['url']='index.php?m='.sm_current_module().'&d=contacts&campaign='.$campaign->ID().'&type=smsdelivered';

							$ui->AddTPL('campaigndetails_table.tpl','', $data);

							$ui->Output(true);
						}
				}

			if (sm_action('clone'))
				{
					if(empty($_getvars['id']))
						exit('Access Denied');

					$campaign=new TCampaign($_getvars['id']);
					if (!$campaign->Exists())
						exit('Access Denied');

					$new_title = $campaign->Title().' Copy';

					$new_campaign = TCampaign::Create(TCompany::CurrentCompany());
					$new_campaign->SetTitle($new_title);
					$new_campaign->SetAssetID($campaign->AssetID());
					$new_campaign->SetEmail1Template($campaign->Email1Template());
					$new_campaign->SetEmail1Subject($campaign->Email1Subject());
					$new_campaign->SetEmail1Message($campaign->Email1Message());
					$new_campaign->SetEmail2Template($campaign->Email2Template());
					$new_campaign->SetEmail2Subject($campaign->Email2Subject());
					$new_campaign->SetEmail2Message($campaign->Email2Message());
					$new_campaign->SetEmail3Template($campaign->Email3Template());
					$new_campaign->SetEmail3Subject($campaign->Email3Subject());
					$new_campaign->SetEmail3Message($campaign->Email3Message());
					$new_campaign->SetVoicemessageAsset($campaign->VoicemessageAsset());

					$sequencelist = new TCampaignSequenceList();
					$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
					$sequencelist->SetFilterCampaign($campaign);
					$sequencelist->Load();

					for ($i=0; $i<$sequencelist->Count(); $i++)
						{
							$sequence = $sequencelist->items[$i];
							$new_sequence = TCampaignSequence::Create(TCompany::CurrentCompany(), $new_campaign);
							$new_sequence->SetCompanyID($sequence->CompanyID());
							$new_sequence->SetCampaignID($new_campaign->ID());
							$new_sequence->SetScheduledTimestamp($sequence->ScheduledTimestamp());
							$new_sequence->SetEmailTemplate($sequence->EmailTemplate());
							$new_sequence->SetEmailSubject($sequence->EmailSubject());
							$new_sequence->SetEmailMessage($sequence->EmailMessage());
							$new_sequence->SetIdAsset($sequence->IdAsset());
							$new_sequence->SetText($sequence->GetText());
							$new_sequence->SetMode($sequence->GetMode());
						}

					sm_redirect('index.php?m=campaignwizard&action=create&id='.$new_campaign->ID());

				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Drip');
					add_path_home();
					add_path_current();
					$limit=30;
					$offset=intval($_getvars['from']);
					$campaigns=new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					if($_getvars['currmode']=='sent')
						{
							$campaigns->ExcludeStatusesArray(Array('notfinished'));
							$campaigns->SetFilterSendBefore(time());
						}
					elseif($_getvars['currmode']=='draft')
						$campaigns->ExcludeStatusesArray(Array('started', 'scheduled'));
					elseif($_getvars['currmode']=='scheduled')
						{
							$campaigns->ExcludeStatusesArray(Array('started', 'notfinished'));
							$campaigns->SetFilterSceduledAfter(time());
						}
					$campaigns->Limit($limit);
					$campaigns->Offset($offset);
					$campaigns->OrderByAddedTime(false);
					$campaigns->Load();

					$data['campaign_count'] = $campaigns->TotalCount();

					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$b = new TButtons();
					if($_getvars['currmode']=='sent')
						$data['currmode']='sent';
					elseif($_getvars['currmode']=='draft')
						$data['currmode']='draft';
					elseif($_getvars['currmode']=='scheduled')
						$data['currmode']='scheduled';
					else
						$data['currmode']=sm_current_action();

					$b->AddButton('run', 'Run Drip', 'index.php?m=campaigns&d=runsequence');
					$b->AddClassname('add_asset_button', 'run');$b->AddButton('add', 'Create Drip', 'index.php?m=campaignwizard&action=create');
					$b->AddClassname('add_asset_button create-button', 'add');
					$ui->Add($b);

					$ui->AddTPL('campaigns_header.tpl', '', $data);
					$ui->div_open('', 'campaignlist');

					$t = new TGrid();
					$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20');
					$t->HideHeader(1);
					$t->AddCol('type', '', '3%');
					$t->AddCol('title', '', '27%');
					$t->AddCol('status', '', '8%');
					$t->AddCol('date', '', '15%');
					$t->AddCol('contactsqty', '');
					$t->AddCol('openers', '');
					$t->AddCol('clickers', '');
					$t->AddCol('unsubscr', '');
					$t->AddCol('action', '','5%');

					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							if($campaigns->items[$i]->Status()=='notfinished')
								$t->RowAddClass('draft');
							$t->Label('type', FA::EmbedCodeFor('envelope'));
							if ($campaigns->items[$i]->HasVoiceMessageAsset())
								$t->LabelAppend('type', ' '.FA::EmbedCodeFor('phone'));
							if ( $campaigns->items[$i]->Status() =='notfinished' )
								$campaignstatus = 'draft';
							elseif ( $campaigns->items[$i]->Status() =='started' )
								$campaignstatus = 'sent';
							elseif ( $campaigns->items[$i]->Status() =='scheduled' && $campaigns->items[$i]->Starttime()<time())
								$campaignstatus = 'sent';
							else
								$campaignstatus = $campaigns->items[$i]->Status();

							if ($campaigns->items[$i]->Starttime()!=0)
								$campaigntime = $campaigns->items[$i]->Starttime();
							else
								$campaigntime = $campaigns->items[$i]->Addedtime();

							if($campaigns->items[$i]->Status()=='notfinished')
								$t->Label('title', '<a href="index.php?m='.sm_current_module().'&d=campaigndetails&id='.$campaigns->items[$i]->ID().'"><span class="text-dark">'.$campaigns->items[$i]->Title().'</span></a>'.'<div class="secondrow">#'.$campaigns->items[$i]->ID().'<span class="status">' .'</span>'.Formatter::DateTime($campaigntime).'</div>');
							else
								$t->Label('title', '<a href="index.php?m='.sm_current_module().'&d=campaigndetails&id='.$campaigns->items[$i]->ID().'"><span class="text-dark">'.$campaigns->items[$i]->Title().'</span></a>'.'<div class="secondrow">#'.$campaigns->items[$i]->ID().'<span class="status">' .'</span>'.Formatter::DateTime($campaigntime).'</div>');
							$t->Label('status','<span class="status-class">'.$campaignstatus.'</span>', $campaignstatus);
							$t->Label('date', '<p class="compaign-flex">DATE</p><span class="text-dark text-td-compaign">'.Formatter::DateTime($campaigntime).'</span>');
							$t->Label('contactsqty', '<p class="compaign-flex">RECIPIENTS</p><span class="text-recipe text-td-compaign">'.$campaigns->items[$i]->ContactsCount().'</span>');
							$t->Label('openers', '<p class="compaign-flex">OPENERS</p><span class="text-openers text-td-compaign">'.$campaigns->items[$i]->OpenersCount().'</span>') ;
							$t->Label('clickers', '<p class="compaign-flex">CLICKERS</p><span class="text-clickers text-td-compaign">'.$campaigns->items[$i]->ClickerCount().'</span>') ;
							$t->Label('unsubscr', '<p class="compaign-flex">UNSUBSCRIBE</p><span class="text-unscrib text-td-compaign">'.$campaigns->items[$i]->UnsubscribersCount().'</span>') ;
							if($campaigns->items[$i]->Status()=='notfinished')
								{
									$t->Label('action', '<div class="dropdown"><button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button><div class="dropdown-menu dropdown-menu-compaign">' .($campaignstatus=='draft'?'<a class="dropdown-item" href="index.php?m=campaignwizard&action=create&id='.$campaigns->items[$i]->ID().'"><i class="fa-solid fa-pen"></i> Edit</a>':'').'<a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox(\'Do you really want to copy this sequence\', \'index.php?m='.sm_current_module().'&d=clone&id='.$campaigns->items[$i]->ID().'\')"><i class="fa-regular fa-copy"></i> Clone</a><a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox(\'Do you really want to delete this sequence\', \'index.php?m='.sm_current_module().'&d=postdelete&id='.$campaigns->items[$i]->ID().'\')"><i class="fa-regular fa-trash-can"></i> Delete</a></div></div>');
								}
							else
								{
									$t->Label('action', '<div class="dropdown"><button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button><div class="dropdown-menu dropdown-menu-compaign">' .'<a class="dropdown-item" href="index.php?m='.sm_current_module().'&d=campaigndetails&id='.$campaigns->items[$i]->ID().'"><i class="fa-solid fa-earth-americas"></i> Report</a>'.($campaignstatus=='draft'?'<a class="dropdown-item" href="index.php?m=campaignwizard&action=create&id='.$campaigns->items[$i]->ID().'"><i class="fa-solid fa-pen"></i> Edit</a>':'').'<a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox(\'Do you really want to copy this sequence\', \'index.php?m='.sm_current_module().'&d=clone&id='.$campaigns->items[$i]->ID().'\')"><i class="fa-regular fa-copy"></i> Clone</a></div></div>');
								}

							unset($campaigntime);
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->div_close();

					$ui->AddPagebarParams($campaigns->TotalCount(), $limit, $offset);

					$ui->html('</div>');
					$ui->Output(true);
				}

			if (sm_action('postdelete'))
				{
					if(empty($_getvars['id']))
						exit('Access Denied');

					$campaign = new TCampaign($_getvars['id']);
					if(!$campaign->Exists() || $campaign->Status()!='notfinished')
						exit('Access Denied');

					$campaignitems = new TCampaignItemList();
					$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
					$campaignitems->SetFilterCampaign($campaign->ID());
					$campaignitems->Load();

					for ($i=0; $i<$campaignitems->Count(); $i++)
						{
							$campaignitems->items[$i]->Remove();
						}

					$campaignschedulers = new TCampaignScheduleList();
					$campaignschedulers->SetFilterCompany(TCompany::CurrentCompany());
					$campaignschedulers->SetFilterCampaign($campaign->ID());
					$campaignschedulers->Load();

					for ($i=0; $i<$campaignschedulers->Count(); $i++)
						{
							$campaignschedulers->items[$i]->Remove();
						}

					$campaigsequences = new TCampaignSequenceList();
					$campaigsequences->SetFilterCompany(TCompany::CurrentCompany());
					$campaigsequences->SetFilterCampaign($campaign->ID());
					$campaigsequences->Load();

					for ($i=0; $i<$campaigsequences->Count(); $i++)
						{
							$campaigsequences->items[$i]->Remove();
						}

					$campaign->Remove();

					sm_redirect('index.php?m='.sm_current_module());
				}

			if (sm_action('sequences'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Sequences');
					$limit=30;
					$offset=intval($_getvars['from']);
					$campaigns=new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('started', 'scheduled'));
					$campaigns->Limit($limit);
					$campaigns->Offset($offset);
					$campaigns->OrderByAddedTime(false);
					$campaigns->Load();
					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$b = new TButtons();
					$ui->div_open('', 'campaignlist');
					$t = new TGrid();
					$t->AddCol('type', '', '3%');
					$t->AddCol('title', 'Sequences', '90%');
					$t->AddEdit();
					$t->AddDelete();

					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							if($campaigns->items[$i]->Status()=='notfinished')
								$t->RowAddClass('draft');
							$t->Label('type', FA::EmbedCodeFor('envelope'));
							if ($campaigns->items[$i]->HasVoiceMessageAsset())
								$t->LabelAppend('type', ' '.FA::EmbedCodeFor('phone'));

							$campaigntime = $campaigns->items[$i]->Addedtime();

							$t->Label('title', '<b>'.$campaigns->items[$i]->Title().'</b>'.'<div class="secondrow">#'.$campaigns->items[$i]->ID().'<span class=""> Created </span>'.Formatter::DateTime($campaigntime).'</div>');
							$t->URL('edit', 'index.php?m=campaignwizard&action=create&id='.$campaigns->items[$i]->ID());
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$campaigns->items[$i]->ID());
							unset($campaigntime);
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->div_close();

					$ui->AddPagebarParams($campaigns->TotalCount(), $limit, $offset);
					$b->AddButton('add', 'Create Drip', 'index.php?m=campaignwizard&action=create');
					$b->AddClassname('add_asset_button', 'add');
					$ui->Add($b);
					$ui->html('</div>');
					$ui->Output(true);
				}

			if (sm_action('runsequence'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Drip');

					add_path_home();
					add_path('Drips', 'index.php?m=campaigns');
					add_path_current();
					sm_title('Run Drip');

					$limit = 30;
					$offset=intval($_getvars['from']);
					$campaigns=new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('started', 'scheduled'));
					$campaigns->Limit($limit);
					$campaigns->Offset($offset);
					$campaigns->OrderByAddedTime(false);
					$campaigns->Load();
					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$b = new TButtons();
					$ui->div_open('', 'campaignlist runcampaigns');
					$t = new TGrid();
					$t->AddClassnameGlobal('hidehead rounded-with-paddings table-padding-20');
					$t->AddCol('type', '', '1%');
					$t->AddCol('title', 'Drip', '90%');
					$t->AddCol('run', 'Run');

					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							if($campaigns->items[$i]->Status()=='notfinished')
								$t->RowAddClass('draft');
							$t->Label('type', FA::EmbedCodeFor('envelope'));
							if ($campaigns->items[$i]->HasVoiceMessageAsset())
								$t->LabelAppend('type', ' '.FA::EmbedCodeFor('phone'));

							$campaigntime = $campaigns->items[$i]->Addedtime();

							$t->Label('title', '<b>'.$campaigns->items[$i]->Title().'</b>'.'<div class="secondrow">#'.$campaigns->items[$i]->ID().'<span class=""> Created </span>'.Formatter::DateTime($campaigntime).'</div>');
							$t->Label('run', 'Start');
							$t->URL('run', 'index.php?m=campaignwizard&id='.$campaigns->items[$i]->ID());
							unset($campaigntime);
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->div_close();

					$ui->AddPagebarParams($campaigns->TotalCount(), $limit, $offset);
					$b->AddButton('add', 'Create Drip', 'index.php?m=campaignwizard&action=create');
					$b->AddClassname('add_asset_button', 'add');
					$ui->Add($b);
					$ui->html('</div>');
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');

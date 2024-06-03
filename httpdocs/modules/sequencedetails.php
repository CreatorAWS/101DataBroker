<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					add_path_home();
					add_path('Sequences', 'index.php?m=sequencestats');
					sm_title('Sequence '.TCompany::CurrentCompany()->LabelForCustomers());

					$limit=30;
					$offset=intval($_getvars['from']);

					$campaigns = new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$campaigns->ExcludeStatusesArray(Array('notfinished'));
					$campaigns->SetFilterSystemCampaignID($_getvars['id']);
					$campaigns->Load();

					$contacts = new TCampaignItemList();
					$contacts->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$contacts->SetFilterCampaignsIDs($campaigns->ExtractIDsArray());
					$contacts->Load();


					$ui = new TInterface();
					$t = new TGrid();
					$t->AddCol('name', 'Name');
					$t->AddCol('campaign', 'Campaign');
					$t->AddCol('email', 'Email');
					$t->AddCol('phone', 'Phone');
					$t->AddCol('status', 'Status');
					$t->AddCol('email_status', 'Email Status');
					$t->AddCol('sms_status', 'SMS Status');

					for ($i = 0; $i < $contacts->Count(); $i++)
						{
							$t->Label('name', $contacts->items[$i]->Name());
							$t->Label('last_name', $contacts->items[$i]->LastName());
							$campaign = new TCampaign($contacts->items[$i]->CampaignID());
							if($campaign->Exists())
								{
									$t->Label('campaign', $campaign->Title());
									$t->URL('campaign', 'index.php?m=campaigns&d=campaigndetails&id='.$campaign->ID());
								}

							$t->Label('email', $contacts->items[$i]->Email());

							if ($contacts->items[$i]->HasPhone())
								{
									$t->Label('phone', Formatter::USPhone($contacts->items[$i]->Phone()));
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
								$t->Label('phone', '-');

							$t->Label('status', $contacts->items[$i]->Status());
							$t->Label('email_status', $contacts->items[$i]->EmailStatus());
							$t->Label('sms_status', $contacts->items[$i]->SMSStatus());
							$t->NewRow();
						}
					if ($t->RowCount() == 0)
						$t->SingleLineLabel('Nothing found');

					$ui->Add($t);
					$ui->AddPagebarParams($contacts->TotalCount(), $limit, $offset);

					$ui->Output(true);

				}
		}
	else
		sm_redirect('index.php?m=account');
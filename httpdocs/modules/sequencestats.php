<?php
	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if( sm_action('list') )
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Sequences Stats');
					$limit=30;
					$offset=intval($_getvars['from']);
					$campaigns = new TSystemCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('notfinished'));
					$campaigns->Limit($limit);
					$campaigns->Offset($offset);
					$campaigns->OrderByID(false);
					$campaigns->Load();
					$ui = new TInterface();

					$t = new TGrid();
					$t->AddCol('title','Title');
					$t->AddCol('number','Campaigns');
					$t->AddCol('opens','Email Opens');
					$t->AddCol('sent','Contacts');
					$t->AddCol('prospects', 'More Stats');

					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							$t->Label('title', $campaigns->items[$i]->Title());
							$t->Label('number', $campaigns->items[$i]->CampaignsCount());
							$t->Label('opens', $campaigns->items[$i]->OpenersCount());
							$t->Label('sent', $campaigns->items[$i]->ContactsCount());
							$t->Label('prospects', 'View');
							$t->URL('prospects', 'index.php?m=sequencedetails&id='.$campaigns->items[$i]->ID());
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($campaigns->TotalCount(), $limit, $offset);

					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=account');


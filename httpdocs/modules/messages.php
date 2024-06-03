<?php

	if ($userinfo['level']>0)
		{
			/** @var $currentcompany TCompany */

			use_api('temployee');
			use_api('tcustomer');
			sm_default_action('inbox');

			if (sm_action('inbox'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');

					add_path_home();
					add_path('Conversations', 'index.php?m=messages');
					add_path('Messages', sm_this_url());

					sm_title('Messages - Incoming');
					$limit=30;
					$offset=abs(intval($_getvars['from']));
					$ui = new TInterface();
					$data['currmode'] = sm_current_action();
					$ui->html('<div class="tablewrapper messageslist">');

					$ui->AddTPL('messages_header.tpl', '', $data);

					$q=new TQuery('smslog');
					$q->AddWhere('id_company', TCompany::CurrentCompany()->ID());
					$q->AddWhere('is_incoming=1');
					$q->OrderBy('id DESC');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					$t = new TGrid();
					$t->AddCol('date', 'Date');
					$t->AddCol('name', 'Name');
					$t->AddCol('message', 'Message');
					$t->AddCol('notes', 'Notes');
					$t->AddCol('view', '', '16');
					$t->SetHeaderImage('view', 'talk');
					for ($i = 0; $i < $q->Count(); $i++)
						{
							$customer=TCustomer::UsingCache($q->items[$i]['id_customer']);
							if ($customer->isDeleted())
								continue;
							$t->Label('date', strftime($lang['datemask'], $q->items[$i]['timeadded']));
							$t->Label('name', $customer->Name());
							$t->Label('number', Formatter::USPhone($customer->Cellphone()));
							$t->Label('message', $q->items[$i]['text']);
							$t->Label('notes', $customer->Note(true));
							$t->Image('view', 'talk');
							$t->URL(Array('date', 'view'), 'index.php?m=customerdetails&d=conversation&id='.$customer->ID());
							$t->NewRow();
							unset($customer);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($q->TotalCount(), $limit, $offset);
					$ui->html('</div>');
					$ui->Output(true);
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_title('Messages - Outgoing');
					$data['currmode'] = sm_current_action();
					$limit=30;
					$offset=abs(intval($_getvars['from']));
					$ui = new TInterface();

					add_path_home();
					add_path('Conversations', 'index.php?m=messages');
					add_path('Messages', sm_this_url());

					$ui->html('<div class="tablewrapper messageslist">');

					$ui->AddTPL('messages_header.tpl', '', $data);

					$q=new TQuery('smslog');
					$q->AddWhere('id_company', TCompany::CurrentCompany()->ID());
					$q->AddWhere('is_incoming=0');
					$q->AddWhere('type IN ("startmessage", "conversation")');

					$q->Limit($limit);
					$q->Offset($offset);
					$q->OrderBy('id DESC');
					$q->Select();
					$t = new TGrid();
					$t->AddCol('date', 'Date');
					$t->AddCol('name', 'Name');
					$t->AddCol('number', 'Number');
					$t->AddCol('manager', 'Manager');
					$t->AddCol('message', 'Message', '30%');
					$t->AddCol('media', 'Media');
					$t->AddCol('notes', 'Notes');
					$t->AddCol('view', '', '16');
					$t->SetHeaderImage('view', 'talk');
					for ($i = 0; $i < $q->Count(); $i++)
						{
							$customer=TCustomer::UsingCache($q->items[$i]['id_customer']);

							if ($customer->isDeleted())
								continue;

							$t->Label('date', strftime($lang['datemask'], $q->items[$i]['timeadded']));
							$t->Label('name', $customer->Name());
							$t->Label('number', Formatter::USPhone($customer->Cellphone()));
							$t->Label('sales1', TEmployee::withID($customer->SalesPersonID())->Name());
							$t->Label('sales2', TEmployee::withID($customer->SalesPerson2ID())->Name());
							$t->Label('manager', TEmployee::withID($customer->SalesManagerID())->Name());
							$t->Label('message', $q->items[$i]['text']);
							$asset=TAsset::withID($q->items[$i]['id_asset']);
							if ($asset->Exists())
								{
									if ($asset->ThumbExists())
										$t->Image('media', $asset->ThumbURL());
									elseif ($asset->isImage())
										$t->Label('media', 'Image');
									elseif ($asset->isVideo())
										$t->Label('media', 'Video');
									else
										$t->Label('media', 'Download');
									$t->URL('media', $asset->DownloadURL());
								}
							$t->Label('notes', $customer->Note(true));
							$t->Label('vehicle', $customer->VehicleFormatted());
							$t->Image('view', 'talk');
							$t->URL(Array('date', 'view'), 'index.php?m=customerdetails&d=conversation&id='.$customer->ID());
							$t->NewRow();
							unset($customer);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($q->TotalCount(), $limit, $offset);
					$ui->html('</div>');
					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=account');

?>
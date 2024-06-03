<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if (sm_action('postdeletecontact'))
				{
					$customer = new TCustomer($_getvars['id']);
					if (!$customer->Exists())
						exit('Access Denied');
					$list = new TSystemCampaign($_getvars['list']);
					if (!$list->Exists())
						exit('Access Denied');

					$list->UnsetContactID($customer->ID());
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('deletecontacts'))
				{
					$list = new TSystemCampaign($_getvars['list']);
					if (!$list->Exists())
						exit('Access Denied');

					if(is_array($_postvars['ids']) && count($_postvars['ids'])>0)
						{
							for ($i=0; $i<count($_postvars['ids']); $i++)
								{
									$list->UnsetContactID(intval($_postvars['ids'][$i]));
								}

						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postaddcontacts'))
				{
					$list = new TSystemCampaign($_getvars['id']);
					if (!$list->Exists())
						exit('Access Denied');

					if(is_array($_postvars['ids']) && count($_postvars['ids'])>0)
						{
							for ($i=0; $i<count($_postvars['ids']); $i++)
								{
									$customer = new TCustomer($_postvars['ids'][$i]);
									if ($customer->Exists())
										{
											if(!$list->HasContactID($customer->ID()))
												$list->SetContactID($customer->ID());
										}
								}

						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('addcontacts'))
				{
					$listcustomer = new TSystemCampaign($_getvars['id']);
					if (!$listcustomer->Exists())
						exit('Access Denied!');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					use_api('smdatetime');
					use_api('ttaglist');
					sm_add_jsfile('customers.js');
					sm_add_jsfile('ext/datepicker/js/bootstrap-datepicker.js', true);
					sm_add_cssfile('ext/datepicker/css/datepicker.css', true);
					add_path_home();
					add_path('Contact Lists', 'index.php?m=sequencecontacts');
					add_path('Add Contacts', sm_this_url());
					sm_title('Add Contacts');
					$extendedfilters=true;
					$offset=abs(intval($_getvars['from']));
					$limit=intval($_getvars['ipp']);
					if ($limit<30)
						$limit=30;
					$tags=new TTagList();
					$tags->OrderByName();
					$tags->Load();

					$ui = new TInterface();
					$ui->div_open( '', 'contactslist');
					$b=new TButtons();

					$customers = new TCustomerList();
					$customers->SetFilterEnabled();
					$customers->SetFilterCompany($currentcompany);
					if(count($listcustomer->GetCampaignCustomersIDsArray())>0)
						$customers->SetFilterExcludeIDs($listcustomer->GetCampaignCustomersIDsArray());
					if (!empty($_getvars['registeredfrom']))
						{
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredfrom']);
							$customers->SetFilterRegisteredFrom($tmp);
							$extendedfilters=true;
						}
					if (!empty($_getvars['registeredto']))
						{
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredto']);
							$customers->SetFilterRegisteredTo($tmp);
							$extendedfilters=true;
						}
					if (!empty($_getvars['tag']))
						{
							$_getvars['tag'.$_getvars['tag']]=1;
							$_getvars['tag']='';
						}
					$tagsfilter=Array();
					for ($i = 0; $i < $tags->Count(); $i++)
						{
							if (!empty($_getvars['tag'.$tags->items[$i]->ID()]))
								{
									$tmp=$tags->items[$i]->GetCustomerIDsArray();
									$tagsfilter=array_merge($tagsfilter, $tmp);
								}
						}
					if (count($tagsfilter)>0)
						{
							$tagsfilter=array_values(array_unique($tagsfilter));
							$customers->SetFilterIDs($tagsfilter);
							$extendedfilters=true;
						}

					$customers->Limit($limit);
					$customers->Offset($offset);
					$customers->Load();

					if (!$extendedfilters)
						$b->AddToggle('extsrch', 'Extended Search', 'ext-search');
					$b->AddButton('smsblast', 'Add Contacts');
					$b->Style('smsblast', 'float:right; display:none;');
					$b->AddClassname('smsblast', 'smsblast');
					$b->OnClick("$('#smsblastform').submit();", 'smsblast');
					$ui->div_open('ext-search', '', $extendedfilters?'':'display:none');
					$ui->h(3, 'Extended Search');
					$f=new TForm('index.php', '', 'get');
					$f->AddHidden('m', 'sequencecontacts');
					$f->AddHidden('d', 'addcontacts');
					$f->AddHidden('id', $listcustomer->ID());

					$f->AddLabel('tagbar', 'Tags', '');
					$f->HideEncloser();
					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$f->AddCheckbox('tag'.$tags->items[$i]->ID(), $tags->items[$i]->Name());
							$f->SetFieldEndText('tag'.$tags->items[$i]->ID(), $tags->items[$i]->Name());
							$f->HideDefinition();
							if ($i+1 != $tags->Count())
								$f->HideEncloser();
						}
					$f->AddText('registeredfrom', 'Registered After');
					$f->Calendar();
					$f->AddText('registeredto', 'Registered Before');
					$f->Calendar();
					$f->AddSelectVL('ipp', 'Show Items', Array(30, 50, 100, 200, 1000), Array(30, 50, 100, 200, 1000));
					$f->LoadValuesArray($_getvars);
					$f->SaveButton('Search');
					$ui->AddForm($f);
					$ui->div_close();
					$ui->html('<form action="index.php?m='.sm_current_module().'&d=postaddcontacts&id='.$listcustomer->ID().'&returnto='.urlencode('index.php?m=sequencecontacts&d=listdetails&id='.$listcustomer->ID()).'" id="smsblastform" method="post">');
					$ui->AddButtons($b);
					for ($i = 0; $i < $customers->Count(); $i++)
						{
							$data['currenmode'] = 'contactlist';
							$customer = new TCustomer($customers->items[$i]->ID());
							$data['id'] = $customer->ID();
							$data['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();

							$data['initials'] = $customer->Initials();
							$data['first_name'] = $customer->FirstName();
							$data['last_name'] = $customer->LastName();

							if ($customer->HasCellphone())
								$data['cellphone'] = Formatter::USPhone($customer->Cellphone());
							elseif ($customer->HasEmail())
								$data['email'] = $customer->Email();

							if ($customer->isSendingMessagesRejected())
								$data['tags'][] = Array(
									'title' => 'Do Not Text',
									'class' => 'label-danger',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesPending())
								$data['tags'][] = Array(
									'title' => 'Pending Accept Texts',
									'class' => 'label-default',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesNoResponse())
								$data['tags'][] = Array(
									'title' => 'No Response',
									'class' => 'label-warning',
									'url' => 'javascript:;'
								);
							for ($j = 0; $j < $tags->Count(); $j++)
								{
									if ($customer->HasTagID($tags->items[$j]->ID()))
										{
											$data['tags'][] = Array(
												'title' => $tags->items[$j]->Name(),
												'url' => 'index.php?m=customers&d=listview&tag='.$tags->items[$j]->ID()
											);
										}
								}
							$ui->AddTPL('customerslist.tpl', '', $data);
							unset($customer);
							unset($data);
						}
					if ($customers->Count() == 0)
						{
							$data['noinfo'] = 'Nothing Found';
							$ui->AddTPL('customerslist.tpl', '', $data);
						}
					$ui->html('</form>');
					$ui->AddButtons($b);
					$ui->javascript("\$('.at-bulk-checkbox').change(function(){checksmsblast()});");
					$ui->br();
					$ui->div_close();
					$ui->Output(true);
				}

			if (sm_action('listdetails'))
				{
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					sm_add_jsfile('customers.js');
					$listcustomer = new TSystemCampaign($_getvars['id']);
					if (!$listcustomer->Exists())
						exit('Access Denied');

					add_path_home();
					add_path('Contact Lists', 'index.php?m='.sm_current_module().'&d=list');
					if ( $listcustomer->Exists() )
						sm_title('Contact List - '.$listcustomer->Title());
					else
						sm_title('Contact List');

					$offset = abs(intval($_getvars['from']));
					$limit = 30;

					$ui = new TInterface();
					$b=new TButtons();
					$ui->div_open( '', 'contactslist');
					$customers = new TCustomerList();
					$customers->SetFilterEnabled();
					$customers->SetFilterCompany($currentcompany);
					$customers->SetFilterIDs($listcustomer->GetCampaignCustomersIDsArray());
					$customers->OrderByUnreadOrLastUpdate(false);
					$customers->Limit($limit);
					$customers->Offset($offset);
					$customers->Load();

					$b->AddButton('smsblast', 'Bulk Delete');
					$b->Style('smsblast', 'float:right; display:none;');
					$b->AddClassname('smsblast', 'smsblast');
					$b->OnClick("$('#smsblastform').submit();", 'smsblast');

					$ui->AddButtons($b);

					$ui->html('<form action="index.php?m='.sm_current_module().'&d=deletecontacts&list='.$listcustomer->ID().'&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
					for ($i = 0; $i < $customers->Count(); $i++)
						{
							$data['currenmode'] = 'contactlist';
							$customer = new TCustomer($customers->items[$i]->ID());
							$data['id'] = $customer->ID();
							$data['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();
							$data['initials'] = $customer->Initials();
							$data['first_name'] = $customer->FirstName();
							$data['last_name'] = $customer->LastName();
							if ($customer->HasCellphone())
								$data['cellphone'] = Formatter::USPhone($customer->Cellphone());
							elseif ($customer->HasEmail())
								$data['email'] = $customer->Email();
							$data['delete'] = 'index.php?m='.sm_current_module().'&d=postdeletecontact&id='.$customer->ID().'&list='.$listcustomer->ID().'&returnto='.urlencode(sm_this_url());
							if ($customer->isSendingMessagesRejected())
								$data['tags'][] = Array(
									'title' => 'Do Not Text',
									'class' => 'label-danger',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesPending())
								$data['tags'][] = Array(
									'title' => 'Pending Accept Texts',
									'class' => 'label-default',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesNoResponse())
								$data['tags'][] = Array(
									'title' => 'No Response',
									'class' => 'label-warning',
									'url' => 'javascript:;'
								);

							$ui->AddTPL('customerslist.tpl', '', $data);
							unset($customer);
							unset($data);
						}
					if ($customers->Count() == 0)
						{
							$data['noinfo'] = 'Nothing Found';
							$ui->AddTPL('customerslist.tpl', '', $data);
						}
					$ui->html('</form>');
					$ui->javascript("\$('.at-bulk-checkbox').change(function(){checksmsblast()});");
					$ui->AddPagebarParams($customers->TotalCount(), $limit, $offset);
					$ui->br();
					$ui->div_close();
					$ui->Output(true);
				}

			if( sm_action('list') )
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Contact Lists');
					$limit=30;
					$offset=intval($_getvars['from']);
					$campaigns = new TSystemCampaignList();
					$campaigns->SetFilterCompany($currentcompany->ID());
					$campaigns->ExcludeStatusesArray(Array('notfinished'));
					$campaigns->Limit($limit);
					$campaigns->Offset($offset);
					$campaigns->OrderByID(false);
					$campaigns->Load();
					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$t = new TGrid();
					$t->AddCol('title', 'Sequence');
					$t->AddCol('count', 'Contacts Count');
					$t->AddCol('addcontacts', 'Add Contacts');

					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							$t->Label('title', $campaigns->items[$i]->Title());
							$t->URL('title', 'index.php?m='.sm_current_module().'&d=listdetails&id='.$campaigns->items[$i]->ID());
							$t->Label('count', $campaigns->items[$i]->GetCampaignCustomersCount());
							$t->Label('addcontacts', 'Add Contacts');
							$t->URL('addcontacts', 'index.php?m='.sm_current_module().'&d=addcontacts&id='.$campaigns->items[$i]->ID());
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($campaigns->TotalCount(), $limit, $offset);
					$ui->html('</div>');
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=dashboard');

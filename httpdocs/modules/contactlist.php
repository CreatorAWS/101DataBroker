<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if (sm_action('postdelete'))
				{
					$object=new TContactsList(intval($_getvars['id']));
					if (is_object($object) && $object->Exists())
						{

							$customers = new TCustomerList();
							$customers->SetFilterCompany(TCompany::CurrentCompany());
							$customers->SetFilterIDs($object->GetCustomerIDsArray());
							$customers->OrderByUnreadOrLastUpdate(false);
							$customers->Load();

							for ($i=0; $i<$customers->Count(); $i++)
								{
									$object->UnsetContactID($customers->items[$i]->ID());
								}

							$object->Remove();
							sm_notify('List Deleted');
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('deletecontacts'))
				{
					$list = new TContactsList($_getvars['list']);
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
					$list = new TContactsList($_getvars['list']);
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
			if (sm_action('postdeletecontact'))
				{
					$customer = new TCustomer($_getvars['id']);
					if (!$customer->Exists())
						exit('Access Denied');
					$list = new TContactsList($_getvars['list']);
					if (!$list->Exists())
						exit('Access Denied');

					$list->UnsetContactID($customer->ID());
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd') || sm_action('postedit'))
				{
					if(empty($_postvars['title']))
						$error_message = 'Fill required field';

					if(empty($error_message))
						{
							if(sm_action('postedit'))
								{
									$contactlist=new TContactsList(intval($_getvars['id']));
									if (!$contactlist->Exists())
										exit('Access Denied');
								}
							else
								{
									$contactlist = TContactsList::Create(TCompany::CurrentCompany()->ID());
								}
							$contactlist->SetTitle($_postvars['title']);
							sm_notify('List Updated');
							if(sm_action('postadd'))
								sm_redirect('index.php?m='.sm_current_module().'&d=listdetails&id='.$contactlist->ID());
							else
								sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));

				}

			if (sm_action('add') || sm_action('edit'))
				{
					if (sm_action('edit'))
						{
							sm_title('Edit List');
							$contactlist=new TContactsList(intval($_getvars['id']));
							if (!$contactlist->Exists())
								exit('Access Denied');
						}
					else
						sm_title('Add List');

					add_path_home();
					add_path('Contact Lists', 'index.php?m='.sm_current_module());
					add_path_current();

					sm_use('ui.interface');
					sm_use('ui.form');

					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					if (sm_action('edit'))
						$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$contactlist->ID().'&returnto='.urlencode($_getvars['returnto']));
					else
						$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode('index.php?m=contactlist'));
					$f->AddText('title', 'Title', true);
					if (sm_action('edit'))
						$f->SetValue('title', $contactlist->Title());
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->Output(true);
				}

			if (sm_action('postmovecontacts'))
				{
					if(empty($_postvars['contactlist']) || empty($_getvars['list']))
						exit('Access Denied');

					$new_list = new TContactsList($_postvars['contactlist']);
					if (!$new_list->Exists())
						exit('Access Denied');

					if(is_array($_postvars['ids']) && count($_postvars['ids'])>0)
						{
							for ($i=0; $i<count($_postvars['ids']); $i++)
								{
									$customer = new TCustomer($_postvars['ids'][$i]);
									if ($customer->Exists())
										{
											if(!$new_list->HasContactID($customer->ID()))
												$new_list->SetContactID($customer->ID());
										}
								}
						}
					$old_list = new TContactsList($_getvars['list']);
					if (!$old_list->Exists())
						exit('Access Denied');

					if(is_array($_postvars['ids']) && count($_postvars['ids'])>0)
						{
							for ($i=0; $i<count($_postvars['ids']); $i++)
								{
									$old_list->UnsetContactID(intval($_postvars['ids'][$i]));
								}
						}

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('movecontacts'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');

					add_path_home();
					add_path('Contact Lists', 'index.php?m='.sm_current_module());
					add_path_current();

					sm_title('Move Contacts');

					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);

					$contactlists = new TContactsLists();
					$contactlists->SetFilterCompany(TCompany::CurrentCompany());
					$contactlists->SetFilterExcludeIDs(Array($_getvars['list']));
					$contactlists->OrderByTitle(false);
					$contactlists->Load();

					$f = new TForm('index.php?m='.sm_current_module().'&d=postmovecontacts&list='.$_getvars['list'].'&returnto='.urlencode('index.php?m=contactlist'));
					$f->AddSelectVL('contactlist', 'Select Contact List', $contactlists->ExtractIDsArray(), $contactlists->ExtractTitlesArray());
					$ids = $_postvars['ids'];
					foreach($_postvars['ids'] as $value)
						{
							$f->InsertHTML( '<input type="hidden" name="ids[]" value="'. $value. '">');
						}
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->Output(true);
				}

			if (sm_action('listdetails'))
				{
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					sm_add_jsfile('customers.js');
					$listcustomer = new TContactsList($_getvars['id']);
					if ( !$listcustomer->Exists() )
						exit('Access Denied');

					add_path_home();
					add_path('Contact Lists', 'index.php?m='.sm_current_module());
					add_path_current();

					if ( $listcustomer->Exists() )
						sm_title($listcustomer->Title());
					else
						sm_title('Contact List');

					$extendedfilters=false;
					$offset=abs(intval($_getvars['from']));
					$limit=intval($_getvars['ipp']);
					if ($limit < 30)
						$limit = 30;


					$ui = new TInterface();
					$b=new TButtons();
					$ui->div_open( '', 'contactslist');
					$customers = new TCustomerList();
					$customers->SetFilterEnabled();
					$customers->SetFilterCompany(TCompany::CurrentCompany());
					$customers->SetFilterIDs($listcustomer->GetCustomerIDsArray());
					$customers->OrderByUnreadOrLastUpdate(false);
					$customers->Limit($limit);
					$customers->Offset($offset);
					$customers->Load();

					if (!$extendedfilters)
						$b->AddToggle('extsrch', 'Extended Search', 'ext-search');


					$b->AddButton('import', 'Import', 'index.php?m=importcustomers&d=import&id='.$listcustomer->ID());
					$b->AddClassname('add_button_contacts');
					$b->AddButton('selectcontact', 'Select Contact', 'index.php?m='.sm_current_module().'&d=addcontacts&list='.$listcustomer->ID());
					$b->AddClassname('add_button_contacts');
					$b->AddButton('addcontacts', 'Add Contact', 'index.php?m=customers&d=add&contactlist='.$listcustomer->ID().'&returnto='.urlencode(sm_this_url()));
					$b->AddClassname('add_button_contacts');

					$b->AddButton('smsblast', 'Bulk Delete');
					$b->Style('smsblast', 'float:right; display:none;');
					$b->AddClassname('smsblast', 'smsblast');
					$b->OnClick("$('#smsblastform').submit();", 'smsblast');

					$b->AddButton('movecontacts', 'Bulk Move To List');
					$b->Style('movecontacts', 'float:right; display:none;');
					$b->AddClassname('smsblast', 'movecontacts');
					$b->OnClick("$('#smsblastform').attr('action', 'index.php?m=".sm_current_module()."&d=movecontacts&list=".$listcustomer->ID()."&returnto=".urlencode(sm_this_url())."'); $('#smsblastform').submit();", 'movecontacts');


					$ui->div_open('ext-search', '', $extendedfilters?'':'display:none');
					$ui->h(3, 'Extended Search');
					$f=new TForm('index.php', '', 'get');
					$f->AddHidden('m', 'contactlist');
					$f->AddHidden('d', 'listdetails');
					$f->AddHidden('id', $listcustomer->ID());

					$f->HideEncloser();

					$f->AddSelectVL('ipp', 'Items Per Page', Array(30, 50, 100, 200, 1000), Array(30, 50, 100, 200, 1000));
					$f->LoadValuesArray($_getvars);
					$f->SaveButton('Search');
					$ui->AddForm($f);
					$ui->div_close();

					$ui->AddButtons($b);

					$ui->html('<form action="index.php?m='.sm_current_module().'&d=deletecontacts&list='.$listcustomer->ID().'&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
					for ($i = 0; $i < $customers->Count(); $i++)
						{
							$data['currenmode'] = 'contactlist';
							$customer = new TCustomer($customers->items[$i]->ID());
							$data['id'] = $customer->ID();
							$data['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();
							if ($customer->HasProfilePhoto())
								$data['profile_photo'] = $customer->ProfilePhotoURL();
							else
								$data['profile_photo'] = 'ext/images/default-avatar.png';
							$data['profile_change_photo_url'] = 'index.php?m=customers&d=setphoto&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							$data['initials'] = $customer->Initials();
							$data['vehicle_condition'] = $customer->VehicleCondition();
							$data['name'] = $customer->Name();
							$data['boxes'] = Array();
							$data['boxes'][] = Array(
								'label' => 'Marketing Messages',
								'value' => $customer->MarketingMessagesCount()
							);
							if ($customer->HasCellphone())
								$data['cellphone'] = Formatter::Phone($customer->Cellphone());
							if ($customer->HasEmail())
								$data['email'] = $customer->Email();

							$data['info'] = 'Info';
							if ($customer->HasUnreadConversation())
								$data['conversation'] = 'Unread';
							else
								$data['conversation'] = 'View';
							$data['conversation_url'] = 'index.php?m=customers&d=log&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							$data['conversation_url'] = 'index.php?m=customerdetails&d=conversation&id='.$customer->ID();
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
							$import = new TImportCustomersList();
							$import->SetFilterReadyToImport();
							$import->SetFilterCompany(TCompany::CurrentCompany());
							$import->SetFilterContactList($listcustomer->ID());
							$import->Load();
							if($import->TotalCount()>0 && $import->items[0]->Exists())
								$data['noinfo'] = 'Import in Progress';
							else
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

			if (sm_action('addcontacts'))
				{
					$listcustomer = new TContactsList($_getvars['list']);
					if (!$listcustomer->Exists())
						exit('Access Denied!');

					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.form');
					sm_use('ui.fa');
					sm_use('ui.buttons');

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
					add_path('Contact Lists', 'index.php?m='.sm_current_module());
					add_path_current();

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
					$customers->SetFilterCompany(TCompany::CurrentCompany());
					if(count($listcustomer->GetCustomerIDsArray())>0)
						$customers->SetFilterExcludeIDs($listcustomer->GetCustomerIDsArray());
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
					$f->AddHidden('m', 'customers');
					$f->AddHidden('d', 'list');
					$f->AddHidden('list', $listcustomer->ID());

					$f->SelectAddBeginVL('tag', '', '----');
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
					$ui->html('<form action="index.php?m='.sm_current_module().'&d=postaddcontacts&list='.$listcustomer->ID().'&returnto='.urlencode('index.php?m=contactlist&d=listdetails&id='.$listcustomer->ID()).'" id="smsblastform" method="post">');
					$ui->AddButtons($b);
					for ($i = 0; $i < $customers->Count(); $i++)
						{
							$data['currenmode'] = 'contactlist';
							$customer = new TCustomer($customers->items[$i]->ID());
							$data['id'] = $customer->ID();
							$data['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();
							if ($customer->HasProfilePhoto())
								$data['profile_photo'] = $customer->ProfilePhotoURL();
							else
								$data['profile_photo'] = 'ext/images/default-avatar.png';

							$data['initials'] = $customer->Initials();
							$data['vehicle_condition'] = $customer->VehicleCondition();
							$data['name'] = $customer->Name();

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
					$ui->AddPagebarParams($customers->TotalCount(), $limit, $offset);
					$ui->br();
					$ui->div_close();
					$ui->Output(true);
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Contacts List');

					add_path_home();
					add_path_current();

					$limit=30;
					$offset=intval($_getvars['from']);

					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$b = new TButtons();
					$b->AddButton('add', 'Create List', 'index.php?m='.sm_current_module().'&d=add');
					$b->AddClassname('add_button_contacts');
					$ui->AddButtons($b);
					$contactlists = new TContactsLists();
					$contactlists->SetFilterCompany(TCompany::CurrentCompany());
					$contactlists->OrderByTitle(false);
					$contactlists->Offset($offset);
					$contactlists->Limit($limit);
					$contactlists->Load();


					$t = new TGrid();
					$t->AddClassnameGlobal('hidehead rounded-with-paddings underline_hrefs');
					$t->AddCol('title', 'Title');
					$t->AddCol('count', 'Contacts Count');
					$t->AddCol('import', 'Import Contacts');
					$t->AddCol('selectcontacts', 'Select Contacts');
					$t->AddCol('addcontacts', 'Add Contacts');
					$t->AddEdit();
					$t->AddDelete();

					for ($i = 0; $i < $contactlists->Count(); $i++)
						{
							$contactlist = $contactlists->items[$i];
							$customercount = $contactlist->GetCustomerCount();
							$t->Label('title', '<p class="compaign-flex">Title</p><a href="index.php?m='.sm_current_module().'&d=listdetails&id='.$contactlist->ID().'">'.$contactlist->Title().'</a>');
							$t->Label('count','<p class="compaign-flex">Contacts Count</p>'.$customercount);
							$t->Label('import', '<p class="compaign-flex">Import Contacts</p><a href="index.php?m=importcustomers&d=import&id='.$contactlist->ID().'">Import</a>');
							$t->Label('selectcontacts', '<p class="compaign-flex">Select Contacts</p><a href="index.php?m='.sm_current_module().'&d=addcontacts&list='.$contactlist->ID().'">Select Contacts</a>');
							$t->Label('addcontacts', '<p class="compaign-flex">Add Contacts</p><a href="index.php?m=customers&d=add&contactlist='.$contactlist->ID().'&returnto='.urlencode(sm_this_url()).'">Add Contacts</a>');
							$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.$contactlist->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$contactlist->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
							unset($contactlist);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($contactlists->TotalCount(), $limit, $offset);
					$ui->html('</div>');
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');
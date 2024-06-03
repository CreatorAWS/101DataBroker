<?php

	/*
	Module Name: Companies Management
	*/

	if ($userinfo['level'] > 0)
		{
			if( $userinfo['level'] < 3 || empty($_getvars['id']) )
				$company = TCompany::CurrentCompany();
			elseif( !empty($_getvars['id']) )
				$company = new TCompany($_getvars['id']);

			if (sm_action('switchcompany'))
				{
					$company=new TCompany(intval($_getvars['id']));
					if ($company->Exists())
						{
							System::MyAccount()->SetCompanyID($company->ID());
							sm_login(System::MyAccount()->ID());
							sm_notify('Company Changed');
							if (!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto']);
							else
								sm_redirect('index.php');
						}
				}

			if (sm_action('postdeleteall'))
				{
					$company = new TCompany(intval($_getvars['id']));
					if(!$company->Exists())
						exit('Access Denied!');

					$q = new TQuery('appointments');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('campaigns');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('campaigns_items');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('campaigns_schedule');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('campaigns_sequences');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);


					$assets = new TAssetList();
					$assets->SetFilterCompany($company);
					$assets->Load();

					for ($i=0; $i < $assets->Count(); $i++)
						{
							$asset = $assets->items[$i];
							if (file_exists($asset->FilePath()))
								{
									unlink($asset->FilePath());
								}
							if (file_exists($asset->ThumbPath()))
								{
									unlink($asset->ThumbPath());
								}
						}
					unset($q);

					$q = new TQuery('company_assets');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('company_tags');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('contacts_lists');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('customer_fields');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('customer_fields_categories');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('customer_notes');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('customers');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('email_templates');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('import_customers');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('maillog');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('mailqueue');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('message_templates');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('messagelog');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('smslog');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('smslog_pending');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('smsqueue');
					$q->Add('from', $company->Cellphone());
					$q->Remove();
					unset($q);

					$q = new TQuery('template_categories');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('voicequeue');
					$q->Add('from', $company->Cellphone());
					$q->Remove();
					unset($q);

					$q = new TQuery('sm_users');
					$q->Add('id_company', $company->ID());
					$q->Remove();
					unset($q);

					$company->Remove();

					sm_extcore();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postaddcategory', 'posteditcategory'))
				{
					use_api('cleaner');
					sm_extcore();
					$error='';
					if (empty($_postvars['category']))
						$error='Fill required fields';

					if(!$company->Exists())
						exit('Access Denied');

					if (empty($error))
						{
							if (sm_action('postaddcategory'))
								{
									$ctg = TFieldsCategory::Create($company->ID(), $_postvars['category'], $company->CustomerFormTemplate());
									sm_notify('Category added');
								}
							else
								{
									$ctg = new TFieldsCategory($_getvars['id_ctg']);
									if(!$ctg->Exists())
										exit('Access Denied');
									$ctg->SetCategory($_postvars['category']);
									sm_notify('Company updated');
								}
							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postaddcategory'=>'addcategory', 'posteditcategory'=>'editcategory'));
				}

			if (sm_action('addcategory', 'editcategory'))
				{
					add_path_home();
					add_path('Companies Management', 'index.php?m='.sm_current_module().'&d=list');
					add_path('Customer Fields Management', 'index.php?m='.sm_current_module().'&d=customerfields&id='.intval($_getvars['id']));
					add_path_current();
					sm_use('ui.interface');
					sm_use('ui.form');

					if(!$company->Exists())
						exit('Access Denied');

					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('editcategory'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=posteditcategory&id_ctg='.intval($_getvars['id_ctg']).'&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postaddcategory&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					$f->SetColumnsWidth('50%', '50%');
					$f->AddText('category', 'Title', true);

					if (sm_action('editcategory'))
						{
							$ctg = new TFieldsCategory($_getvars['id_ctg']);
							if (!$ctg->Exists() || $ctg->CompanyID()!=$company->ID())
								exit('Access Denied');
							$f->LoadValuesArray($ctg->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('category');
				}


			if (sm_action('postaddfield', 'posteditfield'))
				{
					use_api('cleaner');
					sm_extcore();
					$error='';
					if (empty($_postvars['field']))
						$error='Fill required fields';

					if(!$company->Exists())
						exit('Access Denied');

					$ctg = new TFieldsCategory($_getvars['id_ctg']);
					if(!$ctg->Exists() && $_getvars['id_ctg']!=0)
						exit('Access Denied');

					$templateid = $company->CustomerFormTemplate();

					if (empty($error))
						{
							if (sm_action('postaddfield'))
								{
									$field = TFields::Create($company->ID(), $ctg->ID(), $_postvars['field'], $templateid);
									sm_notify('Field added');
								}
							else
								{
									$field = new TFields($_getvars['id_field']);
									if(!$field->Exists())
										exit('Access Denied');
									$field->SetField($_postvars['field']);
									$field->SetTemplateID($templateid);
									sm_notify('Field updated');
								}
							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postaddfield'=>'addfield', 'posteditfield'=>'editfield'));
				}

			if (sm_action('addfield', 'editfield'))
				{
					add_path_home();
					add_path('Companies Management', 'index.php?m='.sm_current_module().'&d=list');
					add_path('Customer Fields Management', 'index.php?m='.sm_current_module().'&d=customerfields&id='.intval($_getvars['id']));
					add_path_current();
					sm_use('ui.interface');
					sm_use('ui.form');

					if(!$company->Exists())
						exit('Access Denied');

					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('editfield'))
						{
							sm_title($lang['common']['edit'].' Field');
							$f=new TForm('index.php?m='.sm_current_module().'&d=posteditfield&id_ctg='.intval($_getvars['id_ctg']).'&id_field='.intval($_getvars['id_field']).'&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add'].' Field');
							$f=new TForm('index.php?m='.sm_current_module().'&d=postaddfield&id_ctg='.intval($_getvars['id_ctg']).'&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					$f->SetColumnsWidth('50%', '50%');
					$f->AddText('field', 'Title', true);

					if (sm_action('editfield'))
						{
							$field = new TFields($_getvars['id_field']);
							if (!$field->Exists() || $field->CompanyID()!=$company->ID())
								exit('Access Denied');
							$f->LoadValuesArray($field->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('field');
				}

			if (sm_action('discusadditfields'))
				{
					use_api('tcompany');

					if (!$company->Exists())
						exit('Access Denied');
					$field = new TFields($_getvars['field']);
					if (!$field->Exists() || $field->CompanyID() != $company->ID())
						exit('Access Denied');
					$field->DisableField();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('encusadditfields'))
				{
					use_api('tcompany');

					if (!$company->Exists())
						exit('Access Denied');
					$field = new TFields($_getvars['field']);
					if (!$field->Exists() || $field->CompanyID() != $company->ID())
						exit('Access Denied');
					$field->EnableField();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('deletefield'))
				{
					use_api('tcompany');

					if (!$company->Exists())
						exit('Access Denied');
					$field = new TFields($_getvars['id_field']);
					if (!$field->Exists() || $field->CompanyID() != $company->ID())
						exit('Access Denied');
					$field->Remove();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('deletecategory'))
				{
					use_api('tcompany');

					if (!$company->Exists())
						exit('Access Denied');
					$ctg = new TFieldsCategory($_getvars['id_ctg']);
					if (!$ctg->Exists() || $ctg->CompanyID() != $company->ID())
						exit('Access Denied');
					$ctg->Remove();

					$q = new TQuery('customer_fields');
					$q->Add('id_ctg', $ctg->ID());
					$q->Remove();

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('customerfields'))
				{
					if(!$company->Exists())
						exit('Access Denied');

					use_api('tcompany');

					add_path_home();
					add_path_current();

					sm_title('Add Form Set Up Page');
					sm_use('ui.interface');
					sm_use('ui.buttons');
					sm_add_cssfile('customerfields.css');
					$ui = new TInterface();

					$data['currmode'] = sm_current_action();
					$ui->AddTPL('settings_header.tpl', '', $data);

					$b = new TButtons();

					$ui->html('<div class="categorysection"><div class="wrp">');
					$ui->html('<div class="title"><h3>Staff Fields</h3></div>');

					$ui->html('<div class="addbtn btn ab-button"><a href="index.php?m='.sm_current_module().'&d=addfield&id='.$company->ID().'&id_ctg=0&returnto='.urlencode(sm_this_url()).'">Add Tag</a></div>');

					$be = new TButtons();
					$bd = new TButtons();

					$fieldslist_en = new TFieldsList();
					$fieldslist_en->SetFilterCategory(0);
					$fieldslist_en->SetFilterCompany($company);
					$fieldslist_en->SetFilterTemplate($company->CustomerFormTemplate());
					$fieldslist_en->Load();

					for ($j = 0; $j < $fieldslist_en->Count(); $j++)
						{
							/** @var $field TFields */
							$field = $fieldslist_en->items[$j];
							if ($field->isDisabled())
								{
									$bd->Button('[+] / '.$field->Field(), 'index.php?m='.sm_current_module().'&d=encusadditfields&field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
									$bd->AddButton('edit'.$j,'<img src="themes/default/images/editvideoico.png"/>', 'index.php?m=' . sm_current_module() . '&d=editfield&id_field=' . intval($field->ID()) . '&id=' . $company->ID() . '&returnto=' . urlencode(sm_this_url()));
									$bd->AddClassname('edit_delete', 'edit'.$j);
									$bd->AddButton('delete'.$j,'<img src="themes/default/images/deletevideoico.png"/>', 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
									$bd->AddClassname('edit_delete deletebutton', 'delete'.$j);
								}
							else
								{
									$be->Button('[X] ' . $field->Field(), 'index.php?m=' . sm_current_module() . '&d=discusadditfields&field=' . intval($field->ID()) . '&id=' . $company->ID() . '&returnto=' . urlencode(sm_this_url()));
									$be->AddButton('edit'.$j,'<img src="themes/default/images/editvideoico.png"/>', 'index.php?m=' . sm_current_module() . '&d=editfield&id_field=' . intval($field->ID()) . '&id=' . $company->ID() . '&returnto=' . urlencode(sm_this_url()));
									$be->AddClassname('edit_delete', 'edit'.$j);
									$be->AddButton('delete'.$j,'<img src="themes/default/images/deletevideoico.png"/>', 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
									$be->AddClassname('edit_delete deletebutton', 'delete'.$j);
								}
						}
					unset($field);

					if ($be->Count()>0)
						{
							$ui->div('Enabled Fields', '', 'enabled_disabled');
							$ui->Add($be);
						}
					if ($bd->Count()>0)
						{
							$ui->div('Disabled Fields', '', 'enabled_disabled');
							$ui->Add($bd);
						}

					$ui->html('</div></div>');
					$b->Button('Add Category', 'index.php?m='.sm_current_module().'&d=addcategory&id='.intval($_getvars['id']).'&returnto='.urlencode(sm_this_url()));

					$categories = new TFieldsCategoriesList();
					$categories->SetFilterCompany($company);
					$categories->SetFilterTemplate($company->CustomerFormTemplate());
					$categories->Load();

					for ($i = 0; $i < $categories->Count(); $i++)
						{
							/** @var $category TFieldsCategory */
							$category = $categories->items[$i];
							$data[$i]['category']['title'] = $category->Category();
							$data[$i]['category']['addfieldurl'] = 'index.php?m='.sm_current_module().'&d=addfield&id='.$company->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());
							$data[$i]['category']['editor_url'] = 'index.php?m='.sm_current_module().'&d=editcategory&id='.$company->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());
							$data[$i]['category']['delete_url'] = 'index.php?m='.sm_current_module().'&d=deletecategory&id='.$company->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());

							$fieldslist_en = new TFieldsList();
							$fieldslist_en->SetFilterCategory($category);
							$fieldslist_en->SetFilterCompany($company);
							$fieldslist_en->SetFilterTemplate($company->CustomerFormTemplate());
							$fieldslist_en->SetFilterEnabled();
							$fieldslist_en->Load();
							$data[$i]['fields']['enabledcount'] = $fieldslist_en->TotalCount();
							for ($j = 0; $j < $fieldslist_en->Count(); $j++)
								{
									/** @var $field TFields */
									$field = $fieldslist_en->items[$j];
									if($field->CtgID()==0)
										continue;
									$data[$i]['fields']['en'][$j]['title'] = $field->Field();
									$data[$i]['fields']['en'][$j]['url'] = 'index.php?m='.sm_current_module().'&d=discusadditfields&field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['en'][$j]['editor_url'] = 'index.php?m='.sm_current_module().'&d=editfield&id_ctg='.$category->ID().'&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['en'][$j]['delete_url'] = 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
								}
							unset($field);

							$fieldslist_dis = new TFieldsList();
							$fieldslist_dis->SetFilterCategory($category);
							$fieldslist_dis->SetFilterCompany($company);
							$fieldslist_dis->SetFilterTemplate($company->CustomerFormTemplate());
							$fieldslist_dis->SetFilterDisabled();
							$fieldslist_dis->Load();
							$data[$i]['fields']['disabledcount'] = $fieldslist_dis->TotalCount();
							for ($j = 0; $j < $fieldslist_dis->Count(); $j++)
								{
									/** @var $field TFields */
									$field = $fieldslist_dis->items[$j];
									$data[$i]['fields']['dis'][$j]['title'] = $field->Field();
									$data[$i]['fields']['dis'][$j]['url'] = 'index.php?m='.sm_current_module().'&d=encusadditfields&field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['dis'][$j]['editor_url'] = 'index.php?m='.sm_current_module().'&d=editfield&id_ctg='.$category->ID().'&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['dis'][$j]['delete_url'] = 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$company->ID().'&returnto='.urlencode(sm_this_url());
								}
							unset($field);

							unset($category);
							unset($field);
						}
					$ui->AddTPL('customerfields.tpl', '', $data);
					$ui->AddButtons($b);

					$ui->Output(true);
				}

		}
	if ($userinfo['level']>2)
		{
			sm_default_action('list');
			
			if (sm_action('postdelete'))
				{
					$q=new TQuery('companies');
					$q->Add('expiration', time());
					$q->Update('id', intval($_getvars['id']));
					sm_extcore();
					sm_redirect($_getvars['returnto']);
				}
			

			if (sm_action('encustomerfields'))
				{
					use_api('tcompany');
					$company=new TCompany(intval($_getvars['id']));
					if ($company->Exists())
						{
							$company->EnableCustomerField($_getvars['field']);
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('discustomerfields'))
				{
					use_api('tcompany');
					$company=new TCompany(intval($_getvars['id']));
					if ($company->Exists())
						{
							$company->DisableCustomerField($_getvars['field']);
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('postworklogo'))
				{
					use_api('tcompany');
					$comp=new TCompany(intval($_getvars['id']));
					if ($comp->Exists())
						{
							$file=sm_upload_file();
							if ($file===false || !file_exists($file))
								$error='Error uploading file';
							if (empty($error))
								{
									if (!$comp->SetSystemLogoImage($file))
										$error='Error uploading file';
								}
							if (empty($error))
								sm_redirect($_getvars['returnto']);
							else
								sm_set_action('worklogo');
						}
				}
			if (sm_action('postadd', 'postedit'))
				{
					use_api('cleaner');
					sm_extcore();
					$error='';
					if (empty($_postvars['email']) && !empty($_postvars['login']))
						$_postvars['email']=$_postvars['login'].'@clients.'.frontend_domain();

					if (!empty($_postvars['login']))
						$usr=sm_userinfo($_postvars['login'], 'login');

					if (!empty($_postvars['login']))
						$usr1=sm_userinfo($_postvars['email'], 'email');

					if (!empty($_postvars['email']) && !is_email($_postvars['email']))
						$error='Wrong Email Address';

					if (empty($_postvars['name']))
						$error='Fill required fields';
					elseif (sm_action('postadd') && (empty($_postvars['login']) || empty($_postvars['password'])))
						$error='Fill required fields';
					elseif (!empty($usr['id']) && $usr['info']['deleted']==0)
						$error='User with this login exists';
					elseif (!empty($usr1['id'])  && $usr1['info']['deleted']==0)
						$error='User with this email exists';
					if (empty($error))
						{
							$q = new TQuery('companies');
							$q->Add('name', dbescape($_postvars['name']));
							$q->Add('state_search', intval($_postvars['state_search']));
							$q->Add('sic_code_search', intval($_postvars['sic_code_search']));
							$q->Add('builtwith_search', intval($_postvars['builtwith_search']));
							$q->Add('google_search', intval($_postvars['google_search']));
							$q->Add('google_places_api_key', dbescape($_postvars['google_places_api_key']));
							$q->Add('builtwith_api_key', dbescape($_postvars['builtwith_api_key']));

							if($_postvars['expiration']==0)
								$q->Add('expiration', 0);
							else
								$q->Add('expiration', strtotime($_postvars['expiration']));
							if (sm_action('postadd'))
								{
									$dealership_id = $q->Insert();
									$user_id = sm_add_user($_postvars['login'], $_postvars['password'], $_postvars['email']);
									sm_set_userfield($user_id, 'id_company', $dealership_id);
									sm_set_userfield($user_id, 'first_name', $_postvars['first_name']);
									sm_set_userfield($user_id, 'last_name', $_postvars['last_name']);
									sm_notify('Company added');
									$company = new TCompany($dealership_id);
								}
							else
								{
									$company=new TCompany(intval($_getvars['id']));
									$q->Update('id', intval($_getvars['id']));
									sm_notify('Company updated');
								}

							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}

			if (sm_action('add', 'edit'))
				{
					add_path_home();
					add_path('Companies Management', 'index.php?m='.sm_current_module().'&d=list');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('edit'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode($_getvars['returnto']));
						}
					$f->SetColumnsWidth('50%', '50%');
					$f->AddText('name', 'Business Name', true);
					$f->AddText('expiration', 'Expiration Date');
					$f->Calendar('expiration', 'd/M/Y');
					$f->SetFieldBottomText('expiration', '0 - for newer expire');


					$f->Separator('Google Places');
					$f->AddText('google_places_api_key', 'API Key');
					$f->SetFieldBottomText('google_places_api_key', 'leave empty to use Master Google API key');

					$f->Separator('BuiltWith API');
					$f->AddText('builtwith_api_key', 'API Key');
					$f->SetFieldBottomText('builtwith_api_key', 'leave empty to use Master BuiltWith API key');

					$f->Separator('SIC-Code Search');
					$f->AddCheckbox('sic_code_search', 'Turn On Sic-Code Search Service');

					$f->Separator('States Search');
					$f->AddCheckbox('state_search', 'Turn On States Search Service');

					$f->Separator('Google Search');
					$f->AddCheckbox('google_search', 'Turn On Google Search Service');

					$f->Separator('BuiltWith Search');
					$f->AddCheckbox('builtwith_search', 'Turn On BuiltWith Search Service');

					$templates = new TFieldsTemplatesList();
					$templates->ShowAllItemsIfNoFilters();
					$templates->Load();
					if ($templates->TotalCount() > 0)
						{
							$f->Separator('Add Form Template');

							$template_ids=$templates->ExtractIDsArray();
							$template_titles=$templates->ExtractNamesArray();

							for ($j = 0; $j < $templates->Count(); $j++)
								{
									$f->AddSelectVL('id_form_template', 'Form Template', $template_ids, $template_titles);
									$f->SelectAddBeginVL('id_form_template', '', 'None');
								}
						}


					if (sm_action('edit'))
						{
							$company = new TCompany($_getvars['id']);
							if(!$company->Exists())
								exit('Access Denied');

							$f->LoadValuesArray($company->GetRawData());
							if($company->ExpirationTimestamp()!=0)
								$f->SetValue('expiration', Formatter::Date($company->ExpirationTimestamp()));

							$f->SetValue('companyemail', $company->EmailFrom());
						}
					else
						{
							$f->Separator('Setup User');
							$f->AddText('login', 'Login', true);
							$f->AddText('email', 'Email (Leave empty for dummy email)');
							$f->AddText('password', 'Password', true);
							$f->AddText('first_name', 'First Name', true)
								->WithValue('Sales');
							$f->AddText('last_name', 'Last Name', true)
								->WithValue('Manager');
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->AddBlock('Hint');
					$ui->p('Setup twilio SMS with URL '.sm_homepage().'index.php?m=receivesms');
					$ui->Output(true);
					sm_setfocus('name');
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					use_api('formatter');
					add_path_home();
					add_path_current();
					sm_title('Companies Management');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=companieswizard');
					$ui->AddButtons($b);
					$t=new TGrid();
					$t->AddCol('id', 'Id');
					$t->AddCol('name', 'Name');
					$t->AddCol('info', 'Info');
					$t->AddCol('twiliophone', 'Twilio Phone');
					$t->AddCol('expiration', 'Expiration');
					$t->AddCol('actions', 'Actions');
					$t->AddCol('deactivate', '');
					$t->AddCol('login', 'Log in');
					$t->AddEdit();
					$t->AddDelete();
					$q=new TQuery('companies');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<count($q->items); $i++)
						{
							$company=new TCompany($q->items[$i]);
							$t->Label('id', $company->ID());
							$t->Label('name', $company->Name());
							$t->Label('twiliophone', Formatter::USPhone($company->PhoneForConversation()));
							$t->Label('info', 'Info');
							$t->Expand('info');
							$html='';
							$html.='Custom logo - '.($company->HasSystemLogoImageURL()?'<a href="'.$company->SystemLogoImageURL().'" target="_blank">Yes</a>':'No').'<br />';
							$t->ExpanderHTML($html);
							$subdomains=Array();
							$tmp='';
							for ($j = 0; $j<count($subdomains); $j++)
								{
									if ($j>0)
										$tmp.='<br />';
									$tmp.=$subdomains[$j];
								}
							$t->Label('subdomains', $tmp);
							if ($company->ExpirationTimestamp()==0)
								$t->Label('expiration', 'Never Expire');
							else
								$t->Label('expiration', strftime('%m/%d/%Y', $company->ExpirationTimestamp()));
							$t->Label('actions', 'Actions');
							$t->DropDownItem('actions', TCompany::CurrentCompany()->LabelForCustomer().' Form', 'index.php?m='.sm_current_module().'&d=customerfields&id='.$company->ID());
							$t->DropDownItem('actions', 'Users Management', 'index.php?m=usersmgmt&d=list&id='.$company->ID());
							if($company->ExpirationTimestamp()==0 || ($company->ExpirationTimestamp()!=0 && $company->ExpirationTimestamp()>time()))
								{
									$t->Label('deactivate', 'Deactivate');
									$t->CustomMessageBox('deactivate', 'Are you sure?');
									$t->URL('deactivate', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
								}
							else
								{
									$t->Label('deactivate', 'Activate');
									$t->URL('deactivate', 'index.php?m='.sm_current_module().'&d=edit&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
								}
							if ($company->ID() != System::MyAccount()->CompanyID())
								{
									$t->Label('login', '<span class="label label-primary">Log in to Business</span>');
									$t->URL('login', 'index.php?m='.sm_current_module().'&d=switchcompany&id='.$company->ID());
								}
							else
								$t->Label('login', '<span class="label label-success">Current Business</span>');

							$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
							if ($company->ID()!=1)
								$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdeleteall&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));

							$t->NewRow();
							unset($company);
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

		}

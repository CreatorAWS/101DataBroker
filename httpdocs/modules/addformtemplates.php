<?php
	if ($userinfo['level']>2)
		{
			sm_default_action('list');

			if (sm_action('postaddcategory', 'posteditcategory'))
				{
					use_api('cleaner');
					sm_extcore();
					$error='';
					if (empty($_postvars['category']))
						$error='Fill required fields';

					$template = new TFieldsTemplate($_getvars['id']);
					if(!$template->Exists())
						exit('Access Denied');

					if (empty($error))
						{
							if (sm_action('postaddcategory'))
								{
									$ctg = TFieldsCategory::Create(0, $_postvars['category'], $template->ID());
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
					add_path('Form Page Templates', 'index.php?m='.sm_current_module());
					add_path('Add Form Page', 'index.php?m='.sm_current_module().'&d=managefields&id='.intval($_getvars['id']));
					add_path_current();
					sm_use('ui.interface');
					sm_use('ui.form');

					$template = new TFieldsTemplate($_getvars['id']);
					if(!$template->Exists())
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
					$f->AddText('category', 'Title', true);

					if (sm_action('editcategory'))
						{
							$ctg = new TFieldsCategory($_getvars['id_ctg']);
							if (!$ctg->Exists() || $ctg->TemplateID()!=$template->ID())
								exit('Access Denied');
							$f->LoadValuesArray($ctg->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('category');
				}

			if (sm_action('deletefield'))
				{
					$template=new TFieldsTemplate(intval($_getvars['id']));
					if (!$template->Exists())
						exit('Access Denied');
					$field = new TFields($_getvars['id_field']);
					if (!$field->Exists() || $field->TemplateID() != $template->ID())
						exit('Access Denied');
					$field->Remove();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('deletecategory'))
				{
					$template=new TFieldsTemplate(intval($_getvars['id']));
					if (!$template->Exists())
						exit('Access Denied');
					$category = new TFieldsCategory($_getvars['id_ctg']);
					if (!$category->Exists() || $category->TemplateID() != $template->ID())
						exit('Access Denied');

					$fieldslist = new TFieldsList();
					$fieldslist->SetFilterTemplate($template);
					$fieldslist->SetFilterCategory($category->ID());
					$fieldslist->SetFilterCompany(0);
					$fieldslist->Load();
					for ($i=0; $i<$fieldslist->Count(); $i++)
						{
							$fieldslist->items[$i]->Remove();
						}

					$category->Remove();

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postaddfield', 'posteditfield'))
				{
					use_api('cleaner');
					sm_extcore();
					$error='';
					if (empty($_postvars['field']))
						$error='Fill required fields';

					$template = new TFieldsTemplate($_getvars['id']);
					if(!$template->Exists())
						exit('Access Denied');

					$ctg = new TFieldsCategory($_getvars['id_ctg']);
					if(!$ctg->Exists() && $_getvars['id_ctg']!=0)
						exit('Access Denied');

					if (empty($error))
						{
							if (sm_action('postaddfield'))
								{
									$field = TFields::Create(0, $ctg->ID(), $_postvars['field'], $template->ID());
									sm_notify('Field added');
								}
							else
								{
									$field = new TFields($_getvars['id_field']);
									if(!$field->Exists())
										exit('Access Denied');
									$field->SetField($_postvars['field']);
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
					add_path('Form Page Templates', 'index.php?m='.sm_current_module());
					add_path('Add Form Page', 'index.php?m='.sm_current_module().'&d=managefields&id='.intval($_getvars['id']));
					add_path_current();
					sm_use('ui.interface');
					sm_use('ui.form');

					$template = new TFieldsTemplate($_getvars['id']);
					if(!$template->Exists())
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
							if (!$field->Exists() || $field->TemplateID()!=$template->ID())
								exit('Access Denied');
							$f->LoadValuesArray($field->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('field');
				}

			if (sm_action('managefields'))
				{
					$template = new TFieldsTemplate(intval($_getvars['id']));
					if(!$template->Exists())
						exit('Access Denied');

					sm_use('ui.interface');
					sm_use('ui.buttons');
					add_path_home();
					add_path('Form Page Templates', 'index.php?m='.sm_current_module());
					add_path_current();
					sm_title('Add Form Page');
					sm_add_cssfile('customerfields.css');
					$ui = new TInterface();
					$b = new TButtons();

					$ui->html('<div class="categorysection"><div class="wrp">');
					$ui->html('<div class="title"><h3>Staff Fields</h3></div>');

					$ui->html('<div class="addbtn btn ab-button"><a href="index.php?m='.sm_current_module().'&d=addfield&id='.$template->ID().'&id_ctg=0&returnto='.urlencode(sm_this_url()).'">Add Tag</a></div>');
					$ui->html('<div class="fields template_fields">');

					$fieldslist_en = new TFieldsList();
					$fieldslist_en->SetFilterCategory(0);
					$fieldslist_en->SetFilterTemplate($template);
					$fieldslist_en->SetFilterCompany(0);
					$fieldslist_en->Load();

					for ($j = 0; $j < $fieldslist_en->Count(); $j++)
						{
							/** @var $field TFields */
							$field = $fieldslist_en->items[$j];

							$delete_url = 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$template->ID().'&returnto='.urlencode(sm_this_url());
							$ui->html('<div class="ab-button">
								<div class="button-title">'.$field->Field().'</div>
                                <a href="index.php?m=' . sm_current_module() . '&d=editfield&id_field=' . intval($field->ID()) . '&id=' . $template->ID() . '&returnto=' . urlencode(sm_this_url()).'" class="edit"><img src="themes/default/images/editvideoico.png"/></a>
                                <a href="javascript:;" onclick="button_msgbox(\''.$delete_url.'\', \'Are you sure?\');" class="edit"><img src="themes/default/images/deletevideoico.png"/></a>
                            </div>');

						}
					unset($field);

					$ui->html('</div></div></div>');
					$b->Button('Add Category', 'index.php?m='.sm_current_module().'&d=addcategory&id='.intval($_getvars['id']).'&returnto='.urlencode(sm_this_url()));

					$categories = new TFieldsCategoriesList();
					$categories->SetFilterTemplate($template->ID());
					$categories->SetFilterCompany(0);
					$categories->Load();

					for ($i = 0; $i < $categories->Count(); $i++)
						{
							/** @var $category TFieldsCategory */
							$category = $categories->items[$i];
							$data[$i]['category']['title'] = $category->Category();
							$data[$i]['category']['addfieldurl'] = 'index.php?m='.sm_current_module().'&d=addfield&id='.$template->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());
							$data[$i]['category']['editor_url'] = 'index.php?m='.sm_current_module().'&d=editcategory&id='.$template->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());
							$data[$i]['category']['delete_url'] = 'index.php?m='.sm_current_module().'&d=deletecategory&id='.$template->ID().'&id_ctg='.$category->ID().'&returnto='.urlencode(sm_this_url());

							$fieldslist_en = new TFieldsList();
							$fieldslist_en->SetFilterCategory($category);
							$fieldslist_en->SetFilterTemplate($template);
							$fieldslist_en->SetFilterCompany(0);
							$fieldslist_en->Load();
							$data[$i]['fields']['enabledcount'] = $fieldslist_en->TotalCount();
							for ($j = 0; $j < $fieldslist_en->Count(); $j++)
								{
									/** @var $field TFields */
									$field = $fieldslist_en->items[$j];
									if($field->CtgID()==0)
										continue;
									$data[$i]['fields']['en'][$j]['title'] = $field->Field();
									$data[$i]['fields']['en'][$j]['url'] = 'index.php?m='.sm_current_module().'&d=discusadditfields&field='.intval($field->ID()).'&id='.$template->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['en'][$j]['editor_url'] = 'index.php?m='.sm_current_module().'&d=editfield&id_ctg='.$category->ID().'&id_field='.intval($field->ID()).'&id='.$template->ID().'&returnto='.urlencode(sm_this_url());
									$data[$i]['fields']['en'][$j]['delete_url'] = 'index.php?m='.sm_current_module().'&d=deletefield&id_field='.intval($field->ID()).'&id='.$template->ID().'&returnto='.urlencode(sm_this_url());
								}
							unset($field);

							unset($category);
							unset($field);
						}
					$ui->AddTPL('customerfieldstemplate.tpl', '', $data);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

			if (sm_action('postdelete'))
				{
					$template = new TFieldsTemplate(intval($_getvars['id']));
					if(!$template->Exists())
						exit('Access Denied!');
					$template->Remove();

					$categories = new TFieldsCategoriesList();
					$categories->SetFilterTemplate($template->ID());
					$categories->SetFilterCompany(0);
					$categories->Load();
					for ($i=0; $i<$categories->Count(); $i++)
						{
							$categories->items[$i]->Remove();
						}

					$fieldslist = new TFieldsList();
					$fieldslist->SetFilterTemplate($template);
					$fieldslist->SetFilterCompany(0);
					$fieldslist->Load();
					for ($i=0; $i<$fieldslist->Count(); $i++)
						{
							$fieldslist->items[$i]->Remove();
						}

					sm_extcore();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd', 'postedit'))
				{
					$error='';
					if (empty($_postvars['title']))
						$error='Fill required fields';

					if (empty($error))
						{
							if (sm_action('postadd'))
								{
									$template = TFieldsTemplate::Create($_postvars['title']);
									sm_notify('Company added');
								}
							else
								{
									$template = new TFieldsTemplate(intval($_getvars['id']));
									$template->SetTitle($_postvars['title']);
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
					add_path('Form Page Templates', 'index.php?m='.sm_current_module().'&d=list');
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

					$f->AddText('title', 'Title Name', true);
					if (sm_action('edit'))
						{
							$template = new TFieldsTemplate(intval($_getvars['id']));
							if (!$template->Exists())
								exit('Access Denied!');
							$f->LoadValuesArray($template->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
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
					sm_add_cssfile('customerfields.css');

					add_path_home();
					add_path_current();
					sm_title('Form Page Templates');
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$b = new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m='.sm_current_module().'&d=add&returnto='.urlencode(sm_this_url()));
					$ui->AddButtons($b);
					$t = new TGrid();
					$t->AddCol('title', 'Title');
					$t->AddCol('view', 'View', '100px');
					$t->AddEdit();
					$t->AddDelete();

					$templates = new TFieldsTemplatesList();
					$templates->ShowAllItemsIfNoFilters();
					$templates->Limit($limit);
					$templates->Offset($offset);
					$templates->Load();
					for ($i = 0; $i < count($templates->items); $i++)
						{
							$t->Label('title', $templates->items[$i]->Title());
							$t->Label('view', 'View');
							$t->URL('view', 'index.php?m='.sm_current_module().'&d=managefields&id='.$templates->items[$i]->ID());
							$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($templates->TotalCount()==0)
						{
							$t->Label('title', 'Nothing Found');
							$t->AttachEmptyCellsToLeft();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($templates->TotalCount(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=dashboard');
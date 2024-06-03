<?php

	if ( $userinfo['level'] > 0 )
		{
			sm_default_action('messagetemplates');

			if (sm_action('postdeletectg'))
				{
					$category = new TTemplateCategories(intval($_getvars['id']));
					if(!$category->Exists())
						exit('Access Denied!');
					$category->Remove();

					$templates=new TMessageTemplateList();
					$templates->SetFilterCompany(TCompany::CurrentCompany());
					$templates->SetFilterCategory($category->ID());
					$templates->Load();

					for ( $i=0; $i<$templates->Count(); $i++ )
						{
							$templates->items[$i]->SetCategoryID(1);
						}

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postaddctg', 'posteditctg'))
				{
					$error='';
					if (empty($_postvars['title']))
						$error='Fill required fields';

					if (empty($error))
						{
							if (sm_action('postaddctg'))
								{
									$template = TTemplateCategories::Create($_postvars['title']);
									sm_notify('Category added');
								}
							else
								{
									$template = new TTemplateCategories(intval($_getvars['id']));
									$template->SetTitle($_postvars['title']);
									sm_notify('Category updated');
								}
							$template->SetCompanyID(TCompany::CurrentCompany()->ID());
							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postaddctg'=>'addctg', 'posteditctg'=>'editctg'));
				}

			if (sm_action('addctg', 'editctg'))
				{
					add_path_home();
					add_path('Categories', 'index.php?m='.sm_current_module().'&d=messagetemplatectgs');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('editctg'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=posteditctg'.($_getvars['type']=='email'?'&type=email':'').'&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postaddctg'.($_getvars['type']=='email'?'&type=email':'').'&returnto='.urlencode($_getvars['returnto']));
						}

					$f->AddText('title', 'Title Name', true);
					if (sm_action('editctg'))
						{
							$category = new TTemplateCategories(intval($_getvars['id']));
							if (!$category->Exists() || $category->CompanyID()!=TCompany::CurrentCompany()->ID())
								exit('Access Denied!');
							$f->LoadValuesArray($category->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title');
				}


			if (sm_action('messagetemplatectgs'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_title('Message Templates');
					$ui = new TInterface();
					$limit=30;
					$offset=intval($_getvars['from']);

					add_path_home();
					add_path_current();

					if ($_getvars['type']=='email')
						$data['currmode'] = 'emails';
					else
						$data['currmode'] = sm_current_action();

					$ui->AddTPL('templates_header.tpl', '', $data);

					$templates=new TTemplateCategoriesList();
					$templates->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
					$templates->Offset($offset);
					$templates->Limit($limit);
					$templates->Load();

					$t=new TGrid();
					$t->AddCol('category', 'Category');
					$t->AddCol('count', 'Templates');
					$t->AddEdit();
					$t->AddDelete();

					for ($i = 0; $i < $templates->Count(); $i++)
						{
							$t->Label('category', $templates->items[$i]->Title());
							if ($_getvars['type']=='email')
								$t->URL('category', 'index.php?m='.sm_current_module().'&d=emailtemplates&id_ctg='.$templates->items[$i]->ID());
							else
								$t->URL('category', 'index.php?m='.sm_current_module().'&d=messagetemplates&id_ctg='.$templates->items[$i]->ID());
							if ($_getvars['type']=='email')
								$t->Label('count', $templates->items[$i]->EmailTemplatesCount());
							else
								$t->Label('count', $templates->items[$i]->TextTemplatesCount());

							if(!empty($templates->items[$i]->CompanyID()))
								{
									$t->URL('edit', 'index.php?m='.sm_current_module().'&d=editctg&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
									$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdeletectg&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
								}

							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('No message templates yet');
					$ui->Add($t);
					$ui->AddPagebarParams($templates->TotalCount(), $limit, $offset);
					$b=new TButtons();
					if ($_getvars['type']=='email')
						$b->Button('Add Category', 'index.php?m='.sm_current_module().'&d=addctg&type=email&returnto='.urlencode(sm_this_url()));
					else
						$b->Button('Add Category', 'index.php?m='.sm_current_module().'&d=addctg&returnto='.urlencode(sm_this_url()));
					$ui->Add($b);
					$ui->Output(true);
				}

			if (sm_action('messagetemplates'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_add_cssfile('templatectgs.css');
					if (empty(intval($_getvars['id_ctg'])))
						$id_category = 1;
					else
						$id_category = intval($_getvars['id_ctg']);

					$templatectg = new TTemplateCategories($id_category);
					if( !$templatectg->Exists() )
						exit('Access Denied');

					add_path_home();
					add_path('Text Templates', sm_this_url());
					sm_title($templatectg->Title());
					add_path_current();

					$ui = new TInterface();

					$ui->AddTPL('templates_header.tpl');

					$ui->div_open('', 'embed_grid');
					$ui->div_open('', 'socialsharewrap settingspage addcustomer');

					$limit = 30;
					$offset = intval($_getvars['from']);

					$templatectgs = new TTemplateCategoriesList();
					$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
					$templatectgs->OrderByTitle();
					$templatectgs->Load();

					for ($i = 0; $i < $templatectgs->Count(); $i++)
						{
							/** @var  $category TTemplateCategories */
							$category = $templatectgs->Item($i);
							$data[$i]['title'] = $category->Title();
							$data[$i]['url'] = 'index.php?m='.sm_current_module().'&d=messagetemplates&id_ctg='.$category->ID();
							$data[$i]['id'] = $category->ID();
							$data[$i]['selected'] = $category->ID() == $id_category;
							$data[$i]['count'] = $category->TextTemplatesCount();
						}
					$ui->div_open('', '', 'display:flex;');
					$ui->div_open('', 'col-md-4');
					$ui->AddTPL('categories_list.tpl', '', $data);
					$ui->div_close();
					$ui->div_open('', 'col-md-8');

					$templates = new TMessageTemplateList();
					$templates->SetFilterCompany(TCompany::CurrentCompany());
					$templates->SetFilterCategory($templatectg->ID());
					$templates->Offset($offset);
					$templates->Limit($limit);
					$templates->Load();

					$t=new TGrid();
					$t->AddCol('text', 'Text', '60%');
					$t->AddCol('image', 'Image', '15%');
					$t->AddCol('preview', 'Preview', '25%');
					$t->AddEdit();
					$t->AddDelete();
					for ($i = 0; $i < $templates->Count(); $i++)
						{
							$t->Label('text', $templates->items[$i]->Text());
							if (!$templates->items[$i]->HasAssetID())
								$t->Label('image', '-');
							else
								{
									$asset=TAsset::withID($templates->items[$i]->AssetID());
									if ($asset->Exists())
										{
											if ($asset->ThumbExists())
												$t->Image('image', $asset->ThumbURL());
											else
												$t->Label('image', $asset->FileNameWithComment());
										}
									else
										$t->Label('image', 'No image found. Please attach a new image from assets');
									unset($asset);
								}
							$t->Label('preview', 'Send Preview Message');
							$t->URL('preview', 'index.php?m=sendpreview&d=sms&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));

							$t->URL('edit', 'index.php?m=messagetemplates&d=edit&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=messagetemplates&d=postdelete&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('No message templates yet');
					$ui->Add($t);
					$ui->AddPagebarParams($templates->TotalCount(), $limit, $offset);
					$b=new TButtons();
					$b->Button('Add Template', 'index.php?m=messagetemplates&d=add&id_ctg='.$_getvars['id_ctg'].'&returnto='.urlencode(sm_this_url()));
					$ui->Add($b);
					$ui->div_close();
					$ui->div_close();

					$ui->div_close();
					$ui->div_close();


					$ui->Output(true);
				}

			if (sm_action('emailtemplates'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					sm_add_cssfile('templatectgs.css');

					if (empty(intval($_getvars['id_ctg'])))
						$id_category = 1;
					else
						$id_category = intval($_getvars['id_ctg']);

					add_path_home();

					$templatectg = new TTemplateCategories($id_category);
					if( !$templatectg->Exists() )
						exit('Access Denied');
					add_path('Email Templates', sm_this_url());
					sm_title($templatectg->Title());
					add_path_current();

					$limit=30;
					$offset=intval($_getvars['from']);
					$ui = new TInterface();

					$ui->AddTPL('templates_header.tpl', '', $data);

					$templatectgs = new TTemplateCategoriesList();
					$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
					$templatectgs->OrderByTitle();
					$templatectgs->Load();

					for ($i = 0; $i < $templatectgs->Count(); $i++)
						{
							/** @var  $category TTemplateCategories */
							$category = $templatectgs->Item($i);
							$data[$i]['title'] = $category->Title();
							$data[$i]['url'] = 'index.php?m='.sm_current_module().'&d=emailtemplates&id_ctg='.$category->ID();
							$data[$i]['id'] = $category->ID();
							$data[$i]['selected'] = $category->ID() == $id_category;
							$data[$i]['count'] = $category->EmailTemplatesCount();
						}

					$ui->div_open('', 'embed_grid');
					$ui->div_open('', 'socialsharewrap settingspage addcustomer');

					$ui->div_open('', '', 'display:flex;');
					$ui->div_open('', 'col-md-4');
					$ui->AddTPL('categories_list.tpl', '', $data);
					$ui->div_close();
					$ui->div_open('', 'col-md-8');

					if(!empty($error))
						$ui->NotificationError($error);

					$templates = new TEmailTemplateList();
					$templates->SetFilterCompany(TCompany::CurrentCompany());
					$templates->SetFilterCategory($id_category);
					$templates->Offset($offset);
					$templates->Limit($limit);
					$templates->Load();
					$t = new TGrid();
					$t->AddCol('title', 'Title');
					$t->AddCol('subject', 'Subject');
					$t->AddCol('preview', 'Preview');
					$t->AddEdit();
					$t->AddDelete();
					for ($i = 0; $i < $templates->Count(); $i++)
						{
							$t->Label('title', $templates->items[$i]->Title());
							$t->Label('subject', $templates->items[$i]->Subject());
							$t->Label('preview', 'Send Preview Email');
							$t->URL('preview', 'index.php?m=sendpreview&d=email&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('edit', 'index.php?m=emailtemplates&d=edit&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=emailtemplates&d=postdelete&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($t->RowCount() == 0)
						$t->SingleLineLabel('No email templates yet');
					$ui->Add($t);
					$ui->AddPagebarParams($templates->TotalCount(), $limit, $offset);
					$b = new TButtons();
					$b->Button('Add Template', 'index.php?m=emailtemplates&d=add&returnto='.urlencode(sm_this_url()));
					$ui->Add($b);
					$ui->div_close();
					$ui->div_close();

					$ui->div_close();
					$ui->div_close();

					$ui->Output(true);
				}


		}
	else
		sm_redirect('index.php?m=dashboard');
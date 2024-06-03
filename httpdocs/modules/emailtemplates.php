<?php

	if ($userinfo['level'] > 0)
		{
			if (sm_action('postdelete'))
				{
					$template = new TEmailTemplate(intval($_getvars['id']));
					if ($template->Exists() && $template->CompanyID() == TCompany::CurrentCompany()->ID())
						{
							$template->Remove();
							sm_notify('Template deleted');
						}
					sm_redirect($_getvars['returnto']);
				}
			if (sm_action('postadd', 'postedit'))
				{
					if (sm_action('postedit'))
						{
							$template = new TEmailTemplate(intval($_getvars['id']));
							if (!$template->Exists())
								$error_message = 'Template not found';
							elseif ($template->CompanyID() != TCompany::CurrentCompany()->ID())
								exit('Access denied! EA-56-0823');
						}
					if (empty($error_message) && (empty($_postvars['title']) || empty($_postvars['subject']) || empty($_postvars['text'])))
						$error_message = 'Fill required fields';
					if (empty($error_message))
						{
							if (empty($_postvars['category']))
								$_postvars['category'] = 1;

							if (sm_action('postadd'))
								$template = TEmailTemplate::Create();
							$template->SetTitle($_postvars['title']);
							$template->SetSubject($_postvars['subject']);
							$template->SetMessage($_postvars['text']);
							$template->SetCategoryID($_postvars['category']);
							if (sm_action('postedit'))
								sm_notify('Template updated');
							else
								sm_notify('Template addded');
							sm_redirect($_getvars['returnto']);
						}
					if (!empty($error_message))
						sm_set_action(Array('postadd' => 'add', 'postedit' => 'edit'));
				}
			if (sm_action('add', 'edit'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					add_path_home();
					add_path('Categories', 'index.php?m=settings&d=messagetemplatectgs&type=email');
					sm_title(sm_action('add') ? 'Add Template' : 'Edit Template');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);

					if (!empty($_getvars['id_ctg']))
						{
							$category = new TTemplateCategories($_getvars['id_ctg']);
							if(!$category->Exists())
								exit('Access Denied');
						}

					$assets = new TAssetList();
					$assets->SetFilterCompany(TCompany::CurrentCompany());
					$assets->SetFilterPublic();
					$assets->Load();
					for($i=0; $i < $assets->Count(); $i++)
						{
							if($assets->items[$i]->isImage())
								{
									$assets_ids[] = $assets->items[$i]->ImagePath();
									$assets_titles[] = $assets->items[$i]->FileNameWithComment();
								}
						}
					if (sm_action('add'))
						$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode($_getvars['returnto']));
					else
						{
							$template = new TEmailTemplate(intval($_getvars['id']));
							if ($template->CompanyID() != TCompany::CurrentCompany()->ID())
								exit('Access denied! EA-56-0823');
							$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$template->ID().'&returnto='.urlencode($_getvars['returnto']));
						}
					$f->AddText('title', 'Title (will not be send in email - using for convenience)', true)
					  ->SetFocus();

					$categories = new TTemplateCategoriesList();
					$categories->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
					$categories->Load();

					$f->AddSelectVL('category', 'Template Category', $categories->ExtractIDsArray(), $categories->ExtractTitlesArray());
					if(sm_action('edit') && !empty($template->CategoryID()))
						$f->SetValue('category', $template->CategoryID());
					elseif (!empty($_getvars['id_ctg']))
						$f->SetValue('category', $category->ID());

					$f->SelectAddBeginVL('category', '', 'None');

					//$f->AddEditor('message', 'Message');
					$data['getimageslisturl'] = 'index.php?m=settings&d=getimageslistajax&theonepage=1';
					$f->InsertTPL('sendemailform.tpl', $data);

					// $f->AddEditor('message', 'Message<div style="font-size: smaller"><br /><br />Available tags:<br />{FIRST_NAME} - first name of recipient<br />{LAST_NAME} - last name of recipient<br />{CONTACT_NAME} - full name of recipient<br />{CONTACT_BUSINESS_NAME} - recipient business name <br />{EMAIL} - email of recipient<br />{CELLPHONE} - cell phone of recipient<br />{BUSINESS} - company name<br />{BUSINESS_CELLPHONE} - company phone </div>');
					if (is_object($template))
						{
							$f->SetValue('title', $template->Title());
							$f->SetValue('subject', $template->Subject());
							$f->SetValue('text', $template->Message());
							$m['email']['subject'] = $template->Subject();
							$m['email']['text'] = $template->Message();
						}
					$f->SaveButton('Save');
					$f->LoadValuesArray($_postvars);

					if (!empty($_postvars))
						{
							$m['email']['subject'] = $_postvars['subject'];
							$m['email']['text'] = $_postvars['text'];
						}

					$ui->Add($f);
					$ui->Output(true);
				}
		}
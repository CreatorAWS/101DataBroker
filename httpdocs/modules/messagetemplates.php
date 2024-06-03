<?php

	if ($userinfo['level'] > 0)
		{
			if (sm_action('postdelete'))
				{
					$template = new TMessageTemplate(intval($_getvars['id']));
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
							$template = new TMessageTemplate(intval($_getvars['id']));
							if (!$template->Exists())
								$error_message='Template not found';
							elseif ($template->CompanyID() != TCompany::CurrentCompany()->ID())
								exit('Access denied! EA-56-0823');
						}
					if (empty($error_message) && empty($_postvars['text']))
						$error_message='Fill required fields';

					if ((TCompany::CurrentCompany()->isUnsubscribeMessageSet() && strlen($_postvars['text'])>142) || (!TCompany::CurrentCompany()->isUnsubscribeMessageSet() && strlen($_postvars['text'])>160))
						$error_message='Message too long';

					if (empty($error_message))
						{
							if (sm_action('postadd'))
								$template=TMessageTemplate::Create();
							$template->SetText($_postvars['text']);
							$asset=new TAsset(intval($_postvars['asset']));
							if ($asset->Exists() && $asset->CompanyID()==TCompany::CurrentCompany()->ID())
								$template->SetAssetID($asset->ID());

							$template->SetCategoryID($_postvars['category']);

							if (sm_action('postedit'))
								sm_notify('Template updated');
							else
								sm_notify('Template addded');
							sm_redirect($_getvars['returnto']);
						}
					if (!empty($error_message))
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}
			if (sm_action('add', 'edit'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
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
					$assets->Load();
					if (sm_action('add'))
						$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode($_getvars['returnto']));
					else
						{
							$template = new TMessageTemplate(intval($_getvars['id']));
							if ($template->CompanyID() != TCompany::CurrentCompany()->ID())
								exit('Access denied! EA-56-0823');
							$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$template->ID().'&returnto='.urlencode($_getvars['returnto']));
						}
					//	$f->AddText('text', 'Message', true)
					$f->AddText('text', 'Message<sup class="adminform-required">*</sup>')
					  ->SetFocus();
					// $f->AddText('text', 'Message<sup class="adminform-required">*</sup><div style="font-size: smaller">Available tags:<br />{FIRST_NAME} - first name of recipient<br />{LAST_NAME} - last name of recipient<br />{CONTACT_NAME} - full name of recipient<br />{CONTACT_BUSINESS_NAME} - recipient business name<br />{EMAIL} - email of recipient<br />{CELLPHONE} - cell phone of recipient<br />{BUSINESS} - company name<br />{BUSINESS_CELLPHONE} - company phone  </div>')
					//   ->SetFocus();

					$categories = new TTemplateCategoriesList();
					$categories->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
					$categories->Load();

					$f->AddSelectVL('category', 'Template Category', $categories->ExtractIDsArray(), $categories->ExtractTitlesArray());
					if(sm_action('edit') && !empty($template->CategoryID()))
						$f->SetValue('category', $template->CategoryID());
					elseif (!empty($_getvars['id_ctg']))
						$f->SetValue('category', $category->ID());

					$f->SelectAddBeginVL('category', '', 'None');
					$f->AddSelectVL('asset', 'Attached Image', Array('0'), Array('- No attachment -'));
					for ($i = 0; $i < $assets->Count(); $i++)
						{
							if (!$assets->items[$i]->isImage() || !$assets->items[$i]->isEligibleForMMS())
								continue;
							$f->SelectAddEndVL('asset', $assets->items[$i]->ID(), $assets->items[$i]->FileNameWithComment());
						}
					if (is_object($template))
						{
							$f->SetValue('text', $template->Text());
							$f->SetValue('asset', $template->AssetID());
						}
					$f->SaveButton('Save');
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->Output(true);
				}
		}
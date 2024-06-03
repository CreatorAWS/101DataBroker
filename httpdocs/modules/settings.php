<?php

	if ( $userinfo['level'] > 0 )
		{
			if (sm_action('getavailabletags'))
				{
					$data['menu'] = '{FIRST_NAME} {LAST_NAME} {CONTACT_NAME} {EMAIL} {CELLPHONE} {BUSINESS} {BUSINESS-CELLPHONE}';
					$data['availabletags']['ids'] = ['{FIRST_NAME}', '{LAST_NAME}', '{CONTACT_NAME}', '{EMAIL}', '{CELLPHONE}', '{BUSINESS}', '{BUSINESS-CELLPHONE}'];
					$data['availabletags']['titles'] = ['{FIRST_NAME} - first name of recipient', '{LAST_NAME} - last name of recipient', '{CONTACT_NAME} - full name of recipient', '{EMAIL} - email of recipient', '{CELLPHONE} - cell phone of recipient', '{BUSINESS} - company name', '{BUSINESS-CELLPHONE} - company phone'];

					exit(json_encode($data));
				}

			if (sm_action('getimageslistajax'))
				{
					$assets = new TAssetList();
					$assets->SetFilterCompany(TCompany::CurrentCompany());
					$assets->SetFilterPublic();
					$assets->Load();

					for($i=0; $i < $assets->Count(); $i++)
						{
							if($assets->items[$i]->isImage())
								{
									$data['asset_ids'][] = $assets->items[$i]->ImagePath();
									$data['asset_titles'][] = $assets->items[$i]->FileNameWithComment();
								}
						}
					exit(json_encode($data));
				}


			sm_default_action('messagetemplates');

			if (sm_action('savecompliancemessages'))
				{
					TCompany::CurrentCompany()->SetInitialComplianceMessageText($_postvars['compliance_text']);
					TCompany::CurrentCompany()->SetInitialComplianceMessageName($_postvars['compliance']);

					sm_notify('Settings updated');
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m='.sm_current_module().'&d=settings');
				}

			if (sm_action('compliancemessage'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					add_path_home();
					add_path_current();

					sm_title('Compliance Message');
					$ui = new TInterface();
					$data['currmode']='compliancemessage';
					$ui->AddTPL('settings_header.tpl', '', $data);
					$f = new TForm('index.php?m='.sm_current_module().'&d=savecompliancemessages&returnto='.urlencode('index.php?m='.sm_current_module().'&d='.sm_current_action()));

					$select_val = array('first_name', 'first_and_last_name', 'anonymous');
					$select_labels = array('First Name', 'Fist and Last Name', 'Staff member(for anonymous)');
					$f->AddSelectVL('compliance', 'Staff memeber representation', $select_val, $select_labels);
					$f->SetValue('compliance', $currentcompany->InitialComplianceMessageName());
					$f->AddTextarea('compliance_text', 'Message');
					$f->SetFieldBottomText('compliance_text', 'Available tags: {NAME} - Staff memeber name, {BUSINESS} - Company Name');

					if ($currentcompany->HasInitialComplianceMessageText())
						$initial_message_text = $currentcompany->InitialComplianceMessageText();
					else
						$initial_message_text = '{NAME} from {BUSINESS} would like to ask you a question.';
					$f->SetValue('compliance_text', $initial_message_text);
					/** @var $myaccount TEmployee */
					if($currentcompany->InitialComplianceTagFirstName())
						$contact = $myaccount->FirstName();
					elseif($currentcompany->InitialComplianceTagFirstLastName())
						$contact = $myaccount->Name();
					else
						$contact = 'Staff memeber';
					$preview=str_replace('{NAME}', $contact, $initial_message_text);
					$preview=str_replace('{BUSINESS}', $currentcompany->Name(), $preview);
					$f->SetValue('initial_asset', TCompany::CurrentCompany()->InitialAssetID());
					if(strlen($preview.' Reply with Yes to accept or No to decline.')>160)
						$ui->NotificationError('The message body exceeds the 160 character limit.');
					$f->InsertHTML('Preview: '.$preview.' Reply with Yes to accept or No to decline.');
					$f->SaveButton('Preview & Save');
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);

					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');

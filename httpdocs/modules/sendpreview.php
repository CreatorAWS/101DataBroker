<?php
	if ($userinfo['level'] > 0)
		{

			function SendEmailFromTemplate($email, $template_id)
				{
					$template=new TEmailTemplate($template_id);
					if (!$template->Exists())
						return false;
					$company=TCompany::UsingCache(TCompany::CurrentCompany()->ID());
					$template->ReplaceKeyVal('FIRST_NAME', 'John');
					$template->ReplaceKeyVal('LAST_NAME', 'Smith');
					$template->ReplaceKeyVal('CONTACT_NAME', 'John Smith');
					$template->ReplaceKeyVal('CONTACT_BUSINESS_NAME', 'John Smith Inc.');
					$template->ReplaceKeyVal('OWNER', $company->Owner());
					$template->ReplaceKeyVal('BUSINESS', $company->Name());
					$template->ReplaceKeyVal('BUSINESS_CELLPHONE', $company->Cellphone());
					$template->ReplaceKeyVal('EMAIL', 'john_smith@test.mail');
					$template->ReplaceKeyVal('CELLPHONE', '1111111111');
					$template->ReplaceUninitializedKeys();
					EmailMessages::QueueEmail($company, $email, $template->Subject(), $template->Message());
					return true;
				}

			function replace_tags_campaign($template)
				{
					$str=str_replace('{FIRST_NAME}', 'John', $template);
					$str=str_replace('{LAST_NAME}', 'Smith', $str);
					$str=str_replace('{CONTACT_NAME}', 'John Smith', $str);
					$str=str_replace('{CONTACT_BUSINESS_NAME}', 'John Smith Inc.', $str);
					$str=str_replace('{EMAIL}', 'john_smith@test.mail', $str);
					$str=str_replace('{CELLPHONE}','1111111111', $str);
					$str=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $str);
					$str=str_replace('{BUSINESS_CELLPHONE}', TCompany::CurrentCompany()->Cellphone(), $str);
					$str=str_replace('{OWNER}', '', $str);
					return $str;
				}

			if (sm_action('postsendemail'))
				{
					$template = new TEmailTemplate(intval($_getvars['id']));
					if (!$template->Exists())
						$error_message = 'Template not found';
					elseif ($template->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access denied! RDEA-22-0581');

					if (empty($error_message) && (empty($_postvars['email'])))
						$error_message = 'Fill required fields';
					elseif (!empty($_postvars['email']) && !is_email($_postvars['email']))
						$error_message = 'Wrong Email Address Format';
					if (empty($error_message))
						{
							SendEmailFromTemplate($_postvars['email'], $template->ID());
							sm_redirect($_getvars['returnto']);
						}
					if (!empty($error_message))
						sm_set_action('email');
				}


			if (sm_action('email'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_title('Send Preview Email');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$template = new TEmailTemplate(intval($_getvars['id']));
					if ($template->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access denied! RDEA-15-0563');
					$f = new TForm('index.php?m='.sm_current_module().'&d=postsendemail&id='.$template->ID().'&returnto='.urlencode($_getvars['returnto']));
					$f->AddText('email', 'Enter your email address', true)
					  ->SetFocus();
					$f->SaveButton('Send Preview Email');
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->Output(true);
				}

			if (sm_action('postsendmessage'))
				{
					$message=new TMessageTemplate(intval($_getvars['id']));
					if (!$message->Exists())
						$error_message = 'Template not found';
					elseif ($message->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access denied! SMEA-155-0345');

					if (empty($error_message) && (empty($_postvars['phonenumber'])))
						$error_message = 'Fill required fields';
					elseif (!empty($_postvars['phonenumber']) && !Validator::USPhone($_postvars['phonenumber']))
						$error_message = 'Phone number is not valid!';
					$asset=new TAsset($message->AssetID());
					if($asset->Exists())
						$attachments[]=sm_homepage().$asset->DownloadURL();
					if (empty($error_message))
						{
							queue_sms($_postvars['phonenumber'], replace_tags_campaign($message->Text()), TCompany::CurrentCompany()->Cellphone(), 0, $attachments);
							sm_redirect($_getvars['returnto']);
						}
					if (!empty($error_message))
						sm_set_action('sms');
				}

			if (sm_action('sms'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_title('Send Preview Message');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$message=new TMessageTemplate(intval($_getvars['id']));
					if ($message->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access denied! SMEA-12-57423');
					$f = new TForm('index.php?m='.sm_current_module().'&d=postsendmessage&id='.$message->ID().'&returnto='.urlencode($_getvars['returnto']));
					$f->AddText('phonenumber', 'Enter your phone number', true)
					  ->SetFocus();
					$f->SaveButton('Send Preview Message');
					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->Output(true);
				}
		}
<?php

	if (is_object(TCompany::CurrentCompany()) && TCompany::CurrentCompany()->Exists())
		{
			/** @var $currentcompany TCompany */
			/** @var $myaccount TEmployee */

			use_api('temployee');
			sm_default_action('view');

			if (sm_actionpost('save'))
				{
					sm_notify('Settings updated');
					sm_redirect('index.php?m='.sm_current_module().'&d=view');
				}
			if (sm_action('view'))
				{
					sm_title('Settings');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					$f = new TForm('index.php?m='.sm_current_module().'&d=save');
					$ui->Add($f);
					$ui->Output(true);
				}
			if (sm_action('postsenduploadlink'))
				{
					if (!Validator::USPhone($_postvars['cellphone']))
						$error_message='Wrong cellphone';
					if (empty($error_message))
						{
							sm_extcore();
							$hash=md5(TEmployee::Current()->ID().'-'.microtime().'-'.rand(1000, 9000));
							sm_tempdata_addint('videologin', $hash, TEmployee::Current()->ID(), 3600*4);
							$txt='Upload video URL (valid for 3 hrs) '.sm_homepage().'vu/'.$hash;
							queue_sms($_postvars['cellphone'], $txt, TCompany::CurrentCompany()->Cellphone());
							sm_notify('Link sent');
							sm_redirect(sm_homepage());
						}
					if (!empty($error_message))
						sm_set_action('senduploadlink');
				}
			if (sm_action('senduploadlink'))
				{
					sm_title('Send Video Upload Link');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$f = new TForm('index.php?m='.sm_current_module().'&d=postsenduploadlink');
					$f->AddText('cellphone', 'Cellphone Number')->SetFocus();
					if (TCompany::CurrentCompany()->HasSendNotificationsToCellphone())
						$f->SetValue('cellphone', Formatter::USPhone(TCompany::CurrentCompany()->SendNotificationsToCellphone()));
					if (TEmployee::Current()->HasCellphone())
						$f->SetValue('cellphone', Formatter::USPhone(TEmployee::Current()->Cellphone()));
					$f->LoadValuesArray($_postvars);
					$f->SaveButton('Send SMS');
					$ui->Add($f);
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php');


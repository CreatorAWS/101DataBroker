<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Test Twilio
	Module URI: http://simancms.org/modules/content/
	Description: Test Twilio module
	Revision: 2014-07-17
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	sm_default_action('view');

	if ($userinfo['level'] == 3)
		{
			if (sm_action('view'))
				{
					$m['title'] = 'Send SMS';
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();
					if ($_getvars['action']=='sendsms')
						{
							sm_extcore();
							$postvars['From']=$_postvars['From'];
							$postvars['To']=$_postvars['To'];
							$postvars['Body']=$_postvars['Body'];
							$ui->html('<hr />');
							$ui->div('Sent Twilio request to: '.$_postvars['url']);
							$ui->div(htmlspecialchars(sm_url_content($_postvars['url'], $postvars)));
							$ui->html('<hr />');
						}
					$f = new TForm('index.php?m=testtwilio&d=view&action=sendsms');
					$f->AddText('url', 'url');
					$f->AddText('From', 'From');
					$f->AddText('To', 'To');
					$f->AddTextarea('Body', 'Body');
					$f->SetValue('url', 'http://'.sm_settings('resource_url').'index.php?m=receivesms');
					//$f->SetValue('To', '+11112223333');
					$f->SetValue('To', $currentcompany->Cellphone());
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('From');
				}
		}


?>
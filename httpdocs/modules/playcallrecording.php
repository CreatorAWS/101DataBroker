<?php

	if ( $userinfo['level']>0 )
		{
			sm_use('ui.interface');

			$customercall = new TCustomerCall(intval($_getvars['id']));
			if ($customercall->Exists())
				{
					sm_use('ui.interface');
					sm_title('Call '.$customercall->ID());
					$ui = new TInterface();
					$ui->html('<audio controls src="'.$customercall->RecordingUrl().'" autoplay="true">');
					$ui->Output(true);
				}
		}
	elseif ($userinfo['level']==0)
		sm_redirect('index.php?m=dashboard');

<?php

	if (sm_action('tos'))
		{
			include_once('includes/admininterface.php');
			sm_title('Terms Of Service');
			$ui = new TInterface();
			$sm['resourcename'] = resource_name();
			$ui->AddTPL('tos.tpl');
			$ui->Output(true);
		}

	if (sm_action('policy'))
		{
			include_once('includes/admininterface.php');
			sm_title('Terms Of Service');
			$ui = new TInterface();

			$sm['resourcename'] = resource_name();

			$ui->AddTPL('policy.tpl');
			$ui->Output(true);
		}
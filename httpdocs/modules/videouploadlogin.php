<?php

	sm_extcore();
	$data=sm_tempdata_getint('videologin', $_getvars['token']);
	if (!empty($data))
		{
			$account=new TEmployee($data);
			if ($account->Exists())
				{
					sm_logout();
					sm_login($account->ID());
					sm_redirect('index.php?m=companyassets&d=add');
				}
		}
<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	$_settings['version'] = '1.6.8';

	$result = execsql("SELECT * FROM ".$tableprefix."settings WHERE mode='default'");
	while ($row = database_fetch_assoc($result))
		{
			$_settings[$row['name_settings']] = $row['value_settings'];
		}

	$special['deviceinfo'] = sm_detect_device();

	if ($special['deviceinfo']['is_mobile'])
		{
			$result = execsql("SELECT * FROM ".$tableprefix."settings WHERE mode='mobile'");
			while ($row = database_fetch_assoc($result))
				{
					$_settings[$row['name_settings']] = $row['value_settings'];
				}
		}

	if ($special['deviceinfo']['is_tablet'])
		{
			$result = execsql("SELECT * FROM ".$tableprefix."settings WHERE mode='tablet'");
			while ($row = database_fetch_assoc($result))
				{
					$_settings[$row['name_settings']] = $row['value_settings'];
				}
		}
	
	include(dirname(__FILE__).'/config_def.php');
	if (file_exists(dirname(__FILE__).'/config_usr.php'))
		include(dirname(__FILE__).'/config_usr.php');

?>
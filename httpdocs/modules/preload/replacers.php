<?php

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	$result = execsql("SELECT key_r, value_r FROM ".$sm['t']."replacers");
	while ($row = database_fetch_row($result))
		{
			$sm['s']['replacers'][$row[0]] = $row[1];
			sm_add_content_modifier($sm['s']['replacers'][$row[0]]);
		}

?>
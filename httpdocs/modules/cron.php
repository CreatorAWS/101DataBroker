<?php

	if (!defined("cron_FUNCTIONS_DEFINED"))
		{

			define("cron_FUNCTIONS_DEFINED", 1);
		}

	if (empty($m['mode']))
		$m['mode'] = 'day';


	if (strcmp($m['mode'], 'day') == 0)
		{
			$cronexecfiles = load_file_list("modules/cron/day/", 'php');
			for ($cronindex = 0; $cronindex < count($cronexecfiles); $cronindex++)
				{
					if (strpos(strtolower($cronexecfiles[$cronindex]), '.php~') === false)
						include("modules/cron/day/".$cronexecfiles[$cronindex]);
				}
		}

	if (strcmp($m['mode'], 'hour') == 0)
		{
			$cronexecfiles = load_file_list("modules/cron/hour/", 'php');
			for ($cronindex = 0; $cronindex < count($cronexecfiles); $cronindex++)
				{
					if (strpos(strtolower($cronexecfiles[$cronindex]), '.php~') === false)
						include("modules/cron/hour/".$cronexecfiles[$cronindex]);
				}
		}

	if (strcmp($m['mode'], 'minute') == 0)
		{
			$cronexecfiles = load_file_list("modules/cron/minute/", 'php');
			for ($cronindex = 0; $cronindex < count($cronexecfiles); $cronindex++)
				{
					if (strpos(strtolower($cronexecfiles[$cronindex]), '.php~') === false)
						include("modules/cron/minute/".$cronexecfiles[$cronindex]);
				}
		}

?>
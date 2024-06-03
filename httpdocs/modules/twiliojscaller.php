<?php

	sm_use('twilio');

	if (System::LoggedIn())
		{
			header('Content-Type: text/javascript');

			$js=file_get_contents(sm_cms_rootdir().'ext/tools/twilio/caller.js');

			$js=str_replace('{TOKENURL}', twilio_json_token_url(), $js);
			//$js=str_replace('', '', $js);
			print($js);
			exit();

		}
	exit;

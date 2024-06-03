<?php

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	function send_next_ringless()
		{
			global $_settings, $sm;
			include_once('ext/Twilio/autoload.php');
			$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
			$AuthToken = sm_settings('twilio_AuthToken');
			$client = new \Twilio\Rest\Client($AccountSid, $AuthToken);
			$from1='+17024661355';
			$from2='+17022002806';
			//$to='+12485607182';
			$to='+17026591465';
			$asset=TAsset::UsingCache(24);
			$url=sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcstmsg&asset='.urlencode($asset->ID());
			$call1 = $client->calls->create($to, $from1, array("url" => $url));
			$sid=$call1->sid;
			unset($call1);
			sleep(1);
			$call2 = $client->calls->create(
				$to,
				$from2,
				array(
					'url' => $url,
					'IfMachine' => 'Continue')
			);
			$callterm = $client
			    ->calls($sid)
			    ->update(
			        array("status" => "completed")
			    );
			return true;
		}
	
	//send_next_ringless();
	
	exit();


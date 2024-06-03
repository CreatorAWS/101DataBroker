<?php

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (sm_action('brdcststatus'))
		{

			header("HTTP/1.1 200 OK");
			$item=TCampaignItem::initWithTwilioSID($_REQUEST['CallSid']);
			$item->SetVoicemailCallDuration($_REQUEST['CallDuration']);
			$item->SetVoicemailCallResultTag($_REQUEST['CallStatus']);
/*
			$item=TCampaignItem::initWithTwilioSID($_postvars['CallSid']);
			$item->SetVoicemailCallDuration($_postvars['CallDuration']);
			$item->SetVoicemailCallResultTag($_postvars['CallStatus']);
*/
			//$_postvars['AnsweredBy']
			exit();
		}
	
	if (sm_action('brdcstmsg'))
		{
			$asset=TAsset::withID($_getvars['asset']);
			if ($asset->Exists() && $asset->isAudio())
				{
					include_once(sm_cms_rootdir().'ext/twilio_old/Services/Twilio.php');
					header("content-type: text/xml");
					$response = new Services_Twilio_Twiml();
					$response->play(sm_homepage().$asset->DownloadURL());
					print $response;
				}
			else
				print('<?xml version="1.0" encoding="UTF-8"?><Response></Response>');
			exit();
		}
	

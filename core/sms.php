<?php

	use_api('tcompany');
	function send_sms($to, $txt, $from='', $attachment=Array())
		{
			if (intval(sm_settings('debug_mode'))!=0 || dirname(dirname(dirname(dirname(__FILE__))))=='/www/dealer/')
				{
					print("From: ".$from."\n");
					print("To: ".$to."\n");
					print("Text: ".$txt."\n");
					print("-----------------\n");
					return;
				}


			$company = TCompany::initWithPhone($from);
			if ($company->Exists() && $company->Cellphone() && $company->HasTwilioAuthToken())
				{
					$AccountSid = $company->TwilioAccountSid();
					$AuthToken = $company->TwilioAuthToken();
				}
			else
				{
					$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
					$AuthToken = sm_settings('twilio_AuthToken');
				}

			$twiliofromnumber = $from;

			include_once('ext/Twilio/autoload.php');
			$client = new \Twilio\Rest\Client($AccountSid, $AuthToken);

			if (is_array($attachment) && count($attachment)>0)
				$attachment=implode(" ",$attachment);

			$option = [
				'from' => $twiliofromnumber,
				'body' => $txt,
				'mediaUrl' => $attachment,
				'statusCallback' => 'https://'.main_domain().'/index.php?m=twiliostats'
			];

			$message = $client->messages->create($to, $option);

			return $message->sid;
		}

	function queue_sms($to, $txt, $from='', $scheduletime=0, $attachments_array=Array(), $id_campaign_item=0, $campaign_id=0)
		{
			if (empty($from))
				{
					if (is_object(TCompany::CurrentCompany()))
						$from = TCompany::CurrentCompany()->Cellphone();
				}
			$q = new TQuery("smsqueue");
			$q->Add('from', dbescape($from));
			$q->Add('to', dbescape($to));
			$q->Add('txt', dbescape($txt));
			$q->Add('sendafter', intval($scheduletime));
			if (is_array($attachments_array) && count($attachments_array)>0)
				$q->Add('additional', dbescape(arrayToNllist($attachments_array)));
			$q->Add('id_campaign_item', intval($id_campaign_item));
			$q->Add('id_campaign', intval($campaign_id));
			$q->Insert();
		}

	function queue_message($company, $customer, $to, $txt, $from='', $scheduletime=0, $attachments_array=Array(), $id_campaign_item=0, $campaign_id=0, $id_campaign_schedule=0)
		{
			/** @var TCompany $company */
			/** @var TCustomer $customer */
			if (empty($from))
				$from=$company->Cellphone();
			$q = new TQuery("smsqueue");
			$q->Add('from', dbescape($from));
			$q->Add('id_company', intval($company->ID()));
			$q->Add('id_customer', intval($customer->ID()));
			$q->Add('type', dbescape('message'));
			$q->Add('to', dbescape($to));
			$q->Add('txt', dbescape($txt));
			$q->Add('sendafter', intval($scheduletime));
			if (is_array($attachments_array) && count($attachments_array)>0)
				$q->Add('additional', dbescape(arrayToNllist($attachments_array)));
			$q->Add('id_campaign_item', intval($id_campaign_item));
			$q->Add('id_campaign', intval($campaign_id));
			$q->Add('id_campaign_schedule', intval($id_campaign_schedule));
			$q->Insert();
		}


<?php

	use_api('tcompany');

	function send_voice($to, $from='', $id_asset=0)
		{
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

			include_once('ext/twilio_old/Services/Twilio.php');
			$client = new Services_Twilio($AccountSid, $AuthToken);

			$asset=TAsset::UsingCache($id_asset);
			if($asset->Exists())
				{
					$url=sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcstmsg&asset='.urlencode($asset->ID());
					$call = $client->account->calls->create($from, $to, $url, Array(
						'IfMachine'=>'Continue',
						'StatusCallback'=>sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcststatus'
					));
				}
			return $call->sid;
		}

	function queue_voice($to, $from='', $scheduletime=0, $id_company=0, $id_customer=0, $id_asset=0)
		{
			if (empty($from))
				{
					if (is_object(TCompany::CurrentCompany()))
						$from = TCompany::CurrentCompany()->Cellphone();
				}
			$q = new TQuery("voicequeue");
			$q->Add('from', dbescape($from));
			$q->Add('to', dbescape($to));
			$q->Add('sendafter', intval($scheduletime));
			$q->Add('id_asset', intval($id_asset));
			$q->Add('id_company', intval($id_company));
			$q->Add('id_customer', intval($id_customer));
			$q->Insert();
		}


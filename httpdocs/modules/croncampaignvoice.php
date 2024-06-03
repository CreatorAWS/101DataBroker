<?php

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	function send_next_bvoice()
		{
			global $_settings, $sm;
			include_once('ext/twilio_old/Services/Twilio.php');
			$bad_id = array();
			$sleep = true;

			while (true)
				{
					$items=new TCampaignScheduleList();
					$items->SetFilterStatus('scheduled');
					$items->SetFilterNextActionTimeBefore(time());
					if (count($bad_id) > 0)
						$items->SetFilterExcludeIDs($bad_id);
					$items->OrderByActionTime();
					$items->Limit(1);
					$items->Load();
					if ($items->Count()==0)
						break;

					$item=$items->items[0];
					print($item->ID()."\n");

					$company=TCompany::UsingCache($item->CompanyID());
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
					$client = new Services_Twilio($AccountSid, $AuthToken);

					$campaign=TCampaign::UsingCache($item->CampaignID());
					$sequence=TCampaignSequence::UsingCache($item->SequenceID());
					$contact=TCampaignItem::UsingCache($item->CustomerID());

					if($sequence->GetMode()!='voice' || $contact->isPhoneTypeTagNotVerified())
						{
							$bad_id[]=$item->ID();
							continue;
						}
					$sleep = false;
					$from=$company->Cellphone();
					if (empty($from))
						$from = sm_settings('twilio_from_number');
					$contact->SetVoicemailCallTime(time());
					$to=$contact->Phone();
					print($contact->ID().' :: '.Formatter::USPhone($from).' -> '.Formatter::USPhone($to)."\n");
					$asset=TAsset::UsingCache($sequence->IdAsset());
					$url=sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcstmsg&asset='.urlencode($asset->ID());
					if($contact->HasPhone())
						{
							$call = $client->account->calls->create($from, $to, $url, Array(
								'IfMachine'=>'Continue',
								'StatusCallback'=>sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcststatus'
							));
							$contact->SetTwilioCallSID($call->sid);
							$item->SetStatus('Voice message sent');
						}
					else
						$item->SetStatus('No Phone Number');
					$contact->SetStatus($sequence->ScheduledTimestamp()/(24*60*60).' day voice message sent');

					break;
				}
			if($sleep == true)
				sleep(5);
			return true;


/*
			$cil=new TCampaignItemList();
			$cil->SetFilterStatus('pending2');
			$cil->SetFilterHasVerifiedPhone();
			$cil->SetFilterNoPhoneCallsYet();
			$cil->Limit(1);
			$cil->Load();
			if ($cil->Count()==0)
				return false;
*/
			/** @var TCampaignItem $item */
/*
			$item=$cil->items[0];
			$company=TCompany::UsingCache($item->CompanyID());
			$campaign=TCampaign::UsingCache($item->CampaignID());
			$item->SetStatus('pendingfinish');
			$from=$company->Cellphone();
			if (empty($from))
				$from = sm_settings('twilio_from_number');
			$item->SetVoicemailCallTime(time());
			$to=$item->Phone();
			print($item->ID().' :: '.Formatter::USPhone($from).' -> '.Formatter::USPhone($to)."\n");
			$asset=TAsset::UsingCache($campaign->VoiceMessageAssetID());
			$url=sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcstmsg&asset='.urlencode($asset->ID());
			$call = $client->account->calls->create($from, $to, $url, Array(
				'IfMachine'=>'Continue',
				'StatusCallback'=>sm_homepage(true).'index.php?m=twiliovoicecampaign&d=brdcststatus'
			));
			$item->SetTwilioCallSID($call->sid);
			sleep(1);
			return true;
*/
		}

	$timeend=time()+58;
	while (time()<=$timeend)
		{
			if (!send_next_bvoice())
				sleep(1);
		}
	
	exit();


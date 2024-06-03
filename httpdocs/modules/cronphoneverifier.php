<?php

	$timeend=time()+58;
	include_once('ext/Twilio/autoload.php');

	function validate_next_number()
		{
			$items=new TCampaignItemList();
			$items->SetFilterNeedPhoneValidation();
			$items->Limit(1);
			$items->OrderByID();
			$items->Load();
			if ($items->Count()==0)
				return false;
			$item=$items->items[0];
			$client = new Twilio\Rest\Client(sm_settings('twilio_AccountSid'), sm_settings('twilio_AuthToken'));
			try
				{
					$number = $client->lookups
						->phoneNumbers($item->Phone())
						->fetch(
							array("type" => "carrier")
						);
					if ($number->carrier["type"]=='landline')
						{
							$item->SetPhoneTypeTag('landline');
						}
					elseif ($number->carrier["type"]=='mobile')
						{
							$item->SetPhoneTypeTag('mobile');
						}
					elseif ($number->carrier["type"]=='voip')
						{
							$item->SetPhoneTypeTag('voip');
						}
					else
						{
							$item->SetPhone('');
							$item->SetPhoneTypeTag('invalid');
						}
				}
			catch (Exception $e)
				{
					$item->SetPhone('');
					$item->SetPhoneTypeTag('invalid');
				}

			return true;
		}

	while (time()<=$timeend)
		if (!validate_next_number())
			break;
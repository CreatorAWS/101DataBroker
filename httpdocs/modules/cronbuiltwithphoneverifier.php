<?php

	$timeend=time()+58;
	include_once('ext/Twilio/autoload.php');

	function validate_next_lead_number()
		{
			$items = new TOrganizationsSearchLeadsList();
			$items->SetFilterNeedPhoneValidation();
			$items->Limit(1);
			$items->OrderByID();
			$items->Load();

			if ( $items->Count() == 0 )
				return false;

			/** @var $item TOrganizationsSearchLead */
			$item = $items->Item(0);
			$item->SetPhoneNumberType('processing');
			$client = new Twilio\Rest\Client(sm_settings('twilio_AccountSid'), sm_settings('twilio_AuthToken'));
			$number = $client->lookups
				->phoneNumbers(Cleaner::Phone($item->Phone()))
				->fetch(
					array("type" => "carrier")
				);
			if ($number->carrier["type"]=='landline')
				{
					$item->SetPhoneNumberType('landline');
				}
			elseif ($number->carrier["type"]=='mobile')
				{
					$item->SetPhoneNumberType('mobile');
				}
			elseif ($number->carrier["type"]=='voip')
				{
					$item->SetPhoneNumberType('voip');
				}
			else
				{
					$item->SetPhone('');
					$item->SetPhoneNumberType('invalid');
				}

			unset($item);

			return true;

		}

	while (time()<=$timeend)
		if (!validate_next_lead_number())
			break;
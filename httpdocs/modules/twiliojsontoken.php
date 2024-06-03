<?php

	sm_use('twilio');
	use Twilio\Jwt\ClientToken;

	if (System::LoggedIn())
		{
			// choose a random username for the connecting user
			$sm['twilio']['identity'] = System::MyAccount()->TwilioClientID();

			if (System::MyCompany()->HasCellphone())
				{
					if (System::MyCompany()->HasTwilioApiSecret() && System::MyCompany()->HasTwilioApiSid() && System::MyCompany()->HasTwilioTwiMLAppSID())
						{
							$AccountSid = System::MyCompany()->TwilioAccountSid();
							$AuthToken = System::MyCompany()->TwilioAuthToken();
							$TwiMLAppSID = System::MyCompany()->TwilioTwiMLAppSID();
						}
					else
						{
							$AccountSid = sm_settings('twilio_AccountSid'); // Set our Account SID and AuthToken
							$AuthToken = sm_settings('twilio_AuthToken');
							$TwiMLAppSID = sm_settings('twilio_twiml_app_sid');
						}
				}

			if ( System::MyAccount()->isRefreshTwilioAccessTokenNeeded())
				{
					$capability = new ClientToken($AccountSid, $AuthToken);
					$capability->allowClientOutgoing($TwiMLAppSID);
					$capability->allowClientIncoming($sm['twilio']['identity']);
					$token = $capability->generateToken();
					System::MyAccount()->SetTwilioAccessToken($token);
				}
			else
				$token = System::MyAccount()->TwilioAccessToken();

			// return serialized token and the user's ID
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
			    'identity' => $sm['twilio']['identity'],
			    'token' => $token,
			));

		}
	exit;

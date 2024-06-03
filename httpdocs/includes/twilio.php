<?php

	include_once(sm_cms_rootdir().'ext/Twilio/autoload.php');

	function twilio_account_sid()
		{
			return sm_settings('twilio_AccountSid');
		}

	function twilio_auth_token()
		{
			return sm_settings('twilio_AuthToken');
		}

	function twilio_default_from_number()
		{
			return sm_settings('twilio_default_from_number');
		}

	function twilio_twiml_app_sid()
		{
			return sm_settings('twilio_twiml_app_sid');
		}

	function twilio_json_token_url()
		{
			return sm_homepage().'index.php?m=twiliojsontoken';
		}


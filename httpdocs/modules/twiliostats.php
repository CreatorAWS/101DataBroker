<?php

	sm_default_action('getstatus');

	if ( sm_action('getstatus') )
		{
			header("HTTP/1.1 200 OK");
/*
			$entityBody = file_get_contents('php://input');

			if($entityBody)
				{
					$data = json_decode($entityBody, true);

					if(!empty($data['MessageSid']))
						{
							$info = getsql("SELECT * FROM campaigns_items WHERE twilio_sms_sid = '".dbescape($data['MessageSid'])."' ORDER BY id LIMIT 1");
							if(!empty($info['id']))
								{
									execsql("UPDATE campaigns_items SET sms_status = '".dbescape($data['MessageStatus'])."' WHERE id=".intval($info['id']));
								}
						}

				}
*/
			$sid = $_REQUEST['MessageSid'];
			$status = $_REQUEST['MessageStatus'];
			if(!empty($sid))
				{
					$info = getsql("SELECT * FROM campaigns_items WHERE twilio_sms_sid = '".dbescape($sid)."' ORDER BY id LIMIT 1");
					if(!empty($info['id']))
						{
							execsql("UPDATE campaigns_items SET sms_status = '".dbescape($status)."' WHERE id=".intval($info['id']));
						}
					else
						{
							$info = getsql("SELECT * FROM campaigns_schedule WHERE twilio_sms_sid = '".dbescape($sid)."' ORDER BY id LIMIT 1");
							if(!empty($info['id']))
								{
									execsql("UPDATE campaigns_schedule SET sms_status = '".dbescape($status)."' WHERE id=".intval($info['id']));
								}
						}
				}
			exit;
		}
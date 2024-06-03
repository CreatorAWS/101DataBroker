<?php

	function sendnextsmsinqueue()
		{
			$info = getsql("SELECT * FROM smsqueue WHERE sendafter<=".time()." ORDER BY id LIMIT 1");
			if (!empty($info['id']))
				{
					//print($info['id'].' - ');
					execsql("DELETE FROM smsqueue WHERE id=".intval($info['id'])." LIMIT 1");
					if (is_debug_environment())
						return true;
					$sendsms=$info['type']=='sms';
					if (!empty($info['id_customer']))
						{
							$sendsms = true;//We sending SMS when no active devices assigned to customer (customer uninstalled the app or not used it too long)
						}
					if ($sendsms)
						{
							if (strcmp(substr($info['to'], 0, 4), '1111')!=0 && strcmp(substr($info['to'], 0, 5), '+1111')!=0)
								{
									if (!empty($info['additional']))
										{
											$attachments=nllistToArray($info['additional']);
											$twiliosid = send_sms($info['to'], $info['txt'], $info['from'], $attachments);
										}
									else
										$twiliosid = send_sms($info['to'], $info['txt'], $info['from']);
									//print("SMS\n");

									if ( !empty($twiliosid) && intval($info['id_campaign_item'])!=0 )
										{
											if ( $info['id_campaign_schedule']!=0 )
												execsql("UPDATE campaigns_schedule SET twilio_sms_sid = '".dbescape($twiliosid)."' WHERE id = ".intval($info['id_campaign_schedule']));
											else
												execsql("UPDATE campaigns_items SET twilio_sms_sid = '".dbescape($twiliosid)."' WHERE id = ".intval($info['id_campaign_item']));
										}
								}
							//else
							//print("Virtual SMS\n");
						}
					//print("\n");
					return true;
				}
			else
				return false;
		}

	$timeend = time() + 50;
	while (time() < $timeend)
		{
			if (!sendnextsmsinqueue())
				sleep(1);
		}

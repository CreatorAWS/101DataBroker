<?php

	sm_default_action('getstatus');

	if ( sm_action('getstatus') )
		{
			header("HTTP/1.1 200 OK");

			$entityBody = file_get_contents('php://input');

			if($entityBody)
				{
					$data = json_decode($entityBody, true);

					for($i=0; $i<count($data); $i++)
						{
							if(!empty($data[$i]['MessageID']))
								{
									$info = getsql("SELECT * FROM campaigns_items WHERE id_email = '".dbescape($data[$i]['MessageID'])."' ORDER BY id LIMIT 1");
									if(!empty($info['id']))
										{
											execsql("UPDATE campaigns_items SET email_status = '".dbescape($data[$i]['event'])."' WHERE id=".intval($info['id']));
											if($data[$i]['event']=='unsub' || $data[$i]['event']=='bounce')
												execsql("INSERT INTO unsubscribed_emails (`customer_email`, `time`) VALUES ('".$info['email']."', ".time().")");
										}
									else
										{
											$campaign_schedule_check = getsql("SELECT * FROM campaigns_schedule WHERE id_email = '".dbescape($data[$i]['MessageID'])."' ORDER BY id LIMIT 1");
											if(!empty($campaign_schedule_check['id']))
												{
													execsql("UPDATE campaigns_schedule SET email_status = '".dbescape($data[$i]['event'])."' WHERE id=".intval($campaign_schedule_check['id']));
													if($data[$i]['event']=='unsub' || $data[$i]['event']=='bounce')
														execsql("INSERT INTO unsubscribed_emails (`customer_email`, `time`) VALUES ('".$info['email']."', ".time().")");
												}
										}
								}
						}
				}
			exit;

		}
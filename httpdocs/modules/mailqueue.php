<?php

	function sendnextemailinqueue()
		{
			include_once(dirname(dirname(dirname(__FILE__))).'/core/api.php');
			$info = getsql("SELECT * FROM mailqueue WHERE sendafter<=".time()." ORDER BY id LIMIT 1");
			if (!empty($info['id']))
				{
					execsql("DELETE FROM mailqueue WHERE id=".intval($info['id'])." LIMIT 1");
					if (is_debug_environment())
						{
							print("Sent to ".$info['to']."\n");
							return true;
						}
					$company = new TCompany($info['id_company']);
					if ($company->Exists())
						$company_name=$company->Name();

					$sending_data = MailJetSend($info['from'], $info['to'], $info['subject'], $info['message'], '', $company_name, $info['id_email']);

					if($sending_data['Messages'][0]['Status']=='success')
						{
							$message_id = $sending_data['Messages'][0]['To'][0]['MessageID'];

							if(!empty($message_id) && $info['id_campaign_item']!=0)
								{
									if ( $info['id_campaign_schedule']!=0 )
										execsql("UPDATE campaigns_schedule SET id_email = '".dbescape($message_id)."' WHERE id = ".intval($info['id_campaign_schedule']));
									else
										execsql("UPDATE campaigns_items SET id_email = '".dbescape($message_id)."' WHERE id = ".intval($info['id_campaign_item']));
								}
						}
					elseif($sending_data['Messages'][0]['Status']=='error')
						{
							if($info['id_campaign_item']!=0)
								{
									if ( $info['id_campaign_schedule']!=0 )
										execsql("UPDATE campaigns_schedule SET id_email = '".dbescape($sending_data['Messages'][0]['Errors']['ErrorCode'])."' WHERE id = ".intval($info['id_campaign_schedule']));
									else
										execsql("UPDATE campaigns_items SET email_status = '".dbescape($sending_data['Messages'][0]['Errors']['ErrorCode'])."' WHERE id = ".intval($info['id_campaign_item']));
								}
						}

					//send_mail($info['from'], $info['to'], $info['subject'], $info['message']);
					return true;
				}
			else
				return false;
		}

		print("Mail queue started\n");
		$timeend = time() + 57;
		while (time() < $timeend)
			{
				if (!sendnextemailinqueue())
					sleep(5);
				else
					sleep(1);
			}

		print("Mail queue finished\n");

	exit();

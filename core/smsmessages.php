<?php

	class SMSMessages
		{
			public static function QueueSMS($to, $txt, $from='', $scheduletime = 0, $attachments_array = Array(), $id_campaign_item = 0, $id_campaign_schedule = 0)
				{
					if (empty($from))
						{
							if (is_object(TCompany::CurrentCompany()))
								$from = TCompany::CurrentCompany()->Cellphone();
						}

					$q = new TQuery("smsqueue");
					$q->Add('from', dbescape($from));
					$q->Add('to', dbescape($to));
					$q->Add('txt', dbescape($txt));
					$q->Add('sendafter', intval($scheduletime));
					if (is_array($attachments_array) && count($attachments_array)>0)
						$q->Add('additional', dbescape(arrayToNllist($attachments_array)));
					$q->Add('id_campaign_item', intval($id_campaign_item));
					$q->Add('id_campaign_schedule', intval($id_campaign_schedule));
					$q->Insert();
				}
		}

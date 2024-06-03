<?php

	class EmailMessages
		{
			public static function QueueEmail($company, $to, $subject, $message, $scheduletime = 0, $attachments = Array(), $campaign_item = 0, $id_campaign_schedule = 0, $id_email = '')
				{
					/** @var $company TCompany */
					$q = new TQuery("mailqueue");
					$q->Add('id_company', intval($company->ID()));
					$q->Add('id_customer', 0);
					$q->Add('id_campaign_item', $campaign_item);
					$q->Add('from', dbescape($company->EmailFrom()));
					$q->Add('to', dbescape($to));
					$q->Add('subject', dbescape($subject));
					$q->Add('message', dbescape($message));
					$q->Add('sendafter', intval($scheduletime));
					$q->Add('id_campaign_schedule', intval($id_campaign_schedule));
					$q->Add('id_email', dbescape($id_email));
					if (is_array($attachments) && count($attachments) > 0)
						$q->Add('additional', dbescape(arrayToNllist($attachments)));
					$q->Insert();
				}
		}

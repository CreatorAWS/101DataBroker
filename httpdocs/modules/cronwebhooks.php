<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	sm_extcore();

	function next_webhook()
		{
			$list=new TWebhookList();
			$list->OrderByID();
			$list->ShowAllItemsIfNoFilters();
			$list->Limit(1);
			$list->Load();
			if ($list->Count()==0)
				return false;
			else
				{
					$webhook=clone $list->Item(0);
					$list->Item(0)->Remove();
					print('Webhook '.$webhook->ID());
					sm_url_content($webhook->WebhookURL(), $webhook->GetPostArray());
					print(" - OK\n");
					return true;
				}
		}

	$end=time()+59;
	while(time()<=$end)
		{
			if (!next_webhook())
				{
					print("No webhooks\n");
					sleep(1);
				}
		}

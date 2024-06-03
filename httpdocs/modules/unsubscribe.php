<?php

	sm_default_action('unsubscribe');

	if (sm_action('unsubscribe'))
		{
			sm_use('ui.interface');
			$ui = new TInterface();
			if(!empty($_getvars['contact']))
				{
					$sequences = new TCampaignItem(intval($_getvars['contact']));
					$sequences->SetStatus('Unsubscribed');
					if ($sequences->Exists())
						{
							$customer = new TCustomer($sequences->PartnerID());
							if($customer->Exists())
								$customer->SetUnsubscribeStatus(1);

							$schedulelist = new TCampaignScheduleList();
							$schedulelist->SetFilterCompany($sequences->CompanyID());
							$schedulelist->SetFilterCustomer($sequences->ID());
							$schedulelist->SetFilterCampaign($sequences->CampaignID());
							$schedulelist->SetFilterStatus('scheduled');
							$schedulelist->Load();

							for ($j=0; $j<$schedulelist->Count(); $j++)
								{
									$schedulelist->items[$j]->SetStatus('Unsubscribed');
								}
							$ui->div('You have successfully unsubscribed from this mailing list!');
						}
				}
			$ui->Output(true);
		}
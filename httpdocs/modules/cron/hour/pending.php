<?php

	function check_pending2()
		{
			$customers=new TCustomerList();
			$customers->SetFilterSMSAcceptPending2();
			$customers->SetFilterSMSAcceptPendingTimeLTE(time()-24*3600);
			$customers->Load();
			for ($i = 0; $i < $customers->Count(); $i++)
				{
					print($customers->items[$i]->ID().' '.$customers->items[$i]->ContactName().' - ');
					$customers->items[$i]->SetSMSAcceptedTag('noresponse');
					$customers->items[$i]->SetSMSPendingTimestamp();
					$pendingmessages=new TPendingMessageList();
					$pendingmessages->SetFilterCustomer($customers->items[$i]);
					$pendingmessages->Load();
					for ($j = 0; $j < $pendingmessages->Count(); $j++)
						{
							$pendingmessages->items[$j]->Remove();
						}
					unset($pendingmessages);
					print("Pending2 -> No Response\n");
				}
		}

	function check_pending1()
		{
			$customers=new TCustomerList();
			$customers->SetFilterSMSAcceptPending1();
			$customers->SetFilterSMSAcceptPendingTimeLTE(time()-24*3600);
			$customers->Load();
			for ($i = 0; $i < $customers->Count(); $i++)
				{
					print($customers->items[$i]->ID().' '.$customers->items[$i]->ContactName().' - ');
					$customers->items[$i]->SetSMSAcceptedTag('pending2');
					$customers->items[$i]->SetSMSPendingTimestamp();
					$customers->items[$i]->ReSendInitialSMS();
					print("Pending1 -> Pending2\n");
				}
		}

	print("Pending SMS accept start\n");
	print("Pending2\n");
	check_pending2();
	print("Pending1\n");
	check_pending1();
	print("Pending SMS accept finish\n");

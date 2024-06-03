<?php

	function check_appointments()
		{
			$list=new TAppointmentList();
			$list->SetFilterNotificationNotSent();
			$list->SetFilterScheduledTimeBetween(time()-3600, time()+3600);
			$list->Limit(1);
			$list->Load();
			for ($i = 0; $i < $list->Count(); $i++)
				{
					$list->items[$i]->SetNotificationSentTimestamp(time());
					$customer=new TCustomer($list->items[$i]->CustomerID());

					if (TCompany::UsingCache($customer->CompanyID())->HasCompanyAppointmentReminder())
						{
							$company_appointment_reminder = TCompany::UsingCache($customer->CompanyID())->CompanyAppointmentReminder();
							$message=str_replace('{DATE}', strftime('%A %m/%d/%Y at %I:%M %p', time()), $company_appointment_reminder);
							$message=str_replace('{BUSINESS}', TCompany::UsingCache($customer->CompanyID())->Name(), $message);
						}
					else
						$message = 'our appointment at '.TCompany::UsingCache($customer->CompanyID())->Name().' is on '.strftime('%A %m/%d/%Y at %I:%M %p', $list->items[$i]->ScheduledTimestamp());

					$customer->SendMessage(
						$message,
						0,
						false,
						'reminder'
					);
					unset($customer);
				}
			return $list->Count()>0;
		}

	print("Appointment reminder start\n");
	$timeend = time() + 5;
	while (time() < $timeend)
		{
			if (!check_appointments())
				sleep(1);
		}
	print("Appointment reminder finish\n");

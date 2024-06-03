<?php

/*
 Module Name: Customers
 Description: Customers
 Revision: 2014-11-01
 */

	if ($userinfo['level']>0)
		{
			/** @var $currentcompany TCompany */

			sm_default_action('list');

			if (sm_action('postdelete'))
				{
					if (sm_action('postdelete'))
						$object=new TAppointment(intval($_getvars['id']));
					if (is_object($object) && $object->Exists())
						{
							$object->Remove();
							sm_notify('Appointment cancelled');
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('postadd'))
				{
					$customer=new TCustomer(intval($_getvars['customer']));
					if ($customer->Exists())
						{
							$time = SMDateTime::TimestampFromUSDateAndTime($_postvars['schedule'], $_postvars['hr'].':'.$_postvars['min'].' '.$_postvars['ampm']);
							if ($time <= SMDateTime::Now())
								$error_message = 'Wrong date or time';
							if (empty($error_message))
								{
									$appointment=TAppointment::Create($customer);
									$appointment->SetScheduledTimestamp($time);
									$appointment->SetNote(htmlescape($_postvars['note']));
									$customer->AppointmentAction($appointment->ID(), $time);
									sm_notify('Appointmen scheduled');
									sm_redirect($_getvars['returnto']);
								}
							if (!empty($error_message))
								sm_set_action('add');
						}
				}

			if (sm_action('postedit'))
				{
					$customer=new TCustomer(intval($_getvars['customer']));
					if (!$customer->Exists())
						exit('Access Denied');

					$appointment = new TAppointment(intval($_getvars['id']));
					if (!$appointment->Exists())
						exit('Access Denied');

					$appointment->SetNote(htmlescape($_postvars['note']));

					sm_redirect($_getvars['returnto']);


				}

			if (sm_action('savemessage'))
				{
					TCompany::CurrentCompany()->SetCompanyAppointmentReminder($_postvars['appointment_message']);

					sm_notify('Settings updated');
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m='.sm_current_module().'&d=reminders');
				}

			if (sm_action('reminders'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_title('Reminder Content');
					$ui = new TInterface();

					$b = new TButtons();
					$b->AddButton('all', 'All', 'index.php?m=appointments');
					$b->AddButton('today', 'Today', 'index.php?m=appointments&date=today');
					$b->AddButton('upcoming', 'Upcoming', 'index.php?m=appointments&date=upcoming');
					$b->AddButton('reminders', 'Reminders', 'index.php?m=appointments&d=reminders');
					$b->AddClassname('current', 'reminders');
					$ui->Add($b);

					$ui->div_open('', 'embed_grid');
					$ui->div_open('', 'socialsharewrap settingspage addcustomer');
					$f = new TForm('index.php?m='.sm_current_module().'&d=savemessage&returnto='.urlencode('index.php?m='.sm_current_module().'&d='.sm_current_action()));

					$f->AddTextarea('appointment_message', 'Message');
					$f->SetFieldBottomText('appointment_message', 'Available tags: {DATE} - Appointment date, {BUSINESS} - Company Name');

					if (TCompany::CurrentCompany()->HasCompanyAppointmentReminder())
						$company_appointment_reminder = TCompany::CurrentCompany()->CompanyAppointmentReminder();
					else
						$company_appointment_reminder = 'our appointment at {BUSINESS} is on {DATE}';
					$f->SetValue('appointment_message', $company_appointment_reminder);

					$preview=str_replace('{DATE}', strftime('%A %m/%d/%Y at %I:%M %p', time()), $company_appointment_reminder);
					$preview=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $preview);

					if(strlen($preview.' Reply with Yes to accept or No to decline.')>160)
						$ui->NotificationError('The message body exceeds the 160 character limit.');

					$f->InsertHTML('Preview: '.$preview);
					$f->SaveButton('Preview & Save');

					$f->LoadValuesArray($_postvars);
					$ui->Add($f);
					$ui->div_close();
					$ui->div_close();

					$ui->Output(true);
				}

			if (sm_action('edit'))
				{
					$customer=new TCustomer(intval($_getvars['customer']));
					if (!$customer->Exists())
						exit('Access Denied');

					$appointment = new TAppointment(intval($_getvars['id']));
					if (!$appointment->Exists())
						exit('Access Denied');

					sm_use('ui.interface');
					sm_use('ui.form');
					sm_title('Edit Appointment');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$appointment->ID().'&customer='.$customer->ID().'&returnto='.urlencode($_getvars['returnto']));
					$f->AddText('note', 'Note')->WithValue($appointment->Note());
					$f->LoadValuesArray($_postvars);
					$f->SaveButton('Save');
					$ui->Add($f);
					$ui->Output(true);
				}

			if (sm_action('add'))
				{
					$customer=new TCustomer(intval($_getvars['customer']));
					if ($customer->Exists())
						{
							sm_use('ui.interface');
							sm_use('ui.form');
							sm_title('Schedule an Appointment');
							$ui = new TInterface();
							if (!empty($error_message))
								$ui->NotificationError($error_message);
							$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&customer='.$customer->ID().'&returnto='.urlencode($_getvars['returnto']));
							$f->AddText('schedule', 'Schedule at')
							  ->WithValue(strftime('%m/%d/%Y'))
							  ->Calendar();
							$f->HideEncloser();
							$f->AddSelect('hr', 'Hr', range(1, 12))->WithValue(intval(date('h')));
							$f->SetFieldClass('hr', 'hrs');
							$f->HideDefinition();
							$f->HideEncloser();
							$tmp = range(0, 59);
							for ($i = 0; $i < count($tmp); $i++)
								{
									if ($tmp[$i] < 10)
										$tmp[$i] = '0'.$tmp[$i];
								}
							$f->AddSelectVL('min', 'Min', range(0, 59), $tmp)->WithValue(intval(date('i')));
							$f->SetFieldClass('min', 'minutes');
							$f->HideDefinition();
							$f->HideEncloser();
							$f->AddSelectVL('ampm', 'ampm', Array('am', 'pm'), Array('AM', 'PM'))->WithValue(date('a'));
							$f->SetFieldClass('ampm', 'ampm');
							$f->HideDefinition();
							$f->AddText('note', 'Note');
							$f->LoadValuesArray($_postvars);
							$f->SaveButton('Schedule');
							$ui->Add($f);
							$ui->style('#schedule {width:120px;display:inline;}');
							$ui->style('#hr, #min, #ampm {width:70px;display:inline;}');
							$ui->Output(true);
						}
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.fa');
					sm_use('ui.buttons');
					sm_title('Appointments');

					$limit=30;
					$offset=intval($_getvars['from']);

					$ui = new TInterface();
					$ui->html('<div class="tablewrapper">');
					$b = new TButtons();
					$appointments = new TAppointmentList();
					$appointments->SetFilterCompany(TCompany::CurrentCompany());
					if($_getvars['date']=='today')
						$appointments->SetFilterScheduledTimeBetween(strtotime('today midnight'), strtotime('tomorrow midnight'));
					if($_getvars['date']=='upcoming')
						$appointments->SetFilterScheduledTimeBetween(strtotime('today midnight')+24*60*60, strtotime('tomorrow midnight')+7*24*60*60);
					$appointments->OrderByTimeScheduled(false);
					$appointments->Offset($offset);
					$appointments->Limit($limit);
					$appointments->Load();
					$b->AddButton('all', 'All', 'index.php?m=appointments');
					$b->AddButton('today', 'Today', 'index.php?m=appointments&date=today');
					$b->AddButton('upcoming', 'Upcoming', 'index.php?m=appointments&date=upcoming');
					$b->AddButton('reminders', 'Reminders', 'index.php?m=appointments&d=reminders');

					if( $_getvars['date'] == 'today' )
						$b->AddClassname('current', 'today');
					elseif ( $_getvars['date'] == 'upcoming' )
						$b->AddClassname('current', 'upcoming');
					else
						$b->AddClassname('current', 'all');

					$ui->Add($b);
					$t = new TGrid();
					$t->AddCol('customer', 'Customer');
					$t->AddCol('note', 'Note');
					$t->AddCol('created', 'Created');
					$t->AddCol('scheduled', 'Scheduled');
					$t->AddCol('status', 'Status');

					for ($i = 0; $i < $appointments->Count(); $i++)
						{
							$appointment = $appointments->items[$i];
							$customer = new TCustomer($appointment->CustomerID());
							if(!$customer->Exists())
								continue;

							$t->Label('customer', '<a href="index.php?m=customerdetails&d=appointments&id='.$customer->ID().'">'.$customer->Name().'</a>');
							$t->Label('note', $appointment->Note());
							$t->Label('created', Formatter::DateTime($appointment->AddedTimestamp()));
							$t->Label('scheduled', Formatter::DateTime($appointment->ScheduledTimestamp()));
							if ($appointment->ScheduledTimestamp()>time())
								$t->Label('status', 'Scheduled');
							else
								$t->Label('status', 'Sent');

							$t->NewRow();
							unset($appointment);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($appointments->TotalCount(), $limit, $offset);
					$ui->Add($b);
					$ui->html('</div>');
					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=account');

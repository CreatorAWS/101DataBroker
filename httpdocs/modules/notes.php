<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('note');

			if (sm_action('note'))
				{
					$m['module']='notes';
					sm_use_template('notes');
					$special['no_blocks'] = true;

					$customer=new TCustomer(intval($_getvars['customer']));
					$notes = new TCustomerNotesList();
					$notes->SetFilterCompany(TCompany::CurrentCompany());
					$notes->SetFilterCustomer($customer);
					$notes->Load();

					for ($i=0; $i<$notes->Count(); $i++)
						{
							/** @var $note TCustomerNote */
							$note = $notes->items[$i];
							$employee=new TEmployee($note->EmployeeID());
							$sm['items'][$i]['id_customer']=$note->CustomerID();
							if ($customer->Exists())
								$sm['items'][$i]['customer']=$customer->Name();

							$sm['items'][$i]['employee'] = $employee->Name();
							$sm['items'][$i]['employee_label']=TCompany::CurrentCompany()->Name();
							$sm['items'][$i]['text']=nl2br($note->Text());
							if ($i>0 && (abs($note->Timeadded() - $notes->items[$i]->Timeadded())<600))
								{
									$sm['items'][$i]['time']=strftime("%I:%M %p", $note->Timeadded());
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-short';
								}
							else
								{
									$sm['items'][$i]['time']=strftime("%m/%d/%Y %I:%M %p", $note->Timeadded());
									$sm['items'][$i]['time_class']='rd-dash-conversation-time-full';
								}
							unset($employee);
						}

				}

			if (sm_action('addnote'))
				{
					/** @var $myaccount TEmployee */
					$customer=new TCustomer(intval($_getvars['customer']));
					if ($customer->Exists() && $customer->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							$note = TCustomerNote::Create(
								TCompany::CurrentCompany()->ID(),
								$customer->ID(),
								$myaccount->ID(),
								time(),
								$_postvars['text']
							);
						}
					exit($customer->ID());
				}

		}
<?php

	sm_use('twilio');

	use Twilio\Twiml;
	header('Content-Type: text/xml; charset=utf-8');
	if (!empty($_postvars['From']) && !empty($_postvars['To']))
		{
			use_api('tcompany');
			use_api('tcustomer');

			$company = TCompany::initWithPhone($_postvars['To']);
			if ($company->Exists())
				{
					$employees = new TEmployeeList($company->ID());
					$employees->SetFilterHasCellPhone();
					$employees->OrderByName();
					$employees->Load();

					if ($employees->Count()>0)
						{
							/** @var  $employee TEmployee */
							$employee = $employees->items[0];
							$response = new Twiml();
							$number_to_call=Cleaner::USPhone($employee->Cellphone());
							$customercall = TCustomerCall::Create($customer, $employee, $number_to_call);
							TMessagesLog::Create('call', $customercall->ID(), $customer->CompanyID(), $customer->ID(), time(), 1, $employee->ID());
							$call_params['statusCallback'] = sm_homepage().'index.php?m=twiliocallcollector&d=completed&id='.$customer->ID().'&call='.$customercall->ID().'&incoming=1';
							$response->dial($employee->Cellphone(), $call_params);
							$response->say("Thanks for calling!");

							echo $response;
						}
				}
		}
	exit;

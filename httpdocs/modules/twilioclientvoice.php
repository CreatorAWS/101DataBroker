<?php

	sm_use('twilio');

	use Twilio\TwiML\VoiceResponse;

	$is_incoming = false;
	$caller_phone = twilio_default_from_number();
	$call_from_customer = false;

	$response = new VoiceResponse();

	// get the phone number from the page request parameters, if given
	if (isset($_REQUEST['To']) && strlen($_REQUEST['To']) > 0)
		{
			$number = htmlspecialchars($_REQUEST['To']);

			$employee = TEmployee::checkIfExistWithTwilioPhone($number);
			if ( $employee !== false && $employee->Exists() )
				{
					$company = new TCompany($employee->CompanyID());
					$is_incoming = true;
				}
			else
				{
					$company = TCompany::checkIfExistWithPhone($number);

					if ( $company !== false && $company->Exists() )
						$is_incoming = true;

				}

			if ( $is_incoming )
				{
					if ( $employee !== false && $employee->Exists())
						{
							$onlineUserID = $employee->TwilioClientID();
						}
					else
						{
							$employees = new TEmployeeList($company);
							$employees->SetFilterNotDeleted();
							$employees->SetFilterOnline();
							$employees->Load();

							if ($employees->Count() > 0)
								{
									$employee = $employees->items[0];
									$onlineUserID = $employee->TwilioClientID();
								}
						}

					if (!empty($onlineUserID))
						{
							$call_params = [];
							$dial = $response->dial('', array('record' => 'record-from-answer'));
							$customer = TCustomer::initWithCustomerPhoneNotDeleted($_postvars['From'], $company->ID());
							if (is_object($customer) && $customer->Exists())
								{
									$number_to_call = Cleaner::USPhone($customer->Cellphone());
									$customercall = TCustomerCall::Create($customer, $employee, $number_to_call, 'Incoming');
									$customer->IncomingCallAction( $customercall->ID() );
									TMessagesLog::Create('call', $customercall->ID(), $customer->CompanyID(), $customer->ID(), time(), 1, $employee->ID() );
									$call_params['statusCallback'] = sm_homepage().'index.php?m=twiliocallcollector&d=completed&id='.$customer->ID().'&call='.$customercall->ID().'&incoming=1';
								}

							$dial->client($onlineUserID, $call_params);
						}
					else
						{
							$customer = TCustomer::initWithCustomerPhoneNotDeleted($_postvars['From'], $company->ID());
							if (is_object($customer) && $customer->Exists())
								{
									$call_from_customer = true;
								}

							if ( $employee !== false && $employee->Exists() && $employee->HasCellphone())
								{
									$number_to_call = Cleaner::USPhone($employee->Cellphone());
									if ($call_from_customer)
										{
											$customercall = TCustomerCall::Create($customer, $employee, $number_to_call, 'Incoming');
											$customer->IncomingCallAction($customercall->ID());
											TMessagesLog::Create('call', $customercall->ID(), $customer->CompanyID(), $customer->ID(), time(), 1, $employee->ID());
										}

									$call_params['statusCallback'] = sm_homepage().'index.php?m=twiliocallcollector&d=completed&id='.$customer->ID().'&call='.$customercall->ID().'&incoming=1';
									$response->dial($employee->Cellphone(), $call_params);
								}
							else
								{
									$employees = new TEmployeeList($company->ID());
									$employees->SetFilterHasCellPhone();
									$employees->SetFilterNotDeleted();
									$employees->OrderByName();
									$employees->Load();

									if ($employees->Count()>0)
										{
											/** @var  $employee TEmployee */
											$employee = $employees->items[0];
											$number_to_call=Cleaner::USPhone($employee->Cellphone());
											if ( $call_from_customer )
												{
													$customercall = TCustomerCall::Create($customer, $employee, $number_to_call, 'Incoming');
													$customer->IncomingCallAction( $customercall->ID() );
													TMessagesLog::Create('call', $customercall->ID(), $customer->CompanyID(), $customer->ID(), time(), 1, $employee->ID());
												}
											$call_params['statusCallback'] = sm_homepage().'index.php?m=twiliocallcollector&d=completed&id='.$customer->ID().'&call='.$customercall->ID().'&incoming=1';
											$response->dial($employee->Cellphone(), $call_params);
										}
								}
						}
				}
			elseif (!$is_incoming)
				{
					$employee = TEmployee::InitWithTwilioClientID($_REQUEST['Caller']);
					if (is_object($employee) && $employee->Exists())
						{
							if ($employee->HasTwilioPhone())
								$caller_phone = $employee->TwilioPhone();
							else
								{
									$company = new TCompany($employee->CompanyID());
									if ($company->Exists() && $company->HasCellphone())
										$caller_phone = $company->Cellphone();
								}

						}

					$dial = $response->dial('', array('callerId' => $caller_phone, 'record' => 'record-from-answer'));
					// wrap the phone number or client name in the appropriate TwiML verb
					// by checking if the number given has only digits and format symbols
					if (preg_match("/^[\d\+\-\(\) ]+$/", $number))
						$dial->number($number);
					elseif (strpos($number, '-')!==false)
						{
							$tmp = explode('-', $number);
							$call_params=Array();

							$customer = new TCustomer($tmp[0]);
							sm_log('customer', $customer->ID(), 'Request '.print_r($_REQUEST, true));

							$number_to_call = Cleaner::USPhone($customer->Cellphone());
							sm_log('customer', $customer->ID(), 'Calling primary number');

							if ( is_object($employee) )
								{
									$customercall = TCustomerCall::Create($customer, $employee, $number_to_call);
									$customer->CallAction( $customercall->ID(), $employee->ID() );
									TMessagesLog::Create('call', $customercall->ID(), $customer->CompanyID(), $customer->ID(), time(), 0, $employee->ID());
								}

							$call_params['statusCallback'] = sm_homepage().'index.php?m=twiliocallcollector&d=completed&id='.$customer->ID().'&call='.$customercall->ID();

							if (!empty($call_params))
								$dial->number($number_to_call, $call_params);
							else
								$dial->number($number_to_call);
						}
					else
						$dial->client($number);
				}
		}
	else
		$response->say("Thanks for calling!");

	header('Content-Type: text/xml; charset=utf-8');
	echo $response;
	exit;
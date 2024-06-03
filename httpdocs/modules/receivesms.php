<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!function_exists('is_twilio_mms'))
		{
			function is_twilio_mms()
				{
					global $_postvars;
					return intval($_postvars['NumMedia']) > 0;
				}

			function put_twilio_mms_to_queue($sms_id, $from, $to)
				{
					global $_postvars;
					$images = Array();
					for ($i = 0; $i < intval($_postvars['NumMedia']); $i++)
						{
							$images[] = $_postvars['MediaUrl'.$i];
						}
					$info = Array(
						'source' => 'incomingsms',
						'id' => $sms_id,
						'from' => $from,
						'to' => $to,
						'images' => $images
					);
					$q = new TQuery('sm_incomingmmsqueue');
					$q->Add('data', dbescape(serialize($info)));
					$q->Insert();
				}
			
			function is_join_message($text)
				{
					$words=explode(' ', str_replace('  ', ' ', trim($text)));
					return (count($words)==3 && strcmp(strtolower($words[2]), 'join')==0);
				}
		}

	$success = false;
	$success_message = '';
	$error_message = '';
	if (!empty($_postvars['From']) && !empty($_postvars['To']))
		{
			use_api('tcompany');
			use_api('tcustomer');
			header("content-type: text/xml");
			$q = new TQuery('smsinbox');
			$q->Add('from', dbescape($_postvars['From']));
			$q->Add('to', dbescape($_postvars['To']));
			$q->Add('text', dbescape($_postvars['Body']));
			$q->Add('request', dbescape(print_r($_REQUEST, true)));
			$q->Add('timeadded', time());
			$q->Insert();
			$company=TCompany::initWithPhone($_postvars['To']);
			if ($company->Exists())
				{
					$customer = TCustomer::initWithCustomerPhone($_postvars['From'], $company->ID());
					if ($customer->Exists() && $customer->isAllowedToReceiveSMSFrom())
						{
							if ($customer->isSendingMessagesPending())
								{
									$customer->SetSMSPendingTimestamp();
									$text=trim(strtolower($_postvars['Body']));
									if ($text=='yes')
										{
											$customer->SetSMSAcceptedTag('yes');
											$customer->SetSMSAcceptedTimestamp();
											$pendingmessages=new TPendingMessageList();
											$pendingmessages->SetFilterCustomer($customer);
											$pendingmessages->Load();
											for ($i = 0; $i < $pendingmessages->Count(); $i++)
												{
													$id = TMessage::CreateOutgoing(
														$pendingmessages->items[$i]->Text(),
														$pendingmessages->items[$i]->CompanyID(),
														$pendingmessages->items[$i]->CustomerID(),
														$pendingmessages->items[$i]->TypeTag(),
														$pendingmessages->items[$i]->EmployeeID(),
														$pendingmessages->items[$i]->AssetID(),
														$pendingmessages->items[$i]->SendAfterTimestamp(),
														$pendingmessages->items[$i]->ContactID(),
														$pendingmessages->items[$i]->CampaignID(),
														$pendingmessages->items[$i]->CampaignScheduleID()
													);
													if (!empty($id))
														TMessagesLog::Create('sms', $id, $pendingmessages->items[$i]->CompanyID(), $pendingmessages->items[$i]->CustomerID(), $pendingmessages->items[$i]->SendAfterTimestamp());
													$pendingmessages->items[$i]->SendAsSMSNow();
													$pendingmessages->items[$i]->Remove();
												}
											unset($pendingmessages);
											$success = true;
										}
									elseif ($text=='no')
										{
											$customer->SetSMSAcceptedTag('no');
											$pendingmessages=new TPendingMessageList();
											$pendingmessages->SetFilterCustomer($customer);
											$pendingmessages->Load();
											for ($i = 0; $i < $pendingmessages->Count(); $i++)
												{
													$pendingmessages->items[$i]->Remove();
												}
											unset($pendingmessages);
											$success = true;
										}
									else
										{
											$error_message='Reply with Yes to accept or No to decline.';
										}
								}
							elseif (trim(strtolower($_postvars['Body']))=='stop')
								{
									$sequences = new TCampaignItemList();
									$sequences->SetFilterPhone($_postvars['From']);
									$sequences->SetFilterCompany($customer->CompanyID());
									$sequences->Load();

									for ($i=0; $i<$sequences->Count(); $i++)
										{
											$schedulelist = new TCampaignScheduleList();
											$schedulelist->SetFilterCompany($sequences->items[$i]->CompanyID());
											$schedulelist->SetFilterCustomer($sequences->items[$i]->ID());
											$schedulelist->SetFilterCampaign($sequences->items[$i]->CampaignID());
											$schedulelist->SetFilterStatus('scheduled');
											$schedulelist->Load();

											for ($j=0; $j<$schedulelist->Count(); $j++)
												{
													$schedulelist->items[$j]->SetStatus('Unsubscribed');
													if($sequences->items[$i]->Status() != 'Unsubscribed')
														$sequences->items[$i]->SetStatus('Unsubscribed');
												}

											$customer->SetUnsubscribeStatus(1);
										}
								}
							else
								{
									$customer->IncomingSMS(htmlspecialchars($_postvars['Body']));
									$customer->IncomingSMSAction();
									$success = true;
								}
						}
				}
		}
	if (!$success)
		{
			if (!empty($error_message))
				exit("<?xml version='1.0' encoding='utf-8' ?>\n<Response><Sms>".$error_message."</Sms></Response>");
			else
				exit("<?xml version='1.0' encoding='utf-8' ?>\n<Response><Sms>Your message was not recognized</Sms></Response>");
		}
	else
		{
			if (!empty($success_message))
				exit("<?xml version='1.0' encoding='utf-8' ?>\n<Response><Sms>".$success_message."</Sms></Response>");
			else
				exit("<?xml version='1.0' encoding='utf-8' ?>\n<Response></Response>");
		}


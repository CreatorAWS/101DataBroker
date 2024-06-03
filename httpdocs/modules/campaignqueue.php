<?php

	/**
	 * @param string $template
	 * @param TCompany $company
	 * @param TCampaignItem $campaign_item
	 */

	function replace_tags_campaign($template, $company, $campaign_item)
		{
			$str=str_replace('{FIRST_NAME}', $campaign_item->FirstName(), $template);
			$str=str_replace('{LAST_NAME}', $campaign_item->LastName(), $str);
			$str=str_replace('{CONTACT_NAME}', $campaign_item->FirstName().' '.$campaign_item->LastName(), $str);
			if (!empty($campaign_item->Company()))
				{
					$str=str_replace('{CONTACT_BUSINESS_NAME}', $campaign_item->Company(), $str);
				}
			elseif (!empty($campaign_item->PartnerID()))
				{
					$customer = new TCustomer($campaign_item->PartnerID());
					if ($customer->Exists())
						$str=str_replace('{CONTACT_BUSINESS_NAME}', $customer->GetBusinessName(), $str);
				}
			$str=str_replace('{EMAIL}', $campaign_item->Email(), $str);
			$str=str_replace('{CELLPHONE}', $campaign_item->Phone(), $str);
			$str=str_replace('{BUSINESS}', $company->Name(), $str);
			$str=str_replace('{BUSINESS_CELLPHONE}', $company->Cellphone(), $str);
			$str=str_replace('{OWNER}', '', $str);
			return $str;
		}
	$sleep = true;
	$timeend = time() + 57;

	print("Campaign checking started\n");

	$campaigns=new TCampaignList();
	$campaigns->SetFilterSceduledBefore(time());
	$campaigns->Load();
	for ($i = 0; $i < $campaigns->Count(); $i++)
		{
			print("Campaign ".$campaigns->items[$i]->ID()." started\n");
			$campaigns->items[$i]->SetStatus('started');
			$items=new TCampaignItemList();
			$items->SetFilterCampaign($campaigns->items[$i]->ID());
			$items->Load();
			for ($j = 0; $j < $items->Count(); $j++)
				{
					$items->items[$j]->SetStatusFirstMessageAfter($campaigns->items[$i]->Starttime());
				}
			unset($items);
		}
	unset($campaigns);
	print("Campaign checking finished\n");

	print("Campaign queue started\n");

	while (time()<$timeend)
		{
			$initialmessages=new TCampaignItemList();
			$initialmessages->SetFilterStatuses(Array(
				'pending1'
			));
			$initialmessages->SetFilterNextActionTimeBefore(time());
			$initialmessages->OrderByNextActionTime();
			$initialmessages->Limit(1);
			$initialmessages->Load();
			if ($initialmessages->Count()==1)
				{
					$sleep = false;
					$initialmessage = $initialmessages->items[0];
					print( $initialmessage->ID() . "\n" );
					if ( $initialmessage->Status() == 'pending1' )
						{
							$items=new TCampaignScheduleList();
							$items->SetFilterStatus('scheduled');
							$items->SetFilterCompany($initialmessage->CompanyID());
							$items->SetFilterCampaign($initialmessage->CampaignID());
							$items->SetFilterCustomer($initialmessage->ID());
							$items->OrderByActionTime();
							$messagescount = $items->TotalCount();
							$company  = TCompany::UsingCache( $initialmessage->CompanyID() );
							$campaign = TCampaign::UsingCache( $initialmessage->CampaignID() );
							$customer = new TCustomer($initialmessage->PartnerID());

							if($company->isUnsubscribeMessageSet())
								$unsubscribetext = '<br/><br/> <a href="https://'.main_domain().'/index.php?m=unsubscribe&contact='.$initialmessage->ID().'">Unsubscribe</a>';
							else
								$unsubscribetext = '';

							if ($customer->Exists())
								$customer->SendEmailFromCompany(replace_tags_campaign( $campaign->GetEmailSubject( 1 ), $company, $initialmessage ), replace_tags_campaign( $campaign->GetEmailMessage( 1 ).' '.$unsubscribetext, $company, $initialmessage), '',  true, 0, $campaign->ID(), $initialmessage->ID());
							else
								EmailMessages::QueueEmail( $company, $initialmessage->Email(), replace_tags_campaign( $campaign->GetEmailSubject( 1 ), $company, $initialmessage ), replace_tags_campaign( $campaign->GetEmailMessage( 1 ).' '.$unsubscribetext, $company, $initialmessage), '',  '', $initialmessage->ID());

							if($messagescount==0)
								$initialmessage->SetStatus( 'finished');
							else
								$initialmessage->SetStatus( 'pending2');

							unset( $items );
							unset( $company );
							unset( $initialmessages);
						}
				}
			$bad_id=array();
			while (true)
				{
					$items=new TCampaignScheduleList();
					$items->SetFilterStatus('scheduled');
					$items->SetFilterNextActionTimeBefore(time());
					if (count($bad_id) > 0)
						$items->SetFilterExcludeIDs($bad_id);
					$items->OrderByActionTime();
					$items->Limit(1);
					$items->Load();

					if ($items->Count()==0)
						break;

					$item=$items->items[0];
					print($item->ID()."\n");

					$company=TCompany::UsingCache($item->CompanyID());
					$campaign=TCampaign::UsingCache($item->CampaignID());
					$sequence=TCampaignSequence::UsingCache($item->SequenceID());
					$contact=TCampaignItem::UsingCache($item->CustomerID());

					if($sequence->GetMode()=='voice')
						{
							$bad_id[]=$item->ID();
							continue;
						}

					$sleep = false;

					if($sequence->GetMode()=='email')
						{
							if($contact->HasEmail())
								{
									$item->SetStatus('Email sent');
									$contact->SetStatus('Email sent');
									$customer = new TCustomer($contact->PartnerID());

									if($company->isUnsubscribeMessageSet())
										$unsubscribetext = '<br/><br/> <a href="https://'.main_domain().'/index.php?m=unsubscribe&contact='.$contact->ID().'">Unsubscribe</a>';
									else
										$unsubscribetext = '';

									if ($customer->Exists())
										$customer->SendEmailFromCompany(replace_tags_campaign( $sequence->EmailSubject(), $company, $contact ), replace_tags_campaign( $sequence->EmailMessage().' '.$unsubscribetext, $company, $contact), '',  true, 0, $campaign->ID(), $contact->ID(), $item->ID());
									else
										EmailMessages::QueueEmail($company, $contact->Email(), replace_tags_campaign($sequence->EmailSubject(), $company, $contact), replace_tags_campaign($sequence->EmailMessage().' '.$unsubscribetext, $company, $contact), '',  '', $contact->ID(), $item->ID());
								}
							else
								{
									$item->SetStatus('No email');
									$contact->SetStatus('No email');
								}

						}
					elseif($sequence->GetMode()=='sms')
						{
							if($contact->HasPhone())
								{
									$item->SetStatus('SMS sent');
									$contact->SetStatus('SMS sent');
									$asset_id = '';
									$asset=new TAsset($sequence->IdAsset());
									if($asset->Exists())
										{
											$attachments[]=sm_homepage().$asset->DownloadURL();
											$asset_id = $asset->ID();
										}
									if(!empty($sequence->GetText()))
										{
											if($company->isUnsubscribeMessageSet())
												$sms_message = $sequence->GetText().' Text STOP 2 stop';
											else
												$sms_message = $sequence->GetText();

											$customer = new TCustomer($contact->PartnerID());
											if ($customer->Exists())
												$customer->SendMessage(replace_tags_campaign($sms_message, $company, $contact), 0, true, 'campaign', '', $attachments, '', $asset_id, $contact->ID(), $campaign->ID(), $item->ID());
											else
												SMSMessages::QueueSMS($contact->Phone(), replace_tags_campaign($sms_message, $company, $contact), $company->Cellphone(), '', $attachments, $contact->ID(), $item->ID());
										}
									unset($attachments);
								}
							else
								{
									$item->SetStatus('No Phone Number');
									$contact->SetStatus('No Phone Number');
								}
						}
					unset($company);
					break;
				}
			if($sleep == true)
				sleep(5);
		}

	print("Campaign queue finished\n");

	exit();

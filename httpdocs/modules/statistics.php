<?php

	if ( $userinfo['level'] > 0 )
		{
			sm_add_cssfile('statistics.css');
			sm_add_body_class('dashboard_home');
			/** @var $myaccount TEmployee */
			sm_default_action('view');
			if (sm_action('view'))
				{
					/** @var $currentcompany TCompany */
					if($userinfo['level'] < 3 && $currentcompany->ExpirationTimestamp()!=0 && $currentcompany->ExpirationTimestamp()<time())
						{
							sm_extcore();
							sm_add_body_class('account_expired');
							$m['error_message']= 'Your Account was Expired';
							sm_logout();
						}

					sm_title('Statistics');
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					$m['module'] = sm_current_module();

					if ($_getvars['time'] == 'week')
						$m['dashboard_week_nav_class'] = 'active';
					elseif ($_getvars['time'] == 'twoweek')
						$m['dashboard_twoweek_nav_class'] = 'active';
					elseif ($_getvars['time'] == 'month')
						$m['dashboard_month_nav_class'] = 'active';
					else
						$m['dashboard_day_nav_class'] = 'active';

					//=========================Customers  =========================*/

					$list=new TCustomerList();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterEnabled();
					$list->SetFilterStatus('received');
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$list->SetFilterWeek();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$list->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$list->SetFilterMonth();
					else
						$list->SetFilterDay();

					$m['received']['count'] = $list->TotalCount();
					unset($list);

					$list=new TCustomerList();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterEnabled();
					$list->SetFilterStatus('contact');
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$list->SetFilterWeek();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$list->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$list->SetFilterMonth();
					else
						$list->SetFilterDay();
					$m['contact']['count'] = $list->TotalCount();
					unset($list);

					$list=new TCustomerList();
					$list->SetFilterEnabled();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterStatus('appointment');
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$list->SetFilterWeek();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$list->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$list->SetFilterMonth();
					else
						$list->SetFilterDay();
					$m['appointment']['count'] = $list->TotalCount();
					unset($list);

					$list=new TCustomerList();
					$list->SetFilterEnabled();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterStatus('sold');
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$list->SetFilterWeek();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$list->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$list->SetFilterMonth();
					else
						$list->SetFilterDay();
					$m['sold']['count'] = $list->TotalCount();
					unset($list);

					$list=new TCustomerList();
					$list->SetFilterEnabled();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterStatus('lost');
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$list->SetFilterWeek();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$list->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$list->SetFilterMonth();
					else
						$list->SetFilterDay();
					$m['lost']['count'] = $list->TotalCount();
					unset($list);

					//=========================Messages Sent =========================*/

					$messages=new TMessagesLogList();
					$messages->SetFilterCompany(TCompany::CurrentCompany());
					$messages->SetFilterMessages();
					$messages->SetFilterOutgoing();
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$messages->SetFilter7days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$messages->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$messages->SetFilter30days();
					else
						$messages->SetFilterDay();

					$m['messages_sent']=$messages->TotalCount();
					unset($messages);

					//=========================Messages Received =========================*/

					$messages = new TMessagesLogList();
					$messages->SetFilterCompany(TCompany::CurrentCompany());
					$messages->SetFilterMessages();
					$messages->SetFilterIncoming();
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$messages->SetFilter7days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$messages->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$messages->SetFilter30days();
					else
						$messages->SetFilterDay();

					$m['messages_received']=$messages->TotalCount();
					unset($messages);

					//=========================Calls =========================*/

					$messages = new TMessagesLogList();
					$messages->SetFilterCompany(TCompany::CurrentCompany());
					$messages->SetFilterCalls();
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$messages->SetFilter7days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$messages->SetFilter14days();
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$messages->SetFilter30days();
					else
						$messages->SetFilterDay();

					$m['calls'] = $messages->TotalCount();
					unset($messages);



					//===================Campaigns===============================

					$campaigns=new TCampaignList();
					$campaigns->ExcludeStatusesArray(Array('notfinished'));
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->SetFilterSendBefore(time());
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-7*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-14*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-30*24*3600), time());
					else
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()), time());
					$m['campaigns_sent']['day']['count'] = $campaigns->TotalCount();
					unset($campaigns);

					$campaigns=new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('started', 'notfinished'));
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-7*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-14*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-30*24*3600), time());
					else
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()), time());
					$campaigns->SetFilterSceduledAfter(time());
					$m['campaigns_scheduled']['day']['count'] = $campaigns->TotalCount();
					unset($campaigns);

					$campaigns=new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('started', 'scheduled'));
					if ( !empty($_getvars['time']) && $_getvars['time'] == 'week' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-7*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'twoweek' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-14*24*3600), time());
					elseif ( !empty($_getvars['time']) && $_getvars['time'] == 'month' )
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()-30*24*3600), time());
					else
						$campaigns->SetFilterAddedTimeBetween(SMDateTime::DayStart(time()), time());
					$m['campaigns_draft']['day']['count'] = $campaigns->TotalCount();
					unset($campaigns);



					//========================= Leads Percentage =========================*/

					$users = new TCustomerList();
					$users->SetFilterEnabled();
					$users->SetFilterCompany(TCompany::CurrentCompany());
					$users->SetFilterNotDeleted();
					$users->Load();
					$totalcount=$users->TotalCount();

					$leads=new TCustomerList();
					$leads->SetFilterEnabled();
					$leads->SetFilterCompany(TCompany::CurrentCompany());
					$leads->SetFilterHasAppointments();
					$leads->SetFilterNotDeleted();
					$has_leads=$leads->TotalCount();

					if($totalcount>0)
						$m['percentage']['count'] = round(($has_leads * 100)/$totalcount);

					$customers = new TCustomerList();
					$customers->SetFilterEnabled();
					$customers->SetFilterCompany(TCompany::CurrentCompany());
					$customers->SetFilterHasAppointments();
					$m['total_leads'] = $customers->TotalCount();
					$m['total_count'] = $totalcount;

					$m['total_openers'] = 0;
					$m['total_clickers'] = 0;
					$m['total_unsubscribers'] = 0;
					$campaigns = new TCampaignList();
					$campaigns->SetFilterCompany(TCompany::CurrentCompany());
					$campaigns->ExcludeStatusesArray(Array('notfinished', 'scheduled'));
					$campaigns->Load();
					for ($i = 0; $i < $campaigns->Count(); $i++)
						{
							$campaign = $campaigns->items[$i];
							$m['total_openers'] = $m['total_openers'] + $campaign->OpenersCount();
							$m['total_clickers'] = $m['total_clickers'] + $campaign->ClickerCount();
							$m['total_unsubscribers'] = $m['total_unsubscribers'] + $campaign->UnsubscribersCount();
							unset($campaign);
						}
					unset($campaigns);

				}
		}
	else
		sm_redirect('index.php?m=account');

<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('view');
			sm_add_body_class('dashboard');
			if (sm_action('view'))
				{
					/** @var $currentcompany TCompany */
					if($userinfo['level']<3 && $currentcompany->ExpirationTimestamp()!=0 && $currentcompany->ExpirationTimestamp()<time())
						{
							sm_extcore();
							$m['error_message']= 'Your Account was Expired';
							sm_logout();
						}
					sm_title('Dashboard');
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					$m['module']='dashboard';

					//=========================Calls Made =========================*/

					$data['days'] = [1,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30];
					$month=date("F",time());
					$year=date("Y",time());
					$m['google_search'] = '';
					$m['builtwith_search'] = '';
					$m['max_value'] = 10;
					for ( $i = 0; $i < count($data['days']); $i++)
						{
							$searches = new TGoogleSearchList();
							$searches->SetFilterCompany(TCompany::CurrentCompany());
							$searches->SetFilterCustomPeriod(strtotime($data['days'][$i].' '.$month.' '.$year), strtotime($data['days'][$i].' '.$month.' '.$year));
							if ($i != 0)
								$m['google_search'] .= ', '.$searches->TotalCount();
							else
								$m['google_search'] .= $searches->TotalCount();

							if ($searches->TotalCount() > $m['max_value'])
								$m['max_value'] = $searches->TotalCount();

							$searches = new TOrganizationSearchesList();
							$searches->SetFilterCompany(TCompany::CurrentCompany());
							$searches->SetFilterCustomPeriod(strtotime($data['days'][$i].' '.$month.' '.$year), strtotime($data['days'][$i].' '.$month.' '.$year));
							if ($i != 0)
								$m['builtwith_search'] .= ', '.$searches->TotalCount();
							else
								$m['builtwith_search'] .= $searches->TotalCount();

							if ($searches->TotalCount() > $m['max_value'])
								$m['max_value'] = $searches->TotalCount();
						}

					if ($m['max_value'] % 5 != 0)
						$m['max_value'] = (ceil($m['max_value'])%5 === 0) ? ceil($m['max_value']) : round(($m['max_value']+5/2)/5)*5;


					// ====================== Total Leads =========================//

					$total_leads = 0;
					$leads = new TGoogleLeadsList();
					$leads->SetFilterCompany(TCompany::CurrentCompany());
					$total_leads = $leads->TotalCount();

					$leads = new TOrganizationsSearchLeadsList();
					$leads->SetFilterCompany(TCompany::CurrentCompany());
					$total_leads = $total_leads + $leads->TotalCount();
					$m['total_leads'] = $total_leads;

					if (System::HasGoogleSearchInstalled())
						$m['google_search_available'] = 1;

					if (System::HasBuiltWithInstalled() && !empty(BuiltWithAPIKey()))
						$m['builtwith_search_available'] = 1;


					// ====================== Total Searches =========================//
					$total_searches = 0;
					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$total_searches = $searches->TotalCount();

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$total_searches = $total_searches + $searches->TotalCount();
					$m['total_searches'] = $total_searches;


					//=========================Leads day =========================*/
					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterDay();
					$searches->Load();

					$list = new TGoogleLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['google']['day']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterDay();
					$searches->Load();

					$list = new TOrganizationsSearchLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['builtwith']['day']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					//=========================Leads week =========================*/


					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterWeek();
					$searches->Load();

					$list = new TGoogleLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['google']['week']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterWeek();
					$searches->Load();

					$list = new TOrganizationsSearchLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['builtwith']['week']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					//=========================Leads month =========================*/

					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterMonth();
					$searches->Load();

					$list = new TGoogleLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['google']['month']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$searches->SetFilterMonth();
					$searches->Load();

					$list = new TOrganizationsSearchLeadsList();
					$list->SetFilterSearchesIDs($searches->ExtractIDsArray());
					$m['builtwith']['month']['count'] = $list->TotalCount();
					unset($searches);
					unset($list);

					$limit = 10;

					// google search start ----------------------
					$panel1 = new TPanel();
					$t = new TGrid();
					$t->AddClassnameGlobal('hidehead');
					$t->AddCol('keywords','','50%');
					$t->AddCol('place','','40%');
					$t->AddCol('leads', '');
					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$searches->Limit($limit);
					$searches->Load();

					if ($searches->Count() > 10)
						$m['googlesearchlist']['norecords'] = 1;
					else
						$m['googlesearchlist']['norecords'] = 0;

					for ($i = 0; $i < $searches->Count(); $i++)
						{
							/** @var  $search TGoogleSearch */
							$search = $searches->Item($i);
							$t->Label('keywords', $search->Keywords());
							$t->Label('place', $search->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany(TCompany::CurrentCompany());
							$leads->SetFilterSearch($search);

							$t->Label('leads', $leads->TotalCount());
							$t->URL('keywords', 'index.php?m=searchleads&id='.$search->ID());
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						{
							$t->SingleLineLabel('Nothing Found');
							$t->RowAddClass('no-appoint-schedule');
						}
					$panel1->Add($t);
					unset($t);
					$m['panel_google_search'] = $panel1->Output();

					// builtwith search start ----------------------
					$panel2 = new TPanel();
					$t = new TGrid();
					$t->AddClassnameGlobal('hidehead');
					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$searches->OrderByID();
					$searches->Limit($limit);
					$searches->Load();

					if ($searches->Count() > 10)
						$m['builtwithsearchlist']['norecords'] = 1;
					else
						$m['builtwithsearchlist']['norecords'] = 0;


					$t = new TGrid();
					$t->AddCol('tech', 'Technology');
					$t->AddCol('leads', 'Leads');

					for ($i = 0; $i < $searches->Count(); $i++)
						{
							/** @var  $search TOrganizationSearch */
							$search = $searches->Item($i);
							$t->Label('tech', $search->Tech());
							$t->URL('tech', 'index.php?m=searchtechleads&d=details&id='.$search->ID());
							$t->Label('leads', $search->LeadsCount());
							$t->NewRow();
							unset($search);
						}
					if ($t->RowCount()==0)
						{
							$t->SingleLineLabel('Nothing Found');
							$t->RowAddClass('no-appoint-schedule');
						}
					$panel2->Add($t);
					unset($t);
					$m['panel_builtwith_search'] = $panel2->Output();
				}
		}
	else
		sm_redirect('index.php?m=account');

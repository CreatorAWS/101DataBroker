<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	sm_default_action('view');
	sm_add_cssfile('leadsearch.css');

	if (System::LoggedIn() && (TCompany::CurrentCompany()->SicCodesSearchEnabled() || TCompany::CurrentCompany()->StatesSearchEnabled()))
		{
			if (!SearchSectionAvailableForCompanies() && !TCompany::isSystemCompany())
				exit('Access Denied!');

			if (System::HasBuiltWithInstalled())
				$sm['build_with_installed'] = true;

			if (System::HasGoogleSearchInstalled())
				$sm['google_search_available'] = true;

			if (TCompany::CurrentCompany()->SicCodesSearchEnabled())
				$sm['sic_code_search_available'] = true;

			if (TCompany::CurrentCompany()->StatesSearchEnabled())
				$sm['states_search_available'] = true;

			if (sm_action('download'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied!');
					$product = new TSicCodes($_getvars['id']);
					if (!$product->Exists())
						exit('Access Denied!');

					if ($product->DownloadFileExists())
						{
							$file = $product->DownloadPathRelative();
							sm_session_close();
							header("Content-type: application/octet-stream");
							header("Content-Disposition: attachment; filename=".basename($file));
							$fp = fopen($file, 'rb');
							fpassthru($fp);
							fclose($fp);
							exit;
						}
				}

			if (sm_action('downloadstate'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied!');
					$product = new TState($_getvars['id']);
					if (!$product->Exists())
						exit('Access Denied!');

					if ($product->DownloadFileExists())
						{
							$file = $product->DownloadPathRelative();
							sm_session_close();
							header("Content-type: application/octet-stream");
							header("Content-Disposition: attachment; filename=".basename($file));
							$fp = fopen($file, 'rb');
							fpassthru($fp);
							fclose($fp);
							exit;
						}
				}

			if (sm_action('ajaxsearch'))
				{
					sm_use('ui.interface');

					$ui = new TInterface();
					$results = [];

					if (isset($_postvars) && (!empty($_postvars['search_query']) || $_postvars['search_query'] === '0') )
						{
							$search = new TSicCodesList();
							$search->SetFilterEnabled();
							$search->SetFilterSearchQuery($_postvars['search_query']);
							$search->Load();

							if ( $search->Count() > 0 )
								{
									$ui->html('<ul id="country-list">');
									for ( $i = 0; $i < $search->Count(); $i++ )
										{
											/** @var  $item TSicCodes */
											$item = $search->Item($i);
											if($_postvars['search_query'] == '0' || !empty(intval($_postvars['search_query'])))
												$ui->html('<li><a href="javascript:;" onclick="$(\'#search_query\').val(\'\');$(\'#search_query\').val(\''.$item->Sic().'\');  $(\'#suggesstion-box\').html(\'\');$(\'form#searchForm\').submit();">'.$item->Sic().'</a></li>');
											else
												$ui->html('<li><a href="javascript:;" onclick="$(\'#search_query\').val(\'\');$(\'#search_query\').val(\''.$item->SicName().'\'); $(\'#suggesstion-box\').html(\'\'); $(\'form#searchForm\').submit();">'.$item->SicName().'</a></li>');
										}
									$ui->html('</ul>');
								}
						}

					$ui->Output(true);
				}

			if (sm_action('ajaxstatesearch'))
				{
					sm_use('ui.interface');

					$ui = new TInterface();
					$results = [];

					if (isset($_postvars) && !empty($_postvars['search_query'])  )
						{
							$search = new TStatesList();
							$search->ShowAllItemsIfNoFilters();
							$search->SetFilterSearchQuery($_postvars['search_query']);
							$search->Load();

							if ( $search->Count() > 0 )
								{
									$ui->html('<ul id="country-list">');
									for ( $i = 0; $i < $search->Count(); $i++ )
										{
											/** @var  $item TState */
											$item = $search->Item($i);
											if(strlen($_postvars['search_query']) == '2')
												$ui->html('<li><a href="javascript:;" onclick="$(\'#search_query\').val(\'\');$(\'#search_query\').val(\''.$item->StateAbbr().'\');  $(\'#suggesstion-box\').html(\'\');$(\'form#searchForm\').submit();">'.$item->StateAbbr().'</a></li>');
											else
												$ui->html('<li><a href="javascript:;" onclick="$(\'#search_query\').val(\'\');$(\'#search_query\').val(\''.$item->State().'\'); $(\'#suggesstion-box\').html(\'\'); $(\'form#searchForm\').submit();">'.$item->State().'</a></li>');
										}
									$ui->html('</ul>');
								}
						}

					$ui->Output(true);
				}
			if (TCompany::CurrentCompany()->SicCodesSearchEnabled())
				{
					if (sm_action('view'))
						{
							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.form');
							sm_use('ui.buttons');
							sm_use('ui.modal');

							add_path_home();
							add_path_current();
							sm_title('Search Leads');
							sm_add_jsfile('call.js');
							sm_add_jsfile('leads.js');

							$ui = new TInterface();

							$ui->AddTPL('searchleads_tabs.tpl');

							$data['ajaxSearchURL'] = 'index.php?m='.sm_current_module().'&d=ajaxsearch&theonepage=1';

							$ui->AddTPL('sicsearchform.tpl', '', $data);
							$b=new TButtons();

							$offset=abs(intval($_getvars['from']));
							$limit=20;

							$list = new TSicCodesList();
							$list->SetFilterEnabled();
							$list->OrderByTitle();
							if (!empty($_getvars['search_query']))
								$list->SetFilterSearchQuery($_getvars['search_query']);
							$list->Limit($limit);
							$list->Offset($offset);
							$list->Load();

							$t=new TGrid();
							$t->AddCol('sic_code', 'SIC-Code');
							$t->AddCol('title', 'Title');
							$t->AddCol('rows', 'Count');
							$t->AddCol('download', 'Download');

							for ($i = 0; $i < $list->Count(); $i++)
								{
									/** @var  $siccode TSicCodes */
									$siccode = $list->Item($i);

									$t->Label('sic_code', $siccode->Sic());
									$t->Label('title', $siccode->SicName());
									$t->Label('rows', $siccode->TotalCount());
									if ($siccode->DownloadFileExists())
										{
											$t->Label('download', 'Download');
											$t->URL('download', 'index.php?m='.sm_current_module().'&d=download&id='.$siccode->ID());
										}
									$t->NewRow();
									unset($siccode);
								}
							if ($t->RowCount()==0)
								$t->SingleLineLabel('Nothing found');

							$ui->AddButtons($b);
							$ui->AddGrid($t);
							$ui->AddPagebarParams($list->TotalCount(), $limit, $offset);

							$ui->Output(true);
						}
				}

			if (TCompany::CurrentCompany()->StatesSearchEnabled())
				{
					if (sm_action('states'))
						{
							sm_use('ui.interface');
							sm_use('ui.grid');
							sm_use('ui.form');
							sm_use('ui.buttons');
							sm_use('ui.modal');

							add_path_home();
							add_path_current();
							sm_title('Search Leads');
							sm_add_jsfile('call.js');
							sm_add_jsfile('leads.js');

							$ui = new TInterface();

							$ui->AddTPL('searchleads_tabs.tpl');

							$data['ajaxSearchURL'] = 'index.php?m='.sm_current_module().'&d=ajaxstatesearch&theonepage=1';

							$ui->AddTPL('statessearchform.tpl', '', $data);
							$b=new TButtons();

							$list = new TStatesList();
							$list->ShowAllItemsIfNoFilters();
							$list->OrderByTitle();
							if (!empty($_getvars['search_query']))
								$list->SetFilterSearchQuery($_getvars['search_query']);
							$list->Load();

							$t=new TGrid();
							$t->AddCol('state', 'State');
							$t->AddCol('abbr', 'Abbreviation');
							$t->AddCol('rows', 'Count');
							$t->AddCol('download', 'Download');

							for ($i = 0; $i < $list->Count(); $i++)
								{
									/** @var  $state TState */
									$state = $list->Item($i);

									$t->Label('state', $state->State());
									$t->Label('abbr', $state->StateAbbr());
									$t->Label('rows', $state->TotalCount());
									if ($state->DownloadFileExists())
										{
											$t->Label('download', 'Download');
											$t->URL('download', 'index.php?m='.sm_current_module().'&d=downloadstate&id='.$state->ID());
										}
									$t->NewRow();
									unset($state);
								}
							if ($t->RowCount()==0)
								$t->SingleLineLabel('Nothing found');

							$ui->AddButtons($b);
							$ui->AddGrid($t);

							$ui->Output(true);
						}
				}

		}
	elseif (System::LoggedIn() && System::HasBuiltWithInstalled())
		sm_redirect('index.php?m=searchtechleads');
	elseif (System::LoggedIn() && System::HasGoogleSearchInstalled())
		sm_redirect('index.php?m=searchleads');
	else
		sm_redirect('index.php');

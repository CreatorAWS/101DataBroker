<?php

	if (System::LoggedIn() && System::HasBuiltWithInstalled())
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

			sm_default_action('view');
			sm_add_cssfile('leadsearch.css');

			if (sm_action('bulkexport'))
				{
					if ( is_array($_postvars['ids']) && count($_postvars['ids']) > 0)
						{
							$list = $_postvars['ids'];
							$contents = [];
							for ($i = 0; $i < count($list); $i++)
								{

									$search = new TOrganizationSearch($list[$i]);
									if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
										exit('Access Denied');

									$leads = new TOrganizationsSearchLeadsList();
									$leads->SetFilterCompany(TCompany::CurrentCompany()->ID());
									$leads->SetFilterSearch($search->ID());
									$leads->Load();

									$headers = ['Title', 'Phone', 'Email', 'Address', 'City', 'State', 'Zip', 'Website', 'Facebook', 'Twitter', 'Instagram', 'LinkedIn'];

									if ($leads->Count()>0)
										{
											for ($j = 0; $j < $leads->Count(); $j++)
												{
													/** @var  $lead TOrganizationsSearchLead */
													$lead = $leads->Item($j);
													$contents[] = [$lead->Title(), implode("\n", $lead->GetPhonesArray(true)), implode("\n", $lead->GetEmailsArray()), $lead->Address(), $lead->City(), $lead->State(), $lead->Zip(), $lead->Website(), $lead->FacebookURL(), $lead->TwitterURL(), $lead->InstagramURL(), $lead->LinkedIn()];
												}
										}
								}
							$file_name = 'files/download/builtwith_leads_export_search_bulk_'.TCompany::CurrentCompany()->ID().'.csv';
							$niceFileName = 'leads_export.csv';

							$file = fopen($file_name, "w");

							fputcsv($file, $headers);
							foreach($contents as $content)
								{
									fputcsv($file, $content);
								}
							fclose($file);

							if (file_exists($file_name))
								{
									header("Content-type: application/csv");
									header("Content-Disposition: attachment; filename=".$niceFileName);
									header("Content-length: " . filesize($file_name));
									header("Pragma: no-cache");
									header("Expires: 0");
									$fp = fopen($file_name, 'rb');
									fpassthru($fp);
									fclose($fp);
									unlink($file_name);
									exit;
								}
						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('bulkdelete'))
				{
					if ( is_array($_postvars['ids']) && count($_postvars['ids']) > 0)
						{
							$list = $_postvars['ids'];
							for ($i = 0; $i < count($list); $i++)
								{
									$lead = new TOrganizationsSearchLead($list[$i]);
									if ($lead->Exists() && $lead->CompanyID() == TCompany::CurrentCompany()->ID())
										$lead->Remove();

									unset($lead);
								}
						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postdelete'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TOrganizationSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$search->Remove();

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('search'))
				{
					if ( empty($_postvars['tech']) )
						$error_message = 'Fill Required Fields';

					if (empty($error_message))
						{
							$get_leads = new TBuiltWithAPI();
							$get_leads->GetTechCompaniesList($_postvars['tech']);

							if ($get_leads->HasErrors())
								$error_message = $get_leads->Error();
							else
								{
									$search = TOrganizationSearch::Create();
									$search->SetNextOffset($get_leads->GerNextOffset());
									$search->SetTech($_postvars['tech']);
									$search->SetCompanyID(TCompany::CurrentCompany()->ID());

									$j = 0;

									foreach ($get_leads->GerResults() as $results)
										{
											$lead = TOrganizationsSearchLead::Create();
											$lead->SetCompanyID(TCompany::CurrentCompany()->ID());
											$lead->SetSearchID($search->ID());
											if( !empty($results['domain'])  && empty(url_validator($results['domain'])))
												$lead->SetWebsite(url_cleaner($results['domain']));
											$lead->SetTitle($results['company']);
											foreach ($results['social'] as $social)
												{
													if( strpos($social, "twitter.com") !== false )
														$lead->SetTwitterurl(url_cleaner($social));
													elseif( strpos($social, "facebook.com") !== false )
														$lead->SetFacebookurl(url_cleaner($social));
													elseif( strpos($social, "instagram.com") !== false )
														$lead->SetInstagramurl(url_cleaner($social));
													elseif( strpos($social, "linkedin.com") !== false )
														$lead->SetLinkedin(url_cleaner($social));
												}

											$phones = [];
											if (!empty($results['phones']) && count($results['phones']) > 0)
												{
													for ($i = 0; $i < count($results['phones']); $i++ )
														{
															if ($i == 0)
																$lead->SetPhone($results['phones'][$i]);
															else
																$phones[] = $results['phones'][$i];

														}
												}

											if ( count($phones) > 0 )
												$lead->SetAdditionalPhone($phones);
											unset($phones);

											$emails = [];
											if (!empty($results['emails']) && count($results['emails']) > 0)
												{
													for ($i = 0; $i < count($results['emails']); $i++ )
														{
															if ($i == 0)
																$lead->SetEmail($results['emails'][$i]);
															else
																$emails[] = $results['emails'][$i];
														}
												}

											if ( count($emails) > 0 )
												$lead->SetAdditionalEmail($emails);
											unset($emails);

											$lead->SetCity($results['city']);
											$lead->SetState($results['state']);
											$lead->SetZip($results['zip']);
											$lead->SetCountry($results['country']);

											$j++;
										}
								}
						}

					if (!empty($error_message))
						{
							$leads['message'] = 'error';
							$leads['error_message'] = $error_message;
						}
					else
						{
							$data['message'] = 'success';
							$data['url'] = 'index.php?m='.sm_current_module().'&d=details&id='.$search->ID();
						}

					print_r(json_encode($data));
					exit();
				}

			if (sm_action('ajaxsearctech'))
				{
					sm_use('ui.interface');

					$ui = new TInterface();
					$results = [];

					if (!empty($_postvars['title']))
						{
							$search = new TBuiltWithTechList();
							$search->SetFilterTitle($_postvars['title']);
							$search->Load();
							if ( $search->Count() > 0 )
								{
									$ui->html('<ul id="country-list">');
									for ( $i = 0; $i < $search->Count(); $i++ )
										{
											/** @var  $item TBuiltWithTech */
											$item = $search->Item($i);
											if ($item->HasTitle())
												{
													$ui->html('<li><a href="javascript:;" onclick="$(\'#search-box\').val(\'\');$(\'#search-box\').val(\''.$item->Title().'\'); $(\'#suggesstion-box\').html(\'\');">'.$item->Title().'</a></li>');
												}
										}
									$ui->html('</ul>');
								}
						}

					$ui->Output(true);
				}

			if (sm_action('searchleads'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.form');
					sm_use('ui.buttons');
					sm_use('ui.modal');

					add_path_home();
					add_path('Leads', 'index.php?m='.sm_current_module());
					add_path_current();
					sm_title('Search Leads');
					sm_add_jsfile('call.js');
					sm_add_jsfile('leads.js');

					$ui = new TInterface();
					$ui->AddTPL('searchleads_tabs.tpl');
					$data['searchURL'] = 'index.php?m='.sm_current_module().'&d=search&theonepage=1';
					$data['ajaxSearchURL'] = 'index.php?m='.sm_current_module().'&d=ajaxsearctech&theonepage=1';
					$ui->AddTPL('techleadsearchform.tpl', '', $data);

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());

					if ($searches->TotalCount() > 0)
						{
							$ui->html('<div class="additional_functionality_section">');
							$ui->html('<a href="index.php?m='.sm_current_module().'" class="previous_searches no_results">Previous Searches</a>');
							$ui->html('</div>');
						}
					$ui->Output(true);
				}

			if (sm_action('startcampaign'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TOrganizationSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					if ( isset($_getvars['type']) )
						{
							if ( $_getvars['type'] == 'email' )
								$contactlist_id = $search->ImportLeadsWithEmailsToContacts();
							elseif ( $_getvars['type'] == 'sms' )
								$contactlist_id = $search->ImportLeadsWithAbilityToSendSMSToContacts();
							elseif ( $_getvars['type'] == 'voice' )
								$contactlist_id = $search->ImportLeadsWithValidPhonesToContacts();
						}
					else
						$contactlist_id = $search->ImportLeadsToContacts();
					$search->SetImported();
					$search->Remove();
					sm_redirect('index.php?m=campaignwizard&action=create&list='.$contactlist_id);
				}

			if (sm_action('import'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TOrganizationSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$contactlist_id = $search->ImportLeadsToContacts();
					$search->SetListID($contactlist_id);
					$search->SetImported();
					sm_redirect('index.php?m=customers');
				}

			if (sm_action('download'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TOrganizationSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$list = new TOrganizationsSearchLeadsList();
					$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
					$list->SetFilterSearch($search->ID());
					if (!empty($_getvars['title']))
						$list->SetFilterName($_getvars['title']);
					if (!empty($_getvars['phone']))
						$list->SetFilterPhone($_getvars['phone']);
					if (!empty($_getvars['email']))
						$list->SetFilterEmail($_getvars['email']);
					if (!empty($_getvars['address']))
						$list->SetFilterAddress($_getvars['address']);
					$list->Load();

					$contents = [];

					$headers = ['Title', 'Phone', 'Email', 'Address', 'City', 'State', 'Zip', 'Website', 'Facebook', 'Twitter', 'Instagram', 'LinkedIn'];

					if ($list->Count()>0)
						{
							for ($i = 0; $i < $list->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
									$lead = $list->Item($i);
									$contents[] = [$lead->Title(), implode("\n", $lead->GetPhonesArray(true)), implode("\n", $lead->GetEmailsArray()), $lead->Address(), $lead->City(), $lead->State(), $lead->Zip(), $lead->Website(), $lead->FacebookURL(), $lead->TwitterURL(), $lead->InstagramURL(), $lead->LinkedIn()];
								}
						}

					$file_name = 'files/download/builtwith_leads_export_search_'.$search->ID().'.csv';
					$niceFileName = 'leads_export.csv';

					$file = fopen($file_name, "w");

					fputcsv($file, $headers);
					foreach($contents as $content)
						{
							fputcsv($file, $content);
						}
					fclose($file);

					if (file_exists($file_name))
						{
							header("Content-type: application/csv");
							header("Content-Disposition: attachment; filename=".$niceFileName);
							header("Content-length: " . filesize($file_name));
							header("Pragma: no-cache");
							header("Expires: 0");
							$fp = fopen($file_name, 'rb');
							fpassthru($fp);
							fclose($fp);
							unlink($file_name);
							exit;
						}
					}

			if (sm_action('details'))
				{
					sm_add_jsfile('leads.js');
					$extendedfilters = false;
					$extendedfilters_count = 0;
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TOrganizationSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					sm_add_jsfile('leads.js');
					sm_add_jsfile('call.js');
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.form');
					sm_use('ui.buttons');
					sm_use('ui.modal');

					add_path_home();
					add_path('Search', 'index.php?m='.sm_current_module());
					add_path_current();
					sm_title('Leads - '.$search->Tech());

					$offset=abs(intval($_getvars['from']));
					$limit=30;

					$leads = new TOrganizationsSearchLeadsList();
					$leads->SetFilterCompany(TCompany::CurrentCompany());
					$leads->SetFilterSearch($search->ID());
					$leads->SetFilterVisible();
					$leads->OrderByID();
					if (!empty($_getvars['title']))
						{
							$leads->SetFilterName($_getvars['title']);
							sm_title('Leads - search');
							$extendedfilters_count++;
							$extendedfilters=true;
						}
					if (!empty($_getvars['phone']))
						{
							$leads->SetFilterPhone($_getvars['phone']);
							sm_title('Leads - find by cellphone number');
							$extendedfilters_count++;
							$extendedfilters=true;
						}
					if (!empty($_getvars['email']))
						{
							$leads->SetFilterEmail($_getvars['email']);
							sm_title('Leads - find by email address');
							$extendedfilters_count++;
							$extendedfilters=true;
						}
					if (!empty($_getvars['address']))
						{
							$leads->SetFilterAddress($_getvars['address']);
							sm_title('Leads - find by address');
							$extendedfilters_count++;
							$extendedfilters=true;
						}
					$leads->Limit($limit);
					$leads->Offset($offset);
					$leads->Load();

					$ui = new TInterface();
					$ui->AddTPL('searchhistory_tabs.tpl');
					$ui->html('<div class="buttons mb-10">');

					$b = new TButtons();
					$b->AddClassnameGlobal('flex-1');
					$ui->html('<div class="additional_functionality_section">');


					$ui->html('<a href="index.php?m='.sm_current_module().'&d=postdelete&id='.$search->ID().'&returnto='.urlencode(sm_this_url(['d'=> '', 'id' => ''])).'" class="google_search_button clear">Clear</a>');
					$ui->html('<a href="'.sm_this_url(['d' => 'download', 'returnto' => urlencode(sm_this_url())]).'" class="google_search_button export">Export CSV</a>');
					$ui->html('<a href="javascript:;" data-toggle="modal" data-target="#extendedsearch" class="filters_button '.($extendedfilters?'active':'').'  ab-button"><svg width="17px" height="15px" viewBox="0 0 17 15" style="margin-right: 10px;">
																<g id="board-search-used" fill="CurrentColor" fill-rule="nonzero">
																	<path d="M9.41367188,14.9238281 C9.65976563,15.0410156 9.9,15.0263672 10.134375,14.8798828 C10.36875,14.7333984 10.4917969,14.5253906 10.5035156,14.2558594 L10.5035156,14.2558594 L10.5035156,8.12109375 L16.321875,1.24804688 C16.415625,1.13085938 16.4742188,1.00195312 16.4976563,0.861328125 C16.5210938,0.720703125 16.5005859,0.583007812 16.4361328,0.448242188 C16.3716797,0.313476562 16.2779297,0.205078125 16.1548828,0.123046875 C16.0318359,0.041015625 15.9,0 15.759375,0 L15.759375,0 L0.74765625,0 C0.60703125,0 0.475195313,0.041015625 0.352148438,0.123046875 C0.229101563,0.205078125 0.135351563,0.313476562 0.0708984375,0.448242188 C0.0064453125,0.583007812 -0.0140625,0.720703125 0.009375,0.861328125 C0.0328125,1.00195312 0.09140625,1.13085938 0.18515625,1.24804688 L0.18515625,1.24804688 L6.00351563,8.12109375 L6.00351563,12.7617188 C6.00351563,12.9023438 6.04160156,13.03125 6.11777344,13.1484375 C6.19394531,13.265625 6.29648438,13.359375 6.42539063,13.4296875 L6.42539063,13.4296875 L9.41367188,14.9238281 Z M9.009375,13.0429688 L7.49765625,12.2871094 L7.49765625,7.85742188 C7.49765625,7.66992188 7.4390625,7.50585938 7.321875,7.36523437 L7.321875,7.36523437 L2.36484375,1.51171875 L14.1421875,1.51171875 L9.18515625,7.36523437 C9.06796875,7.50585938 9.009375,7.66992188 9.009375,7.85742188 L9.009375,7.85742188 L9.009375,13.0429688 Z" id="Shape"></path>
																</g></svg>'.(($extendedfilters_count>0)?'<span class="filters_count">'.$extendedfilters_count.'</span>':'').'Filters</a>');
					if ( $extendedfilters_count > 0 )
						$ui->html('<a href="index.php?m='.sm_current_module().'&d='.sm_current_action().'&d=details&id='.$search->ID().'" class="filterbutton ab-button">Clear Filters</a>');
//					if (!$search->isImported())
//						{
//							$ui->html('<a href="'.sm_this_url(['d' => 'import']).'" class="google_search_button import">Save To '.TCompany::CurrentCompany()->LabelForCustomers().'</a>');
////							$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign']).'" class="google_search_button import">Start Campaign</a>');
//							$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'email']).'" class="google_search_button import">Start Email Campaign</a>');
//							$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'sms']).'" class="google_search_button import">Start SMS Campaign</a>');
//							$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'voice']).'" class="google_search_button import">Start Voice Campaign</a>');
//						}


					$b->AddButton('bulkdelete', 'Bulk Delete');
					$b->Style('bulkdelete', 'display:none;');
					$b->AddClassname('bulkdelete', 'bulkdelete');
					$b->OnClick("$('#bulkdeleteform').submit();", 'bulkdelete');

					$b->Button('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" style="margin-right: 5px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg> Search', 'index.php?m='.sm_current_module().'&d=searchleads');
					$b->AddClassname('pull-right flex');

					$ui->AddButtons($b);
					$ui->html('</div>');
					$ui->html('</div>');


					$ui->div_open('extendedsearch', 'modal fade');
					$ui->div_open('', 'modal-dialog');
					$ui->div_open('', 'modal-content');
					$ui->div_open('ext-search', '');

					$ui->h(3, 'Extended Search');
					$f=new TForm('index.php', '', 'get');
					$f->AddHidden('m', sm_current_module());
					$f->AddHidden('d', 'view');
					$f->AddHidden('id', $search->ID());

					$f->AddText('title', 'Title');
					$f->AddText('phone', 'Phone');
					$f->AddText('email', 'Email');
					$f->AddText('address', 'Address');

					$f->LoadValuesArray($_getvars);
					$f->SaveButton('Search');
					$ui->AddForm($f);
					$ui->html('<a href="index.php?m='.sm_current_module().'&d='.sm_current_action().'&d=details&id='.$search->ID().'" style="margin-left: 20px; margin-top: -70px;position: absolute;">Clear Filters</a>');
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();

					$t = new TGrid();
					$t->AddCol('title', 'Company');
					$t->AddCol('phone', 'Phone');
					$t->AddCol('email', 'Email');
					$t->AddCol('address', 'Address');
					$t->AddCol('social', 'Social');
					$t->AddCol('chk1', '', '50px');

					for ($i = 0; $i < $leads->Count(); $i++)
						{
							/** @var  $lead TOrganizationsSearchLead */
							$lead = $leads->Item($i);

							$t->Label('title', $lead->Title());
							$social_media = '';

							if ($lead->HasFacebookURL())
								$social_media .= '<a href="'.$lead->FacebookURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>';
							if ($lead->HasTwitterURL())
								$social_media .= '<a href="'.$lead->TwitterURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>';
							if ($lead->HasInstagramURL())
								$social_media .= '<a href="'.$lead->InstagramURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>';
							if ($lead->HasLinkedin())
								$social_media .= '<a href="'.$lead->Linkedin().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>';
							if ($lead->HasWebsite())
								$social_media .= '<a href="https://'.$lead->Website().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" fill="none" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 01-5.276 3.67m0 0a9 9 0 01-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25" /></svg></a>';

							$t->Label('social', '<div class="social_profiles_wrapper">'.$social_media.'</div>');
							implode("\n", $lead->GetEmailsArray());

							if ($lead->HasEmails())
								{
									$email = '';
									foreach ( $lead->GetEmailsArray() as $addtnl_email)
										{
											if (!empty($email))
												$email .= '<br/>';
											$email .= $addtnl_email;
										}
								}

							$t->Label('email', $email);

							if ($lead->HasPhones())
								{
									$phone = '';
									foreach ( $lead->GetPhonesArray() as $addtnl_phone)
										{
											if (!empty($phone))
												$phone .= '<br/>';
											$phone .= Formatter::Phone($addtnl_phone);
										}
								}

							$t->Label('phone', $phone);
							$t->Label('address', $lead->AddressFormatted());

							$t->Label('chk1', '<label class="checkbox-container"><input type="checkbox" name="ids[]" value="'.$lead->ID().'" onclick="checkfordelete()" class="admintable-control-checkbox" /><span class="checkmark"></span></label>');
							$t->NewRow();
							unset($search);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');

					$ui->html('<form id="bulkdeleteform" action="index.php?m='.sm_current_module().'&d=bulkdelete&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
					$ui->AddGrid($t);

					$ui->html('</form>');
					$ui->AddPagebarParams($leads->TotalCount(), $limit, $offset);
					$ui->div_open('messagemodal', 'modal fade');
					$ui->div_open('', 'modal-dialog');
					$ui->div_open('', 'modal-content');
					$ui->div_open('messagemodal_content');
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();
					$ui->Output(true);
				}

			if (sm_action('view'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.form');
					sm_use('ui.buttons');
					sm_use('ui.modal');
					sm_add_jsfile('leads.js');

					add_path_home();
					add_path_current();
					sm_title('Search History');

					$ui = new TInterface();
					$ui->AddTPL('searchhistory_tabs.tpl');

					$ui->html('<div class="buttons mb-10">');
					$b = new TButtons();

					$b->AddButton('bulkexport', 'Bulk Export');
					$b->Style('bulkexport', 'display:none; margin-left:10px;');
					$b->AddClassname('bulkexport pull-right', 'bulkexport');
					$b->OnClick("$('#bulkexportform').submit();", 'bulkexport');

					$b->Button('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" style="margin-right: 5px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg> Search', 'index.php?m='.sm_current_module().'&d=searchleads');
					$b->AddClassname('pull-right flex mb-10');
					$ui->Add($b);
					$ui->html('</div>');

					$offset=abs(intval($_getvars['from']));
					$limit=30;

					$searches = new TOrganizationSearchesList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$searches->OrderByID();
					$searches->Limit($limit);
					$searches->Offset($offset);
					$searches->Load();

					$t = new TGrid();
					$t->AddCol('tech', 'Technology');
					$t->AddCol('leads', 'Leads');
					$t->AddDelete();
					$t->AddCol('chk1', '', '50px');

					for ($i = 0; $i < $searches->Count(); $i++)
						{
							/** @var  $search TOrganizationSearch */
							$search = $searches->Item($i);
							$t->Label('tech', $search->Tech());
							$t->URL('tech', 'index.php?m='.sm_current_module().'&d=details&id='.$search->ID());
							$t->Label('leads', $search->LeadsCount());
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$search->ID().'&returnto='.urlencode(sm_this_url()));
							$t->Label('chk1', '<label class="checkbox-container"><input type="checkbox" name="ids[]" value="'.$search->ID().'" onclick="checkforexport()" class="admintable-control-checkbox" /><span class="checkmark"></span></label>');
							$t->NewRow();
							unset($search);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');

					$ui->html('<form id="bulkexportform" action="index.php?m='.sm_current_module().'&d=bulkexport&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
					$ui->AddGrid($t);
					$ui->html('</form>');
					$ui->AddPagebarParams($searches->TotalCount(), $limit, $offset);

					$ui->Output(true);
				}
		}
	elseif (System::LoggedIn() && System::HasGoogleSearchInstalled())
		sm_redirect('index.php?m=searchtechleads');
	elseif (System::LoggedIn() && TCompany::CurrentCompany()->SicCodesSearchEnabled())
		sm_redirect('index.php?m=searchsiccode');
	else
		sm_redirect('index.php');
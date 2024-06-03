<?php

	use SKAgarwal\GoogleApi\PlacesApi;

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	require sm_cms_rootdir().'ext/googleplaces/vendor/autoload.php';
	sm_default_action('view');
	sm_add_cssfile('leadsearch.css');

	function getmoregoogleleads($search_id, $place_geo, $keywords, $next_page_token = '')
		{
			$search = new TGoogleSearch($search_id);
			if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
				exit('Access Denied');

			$googlePlaces = new PlacesApi(GooglePlacesAPIKey());
			$get_next_page_token = '';
			$params['pagetoken'] = $next_page_token;
			$params['keyword'] = $keywords;

			$response = $googlePlaces->nearbySearch($place_geo, 200000, $params);
			$results = $response->all();

			if (count($results['results']) > 0)
				{
					$search->SetNextPagetoken($results['next_page_token']);

					$get_next_page_token = $results['next_page_token'];

					foreach ($results['results'] as $item)
						{
							$lead = TGoogleLeads::initWithPlaceID($item['place_id'], TCompany::CurrentCompany()->ID());
							if (!is_object($lead) || !$lead->Exists())
								{
									$response = $googlePlaces->placeDetails($item['place_id']);
									$results = $response->all();
									$lead = TGoogleLeads::Create();
									$lead->SetSearchID($search);
									$lead->SetCompanyID(TCompany::CurrentCompany());
									$lead->SetPlaceID($item['place_id']);
									$lead->SetTitle($results['result']['name']);
									$lead->SetAddress($results['result']['formatted_address']);
									$lead->SetPhone(Cleaner::Phone($results['result']['international_phone_number']));
									if(empty($results['result']['website']))
										$lead->SetEmailChecked();
									else
										$lead->SetWebsite($results['result']['website']);
									$lead->SetRating($results['result']['rating']);
									$lead->SetGoogleUrl($results['result']['url']);
									$lead->SetReviews($results['result']['user_ratings_total']);

									$addressComponents = $results['result']['address_components'];
									foreach ($addressComponents as $addressItem)
										{
											if ($addressItem['types'][0] == 'subpremise')
												$subpremise = $addressItem['long_name'];

											if ($addressItem['types'][0] == 'street_number')
												$streetNumber = $addressItem['long_name'];

											if ($addressItem['types'][0] == 'route')
												$street = $addressItem['long_name'];

											$address1 = trim($streetNumber.' '.$street.' '.$subpremise);

											$lead->SetAddress1($address1);

											if ($addressItem['types'][0] == 'locality')
												$lead->SetCity($addressItem['long_name']);

											if ($addressItem['types'][0] == 'administrative_area_level_1')
												$lead->SetState($addressItem['long_name']);

											if ($addressItem['types'][0] == 'postal_code')
												$lead->SetZip( $addressItem['long_name']);
										}

									if( !$lead->HasCity())
										{
											$longAddress = explode(',', $results['result']['formatted_address']);
											$lead->SetCity(trim($longAddress[1]));
										}

									foreach ($results['result']['reviews'] as $reviews)
										{
											$review = TGoogleLeadReviews::Create();
											$review->SetLeadID($lead->ID());
											$review->SetAuthorName($reviews['author_name']);
											$review->SetAuthorUrl($reviews['author_url']);
											$review->SetProfilePhotoUrl($reviews['profile_photo_url']);
											$review->SetRating($reviews['rating']);
											$review->SetRelativeTimeDescription($reviews['relative_time_description']);
											$review->SetText(Cleaner::RemoveEmoji($reviews['text']));
											$review->SetTime($reviews['time']);
										}
								}
						}
					$data['message'] = 'success';
					$data['url'] = 'index.php?m=searchleads&id='.$search->ID();
					$data['next_page_token'] = $get_next_page_token;
				}
			else
				{
					$data['message'] = 'success';
					$data['url'] = 'index.php?m=searchleads&id='.$search->ID();
					$data['next_page_token'] = $get_next_page_token;
				}

			return $data;
		}

	function getgoogleleads($place_geo, $keywords, $google_place)
		{
			$search = TGoogleSearch::initWithPlaceGeoAndKeyword($place_geo, $keywords);

			if (is_object($search) && $search->Exists())
				{
					$data['search_id'] = $search->ID();
					$data['message'] = 'success';
					$data['url'] = 'index.php?m=searchleads&id='.$search->ID();
					if ($search->HasNextPagetoken())
						$data['pagetoken'] = $search->NextPagetoken();
					else
						$data['pagetoken'] = '';

					return $data;
				}
			else
				{
					$googlePlaces = new PlacesApi(GooglePlacesAPIKey());
					$params['keyword'] = $keywords;

					$response = $googlePlaces->nearbySearch($place_geo, 200000, $params);
					$results = $response->all();
					$leads = [];

					if (count($results['results']) > 0)
						{
							$search = TGoogleSearch::Create();
							$search->SetCompanyID(TCompany::CurrentCompany());
							$search->SetKeywords($keywords);
							$search->SetPlaceGeo($place_geo);
							$search->SetPlaceText($google_place);
							if (!empty($results['next_page_token']))
								$search->SetNextPagetoken($results['next_page_token']);

							foreach ($results['results'] as $item)
								{
									$lead = TGoogleLeads::initWithPlaceID($item['place_id'], TCompany::CurrentCompany()->ID());
									if (is_object($lead) && $lead->Exists())
										$leads[] = $lead->LoadLeadDetailsFormatted();
									else
										{
											$response = $googlePlaces->placeDetails($item['place_id']);
											$results = $response->all();
											$lead = TGoogleLeads::Create();
											$lead->SetSearchID($search);
											$lead->SetCompanyID(TCompany::CurrentCompany());
											$lead->SetPlaceID($item['place_id']);
											$lead->SetTitle($results['result']['name']);
											$lead->SetAddress($results['result']['formatted_address']);
											$lead->SetPhone(Cleaner::Phone($results['result']['international_phone_number']));
											if(empty($results['result']['website']))
												$lead->SetEmailChecked();
											else
												$lead->SetWebsite($results['result']['website']);
											$lead->SetRating($results['result']['rating']);
											$lead->SetGoogleUrl($results['result']['url']);
											$lead->SetReviews($results['result']['user_ratings_total']);

											$addressComponents = $results['result']['address_components'];
											foreach ($addressComponents as $addressItem)
												{
													if ($addressItem['types'][0] == 'subpremise')
														$subpremise = $addressItem['long_name'];

													if ($addressItem['types'][0] == 'street_number')
														$streetNumber = $addressItem['long_name'];

													if ($addressItem['types'][0] == 'route')
														$street = $addressItem['long_name'];

													$address1 = trim($streetNumber.' '.$street.' '.$subpremise);

													$lead->SetAddress1($address1);

													if ($addressItem['types'][0] == 'locality')
														$lead->SetCity($addressItem['long_name']);

													if ($addressItem['types'][0] == 'administrative_area_level_1')
														$lead->SetState($addressItem['long_name']);

													if ($addressItem['types'][0] == 'postal_code')
														$lead->SetZip( $addressItem['long_name']);
												}

											if( !$lead->HasCity())
												{
													$longAddress = explode(',', $results['result']['formatted_address']);
													$lead->SetCity(trim($longAddress[1]));
												}

											foreach ($results['result']['reviews'] as $reviews)
												{
													$review = TGoogleLeadReviews::Create();
													$review->SetLeadID($lead->ID());
													$review->SetAuthorName($reviews['author_name']);
													$review->SetAuthorUrl($reviews['author_url']);
													$review->SetProfilePhotoUrl($reviews['profile_photo_url']);
													$review->SetRating($reviews['rating']);
													$review->SetRelativeTimeDescription($reviews['relative_time_description']);
													$review->SetText(Cleaner::RemoveEmoji($reviews['text']));
													$review->SetTime($reviews['time']);
												}
											$leads[] = $lead->LoadLeadDetailsFormatted();
										}
								}

							$data['search_id'] = $search->ID();
							$data['message'] = 'success';
							$data['url'] = 'index.php?m=searchleads&id='.$search->ID();
							if ($search->HasNextPagetoken())
								$data['next_page_token'] = $search->NextPagetoken();
							else
								$data['next_page_token'] = '';

							return $data;
						}
					else
						{
							$data['message'] = 'error';
							$data['error_message'] = 'Nothing Found';
							return $data;
						}
				}
		}

	if (System::LoggedIn() && System::HasGoogleSearchInstalled())
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

			if (sm_action('bulkdelete'))
				{
					if ( is_array($_postvars['ids']) && count($_postvars['ids']) > 0)
						{
							$list = $_postvars['ids'];
							for ($i = 0; $i < count($list); $i++)
								{
									$lead = new TGoogleLeads($list[$i]);
									if ($lead->Exists() && $lead->CompanyID() == TCompany::CurrentCompany()->ID())
										$lead->Remove();

									unset($lead);
								}
						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('bulkexport'))
				{
					if ( is_array($_postvars['ids']) && count($_postvars['ids']) > 0)
						{
							$list = $_postvars['ids'];
							$contents = [];
							for ($i = 0; $i < count($list); $i++)
								{

									$search = new TGoogleSearch($list[$i]);
									if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
										exit('Access Denied');

									$leads = new TGoogleLeadsList();
									$leads->SetFilterCompany(TCompany::CurrentCompany()->ID());
									$leads->SetFilterSearch($search->ID());
									$leads->Load();

									$headers = ['Title', 'Phone', 'Email', 'Address', 'City', 'State', 'Zip', 'Website', 'Google URL', 'Reviews', 'Avg. Rating', 'Facebook', 'Twitter', 'Instagram', 'LinkedIn'];

									if ($leads->Count()>0)
										{
											for ($j = 0; $j < $leads->Count(); $j++)
												{
													/** @var  $lead TGoogleLeads */
													$lead = $leads->Item($j);
													$contents[] = [$lead->Title(), Formatter::Phone($lead->Phone()), $lead->Email(), $lead->Address1(), $lead->City(), $lead->State(), $lead->Zip(), $lead->Website(), $lead->GoogleUrl(), $lead->Reviews(), $lead->Rating(), $lead->FacebookURL(), $lead->TwitterURL(), $lead->InstagramURL(), $lead->LinkedInURL()];
												}
										}
								}

							$file_name = 'files/download/leads_export_search_bulk_'.TCompany::CurrentCompany()->ID().'.csv';
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

			if (sm_action('postdelete'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$lead = new TGoogleLeads($_getvars['id']);
					if (!$lead->Exists() || $lead->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$lead->Remove();

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('download'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TGoogleSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					if (!empty($_getvars['tag']))
						{
							$_getvars['tags_selected'] = $_getvars['tag'];
							$_getvars['tag']='';
						}

					$tags = new TTagList();
					$tags->OrderByName();
					$tags->Load();

					$list = new TGoogleLeadsList();
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

					$headers = ['Title', 'Phone', 'Email', 'Address', 'City', 'State', 'Zip', 'Website', 'Google URL', 'Reviews', 'Avg. Rating', 'Facebook', 'Twitter', 'Instagram', 'LinkedIn'];

					if ($list->Count()>0)
						{
							for ($i = 0; $i < $list->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $list->Item($i);
									$contents[] = [$lead->Title(), $lead->Phone(), $lead->Email(), $lead->Address1(), $lead->City(), $lead->State(), $lead->Zip(), $lead->Website(), $lead->GoogleUrl(), $lead->Reviews(), $lead->Rating(), $lead->FacebookURL(), $lead->TwitterURL(), $lead->InstagramURL(), $lead->LinkedInURL()];
								}
						}

					$file_name = 'files/download/leads_export_search_'.$search->ID().'.csv';
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

			if (sm_action('searchplace'))
				{
					if (empty($_postvars['place_geo']) || empty($_postvars['google_place']) || empty($_postvars['keywords']))
						$error_message = 'Fill Required Fields';

					if (empty($error_message))
						{
							$leads = getgoogleleads($_postvars['place_geo'], $_postvars['keywords'], $_postvars['google_place']);

							if (isset($leads['next_page_token']) && !empty($leads['next_page_token']))
								{
									$get_more = true;
									$next_page_token = $leads['next_page_token'];
									while ($get_more)
										{
											if (isset($next_page_token) && !empty($next_page_token))
												$moreleads = getmoregoogleleads($leads['search_id'], $_postvars['place_geo'], $_postvars['keywords'], $next_page_token);

											$next_page_token = $moreleads['next_page_token'];

											if (!isset($next_page_token) || empty($next_page_token))
												$get_more = false;
										}
								}
						}
					else
						{
							$leads['message'] = 'error';
							$leads['error_message'] = $error_message;
						}

					print_r(json_encode($leads));
					exit();
				}

			if (sm_action('loadmoreleads'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TGoogleSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					if (empty($_postvars['place_geo']) || empty($_postvars['google_place']) || empty($_postvars['keywords']))
						$error_message = 'Fill Required Fields';

					if (empty($error_message) && !empty($_postvars['next_page_token']))
						{
							$googlePlaces = new PlacesApi(GooglePlacesAPIKey());

							if (!empty($_postvars['next_page_token']))
								$params['pagetoken'] = $_postvars['next_page_token'];

							$params['keyword'] = $_postvars['keywords'];

							$response = $googlePlaces->nearbySearch($_postvars['place_geo'], 200000, $params);
							$results = $response->all();
							$leads = [];

							if (count($results['results']) > 0)
								{
									$search->SetNextPagetoken($results['next_page_token']);

									foreach ($results['results'] as $item)
										{
											$lead = TGoogleLeads::initWithPlaceID($item['place_id'], TCompany::CurrentCompany()->ID());
											if (is_object($lead) && $lead->Exists())
												{
													$leads[] = $lead->LoadLeadDetailsFormatted();
												}
											else
												{
													$response = $googlePlaces->placeDetails($item['place_id']);
													$results = $response->all();

													$lead = TGoogleLeads::Create();
													$lead->SetSearchID($search);
													$lead->SetCompanyID(TCompany::CurrentCompany());
													$lead->SetPlaceID($item['place_id']);
													$lead->SetTitle($results['result']['name']);
													$lead->SetAddress($results['result']['formatted_address']);
													$lead->SetPhone(Cleaner::Phone($results['result']['international_phone_number']));
													if(empty($results['result']['website']))
														$lead->SetEmailChecked();
													else
														$lead->SetWebsite($results['result']['website']);
													$lead->SetRating($results['result']['rating']);
													$lead->SetGoogleUrl($results['result']['url']);

													$addressComponents = $results['result']['address_components'];
													foreach ($addressComponents as $addressItem)
														{
															if ($addressItem['types'][0] == 'subpremise')
																$subpremise = $addressItem['long_name'];

															if ($addressItem['types'][0] == 'street_number')
																$streetNumber = $addressItem['long_name'];

															if ($addressItem['types'][0] == 'route')
																$street = $addressItem['long_name'];

															$address1 = trim($streetNumber.' '.$street.' '.$subpremise);

															$lead->SetAddress1($address1);

															if ($addressItem['types'][0] == 'locality')
																$lead->SetCity($addressItem['long_name']);

															if ($addressItem['types'][0] == 'administrative_area_level_1')
																$lead->SetState($addressItem['long_name']);

															if ($addressItem['types'][0] == 'postal_code')
																$lead->SetZip( $addressItem['long_name']);
														}

													if( !$lead->HasCity())
														{
															$longAddress = explode(',', $results['result']['formatted_address']);
															$lead->SetCity(trim($longAddress[1]));
														}

													$lead->SetReviews($results['result']['user_ratings_total']);
													foreach ($results['result']['reviews'] as $reviews)
														{
															$review = TGoogleLeadReviews::Create();
															$review->SetLeadID($lead->ID());
															$review->SetAuthorName($reviews['author_name']);
															$review->SetAuthorUrl($reviews['author_url']);
															$review->SetProfilePhotoUrl($reviews['profile_photo_url']);
															$review->SetRating($reviews['rating']);
															$review->SetRelativeTimeDescription($reviews['relative_time_description']);
															$review->SetText(Cleaner::RemoveEmoji($reviews['text']));
															$review->SetTime($reviews['time']);
														}
													$leads[] = $lead->LoadLeadDetailsFormatted();
												}
										}
								}
							else
								sm_notify('No New Leads Loaded');
						}

					if (!empty($error_message))
						sm_set_action('view');
					else
						sm_redirect($_getvars['returnto']);
				}

			if (sm_action('reseatsearch'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TGoogleSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$search->Remove();

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('startcampaign'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TGoogleSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$tags = new TTagList();
					$tags->OrderByName();
					$tags->Load();

					if (!empty($_getvars['tag']))
						{
							$_getvars['tags_selected'] = $_getvars['tag'];
							$_getvars['tag']='';
						}

					$tagsfilter=Array();
					if(!empty($_getvars['tags_selected']))
						{
							$tags_array=explode(',', $_getvars['tags_selected']);
							for ($i = 0; $i < $tags->Count(); $i++)
								{
									for($j=0; $j<count($tags_array); $j++)
										{
											if($tags_array[$j]==$tags->items[$i]->ID())
												{
													$tmp=$tags->items[$i]->GetCustomerIDsArray();
													$tagsfilter = array_merge($tagsfilter, $tmp);
												}
										}
								}
							if (count($tagsfilter)>0)
								$tagsfilter = array_values(array_unique($tagsfilter));
						}
					if ( isset($_getvars['type']) )
						{
							if ( $_getvars['type'] == 'email' )
								$contactlist_id = $search->ImportLeadsWithEmailsToContacts($tagsfilter);
							elseif ( $_getvars['type'] == 'sms' )
								$contactlist_id = $search->ImportLeadsWithAbilityToSendSMSToContacts($tagsfilter);
							elseif ( $_getvars['type'] == 'voice' )
								$contactlist_id = $search->ImportLeadsWithValidPhonesToContacts($tagsfilter);
						}
					else
						$contactlist_id = $search->ImportToContacts($tagsfilter);
					$search->SetImported();
					$search->Remove();
					sm_redirect('index.php?m=campaignwizard&action=create&list='.$contactlist_id);
				}

			if (sm_action('import'))
				{
					if (empty($_getvars['id']))
						exit('Access Denied');

					$search = new TGoogleSearch($_getvars['id']);
					if (!$search->Exists() || $search->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					$tags = new TTagList();
					$tags->OrderByName();
					$tags->Load();

					if (!empty($_getvars['tag']))
						{
							$_getvars['tags_selected'] = $_getvars['tag'];
							$_getvars['tag']='';
						}

					$tagsfilter=Array();
					if(!empty($_getvars['tags_selected']))
						{
							$tags_array=explode(',', $_getvars['tags_selected']);
							for ($i = 0; $i < $tags->Count(); $i++)
								{
									for($j=0; $j<count($tags_array); $j++)
										{
											if($tags_array[$j]==$tags->items[$i]->ID())
												{
													$tmp=$tags->items[$i]->GetCustomerIDsArray();
													$tagsfilter = array_merge($tagsfilter, $tmp);
												}
										}
								}
							if (count($tagsfilter)>0)
								{
									$tagsfilter = array_values(array_unique($tagsfilter));
								}
						}
					$contactlist_id = $search->ImportToContacts($tagsfilter);
					$search->SetListID($contactlist_id);
					$search->SetImported();
					sm_redirect('index.php?m=customers');
				}

			if (sm_action('getreviews'))
				{
					sm_use('ui.interface');

					if (empty($_getvars['id']))
						exit('Access Denied');

					$lead = new TGoogleLeads($_getvars['id']);
					if (!$lead->Exists() || $lead->CompanyID() != TCompany::CurrentCompany()->ID())
						exit('Access Denied');

					sm_use('ui.interface');
					$ui = new TInterface();

					$list = new TGoogleLeadReviewsList();
					$list->SetFilterLead($lead->ID());
					$list->Load();

					for ( $i = 0; $i < $list->Count(); $i++ )
						{
							/** @var  $review  TGoogleLeadReviews */
							$review = $list->Item($i);

							$data[] = [
								'author_name' => $review->AuthorName(),
								'author_url' => $review->AuthorUrl(),
								'author_img' => $review->ProfilePhotoUrl(),
								'rating' => $review->Rating(),
								'time' => $review->RelativeTimeDescription(),
								'text' => $review->Text()
							];
						}
					$ui->AddTPL('leadreviews.tpl', '', $data);
					$ui->Output(true);
				}

			if (sm_action('getaddress'))
				{
					if (!empty($_postvars['placeid']))
						{
							$googlePlaces = new PlacesApi(GooglePlacesAPIKey());
							$response = $googlePlaces->placeDetails($_getvars['placeid']);
							$results = $response->all();
							exit($results['result']['adr_address']);
						}
					else
						exit();
				}

			if (sm_action('getemailstatus'))
				{
					if (!empty($_getvars['id']))
						{
							$lead = new TGoogleLeads($_getvars['id']);
							if (!$lead->Exists())
								exit('No Email');
							if ( $lead->HasEmail() )
								{
									print($lead->Email());
									exit();
								}
							elseif (!$lead->HasEmail() && $lead->EmailCheckedVal() == 0)
								{
									print('<img src="themes/default/images/admintable/processing.gif">');
									exit();
								}
							else
								{
									print('');
									exit();
								}
						}
					else
						{
							print('No Email');
							exit();
						}
				}

			if (sm_action('search_history'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					add_path_home();
					add_path_current();
					sm_title('Search History');

					sm_add_jsfile('leads.js');
					$sm['googlekey'] = GooglePlacesAPIKey();

					$ui = new TInterface();

					$ui->AddTPL('searchhistory_tabs.tpl');

					$ui->html('<div class="buttons mb-10">');
					$b = new TButtons();

					$b->AddButton('bulkexport', 'Bulk Export');
					$b->Style('bulkexport', 'display:none; margin-left:10px;');
					$b->AddClassname('bulkexport pull-right', 'bulkexport');
					$b->OnClick("$('#bulkexportform').submit();", 'bulkexport');

					$b->Button('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" style="margin-right: 5px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg> Search', 'index.php?m='.sm_current_module());
					$b->AddClassname('pull-right flex mb-10');

					$ui->Add($b);
					$ui->html('</div>');

					$offset = abs(intval($_getvars['from']));
					$limit = 20;

					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());
					$searches->OrderByID();
					$searches->Limit($limit);
					$searches->Offset($offset);
					$searches->Load();

					$t = new TGrid();
					$t->AddCol('keywords', 'Keywords');
					$t->AddCol('place', 'Place');
					$t->AddCol('leads', 'Leads');
					$t->AddCol('actions_col', '', '100');
					$t->AddDelete();
					$t->AddCol('chk1', '', '50px');
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
							$t->URL('keywords', 'index.php?m='.sm_current_module().'&id='.$search->ID());
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=reseatsearch&id='.$search->ID().'&returnto='.urlencode(sm_this_url()));
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
					$sm['googlekey'] = GooglePlacesAPIKey();
					$extendedfilters = false;
					$extendedfilters_count = 0;

					if (!empty($_getvars['id']))
						{
							$search = new TGoogleSearch($_getvars['id']);
							if ($search->Exists())
								{
									$data['keyword'] = $search->Keywords();
									$data['google_place'] = $search->PlaceText();
									$data['place_geo'] = $search->PlaceGeo();
									$data['next_page_token'] = $search->NextPagetoken();
								}
						}

					if (!empty($_getvars['tag']))
						{
							$_getvars['tags_selected'] = $_getvars['tag'];
							$_getvars['tag']='';
						}

					$searches = new TGoogleSearchList();
					$searches->SetFilterCompany(TCompany::CurrentCompany());

					$data['searchURL'] = 'index.php?m='.sm_current_module().'&d=searchplace&theonepage=1';
					if (!empty($_getvars['id']) && $search->Exists())
						$data['is_imported'] = 1;

					if (!empty($_getvars['id']))
						$ui->AddTPL('searchhistory_tabs.tpl');
					else
						$ui->AddTPL('searchleads_tabs.tpl');
					
					$ui->AddTPL('leadsearchform.tpl', '', $data);
					$b=new TButtons();
					if (!empty($_getvars['id']))
						{
							$offset=abs(intval($_getvars['from']));
							$limit=20;

							if ($search->Exists())
								{
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
									$ui->html('<a href="index.php?m='.sm_current_module().'&d='.sm_current_action().'&id='.$search->ID().'" style="margin-left: 20px; margin-top: -70px;position: absolute;">Clear Filters</a>');
									$ui->div_close();
									$ui->div_close();
									$ui->div_close();
									$ui->div_close();

									$leads = new TGoogleLeadsList();
									$leads->SetFilterCompany(TCompany::CurrentCompany());
									$leads->SetFilterSearch($search);
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
									$leads->OrderByID();
									$leads->Limit($limit);
									$leads->Offset($offset);
									$leads->Load();

									$b->AddButton('bulkdelete', 'Bulk Delete');
									$b->Style('bulkdelete', 'display:none;');
									$b->AddClassname('bulkdelete', 'bulkdelete');
									$b->OnClick("$('#bulkdeleteform').submit();", 'bulkdelete');

									$t=new TGrid();
									$t->AddCol('id', '');
									$t->AddCol('title', 'Title');
									$t->AddCol('phone', 'Phone');
									$t->AddCol('email', 'Email');
									$t->AddCol('address', 'Address');
									$t->AddCol('tags', 'Tags');
									$t->AddCol('website', 'Website');
									$t->AddCol('googleURL', 'Google Link', 130);
									$t->AddCol('social', 'Social Media');
									$t->AddCol('reviews', 'Reviews');
									$t->AddCol('rating', 'Avg. Rating', 20);
									$t->AddCol('actions_col', '', '10');
									$t->AddCol('chk1', '', '50px');

									$verification_needed = false;

									for ($i = 0; $i < $leads->Count(); $i++)
										{
											/** @var  $lead TGoogleLeads */
											$lead = $leads->Item($i);

											if ($lead->EmailCheckedVal() == 0)
												$verification_needed = true;

											$t->Label('id', '<span>'.$lead->ID().'</span>');
											$t->Label('title', $lead->Title());
											$t->Label('phone', Formatter::Phone($lead->Phone()));

											if ( empty($lead->EmailCheckedVal()) )
												$t->Label('email', '<img src="themes/default/images/admintable/processing.gif">');
											elseif ( $lead->EmailCheckedVal() == 1 && $lead->Email() )
												$t->Label('email', $lead->Email());

											$t->Label('address', $lead->Address());
											if ($lead->HasWebsite())
												{
													$t->Label('website', 'Website');
													$t->URL('website', $lead->Website(), true);
												}
											if ($lead->HasGoogleUrl())
												{
													$t->Label('googleURL', 'Google URL');
													$t->URL('googleURL', $lead->GoogleUrl(), true);
												}

											$social_media = '';
											if ( $lead->HasSocialURLS() )
												{
													if ($lead->HasFacebookURL())
														$social_media .= '<a href="'.$lead->FacebookURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>';
													if ($lead->HasTwitterURL())
														$social_media .= '<a href="'.$lead->TwitterURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>';
													if ($lead->HasInstagramURL())
														$social_media .= '<a href="'.$lead->InstagramURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>';
													if ($lead->HasLinkedInURL())
														$social_media .= '<a href="'.$lead->LinkedInURL().'" class="social_profiles" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>';
												}
											$t->Label('social', '<div class="social_profiles_wrapper">'.$social_media.'</div>');
											$t->Label('reviews', $lead->Reviews());
											$t->URL('reviews', 'javascript:;');

											$onclick = "Modal.open({ajaxContent:'index.php?m=".sm_current_module()."&d=getreviews&id=".$lead->ID()."&theonepage=1',width:'50%',height:'60%',draggable: false,ajaxSuccessCallback:function(){addcustomer_init();$('select').niceSelect();$('#js-tags-selector').niceSelect('destroy');$('#js-tags-selector').selectize({plugins: ['remove_button'],create: true});}})";

											$t->OnClick('reviews', $onclick);
											$t->Label('rating', $lead->Rating());
											$t->Label('actions_col', '<svg width="3px" height="15px" viewBox="0 0 3 15" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Symbols" stroke="none" stroke-width="1" fill="#777EBB" fill-rule="evenodd"><path d="M1.5,12 C2.32842712,12 3,12.6715729 3,13.5 C3,14.3284271 2.32842712,15 1.5,15 C0.671572875,15 0,14.3284271 0,13.5 C0,12.6715729 0.671572875,12 1.5,12 Z M1.5,6 C2.32842712,6 3,6.67157288 3,7.5 C3,8.32842712 2.32842712,9 1.5,9 C0.671572875,9 0,8.32842712 0,7.5 C0,6.67157288 0.671572875,6 1.5,6 Z M1.5,-1.77635684e-15 C2.32842712,-1.77635684e-15 3,0.671572875 3,1.5 C3,2.32842712 2.32842712,3 1.5,3 C0.671572875,3 0,2.32842712 0,1.5 C0,0.671572875 0.671572875,-1.77635684e-15 1.5,-1.77635684e-15 Z" id="path-1"></path></g></svg>');
											$t->DropDownItem('actions_col', 'Delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$lead->ID().'&returnto='.urlencode(sm_this_url()), 'Are you sure?');
											$t->Label('chk1', '<label class="checkbox-container"><input type="checkbox" name="ids[]" value="'.$lead->ID().'" onclick="checkfordelete()" class="admintable-control-checkbox" /><span class="checkmark"></span></label>');
											$t->NewRow();
											unset($lead);
										}
									if ($t->RowCount()==0)
										$t->SingleLineLabel('Nothing found');

									if ($verification_needed)
										sm_add_jsfile('statuschecker.js');

									$ui->html('<div class="additional_functionality_section">');
									if ($searches->TotalCount() > 0)
										$ui->html('<a href="index.php?m='.sm_current_module().'&d=search_history" class="previous_searches">Previous Searches</a>');
									if (!empty($search->NextPagetoken()))
										{
											if (!empty($error_message))
												$ui->NotificationError($error_message);

											$f = new TForm('index.php?m='.sm_current_module().'&d=loadmoreleads&id='.$search->ID().'&returnto='.urlencode(sm_this_url()));
											$f->AddHidden('place_geo', $search->PlaceGeo());
											$f->AddHidden('google_place', $search->PlaceText());
											$f->AddHidden('keywords', $search->Keywords());
											$f->AddHidden('next_page_token', $search->NextPagetoken());
											$f->SaveButton('Load More');
											$ui->AddForm($f);
										}

									$ui->html('<a href="index.php?m='.sm_current_module().'&d=reseatsearch&id='.$search->ID().'&returnto='.urlencode(sm_this_url(['id' => ''])).'" class="google_search_button clear">Clear</a>');
									$ui->html('<a href="'.sm_this_url(['d' => 'download', 'returnto' => urlencode(sm_this_url())]).'" class="google_search_button export">Export CSV</a>');

									$ui->html('<a href="javascript:;" data-toggle="modal" data-target="#extendedsearch" class="filters_button '.($extendedfilters?'active':'').'  ab-button"><svg width="17px" height="15px" viewBox="0 0 17 15" style="margin-right: 10px;">
																<g id="board-search-used" fill="CurrentColor" fill-rule="nonzero">
																	<path d="M9.41367188,14.9238281 C9.65976563,15.0410156 9.9,15.0263672 10.134375,14.8798828 C10.36875,14.7333984 10.4917969,14.5253906 10.5035156,14.2558594 L10.5035156,14.2558594 L10.5035156,8.12109375 L16.321875,1.24804688 C16.415625,1.13085938 16.4742188,1.00195312 16.4976563,0.861328125 C16.5210938,0.720703125 16.5005859,0.583007812 16.4361328,0.448242188 C16.3716797,0.313476562 16.2779297,0.205078125 16.1548828,0.123046875 C16.0318359,0.041015625 15.9,0 15.759375,0 L15.759375,0 L0.74765625,0 C0.60703125,0 0.475195313,0.041015625 0.352148438,0.123046875 C0.229101563,0.205078125 0.135351563,0.313476562 0.0708984375,0.448242188 C0.0064453125,0.583007812 -0.0140625,0.720703125 0.009375,0.861328125 C0.0328125,1.00195312 0.09140625,1.13085938 0.18515625,1.24804688 L0.18515625,1.24804688 L6.00351563,8.12109375 L6.00351563,12.7617188 C6.00351563,12.9023438 6.04160156,13.03125 6.11777344,13.1484375 C6.19394531,13.265625 6.29648438,13.359375 6.42539063,13.4296875 L6.42539063,13.4296875 L9.41367188,14.9238281 Z M9.009375,13.0429688 L7.49765625,12.2871094 L7.49765625,7.85742188 C7.49765625,7.66992188 7.4390625,7.50585938 7.321875,7.36523437 L7.321875,7.36523437 L2.36484375,1.51171875 L14.1421875,1.51171875 L9.18515625,7.36523437 C9.06796875,7.50585938 9.009375,7.66992188 9.009375,7.85742188 L9.009375,7.85742188 L9.009375,13.0429688 Z" id="Shape"></path>
																</g></svg>'.(($extendedfilters_count>0)?'<span class="filters_count">'.$extendedfilters_count.'</span>':'').'Filters</a>');
									if ( $extendedfilters_count > 0 )
										$ui->html('<a href="index.php?m='.sm_current_module().'&d='.sm_current_action().'&id='.$search->ID().'" class="filterbutton ab-button">Clear Filters</a>');

									// if (!$search->isImported())
									// 	{
									// 		$ui->html('<a href="'.sm_this_url(['d' => 'import']).'" class="google_search_button import">Save To '.TCompany::CurrentCompany()->LabelForCustomers().'</a>');
									// 		$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign']).'" class="google_search_button import">Start Campaign</a>');
									// 		$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'email']).'" class="google_search_button import">Start Email Campaign</a>');
									// 		$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'sms']).'" class="google_search_button import">Start SMS Campaign</a>');
									// 		$ui->html('<a href="'.sm_this_url(['d' => 'startcampaign', 'type' => 'voice']).'" class="google_search_button import">Start Voice Campaign</a>');
									// 	}
									$ui->html('</div>');

									$ui->AddButtons($b);
									$ui->html('<form id="bulkdeleteform" action="index.php?m='.sm_current_module().'&d=bulkdelete&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
									$ui->AddGrid($t);
									$ui->html('</form>');
									$ui->div_open('messagemodal', 'modal fade');
									$ui->div_open('', 'modal-dialog');
									$ui->div_open('', 'modal-content');
									$ui->div_open('messagemodal_content');
									$ui->div_close();
									$ui->div_close();
									$ui->div_close();
									$ui->div_close();
									$ui->AddPagebarParams($leads->TotalCount(), $limit, $offset);
								}
						}
					else
						{
							if ($searches->TotalCount() > 0)
								{
									$ui->html('<div class="additional_functionality_section">');
									$ui->html('<a href="index.php?m='.sm_current_module().'&d=search_history" class="previous_searches no_results">Previous Searches</a>');
									$ui->html('</div>');
								}
						}
					$ui->Output(true);
				}

			if (sm_action('loadfilters'))
				{
					exit($_postvars);
				}
		}
	elseif (System::LoggedIn() && System::HasBuiltWithInstalled())
		sm_redirect('index.php?m=searchtechleads');
	elseif (System::LoggedIn() && TCompany::CurrentCompany()->SicCodesSearchEnabled())
		sm_redirect('index.php?m=searchsiccode');
	else
		sm_redirect('index.php');

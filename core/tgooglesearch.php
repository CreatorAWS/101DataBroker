<?php

	if (!defined("TGoogleSearch_DEFINED"))
		{
			Class TGoogleSearch
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('google_searches')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function initWithPlaceGeoAndKeyword($place_geo, $keywords)
						{
							use_api('cleaner');
							$info = TQuery::ForTable('google_searches')->Add('place_geo', dbescape($place_geo))->Add('keywords', dbescape($keywords))->OrderBy('id')->Get();
							$search = new TGoogleSearch($info);
							return $search;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TGoogleSearch'][$id]))
								{
									$object=new TGoogleSearch($id);
									if ($object->Exists())
										$sm['cache']['TGoogleSearch'][$id]=$object->GetRawData();
								}
							else
								$object=new TGoogleSearch($sm['cache']['TGoogleSearch'][$id]);
							return $object;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}


					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>Cleaner::IntObjectID($val)));
						}

					function Keywords()
						{
							return $this->info['keywords'];
						}

					function SetKeywords($val)
						{
							$this->UpdateValues(Array('keywords'=>$val));
						}

					function HasKeywords()
						{
							return !empty($this->info['keywords']);
						}

					function isImported()
						{
							return $this->info['is_imported'] == 1;
						}

					function SetImported()
						{
							$this->UpdateValues(Array('is_imported'=>1));
						}

					function PlaceGeo()
						{
							return $this->info['place_geo'];
						}

					function SetPlaceGeo($val)
						{
							$this->UpdateValues(Array('place_geo'=>$val));
						}

					function HasPlaceGeo()
						{
							return !empty($this->info['place_geo']);
						}


					function PlaceText()
						{
							return $this->info['place_text'];
						}

					function SetPlaceText($val)
						{
							$this->UpdateValues(Array('place_text'=>$val));
						}

					function HasPlaceText()
						{
							return !empty($this->info['place_text']);
						}

					function ListID()
						{
							return $this->info['id_list'];
						}

					function SetListID($val)
						{
							$this->UpdateValues(Array('id_list'=>intval($val)));
						}

					function NextPagetoken()
						{
							return $this->info['next_pagetoken'];
						}

					function SetNextPagetoken($val)
						{
							$this->UpdateValues(Array('next_pagetoken'=>$val));
						}

					function HasNextPagetoken()
						{
							return !empty($this->info['next_pagetoken']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('google_searches');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create()
						{
							$sql = new TQuery('google_searches');
							$sql->Add('addedtime', time());
							$object = new TGoogleSearch($sql->Insert());
							return $object;
						}

					function ImportToContacts($tagsfilter = [])
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Keywords().' - '.$this->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							if (is_array($tagsfilter) && count($tagsfilter) > 0)
								$leads->SetFilterCustomerIDs($tagsfilter);
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);

									if ($lead->HasCustomerID())
										{
											$customer = new TCustomer($lead->CustomerID());
											if (is_object($customer) && $customer->Exists())
												{
													$customer->Enable();
													if(!$contactList->HasContactID($customer->ID()))
														$contactList->SetContactID($customer->ID());
												}
										}
								}

							return $contactList->ID();
						}
					function ImportLeadsWithEmailsToContacts($tagsfilter = [])
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Keywords().' - '.$this->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							if (is_array($tagsfilter) && count($tagsfilter) > 0)
								$leads->SetFilterCustomerIDs($tagsfilter);
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);

									if ($lead->HasCustomerID())
										{
											$customer = new TCustomer($lead->CustomerID());
											if (is_object($customer) && $customer->Exists())
												{
													if ($customer->HasEmail())
														{
															$customer->Enable();
															if(!$contactList->HasContactID($customer->ID()))
																$contactList->SetContactID($customer->ID());
														}
												}
										}
								}

							return $contactList->ID();
						}

					function ImportLeadsWithAbilityToSendSMSToContacts($tagsfilter = [])
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Keywords().' - '.$this->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							if (is_array($tagsfilter) && count($tagsfilter) > 0)
								$leads->SetFilterCustomerIDs($tagsfilter);
							$leads->SetFilterSearch($this->ID());
							$leads->SetFilterValidForSMSPhones();
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);

									if ($lead->HasCustomerID())
										{
											$customer = new TCustomer($lead->CustomerID());
											if (is_object($customer) && $customer->Exists())
												{
													$customer->Enable();
													if(!$contactList->HasContactID($customer->ID()))
														$contactList->SetContactID($customer->ID());
												}
										}
								}

							return $contactList->ID();
						}

					function ImportLeadsWithValidPhonesToContacts($tagsfilter = [])
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Keywords().' - '.$this->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							if (is_array($tagsfilter) && count($tagsfilter) > 0)
								$leads->SetFilterCustomerIDs($tagsfilter);
							$leads->SetFilterSearch($this->ID());
							$leads->SetFilterValidPhones();
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);

									if ($lead->HasCustomerID())
										{
											$customer = new TCustomer($lead->CustomerID());
											if (is_object($customer) && $customer->Exists())
												{
													$customer->Enable();
													if(!$contactList->HasContactID($customer->ID()))
														$contactList->SetContactID($customer->ID());
												}
										}
								}

							return $contactList->ID();
						}

					function ImportLeadsWithAddressToContacts($tagsfilter = [])
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Keywords().' - '.$this->PlaceText());

							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							if (is_array($tagsfilter) && count($tagsfilter) > 0)
								$leads->SetFilterCustomerIDs($tagsfilter);
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);

									if ($lead->HasCustomerID())
										{
											$customer = new TCustomer($lead->CustomerID());
											if (is_object($customer) && $customer->Exists())
												{
													if ($customer->HasFullAddressAndName())
														{
															$customer->Enable();
															if(!$contactList->HasContactID($customer->ID()))
																$contactList->SetContactID($customer->ID());
														}
												}
										}
								}

							return $contactList->ID();
						}

					function Remove()
						{
							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);
									$lead->Remove();
								}

							TQuery::ForTable('google_searches')->AddWhere('id', intval($this->ID()))->Remove();
						}

					function Clear()
						{
							$leads = new TGoogleLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TGoogleLeads */
									$lead = $leads->Item($i);
									$lead->Remove();
								}
						}

				}
			define("TGoogleSearch_DEFINED", 1);
		}

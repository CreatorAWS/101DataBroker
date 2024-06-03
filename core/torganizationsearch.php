<?php

	if (!defined("TOrganizationSearch_DEFINED"))
		{
			Class TOrganizationSearch
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('organizations_searches')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TOrganizationSearch'][$id]))
								{
									$object=new TOrganizationSearch($id);
									if ($object->Exists())
										$sm['cache']['TOrganizationSearch'][$id]=$object->GetRawData();
								}
							else
								$object=new TOrganizationSearch($sm['cache']['TOrganizationSearch'][$id]);
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

					function isImported()
						{
							return $this->info['is_imported'] == 1;
						}

					function SetImported()
						{
							$this->UpdateValues(Array('is_imported'=>1));
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function LeadsCount($show_hidden = false)
						{
							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterSearch($this->ID());
							if (!$show_hidden)
								$leads->SetFilterVisible();

							return $leads->TotalCount();
						}

					function Tech()
						{
							return $this->info['tech'];
						}

					function SetTech($val)
						{
							$this->UpdateValues(Array('tech'=>$val));
						}

					function HasTech()
						{
							return !empty($this->info['tech']);
						}


					function Addedtime()
						{
							return intval($this->info['addedtime']);
						}

					function SetAddedtime($val)
						{
							$this->UpdateValues(Array('addedtime'=>intval($val)));
						}

					function HasAddedtime()
						{
							return !empty($this->info['addedtime']);
						}


					function NextOffset()
						{
							return $this->info['next_offset'];
						}

					function SetNextOffset($val)
						{
							$this->UpdateValues(Array('next_offset'=>$val));
						}

					function HasNextOffset()
						{
							return !empty($this->info['next_offset']);
						}


					function CampaignID()
						{
							return intval($this->info['id_campaign']);
						}

					function SetCampaignID($val)
						{
							$this->UpdateValues(Array('id_campaign'=>intval($val)));
						}

					function HasCampaignID()
						{
							return !empty($this->info['id_campaign']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('organizations_searches');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create()
						{
							$sql=new TQuery('organizations_searches');
							$sql->Add('addedtime', time());
							$object = new TOrganizationSearch($sql->Insert());
							return $object;
						}

					function ListID()
						{
							return $this->info['id_list'];
						}

					function SetListID($val)
						{
							$this->UpdateValues(Array('id_list'=>intval($val)));
						}

					function ImportLeadsWithEmailsToContacts()
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Tech());

							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
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

					function ImportLeadsWithAddressToContacts()
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Tech());

							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
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

					function ImportLeadsWithAbilityToSendSMSToContacts()
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Tech());

							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterValidForSMSPhones();
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
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

					function ImportLeadsWithValidPhonesToContacts()
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Tech());

							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterValidPhones();
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
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

					function ImportLeadsToContacts()
						{
							$contactList = TContactsList::Create(TCompany::CurrentCompany()->ID());
							$contactList->SetTitle($this->Tech());

							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterCompany($this->CompanyID());
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++)
								{
									/** @var  $lead TOrganizationsSearchLead */
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

					function Remove()
						{
							$leads = new TOrganizationsSearchLeadsList();
							$leads->SetFilterSearch($this->ID());
							$leads->Load();

							for ($i = 0; $i < $leads->Count(); $i++ )
								{
									/** @var TOrganizationsSearchLead $lead */
									$lead = $leads->Item($i);
									$lead->RemoveAdditionalEmails();
									$lead->RemoveAdditionalPhones();
									$lead->Remove();
								}

							TQuery::ForTable('organizations_searches')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TOrganizationSearch_DEFINED", 1);
		}

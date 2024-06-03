<?php

	use GS\Company\Countries;
	use GS\Organization\Decorator\OrganizationCellPhone;

	if (!defined("TOrganizationsSearchLead_DEFINED"))
		{
			Class TOrganizationsSearchLead
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('organizations_search_leads')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TOrganizationsSearchLead'][$id]))
								{
									$object=new TOrganizationsSearchLead($id);
									if ($object->Exists())
										$sm['cache']['TOrganizationsSearchLead'][$id]=$object->GetRawData();
								}
							else
								$object=new TOrganizationsSearchLead($sm['cache']['TOrganizationsSearchLead'][$id]);
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
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function HasCompanyID()
						{
							return !empty($this->info['id_company']);
						}

					function isHidden()
						{
							return !empty($this->info['hidden']);
						}

					function SetHidden()
						{
							$this->UpdateValues(Array('hidden' => 1));
						}

					function SetVisible()
						{
							$this->UpdateValues(Array('hidden' => 0));
						}

					function SearchID()
						{
							return intval($this->info['id_search']);
						}

					function SetSearchID($val)
						{
							$this->UpdateValues(Array('id_search'=>intval($val)));
						}

					function HasSearchID()
						{
							return !empty($this->info['id_search']);
						}


					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('title'=>$val));
						}

					function HasTitle()
						{
							return !empty($this->info['title']);
						}


					function Phone()
						{
							return $this->info['phone'];
						}

					function SetPhone($val)
						{
							$this->UpdateValues(Array('phone'=>$val));
						}

					function HasPhone()
						{
							return !empty($this->info['phone']);
						}


					function Website()
						{
							return $this->info['website'];
						}

					function WebsiteFullURL()
						{
							return 'https://'.$this->info['website'];
						}

					function SetWebsite($val)
						{
							$this->UpdateValues(Array('website'=>$val));
						}

					function HasWebsite()
						{
							return !empty($this->info['website']);
						}


					function Email()
						{
							return $this->info['email'];
						}

					function SetEmail($val)
						{
							$this->UpdateValues(Array('email'=>$val));
						}

					function HasEmail()
						{
							return !empty($this->info['email']);
						}

					function AddressFormatted()
						{
							$arr = [];

							if ($this->HasAddress())
								$arr[] = $this->Address();
							if ($this->HasCity())
								$arr[] = $this->City();
							if ($this->HasState())
								$arr[] = $this->State();
							if ($this->HasZip())
								$arr[] = $this->Zip();
							if ($this->HasCountry())
								$arr[] = $this->Country();

							return implode (", ", $arr);
						}

					function Address()
						{
							return $this->info['address'];
						}

					function SetAddress($val)
						{
							$this->UpdateValues(Array('address'=>$val));
						}

					function HasAddress()
						{
							return !empty($this->info['address']);
						}


					function City()
						{
							return $this->info['city'];
						}

					function SetCity($val)
						{
							$this->UpdateValues(Array('city'=>$val));
						}

					function HasCity()
						{
							return !empty($this->info['city']);
						}


					function State()
						{
							return $this->info['state'];
						}

					function SetState($val)
						{
							$this->UpdateValues(Array('state'=>$val));
						}

					function HasState()
						{
							return !empty($this->info['state']);
						}

					function Zip()
						{
							return $this->info['zip'];
						}

					function SetZip($val)
						{
							$this->UpdateValues(Array('zip'=>$val));
						}

					function HasZip()
						{
							return !empty($this->info['zip']);
						}

					function Country()
						{
							return $this->info['country'];
						}

					function SetCountry($val)
						{
							$this->UpdateValues(Array('country'=>$val));
						}

					function HasCountry()
						{
							return !empty($this->info['country']);
						}

					function Facebookurl()
						{
							return $this->info['facebookurl'];
						}

					function SetFacebookurl($val)
						{
							$this->UpdateValues(Array('facebookurl'=>$val));
						}

					function HasFacebookurl()
						{
							return !empty($this->info['facebookurl']);
						}


					function Twitterurl()
						{
							return $this->info['twitterurl'];
						}

					function SetTwitterurl($val)
						{
							$this->UpdateValues(Array('twitterurl'=>$val));
						}

					function HasTwitterurl()
						{
							return !empty($this->info['twitterurl']);
						}


					function Instagramurl()
						{
							return $this->info['instagramurl'];
						}

					function SetInstagramurl($val)
						{
							$this->UpdateValues(Array('instagramurl'=>$val));
						}

					function HasInstagramurl()
						{
							return !empty($this->info['instagramurl']);
						}


					function Linkedin()
						{
							return $this->info['linkedin'];
						}

					function SetLinkedin($val)
						{
							$this->UpdateValues(Array('linkedin'=>$val));
						}

					function HasLinkedin()
						{
							return !empty($this->info['linkedin']);
						}


					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer'=>intval($val)));
						}

					function HasCustomerID()
						{
							return !empty($this->info['id_customer']);
						}


					function PhoneNumberType()
						{
							return $this->info['phone_number_type'];
						}

					function SetPhoneNumberType($val)
						{
							$this->UpdateValues(Array('phone_number_type'=>$val));
						}

					function isPhoneTypeTagLandline()
						{
							return $this->PhoneNumberType()=='landline';
						}

					function isPhoneTypeTagCell()
						{
							return $this->PhoneNumberType()=='mobile';
						}

					function isPhoneTypeTagVOIP()
						{
							return $this->PhoneNumberType()=='voip';
						}

					function isPhoneTypeTagInvalid()
						{
							return $this->PhoneNumberType()=='invalid';
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('organizations_search_leads');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create()
						{
							$sql=new TQuery('organizations_search_leads');
							$object = new TOrganizationsSearchLead($sql->Insert());
							return $object;
						}

					function Remove()
						{
							if ($this->HasCustomerID())
								{
									$customer = new TCustomer($this->CustomerID());
									if ($customer->Exists() && !$customer->isEnabled())
										{
											$customer->UnsetBuiltWithSearchID($this->SearchID());
											$customer->Delete();
										}
								}

							TQuery::ForTable('organizations_search_leads')->AddWhere('id', intval($this->ID()))->Remove();
						}

					function SetAdditionalPhone($phone)
						{
							$this->SetMetaData('additional_phones', @serialize($phone));
						}

					function LoadAdditionalPhones()
						{
							return @unserialize($this->GetMetaData('additional_phones'));
						}

					function RemoveAdditionalPhones()
						{
							$this->UnsetMetaData('additional_phones');
						}

					function RemoveAdditionalEmails()
						{
							$this->UnsetMetaData('additional_emails');
						}

					function SetAdditionalEmail($email)
						{
							$this->SetMetaData('additional_emails', @serialize($email));
						}

					function LoadAdditionalEmails()
						{
							return @unserialize($this->GetMetaData('additional_emails'));
						}

					function HasPhones()
						{
							return !empty($this->GetPhonesArray());
						}

					function GetPhonesArray($formatted = false)
						{
							$phones = [];

							if ($this->HasPhone())
								{
									if ($formatted)
										$phones[] = Formatter::Phone($this->Phone());
									else
										$phones[] = Cleaner::Phone($this->Phone());
								}


							foreach ($this->LoadAdditionalPhones() as $phone)
								{
									if ($formatted)
										$phones[] = Formatter::Phone($phone);
									else
										$phones[] = Cleaner::Phone($phone);
								}

							return $phones;
						}

					function HasEmails()
						{
							return !empty($this->GetEmailsArray());
						}

					function GetEmailsArray()
						{
							$emails = [];

							if ($this->HasEmail())
								$emails[] = $this->Email();

							foreach ($this->LoadAdditionalEmails() as $email)
								{
									$emails[] = $email;
								}

							return $emails;
						}

					function GetTaxonomy($object_name, $object_id, $use_object_id_as_rel_id=false)
						{
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							if ($use_object_id_as_rel_id)
								{
									$q->Add('rel_id', dbescape($object_id));
									$q->SelectFields('object_id as taxonomyid');
								}
							else
								{
									$q->Add('object_id', dbescape($object_id));
									$q->SelectFields('rel_id as taxonomyid');
								}
							$q->Select();
							return $q->ColumnValues('taxonomyid');
						}

					function SetTaxonomy($object_name, $object_id, $rel_id)
						{
							if (is_array($rel_id))
								{
									for ($i = 0; $i<count($rel_id); $i++)
										{
											$this->GetTaxonomy($object_name, $object_id, $rel_id[$i]);
											return;
										}
								}
							if (in_array($rel_id, $this->GetTaxonomy($object_name, $object_id)))
								return;
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							$q->Add('object_id', intval($object_id));
							$q->Add('rel_id', intval($rel_id));
							$q->Insert();
						}

					function UnsetTaxonomy($object_name, $object_id, $rel_id)
						{
							if (is_array($rel_id))
								{
									for ($i = 0; $i<count($rel_id); $i++)
										{
											sm_unset_taxonomy($object_name, $object_id, $rel_id[$i]);
											return;
										}
								}
							$q=new TQuery('taxonomy');
							$q->Add('object_name', dbescape($object_name));
							$q->Add('object_id', intval($object_id));
							$q->Add('rel_id', intval($rel_id));
							$q->Remove();
						}

					function SetMetaData($key, $val)
						{
							$this->SetCustomMetadata('organization_lead', $this->ID(), $key, $val);
						}

					function UnsetMetaData($key): void
						{
							$this->SetMetaData($key, NULL);
						}

					function ImportCustomer($search)
						{
							/** @var  $search TGoogleSearch */

							if($this->HasTitle())
								$customer = TCustomer::initWithBusinessTitle($this->Title(), $this->CompanyID());

							if(!is_object($customer) || $this->HasPhones() )
								$customer = TCustomer::initWithRawPhonesArray($this->GetPhonesArray(), $this->CompanyID());

							if(!is_object($customer) && $this->HasEmails() )
								$customer = TCustomer::initWithEmailsArray($this->GetEmailsArray(), $this->CompanyID());

							if (!is_object($customer) || !$customer->Exists())
								{
									$customer = TCustomer::Create();
									$customer->SetCompanyID(TCompany::CurrentCompany()->ID());
									$customer->SetCompany($this->Title());
									if ($this->HasAddress())
										$customer->SetAddress($this->Address());
									if ($this->HasCity())
										$customer->SetCity($this->City());
									if ($this->HasState())
										$customer->SetState($this->State());
									if ($this->HasZip())
										$customer->SetZip($this->Zip());
									if ($this->HasFacebookURL())
										$customer->SetFacebookUrl($this->FacebookURL());
									if ($this->HasTwitterURL())
										$customer->SetTwitterUrl($this->TwitterURL());
									if ($this->HasInstagramURL())
										$customer->SetInstagramUrl($this->InstagramURL());
									if ($this->HasLinkedin())
										$customer->SetLinkedin($this->LinkedIn());
									if ($this->HasWebsite())
										$customer->SetWebsite(url_cleaner($this->Website()));
									$customer->SetSMSAcceptedTimestamp();
									$customer->SetSMSAcceptedTag('yes');
									$customer->SetLastUpdateTime();
									$customer->SetTypeCompany();
									$customer->Disable();
									if ( $this->HasEmail() )
										{
											$main_email_exist = false;
											for ( $i = 0; $i < count($this->GetEmailsArray()); $i++)
												{
													if (TEmails::emailExists($this->GetEmailsArray()[$i], $this->CompanyID()))
														continue;

													if (!empty($this->GetEmailsArray()[$i]) && !$main_email_exist)
														{
															$customer->SetEmail($this->GetEmailsArray()[$i]);
															$main_email_exist = true;
															continue;
														}

													if (!empty($this->GetEmailsArray()[$i]) && $main_email_exist)
														TEmails::Create($this->GetEmailsArray()[$i], $this->CompanyID(), $customer->ID());
												}
										}

									if ($this->HasPhones())
										{
											$main_phone_exist = false;
											for ( $i = 0; $i < count($this->GetPhonesArray()); $i++)
												{
													if (Validator::isPhone($this->GetPhonesArray()[$i]))
														{
															if (TPhone::phoneFullExists($this->GetPhonesArray()[$i], $this->CompanyID()))
																continue;

															if (!empty($this->GetPhonesArray()[$i]) && !$main_phone_exist)
																{
																	$customer->CreateMainCellPhone($this->GetPhonesArray()[$i]);
																	$main_phone_exist = true;
																	continue;
																}

															if (!empty($this->GetPhonesArray()[$i]) && $main_phone_exist)
																TPhone::Create($this->GetPhonesArray()[$i], $customer->ID(), $this->CompanyID(), TPhone::OTHER_PHONE);
														}
												}
										}
								}
							if (is_object($customer) && $customer->Exists())
								{
									$this->SetCustomerID($customer->ID());
									if (is_object($search) && $search->Exists())
										$customer->SetBuiltWithSearchID($search->ID());
								}
						}

					function SetCustomMetadata($object_name, $object_id, $key_name, $val)
						{
							global $sm;
							$q=new TQuery('sm_metadata');
							$q->Add('object_name', dbescape($object_name));
							$q->Add('object_id', dbescape($object_id));
							$q->Add('key_name', dbescape($key_name));
							$info=$q->Get();
							if ($val===NULL)
								{
									$q->Remove();
									unset($sm['cache']['metadata'][$object_name][$object_id][$key_name]);
								}
							else
								{
									$q->Add('val', dbescape($val));
									if (empty($info['id']))
										{
											$q->Insert();
										}
									else
										{
											$q->Update('id', intval($info['id']));
										}
									$sm['cache']['metadata'][$object_name][$object_id][$key_name]=$val;
								}
						}

					function GetMetaData($key)
						{
							return sm_metadata('organization_lead', $this->ID(), $key);
						}

				}
			define("TOrganizationsSearchLead_DEFINED", 1);
		}

<?php

	if (!defined("TGoogleLeads_DEFINED"))
		{
			Class TGoogleLeads
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('google_leads')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TGoogleLeads'][$id]))
								{
									$object=new TGoogleLeads($id);
									if ($object->Exists())
										$sm['cache']['TGoogleLeads'][$id]=$object->GetRawData();
								}
							else
								$object=new TGoogleLeads($sm['cache']['TGoogleLeads'][$id]);
							return $object;
						}

					public static function initWithPlaceID($place_id, $company_id)
						{
							use_api('cleaner');
							$info = TQuery::ForTable('google_leads')->Add('place_id', dbescape($place_id))->Add('id_company', intval($company_id))->OrderBy('id')->Get();
							$lead = new TGoogleLeads($info);
							return $lead;
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

					function EmailCheckedVal()
						{
							return intval($this->info['email_checked']);
						}

					function SetEmailChecked()
						{
							$this->UpdateValues(Array('email_checked'=>1));
						}

					function SearchID()
						{
							return intval($this->info['id_search']);
						}

					function SetSearchID($val)
						{
							$this->UpdateValues(Array('id_search'=>Cleaner::IntObjectID($val)));
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

					function PlaceID()
						{
							return $this->info['place_id'];
						}

					function SetPlaceID($val)
						{
							$this->UpdateValues(Array('place_id'=>$val));
						}

					function HasPlaceID()
						{
							return !empty($this->info['place_id']);
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

					function SetWebsite($val)
						{
							$this->UpdateValues(Array('website'=>$val));
						}

					function HasWebsite()
						{
							return !empty($this->info['website']);
						}


					function GoogleUrl()
						{
							return $this->info['google_url'];
						}

					function SetGoogleUrl($val)
						{
							$this->UpdateValues(Array('google_url'=>$val));
						}

					function HasGoogleUrl()
						{
							return !empty($this->info['google_url']);
						}


					function Reviews()
						{
							return intval($this->info['reviews']);
						}

					function SetReviews($val)
						{
							$this->UpdateValues(Array('reviews'=>intval($val)));
						}

					function HasReviews()
						{
							return !empty($this->info['reviews']);
						}


					function Rating()
						{
							return $this->info['rating'];
						}

					function SetRating($val)
						{
							$this->UpdateValues(Array('rating' => floatval($val)));
						}

					function HasRating()
						{
							return !empty($this->info['rating']);
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
							return !empty($this->info['email']) && $this->info['email'] != 'No Email';
						}

					function HasAddress1()
						{
							return !empty($this->info['address1']);
						}

					function Address1()
						{
							return $this->info['address1'];
						}

					function SetAddress1($val)
						{
							$this->UpdateValues(Array('address1' => $val));
						}

					function HasCity()
						{
							return !empty($this->info['city']);
						}

					function City()
						{
							return $this->info['city'];
						}

					function SetCity($val)
						{
							$this->UpdateValues(Array('city' => $val));
						}

					function HasState()
						{
							return !empty($this->info['state']);
						}

					function State()
						{
							return $this->info['state'];
						}

					function SetState($val)
						{
							$this->UpdateValues(Array('state' => $val));
						}

					function HasZip()
						{
							return !empty($this->info['zip']);
						}

					function Zip()
						{
							return $this->info['zip'];
						}

					function SetZip($val)
						{
							$this->UpdateValues(Array('zip' => $val));
						}

					function PhoneTypeTag()
						{
							return $this->info['phone_number_type'];
						}

					function SetPhoneTypeTag($val)
						{
							$this->UpdateValues(Array('phone_number_type' => $val));
						}

					function isPhoneTypeTagLandline()
						{
							return $this->PhoneTypeTag()=='landline';
						}

					function isPhoneTypeTagCell()
						{
							return $this->PhoneTypeTag()=='mobile';
						}

					function isPhoneTypeTagVOIP()
						{
							return $this->PhoneTypeTag()=='voip';
						}

					function isPhoneTypeTagInvalid()
						{
							return $this->PhoneTypeTag()=='invalid';
						}

					function HasCustomerID()
						{
							return !empty($this->info['id_customer']);
						}

					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer' => intval($val)));
						}

					function HasSocialURLS()
						{
							return !empty($this->info['facebookurl']) || !empty($this->info['twitterurl']) || !empty($this->info['instagramurl']) || !empty($this->info['linkedin']);
						}

					function FacebookURL()
						{
							return $this->info['facebookurl'];
						}

					function HasFacebookURL()
						{
							return !empty($this->info['facebookurl']);
						}

					function TwitterURL()
						{
							return $this->info['twitterurl'];
						}

					function HasTwitterURL()
						{
							return !empty($this->info['twitterurl']);
						}

					function InstagramURL()
						{
							return $this->info['instagramurl'];
						}

					function HasInstagramURL()
						{
							return !empty($this->info['instagramurl']);
						}

					function HasLinkedInURL()
						{
							return !empty($this->info['linkedin']);
						}

					function LinkedInURL()
						{
							return $this->info['linkedin'];
						}

					function SetFacebookUrl($val)
						{
							$upd['facebookurl'] = $val;
							$this->UpdateValues($upd);
						}

					function SetTwitterUrl($val)
						{
							$upd['twitterurl'] = $val;
							$this->UpdateValues($upd);
						}

					function SetInstagramUrl($val)
						{
							$upd['instagramurl'] = $val;
							$this->UpdateValues($upd);
						}

					function SetLinkedin($val)
						{
							$upd['linkedin'] = $val;
							$this->UpdateValues($upd);
						}

					function LoadLeadDetailsFormatted()
						{
							$data['name'] = $this->Title();
							$data['email'] = $this->Email();
							$data['address'] = $this->Address();
							$data['phone'] = $this->Phone();
							$data['website'] = $this->Website();
							$data['rating'] = $this->Rating();
							$data['google_url'] = $this->GoogleUrl();
							$data['user_ratings_total'] = $this->Reviews();

							return $data;
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('google_leads');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create()
						{
							$sql=new TQuery('google_leads');
							$object = new TGoogleLeads($sql->Insert());
							return $object;
						}

					function ImportCustomer($search)
						{
							/** @var  $search TGoogleSearch */

							$customer = TCustomer::initWithCustomerPhoneNotDeleted($this->Phone(), $this->CompanyID());
							if (!is_object($customer) || !$customer->Exists())
								{
									$customer = TCustomer::Create();
									$customer->SetCompanyID(TCompany::CurrentCompany()->ID());
									$customer->SetCompany($this->Title());
									$customer->SetCellPhone($this->Phone());
									if ($this->HasAddress1())
										$customer->SetAddress($this->Address1());
									if ($this->HasCity())
										$customer->SetCity($this->City());
									if ($this->HasState())
										$customer->SetState($this->State());
									if ($this->HasZip())
										$customer->SetZip($this->Zip());
									if ($this->HasAddress())
										{
											$longAddress = explode(',', $this->Address());
											$customer->SetCountry(trim($longAddress[3]));
										}
									if ($this->HasFacebookURL())
										$customer->SetFacebookUrl($this->FacebookURL());
									if ($this->HasTwitterURL())
										$customer->SetTwitterUrl($this->TwitterURL());
									if ($this->HasInstagramURL())
										$customer->SetInstagramUrl($this->InstagramURL());
									if ($this->HasLinkedInURL())
										$customer->SetLinkedin($this->LinkedInURL());
									if ($this->HasWebsite())
										$customer->SetWebsite(url_cleaner($this->Website()));
									$customer->SetSMSAcceptedTimestamp();
									$customer->SetSMSAcceptedTag('yes');
									$customer->SetLastUpdateTime();
									$customer->SetTypeCompany();
									$customer->Disable();
									if ( $this->HasEmail() )
										$customer->SetEmail($this->Email());
								}
							if (is_object($customer) && $customer->Exists())
								{
									$this->SetCustomerID($customer->ID());
									if (is_object($search) && $search->Exists())
										$customer->SetSearchID($search->ID());
								}
						}

					function Remove()
						{
							$reviews = new TGoogleLeadReviewsList();
							$reviews->SetFilterLead($this->ID());
							$reviews->Load();

							for ($i = 0; $i < $reviews->Count(); $i++)
								{
									/** @var  $review TGoogleLeadReviews */
									$review = $reviews->Item($i);
									$review->Remove();
								}

							if ($this->HasCustomerID())
								{
									$customer = new TCustomer($this->CustomerID());
									if ($customer->Exists() && !$customer->isEnabled())
										{
											if ($customer->SearchCount() == 1)
												{
													$customer->UnsetSearchID($this->SearchID());
													$customer->Delete();
												}
										}
								}

							TQuery::ForTable('google_leads')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TGoogleLeads_DEFINED", 1);
		}

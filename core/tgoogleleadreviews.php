<?php

	if (!defined("TGoogleLeadReviews_DEFINED"))
		{
			Class TGoogleLeadReviews
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('google_reviews')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TGoogleLeadReviews'][$id]))
								{
									$object=new TGoogleLeadReviews($id);
									if ($object->Exists())
										$sm['cache']['TGoogleLeadReviews'][$id]=$object->GetRawData();
								}
							else
								$object=new TGoogleLeadReviews($sm['cache']['TGoogleLeadReviews'][$id]);
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

					function LeadID()
						{
							return intval($this->info['id_lead']);
						}

					function SetLeadID($val)
						{
							$this->UpdateValues(Array('id_lead'=>intval($val)));
						}

					function HasLeadID()
						{
							return !empty($this->info['id_lead']);
						}

					function AuthorName()
						{
							return $this->info['author_name'];
						}

					function SetAuthorName($val)
						{
							$this->UpdateValues(Array('author_name'=>$val));
						}

					function HasAuthorName()
						{
							return !empty($this->info['author_name']);
						}

					function AuthorUrl()
						{
							return $this->info['author_url'];
						}

					function SetAuthorUrl($val)
						{
							$this->UpdateValues(Array('author_url'=>$val));
						}

					function HasAuthorUrl()
						{
							return !empty($this->info['author_url']);
						}

					function ProfilePhotoUrl()
						{
							return $this->info['profile_photo_url'];
						}

					function SetProfilePhotoUrl($val)
						{
							$this->UpdateValues(Array('profile_photo_url'=>$val));
						}

					function HasProfilePhotoUrl()
						{
							return !empty($this->info['profile_photo_url']);
						}

					function Rating()
						{
							return floatval($this->info['rating']);
						}

					function SetRating($val)
						{
							$this->UpdateValues(Array('rating'=>floatval($val)));
						}

					function HasRating()
						{
							return !empty($this->info['rating']);
						}

					function RelativeTimeDescription()
						{
							return $this->info['relative_time_description'];
						}

					function SetRelativeTimeDescription($val)
						{
							$this->UpdateValues(Array('relative_time_description'=>$val));
						}

					function HasRelativeTimeDescription()
						{
							return !empty($this->info['relative_time_description']);
						}

					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text'=>$val));
						}

					function HasText()
						{
							return !empty($this->info['text']);
						}

					function Time()
						{
							return intval($this->info['time']);
						}

					function SetTime($val)
						{
							$this->UpdateValues(Array('time'=>intval($val)));
						}

					function HasTime()
						{
							return !empty($this->info['time']);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('google_reviews');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create()
						{
							$sql=new TQuery('google_reviews');
							$object = new TGoogleLeadReviews($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('google_reviews')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TGoogleLeadReviews_DEFINED", 1);
		}

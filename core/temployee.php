<?php

	class TEmployee
		{
			var $info;
			var $tagids = NULL;

			function TEmployee($id_or_cahcedinfo)
				{
					global $sm;
					if (is_array($id_or_cahcedinfo))
						{
							$this->info=$id_or_cahcedinfo;
						}
					else
						{
							$this->info=TQuery::ForTable('sm_users')->Add('id_user', intval($id_or_cahcedinfo))->Get();
						}
				}

			public static function checkIfExistWithTwilioPhone($twiliophone)
				{
					use_api('cleaner');
					/** @var $company TCompany */
					$info = TQuery::ForTable('sm_users')->Add('twilio_number', dbescape(Cleaner::USPhone($twiliophone)))->Get();
					if (!empty($info))
						{
							$employee = new TEmployee($info);
							return $employee;
						}
					else
						return false;
				}

			function GetRawData()
				{
					return $this->info;
				}

			function SetOnlineTime()
				{
					$params['online_time']=time();
					$this->UpdateValues($params);
				}

			public static function UsingCache($id)
				{
					global $sm;
					if (!is_array($sm['cache']['TEmployee'][$id]))
						{
							$object=new TEmployee($id);
							if ($object->Exists())
								$sm['cache']['TEmployee'][$id]=$object->GetRawData();
						}
					else
						$object=new TEmployee($sm['cache']['TEmployee'][$id]);
					return $object;
				}

            public static function LoginExists($login)
                {
                    $q=new TQuery('sm_users');
                    $q->Add('login', $login);
                    $userexist=$q->Get();
                    if ($userexist)
                        return true;
                }

			public static function withID($id)
				{
					$employee=new TEmployee(intval($id));
					return $employee;
				}

			function Exists()
				{
					return !empty($this->info['id_user']);
				}

			function Cellphone()
				{
					return $this->info['cellphone'];
				}

			function Name()
				{
					return $this->FirstName().' '.$this->LastName();
				}

			function FirstName()
				{
					return $this->info['first_name'];
				}

			function LastName()
				{
					return $this->info['last_name'];
				}

            function Email()
                {
                    return $this->info['email'];
                }

            function HasEmail()
                {
                    return !empty($this->info['email']);
                }

            function HasCellphone()
                {
                    return !empty($this->info['cellphone']);
                }

            function Login()
                {
                    return $this->info['login'];
                }
			function NotificationAboutMessageFromMemberTag()
				{
					return $this->info['new_msg_from_member_notif'];
				}
			function ID()
				{
					return intval($this->info['id_user']);
				}
			function CompanyID()
				{
					return intval($this->info['id_company']);
				}
			function isUser()
				{
					return intval($this->info['user_status'])>0;
				}
			function UpdateValues($params)
				{
					global $sm;
					if (empty($params) || !is_array($params))
						return;
					$q=new TQuery('sm_users');
					foreach ($params as $key=>$val)
						{
							$this->info[$key]=$val;
							$q->Add($key, dbescape($this->info[$key]));
						}
					$q->Update('id_user', $this->ID());
				}
			function SetPrimaryRoleSalesPerson()
				{
					$this->SetPrimaryRoleTag('salesperson');
				}
			function SetPrimaryRoleSalesManager()
				{
					$this->SetPrimaryRoleTag('salesmanager');
				}
			function SetPrimaryRoleTag($role)
				{
					$params['primary_role']=$role;
					$this->UpdateValues($params);
				}
			function PrimaryRoleTag()
				{
					return $this->info['primary_role'];
				}
			function PrimaryRoleTitle()
				{
					if ($this->PrimaryRoleTag()=='salesmanager')
						return 'Sales Manager';
					if ($this->PrimaryRoleTag()=='salesperson')
						return 'Sales Person';
				}
			function isAllowedToDownloadAssets()
				{
					return true;
				}
			function isAllowedToManageAssets()
				{
					return true;
				}
            function isSuperAdmin()
				{
					return $this->info['user_status']==3;
				}
            function SetLastName($val)
                {
                    $upd['last_name']=$val;
                    $this->UpdateValues($upd);
                }
            function SetFirstName($val)
                {
                    $upd['first_name']=$val;
                    $this->UpdateValues($upd);
                }
            function SetEmail($val)
                {
                    $upd['email']=$val;
                    $this->UpdateValues($upd);
                }
			function SetPassword($val)
				{
					$upd['password']=md5($val);
					$this->UpdateValues($upd);
				}
            function SetCellphone($val)
                {
                    $upd['cellphone']=$val;
                    $this->UpdateValues($upd);
                }
            function SetNotifications($message_from_customer)
                {
					if (!in_array($message_from_customer, Array('no', 'email', 'cellphone')))
						$message_from_customer='no';
					$upd['new_msg_from_member_notif']=$message_from_customer;
					$this->UpdateValues($upd);
                }
            function SetLogin($val)
                {
                    $upd['login']=$val;
                    $this->UpdateValues($upd);
                }
            function SetCompanyID($val)
                {
                    $upd['id_company']=$val;
                    $this->UpdateValues($upd);
                }

            function Remove()
                {
                    $params['deleted']=time();
                    $params['user_status']=0;
                    $this->UpdateValues($params);
                }

			function SendEmail($subject, $message)
				{
					$company = new TCompany($this->CompanyID());
					if(!empty($company->EmailFrom()))
						$from = $company->EmailFrom();
					else
						$from = 'noreply@'.mail_domain();

					if ($this->HasEmail())
						{
							MailJetSend($from, $this->Email(), $subject, $message);
						}
				}

			function SendSystemEmail($subject, $message)
				{
					$company = new TCompany(1);
					if(!empty($company->EmailFrom()))
						$from = $company->EmailFrom();
					else
						$from = 'noreply@'.mail_domain();

					if ($this->HasEmail())
						{
							MailJetSend($from, $this->Email(), $subject, $message);
						}
				}

			function SendSMS($text, $from='')
				{
					use_api('sms');
					if ($this->HasCellphone())
						queue_sms($this->Cellphone(), $text, $from);
				}
			public static function Current()
				{
					global $myaccount;
					return $myaccount;
				}

			public static function InitWithTwilioClientID($id)
				{
					$id=str_replace('client:', '', $id);
					$id=explode('XYZ', $id);
					$object = new TEmployee($id[1]);
					return $object;
				}

			function TwilioClientID()
				{
					return 'pbcrmstaffmemberXYZ'.$this->ID();
				}

			function TwilioPhone()
				{
					return $this->info['twilio_number'];
				}

			function HasTwilioPhone()
				{
					return !empty($this->info['twilio_number']);
				}

			function SetTwilioPhone($twilio_phone)
				{
					$this->UpdateValues(Array( 'twilio_number' => $twilio_phone ));
				}

			function TwilioSid()
				{
					return $this->info['twiliophone_sid'];
				}

			function HasTwilioSid()
				{
					return !empty($this->TwilioSid());
				}

			function SetTwilioSid( $val )
				{
					$this->UpdateValues(Array('twiliophone_sid'=>$val));
				}

			function LoadTags($rewritecache = false)
				{
					if ($rewritecache || $this->tagids === NULL)
						{
							$this->tagids = $this->GetTaxonomy('employeetotags', $this->ID());
						}
				}

			function GetTagIDsArray()
				{
					$this->LoadTags();
					return $this->tagids;
				}

			function SetTagID($tag_id)
				{
					sm_extcore();
					$this->SetTaxonomy('employeetotags', $this->ID(), intval($tag_id));
					$this->LoadTags(true);
				}

			function UnsetTagID($tag_id)
				{
					sm_extcore();
					$this->UnsetTaxonomy('employeetotags', $this->ID(), intval($tag_id));
					$this->LoadTags(true);
				}

			function UnsetAllUserTags()
				{
					$tags_arr = $this->GetTagIDsArray();
					for ( $i = 0; $i < count($tags_arr); $i++)
						{
							$tag = new TTag($tags_arr[$i]);
							if ($tag->Exists())
								{
									$this->UnsetTagID($tag->ID());
								}

						}
				}

			function HasTagID($tag_id)
				{
					$this->LoadTags();
					return in_array($tag_id, $this->tagids);
				}

			function Status()
				{
					return ($this->info['online_time'] + 40) > time()?'<span class="user_status online">Online</span>':'<span class="user_status">Offline</span>';
				}

			function TwilioAccessToken()
				{
					return $this->info['twilio_access_tocken'];
				}

			function HasTwilioAccessToken()
				{
					return !empty($this->info['twilio_access_tocken']);
				}

			function SetTwilioAccessToken($tocken)
				{
					$this->UpdateValues(Array( 'twilio_access_tocken' => $tocken ));
					$this->SetTwilioAccessTokenExpiration();
				}

			function TwilioAccessTokenExpiration()
				{
					return $this->info['twilio_tocken_expiration_time'];
				}

			function HasTwilioAccessTokenExpiration()
				{
					return !empty($this->info['twilio_tocken_expiration_time']);
				}

			function SetTwilioAccessTokenExpiration()
				{
					$tocken_exp = time() + 3600;
					$this->UpdateValues(Array( 'twilio_tocken_expiration_time' => $tocken_exp ));
				}

			function isRefreshTwilioAccessTokenNeeded()
				{
					return $this->TwilioAccessTokenExpiration() < time();
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
    }
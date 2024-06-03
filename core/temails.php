<?php

	if (!defined("TEmails_DEFINED"))
		{
			Class TEmails
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('emails')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function emailExists($email, $id_company): bool
						{
							$info = TQuery::ForTable('emails')->Add('company_id', Cleaner::IntObjectID($id_company))->Add('email', dbescape($email))->OrderBy('id')->Get();
							$phone = new TEmails($info);
							if ($phone->Exists())
								return true;
							else
								return false;
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TEmails'][$id]))
								{
									$object=new TEmails($id);
									if ($object->Exists())
										$sm['cache']['TEmails'][$id]=$object->GetRawData();
								}
							else
								$object=new TEmails($sm['cache']['TEmails'][$id]);
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


					function CustomerID()
						{
							return intval($this->info['customer_id']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('customer_id'=>intval($val)));
						}

					function HasCustomerID()
						{
							return !empty($this->info['customer_id']);
						}


					function CompanyID()
						{
							return intval($this->info['company_id']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('company_id'=>intval($val)));
						}

					function HasCompanyID()
						{
							return !empty($this->info['company_id']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('emails');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($email, $company_id = 0, $customer_id = 0)
						{
							$sql=new TQuery('emails');
							$sql->Add('email', dbescape($email));
							$sql->Add('customer_id', intval($customer_id));
							$sql->Add('company_id', intval($company_id));
							$object = new TEmails($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('emails')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TEmails_DEFINED", 1);
		}

<?php

	if (!defined("TEmailTemplate_DEFINED"))
		{
			Class TEmailTemplate
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('email_templates')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TEmailTemplate'][$id]))
								{
									$object = new TEmailTemplate($id);
									if ($object->Exists())
										$sm['cache']['TEmailTemplate'][$id] = $object->GetRawData();
								}
							else
								$object = new TEmailTemplate($sm['cache']['TEmailTemplate'][$id]);
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

					function SetCategoryID($val)
						{
							$this->UpdateValues(Array('id_ctg' => $val));
						}
					function CategoryID()
						{
							return intval($this->info['id_ctg']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company' => intval($val)));
						}

					function Title()
						{
							return $this->info['name'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('name' => $val));
						}

					function HasTitle()
						{
							return !empty($this->info['name']);
						}


					function Subject()
						{
							return $this->info['subject'];
						}

					function SetSubject($val)
						{
							$this->UpdateValues(Array('subject' => $val));
						}

					function Message()
						{
							return $this->info['message'];
						}

					function SetMessage($val)
						{
							$this->UpdateValues(Array('message' => $val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('email_templates');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							TQuery::ForTable('email_templates')->AddWhere('id', intval($this->ID()))->Remove();
						}

					public static function Create($company=NULL)
						{
							if ($company==NULL)
								$company=TCompany::CurrentCompany();
							$q = new TQuery('email_templates');
							$q->Add('id_company', intval($company->ID()));
							$object=new TEmailTemplate($q->Insert());
							return $object;
						}

					function ReplaceKeyVal($key, $val)
						{
							$subject=$this->Subject();
							$subject=str_replace('{'.strtoupper($key).'}', $val, $subject);
							$subject=str_replace('{'.strtolower($key).'}', $val, $subject);
							$this->info['subject']=$subject;
							$message=$this->Message();
							$message=str_replace('{'.strtoupper($key).'}', $val, $message);
							$message=str_replace('{'.strtolower($key).'}', $val, $message);
							$this->info['message']=$message;
						}

					function ReplaceUninitializedKeys()
						{
							$this->ReplaceKeyVal('URL', '');
							$this->ReplaceKeyVal('FIRST_NAME', '');
							$this->ReplaceKeyVal('LAST_NAME', '');
							$this->ReplaceKeyVal('CONTACT_NAME', '');
							$this->ReplaceKeyVal('CONTACT_BUSINESS_NAME', '');
							$this->ReplaceKeyVal('OWNER', '');
							$this->ReplaceKeyVal('BUSINESS', '');
							$this->ReplaceKeyVal('BUSINESS_CELLPHONE', '');
							$this->ReplaceKeyVal('EMAIL', '');
							$this->ReplaceKeyVal('CELLPHONE', '');
						}

				}

			define("TEmailTemplate_DEFINED", 1);
		}

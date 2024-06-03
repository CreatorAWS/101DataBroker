<?php

	if (!defined("TGenericEmail_DEFINED"))
		{
			Class TGenericEmail
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable(static::Table())->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function initWithEmailID($id_email)
						{
							use_api('cleaner');
							$info = TQuery::ForTable(static::Table())->Add('id_email', dbescape($id_email))->OrderBy('id')->Get();
							$email = new TEmail($info);
							return $email;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function EmailID()
						{
							return $this->info['id_email'];
						}

					function HasEmailID()
						{
							return !empty($this->info['id_email']);
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
							$this->UpdateValues(Array('id_company' => intval($val)));
						}


					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function CampaignID()
						{
							return intval($this->info['id_campaign']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer' => intval($val)));
						}

					function HasCellphone()
						{
							return !empty($this->info['cellphone']);
						}


					function EmployeeID()
						{
							return intval($this->info['id_employee']);
						}

					function SetEmployeeID($val)
						{
							$this->UpdateValues(Array('id_employee' => intval($val)));
						}

					function SendAfterTimestamp()
						{
							return intval($this->info['sendafter']);
						}

					function SetSendAfterTimestamp($val)
						{
							$this->UpdateValues(Array('sendafter' => intval($val)));
						}


					function HasSendAfterTimestamp()
						{
							return !empty($this->info['sendafter']);
						}


					function Message()
						{
							return $this->info['message'];
						}

					function SetMessage($val)
						{
							$this->UpdateValues(Array('message' => $val));
						}

					function Subject()
						{
							return $this->info['subject'];
						}

					function SetSubject($val)
						{
							$this->UpdateValues(Array('subject' => $val));
						}

					function HasAttachments()
						{
							return !empty($this->info['file']);
						}

					function Attachments()
						{
							return @unserialize($this->info['file']);
						}

					function HasAttachment($id, $ext)
						{
							return file_exists('files/img/'.$id.'.'.$ext);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery(static::Table());
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							$q = new TQuery(static::Table());
							$q->AddWhere('id', intval($this->ID()));
							$q->Remove();
						}

					public static function CreateOutgoing($subject, $message, $company, $customer, $employee=0, $scheduletime=0, $campaign=0, $id_campaign_schedule = 0, $id_email = '')
						{
							$q = new TQuery(static::Table());
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_customer', intval(Cleaner::IntObjectID($customer)));
							$q->Add('id_employee', intval(Cleaner::IntObjectID($employee)));
							$q->Add('id_campaign', intval(Cleaner::IntObjectID($campaign)));
							if (intval($scheduletime) == 0)
								{
									$q->Add('sendafter', time());
								}
							else
								{
									$q->Add('sendafter', intval($scheduletime));
								}
							$q->Add('subject', dbescape($subject));
							$q->Add('message', dbescape($message));
							$q->Add('id_campaign_schedule', intval($id_campaign_schedule));
							$q->Add('id_email', dbescape($id_email));
							$id = $q->Insert();
							return $id;
						}

					public static function CreateIncoming($subject, $message, $company, $customer, $employee=0)
						{
							$q = new TQuery(static::Table());
							$q->Add('id_company', intval(Cleaner::IntObjectID($company)));
							$q->Add('id_customer', intval(Cleaner::IntObjectID($customer)));
							$q->Add('id_employee', intval(Cleaner::IntObjectID($employee)));
							$q->Add('subject', dbescape($subject));
							$q->Add('message', dbescape($message));
							$q->Add('incoming', 1);

							$id = $q->Insert();
							return $id;
						}

				}

			define("TGenericEmail_DEFINED", 1);
		}

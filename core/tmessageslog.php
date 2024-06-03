<?php

	if (!defined("TMessagesLog_DEFINED"))
		{
			Class TMessagesLog
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('messagelog')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function initWithMessageIDAndType($id_message, $type)
						{
							use_api('cleaner');
							$info = TQuery::ForTable('messagelog')->Add('id_message', intval($id_message))->Add('type', dbescape($type))->Get();
							$message = new TMessagesLog($info);
							return $message;
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TMessagesLog'][$id]))
								{
									$object=new TMessagesLog($id);
									if ($object->Exists())
										$sm['cache']['TMessagesLog'][$id]=$object->GetRawData();
								}
							else
								$object=new TMessagesLog($sm['cache']['TMessagesLog'][$id]);
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

					function EmployeeID()
						{
							return intval($this->info['id_employee']);
						}

					function isIncoming()
						{
							return !empty(intval($this->info['incoming']));
						}

					function Type()
						{
							return $this->info['type'];
						}

					function SetType($val)
						{
							$this->UpdateValues(Array('type'=>$val));
						}

					function MessageID()
						{
							return intval($this->info['id_message']);
						}

					function SetMessageID($val)
						{
							$this->UpdateValues(Array('id_message'=>intval($val)));
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function TimeSent()
						{
							return intval($this->info['timesent']);
						}

					function TimeRead()
						{
							return intval($this->info['timeread']);
						}

					function SetTimeRead($time)
						{
							$this->UpdateValues(Array('timeread'=>intval($time)));
						}

					function isUnread()
						{
							return intval($this->info['timeread'])==0;
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer'=>intval($val)));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('messagelog');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($type, $id_message, $company, $customer, $time = 0, $incoming='', $id_employee=0, $id_reply = 0)
						{
							$sql=new TQuery('messagelog');
							$sql->Add('type', dbescape($type));
							$sql->Add('id_message', intval(Cleaner::IntObjectID($id_message)));
							$sql->Add('id_customer', intval(Cleaner::IntObjectID($customer)));
							$sql->Add('id_company', intval(Cleaner::IntObjectID($company)));
							if (!empty($incoming))
								$sql->Add('incoming', intval($incoming));
							if (!empty($id_employee))
								$sql->Add('id_employee', intval(Cleaner::IntObjectID($id_employee)));
							if ($time == 0 )
								$sql->Add('timesent', time());
							else
								$sql->Add('timesent', intval($time));
							$sql->Add('id_reply', intval($id_reply));
							$object = new TMessagesLog($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('messagelog')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TMessagesLog_DEFINED", 1);
		}

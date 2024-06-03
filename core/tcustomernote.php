<?php

	if (!defined("TCustomerNote_DEFINED"))
		{
			Class TCustomerNote
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('customer_notes')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCustomerNote'][$id]))
								{
									$object=new TCustomerNote($id);
									if ($object->Exists())
										$sm['cache']['TCustomerNote'][$id]=$object->GetRawData();
								}
							else
								$object=new TCustomerNote($sm['cache']['TCustomerNote'][$id]);
							return $object;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function EmployeeID()
						{
							return intval($this->info['id_employee']);
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

					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer'=>intval($val)));
						}

					function Timeadded()
						{
							return intval($this->info['timeadded']);
						}

					function SetTimeadded($val)
						{
							$this->UpdateValues(Array('timeadded'=>intval($val)));
						}

					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text'=>$val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('customer_notes');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_company, $id_customer, $id_employee, $timeadded, $text)
						{
							$sql=new TQuery('customer_notes');
							$sql->Add('id_company', intval($id_company));
							$sql->Add('id_customer', intval($id_customer));
							$sql->Add('id_employee', intval($id_employee));
							$sql->Add('timeadded', intval($timeadded));
							$sql->Add('text', dbescape($text));
							$object = new TCustomerNote($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('customer_notes')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TCustomerNote_DEFINED", 1);
		}

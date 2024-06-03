<?php

	if (!defined("TImportCustomers_DEFINED"))
		{
			Class TImportCustomers
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('import_customers')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TImportCustomers'][$id]))
								{
									$object=new TImportCustomers($id);
									if ($object->Exists())
										$sm['cache']['TImportCustomers'][$id]=$object->GetRawData();
								}
							else
								$object=new TImportCustomers($sm['cache']['TImportCustomers'][$id]);
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


					function Filename()
						{
							return $this->info['filename'];
						}

					function SetFilename($val)
						{
							$this->UpdateValues(Array('filename'=>$val));
						}

					function Fields()
						{
							return $this->info['fields'];
						}

					function SetFields($val)
						{
							$this->UpdateValues(Array('fields'=>$val));
						}

					function Tags()
						{
							return $this->info['tags'];
						}

					function SetTags($val)
						{
							$this->UpdateValues(Array('tags'=>$val));
						}

					function ContactListID()
						{
							return intval($this->info['id_contact_list']);
						}

					function SetContactListID($val)
						{
							$this->UpdateValues(Array('id_contact_list'=>intval($val)));
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function ReadyToImport()
						{
							return intval($this->info['ready_to_import']);
						}

					function SetReadyToImport($val)
						{
							$this->UpdateValues(Array('ready_to_import'=>intval($val)));
						}

					function SetStaus($val)
						{
							$this->UpdateValues(Array('status'=>$val));
						}

					function Staus()
						{
							return $this->info['status'];
						}

					function ComplianceMessage()
						{
							return intval($this->info['compliance_message']);
						}

					function SetComplianceMessage($val)
						{
							$this->UpdateValues(Array('compliance_message'=>intval($val)));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('import_customers');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($filename, $fields, $tags, $id_contact_list, $id_company, $ready_to_import=0, $compliance_message=0)
						{
							$sql=new TQuery('import_customers');
							$sql->Add('filename', dbescape($filename));
							$sql->Add('fields', dbescape($fields));
							$sql->Add('tags', dbescape($tags));
							$sql->Add('id_contact_list', intval($id_contact_list));
							$sql->Add('id_company', intval($id_company));
							$sql->Add('ready_to_import', intval($ready_to_import));
							$sql->Add('compliance_message', intval($compliance_message));
							$object = new TImportCustomers($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('import_customers')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TImportCustomers_DEFINED", 1);
		}

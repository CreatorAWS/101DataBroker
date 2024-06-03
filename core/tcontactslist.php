<?php

	if (!defined("TContactsList_DEFINED"))
		{
			Class TContactsList
				{
					protected $info;
					var $contactids = NULL;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('contacts_lists')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TContactsList'][$id]))
								{
									$object=new TContactsList($id);
									if ($object->Exists())
										$sm['cache']['TContactsList'][$id]=$object->GetRawData();
								}
							else
								$object=new TContactsList($sm['cache']['TContactsList'][$id]);
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

					function LoadLists($rewritecache = false)
						{
							if ($rewritecache || $this->contactids === NULL)
								{
									$this->contactids = $this->GetTaxonomy('customertolist', $this->ID());
								}
						}

					function SetContactID($contact_id)
						{
							$this->SetTaxonomy('customertolist', intval($contact_id), $this->ID());
						}

					function UnsetContactID($contact_id)
						{
							$this->UnsetTaxonomy('customertolist', intval($contact_id), $this->ID());
							$this->LoadLists(true);
						}

					function HasContactID($contact_id)
						{
							$this->LoadLists();
							return in_array(intval($contact_id), $this->contactids);
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('title'=>$val));
						}

					function GetCustomerCount()
						{
							return count($this->GetCustomerIDsArray());
						}

					function GetCustomerIDsArray()
						{
							$this->LoadLists();
							return $this->GetTaxonomy('customertolist', $this->ID(), true);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('contacts_lists');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_company)
						{
							$sql=new TQuery('contacts_lists');
							$sql->Add('id_company', intval($id_company));
							$object = new TContactsList($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('contacts_lists')->AddWhere('id', intval($this->ID()))->Remove();
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
			define("TContactsList_DEFINED", 1);
		}

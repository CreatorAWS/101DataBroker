<?php

	if (!defined("TFieldsCategory_DEFINED"))
		{
			Class TFieldsCategory
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('customer_fields_categories')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}
					public static function IntWithName($id_company, $name, $id_template)
						{
							use_api('cleaner');
							$info = TQuery::ForTable('customer_fields_categories')->Add('id_company', intval($id_company))->Add('category', dbescape($name))->Add('id_template', intval($id_template))->OrderBy('id')->Get();
							$category = new TFieldsCategory($info);
							return $category;
						}
					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TFieldsCategory'][$id]))
								{
									$object=new TFieldsCategory($id);
									if ($object->Exists())
										$sm['cache']['TFieldsCategory'][$id]=$object->GetRawData();
								}
							else
								$object=new TFieldsCategory($sm['cache']['TFieldsCategory'][$id]);
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

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function TemplateID()
						{
							return intval($this->info['id_template']);
						}

					function SetTemplateID($val)
						{
							$this->UpdateValues(Array('id_template'=>intval($val)));
						}

					function Category()
						{
							return $this->info['category'];
						}

					function SetCategory($val)
						{
							$this->UpdateValues(Array('category'=>$val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('customer_fields_categories');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_company=0, $category, $id_template=0)
						{
							$sql=new TQuery('customer_fields_categories');
							$sql->Add('id_company', intval($id_company));
							$sql->Add('category', dbescape($category));
							$sql->Add('id_template', intval($id_template));
							$object = new TFieldsCategory($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('customer_fields_categories')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TFieldsCategory_DEFINED", 1);
		}

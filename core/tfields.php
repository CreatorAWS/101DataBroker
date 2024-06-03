<?php

	if (!defined("TFields_DEFINED"))
		{
			Class TFields
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('customer_fields')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function CreateOrIntWithName($company, $name, $id_template, $category = 0)
						{
							$info = TQuery::ForTable('customer_fields')->Add('id_company', Cleaner::IntObjectID($company))->Add('field', dbescape($name))->Add('id_template', Cleaner::IntObjectID($id_template))->OrderBy('id')->Get();
							$field = new TFieldsCategory($info);

							if(!$field->Exists())
								{
									if (empty($category))
										{
											$category_check = TFieldsCategory::IntWithName($company->ID(), 'Other', $company->CustomerFormTemplate());
											if(!$category_check->Exists())
												$new_category = TFieldsCategory::Create($company->ID(), 'Other', $company->CustomerFormTemplate());
											else
												$new_category = $category_check;
										}
									$new_field = TFields::Create($company, $new_category->ID(), $name, $company->CustomerFormTemplate());
								}
							else
								$new_field = $field;

							return $new_field;
						}

					public static function IntWithName($id_company, $name, $id_template)
						{
							use_api('cleaner');
							$info = TQuery::ForTable('customer_fields')->Add('id_company', intval($id_company))->Add('field', dbescape($name))->Add('id_template', intval($id_template))->OrderBy('id')->Get();
							$field = new TFieldsCategory($info);
							return $field;
						}
					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TFields'][$id]))
								{
									$object=new TFields($id);
									if ($object->Exists())
										$sm['cache']['TFields'][$id]=$object->GetRawData();
								}
							else
								$object=new TFields($sm['cache']['TFields'][$id]);
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

					function CtgID()
						{
							return intval($this->info['id_ctg']);
						}

					function SetCtgID($val)
						{
							$this->UpdateValues(Array('id_ctg'=>intval($val)));
						}

					function isDisabled()
						{
							return $this->info['disabled']==1;
						}
					function DisableField()
						{
							$this->UpdateValues(Array('disabled'=>1));
						}
					function EnableField()
						{
							$this->UpdateValues(Array('disabled'=>0));
						}
					function Field()
						{
							return $this->info['field'];
						}

					function SetField($val)
						{
							$this->UpdateValues(Array('field'=>$val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('customer_fields');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_company=0, $id_ctg, $field, $id_template=0)
						{
							$sql=new TQuery('customer_fields');
							$sql->Add('id_company', Cleaner::IntObjectID($id_company));
							$sql->Add('id_ctg', Cleaner::IntObjectID($id_ctg));
							$sql->Add('id_template', Cleaner::IntObjectID($id_template));
							$sql->Add('field', dbescape($field));
							$object = new TFields($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('customer_fields')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TFields_DEFINED", 1);
		}

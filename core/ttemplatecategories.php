<?php

	if (!defined("TTemplateCategories_DEFINED"))
		{
			Class TTemplateCategories
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('template_categories')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function GetCategoriesTitles()
						{
							return self::GetCategoriesListObject()->ExtractTitlesArray();
						}
					public static function GetCategoriesIDs()
						{
							return self::GetCategoriesListObject()->ExtractIDsArray();
						}
					public static function GetCategoriesListObject()
						{
							$categories = new TTemplateCategoriesList();
							$categories->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
							return $categories->Load();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TTemplateCategories'][$id]))
								{
									$object=new TTemplateCategories($id);
									if ($object->Exists())
										$sm['cache']['TTemplateCategories'][$id]=$object->GetRawData();
								}
							else
								$object=new TTemplateCategories($sm['cache']['TTemplateCategories'][$id]);
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


					function Title()
						{
							return $this->info['title'];
						}

					function SetTitle($val)
						{
							$this->UpdateValues(Array('title'=>$val));
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function TextTemplatesCount()
						{
							$templates = new TMessageTemplateList();
							$templates->SetFilterCompany(TCompany::CurrentCompany());
							$templates->SetFilterCategory($this->ID());
							return $templates->TotalCount();
						}

					function EmailTemplatesCount()
						{
							$templates = new TEmailTemplateList();
							$templates->SetFilterCompany(TCompany::CurrentCompany());
							$templates->SetFilterCategory($this->ID());
							return $templates->TotalCount();
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('template_categories');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($title)
						{
							$sql=new TQuery('template_categories');
							$sql->Add('title', dbescape($title));
							$object = new TTemplateCategories($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('template_categories')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TTemplateCategories_DEFINED", 1);
		}

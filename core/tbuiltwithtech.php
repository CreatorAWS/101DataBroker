<?php

	if (!defined("TBuiltWithTech_DEFINED"))
		{
			Class TBuiltWithTech
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('builtwith_tech')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TBuiltWithTech'][$id]))
								{
									$object=new TBuiltWithTech($id);
									if ($object->Exists())
										$sm['cache']['TBuiltWithTech'][$id]=$object->GetRawData();
								}
							else
								$object=new TBuiltWithTech($sm['cache']['TBuiltWithTech'][$id]);
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

					function HasTitle()
						{
							return !empty($this->info['title']);
						}


					function CategoryID()
						{
							return intval($this->info['id_category']);
						}

					function SetCategoryID($val)
						{
							$this->UpdateValues(Array('id_category'=>intval($val)));
						}

					function HasCategoryID()
						{
							return !empty($this->info['id_category']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('builtwith_tech');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($title, $id_category)
						{
							$sql=new TQuery('builtwith_tech');
							$sql->Add('title', dbescape($title));
							$sql->Add('id_category', intval($id_category));
							$object = new TBuiltWithTech($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('builtwith_tech')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TBuiltWithTech_DEFINED", 1);
		}

<?php

	if (!defined("TBuiltWithTechGroup_DEFINED"))
		{
			Class TBuiltWithTechGroup
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('builtwith_tech_grouos')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TBuiltWithTechGroup'][$id]))
								{
									$object=new TBuiltWithTechGroup($id);
									if ($object->Exists())
										$sm['cache']['TBuiltWithTechGroup'][$id]=$object->GetRawData();
								}
							else
								$object=new TBuiltWithTechGroup($sm['cache']['TBuiltWithTechGroup'][$id]);
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


					function GroupID()
						{
							return intval($this->info['id_group']);
						}

					function SetGroupID($val)
						{
							$this->UpdateValues(Array('id_group'=>intval($val)));
						}

					function HasGroupID()
						{
							return !empty($this->info['id_group']);
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


					function Url()
						{
							return $this->info['url'];
						}

					function SetUrl($val)
						{
							$this->UpdateValues(Array('url'=>$val));
						}

					function HasUrl()
						{
							return !empty($this->info['url']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('builtwith_tech_grouos');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_group = 0, $title = '', $url = '')
						{
							$sql=new TQuery('builtwith_tech_grouos');
							$sql->Add('id_group', intval($id_group));
							$sql->Add('title', dbescape($title));
							$sql->Add('url', dbescape($url));
							$object = new TBuiltWithTechGroup($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('builtwith_tech_grouos')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TBuiltWithTechGroup_DEFINED", 1);
		}

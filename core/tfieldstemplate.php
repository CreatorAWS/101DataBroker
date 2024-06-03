<?php

	if (!defined("TFieldsTemplate_DEFINED"))
		{
			Class TFieldsTemplate
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('customer_fields_templates')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TFieldsTemplate'][$id]))
								{
									$object=new TFieldsTemplate($id);
									if ($object->Exists())
										$sm['cache']['TFieldsTemplate'][$id]=$object->GetRawData();
								}
							else
								$object=new TFieldsTemplate($sm['cache']['TFieldsTemplate'][$id]);
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

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('customer_fields_templates');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($title)
						{
							$sql=new TQuery('customer_fields_templates');
							$sql->Add('title', dbescape($title));
							$object = new TFieldsTemplate($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('customer_fields_templates')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TFieldsTemplate_DEFINED", 1);
		}

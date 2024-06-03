<?php

	if (!defined("TMessageTemplate_DEFINED"))
		{
			Class TMessageTemplate
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('message_templates')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					public static function initWithText($text)
						{
							$info = TQuery::ForTable('message_templates')->AddWhere('text = "'.dbescape($text).'"')->Get();
							$template = new TMessageTemplate($info);
							return $template;
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
							$this->UpdateValues(Array('id_company' => intval($val)));
						}


					function Title()
						{
							return $this->Text();
						}

					function Text()
						{
							return $this->info['text'];
						}

					function SetText($val)
						{
							$this->UpdateValues(Array('text' => $val));
						}

					function AssetID()
						{
							return intval($this->info['id_asset']);
						}

					function SetCategoryID($val)
						{
							$this->UpdateValues(Array('id_ctg' => $val));
						}
					function CategoryID()
						{
							return intval($this->info['id_ctg']);
						}

					function SetAssetID($val)
						{
							$this->UpdateValues(Array('id_asset' => intval($val)));
						}

					function HasAssetID()
						{
							return !empty($this->info['id_asset']);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('message_templates');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							$q = new TQuery('message_templates');
							$q->AddWhere('id', intval($this->ID()));
							$q->Remove();
						}

					public static function Create()
						{
							$q = new TQuery('message_templates');
							$q->Add('id_company', intval(TCompany::CurrentCompany()->ID()));
							$object=new TMessageTemplate($q->Insert());
							return $object;
						}

				}

			define("TMessageTemplate_DEFINED", 1);
		}

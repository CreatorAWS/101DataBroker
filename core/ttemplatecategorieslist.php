<?php

	if (!defined("TTemplateCategoriesList_DEFINED"))
		{
			Class TTemplateCategoriesList extends TGenericList
				{
				/** @var TTemplateCategories[] $items */
					public $items;
					protected $tablename = 'template_categories';
					protected $idfield = 'id';
					protected $titlefield='title';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCompanyAndDefault($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id).' OR id_company=0';
						}

					function SetFilterDefaultCategories()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company = 0';
						}

					protected function InitItem($index)
						{
							$item = new TTemplateCategories($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TTemplateCategoriesList_DEFINED", 1);
		}

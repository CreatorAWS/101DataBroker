<?php

	if (!defined("TFieldsCategoriesList_DEFINED"))
		{
			Class TFieldsCategoriesList extends TGenericList
				{
				/** @var TFields[] $itemsinfo */
					public $items;
					protected $tablename='customer_fields_categories';
					protected $idfield='id';
					protected $titlefield='category';

					function SetFilterCompany($id_company)
						{
							if (is_object($id_company))
								$id_company=$id_company->ID();
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_company=".intval($id_company);
						}

					function SetFilterTemplate($id_template)
						{
							if (is_object($id_template))
								$id_template=$id_template->ID();
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_template=".intval($id_template);
						}

					protected function InitItem($index)
						{
							$item = new TFieldsCategory($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TFieldsCategoriesList_DEFINED", 1);
		}

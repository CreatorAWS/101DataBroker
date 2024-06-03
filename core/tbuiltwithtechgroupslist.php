<?php

	if (!defined("TBuiltWithTechGroupsList_DEFINED"))
		{

			Class TBuiltWithTechGroupsList extends TGenericList
				{
					/** @var TBuiltWithTechGroup[] $items */
					public $items;
					protected $tablename = 'builtwith_tech_grouos';
					protected $idfield = 'id';
					protected $titlefield = 'title';

					function SetFilterCategory($category_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_group='.Cleaner::IntObjectID($category_or_id);
						}

					function SetFilterMainCategory()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_group = 0 ';
						}

					protected function InitItem($index)
						{
							$item = new TBuiltWithTechGroup($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TBuiltWithTechGroupsList_DEFINED", 1);
		}

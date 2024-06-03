<?php

	if (!defined("TBuiltWithTechList_DEFINED"))
		{

			Class TBuiltWithTechList extends TGenericList
				{
					/** @var TBuiltWithTech[] $items */
					public $items;
					protected $tablename = 'builtwith_tech';
					protected $idfield = 'id';
					protected $titlefield = 'title';

					function SetFilterCategory($category_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_category='.Cleaner::IntObjectID($category_or_id);
						}

					function SetFilterTitle($title)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' title LIKE "'.$title.'%"';
						}

					protected function InitItem($index)
						{
							$item = new TBuiltWithTech($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TBuiltWithTechList_DEFINED", 1);
		}

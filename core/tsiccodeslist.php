<?php

	if (!defined("TSicCodesList_DEFINED"))
		{
			Class TSicCodesList extends TGenericList
				{
					/** @var TSicCodes[] $items */
					public $items;
					protected $tablename='sic_codes';
					protected $idfield='id';
					protected $titlefield='sic';

					function SetFilterEnabled()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' disabled=0';
						}

					function SetFilterSearchQuery($query)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' (sic LIKE "'.$query.'%" OR sic_name LIKE "'.$query.'%")';
						}

					protected function InitItem($index)
						{
							$item = new TSicCodes($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TSicCodesList_DEFINED", 1);
		}

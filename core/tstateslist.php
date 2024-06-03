<?php

	if (!defined("TStatesList_DEFINED"))
		{
			Class TStatesList extends TGenericList
				{
					/** @var TState[] $items */
					public $items;
					protected $tablename='states';
					protected $idfield='id';
					protected $titlefield='state';

					function SetFilterSearchQuery($query)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' (state LIKE "'.$query.'%" OR state_abbr LIKE "'.$query.'%")';
						}

					protected function InitItem($index)
						{
							$item = new TState($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TStatesList_DEFINED", 1);
		}

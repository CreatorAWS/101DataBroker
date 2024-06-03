<?php

	if (!defined("TCompanyList_DEFINED"))
		{
			Class TCompanyList extends TGenericList
				{
					/** @var TCompany[] $itemsinfo */
					public $items;
					protected $tablename='companies';
					protected $idfield='id';
					protected $titlefield='name';

					function SetFilterNotExpired()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' (expiration=0 OR expiration>'.time().')';
						}

					protected function InitItem($index)
						{
							$item = new TCompany($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCompanyList_DEFINED", 1);
		}

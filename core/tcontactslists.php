<?php

	if (!defined("TContactsLists_DEFINED"))
		{
			Class TContactsLists extends TGenericList
				{
				/** @var TContactsList[] $items */
					public $items;
					protected $tablename='contacts_lists';
					protected $idfield='id';
					protected $titlefield='title';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					protected function InitItem($index)
						{
							$item = new TContactsList($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TContactsLists_DEFINED", 1);
		}

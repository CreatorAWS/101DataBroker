<?php

	if (!defined("TImportCustomersList_DEFINED"))
		{
			Class TImportCustomersList extends TGenericList
				{
				/** @var TImportCustomers[] $itemsinfo */
					public $items;
					protected $tablename='import_customers';
					protected $idfield='id';

					function SetFilterReadyToImport()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' ready_to_import=1';
						}

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterContactList($list_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_contact_list='.Cleaner::IntObjectID($list_or_id);
						}

					protected function InitItem($index)
						{
							$item = new TImportCustomers($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TImportCustomersList_DEFINED", 1);
		}

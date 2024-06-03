<?php

	if (!defined("TCustomerNotesList_DEFINED"))
		{
			Class TCustomerNotesList extends TGenericList
				{
				/** @var TEmailTemplate[] $items */
					public $items;
					protected $tablename='customer_notes';
					protected $idfield='id';
					protected $titlefield='text';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCustomer($id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_customer='.Cleaner::IntObjectID($id);
						}

					protected function InitItem($index)
						{
							$item = new TCustomerNote($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCustomerNotesList_DEFINED", 1);
		}

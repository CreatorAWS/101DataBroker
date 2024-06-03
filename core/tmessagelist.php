<?php

	if (!defined("TMessageList_DEFINED"))
		{
			Class TMessageList extends TGenericList
				{
					/** @var TMessage[] $items */
					public $items;
					protected $tablename='smslog';
					protected $idfield='id';
					protected $titlefield='text';

					function SetFilterCustomer($customer_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_customer='.Cleaner::IntObjectID($customer_or_id);
						}

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterScheduledTimeBetween($from, $to)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' sendafter BETWEEN '.intval($from).' AND '.intval($to);
						}

					function SetFilterAddedTimeBetween($from, $to)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' timeadded BETWEEN '.intval($from).' AND '.intval($to);
						}

					function SetFilterIncoming()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' is_incoming=1';
						}

					function SetFilterOutgoing()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' is_incoming=0';
						}

					protected function InitItem($index)
						{
							$item = new TMessage($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TMessageList_DEFINED", 1);
		}

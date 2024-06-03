<?php

	if (!defined("TCustomerCallList_DEFINED"))
		{
			Class TCustomerCallList extends TGenericList
				{
					/** @var TCustomerCall[] $items */
					public $items;
					protected $tablename='customer_calls';
					protected $idfield='id';
					protected $titlefield='phone';

					function __construct()
						{
							parent::__construct();
						}

					function SetFilterCompany($company)
						{
						}

					function SetFilterStatus($status)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_status=".Cleaner::IntObjectID($status);
						}

					function SetFilterCustomer($customer)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_customer=".Cleaner::IntObjectID($customer);
						}

					function OrderByTime($asc=true)
						{
							$this->orderby='timemade';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					function SetFilterEmployee($employee)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_emloyee=".Cleaner::IntObjectID($employee);
						}

					protected function InitItem($index)
						{
							$item = new TCustomerCall($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCustomerCallList_DEFINED", 1);
		}

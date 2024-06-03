<?php

	if (!defined("TAppointmentList_DEFINED"))
		{
			Class TAppointmentList extends TGenericList
				{
					/** @var TAppointment[] $items */
					public $items;
					protected $tablename='appointments';
					protected $idfield='id';
					protected $titlefield='note';

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

					function SetFilterNotificationNotSent()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' notification_sent=0';
						}

					function SetFilterScheduledTimeBetween($from, $to)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' timescheduled BETWEEN '.intval($from).' AND '.intval($to);
						}

					function OrderByTimeScheduled($asc=true)
						{
							$this->orderby='timescheduled';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					protected function InitItem($index)
						{
							$item = new TAppointment($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TAppointmentList_DEFINED", 1);
		}

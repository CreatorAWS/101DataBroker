<?php

	if (!defined("TOrganizationSearchesList_DEFINED"))
		{
			Class TOrganizationSearchesList extends TGenericList
				{
				/** @var TOrganizationSearch[] $items */
					public $items;
					protected $tablename='organizations_searches';
					protected $idfield='id';
					protected $titlefield='tech';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterDay()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::DayStart(). " AND addedtime<=".SMDateTime::DayEnd();
						}

					function SetFilterMonth()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::MonthStart(). " AND addedtime<=".time();
						}

					function SetFilter7days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::DayStart(time()-7*24*3600). " AND addedtime<=".time();
						}

					function SetFilter14days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::DayStart(time()-14*24*3600). " AND addedtime<=".time();
						}

					function SetFilter30days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::DayStart(time()-30*24*3600). " AND addedtime<=".time();
						}

					function SetFilterWeek()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::WeekStart(). " AND addedtime<=".time();
						}

					function SetFilterCustomPeriod(int $start_period, int $end_period): self
						{
							$this->SetFilterIntValuesBetween('addedtime', $start_period, SMDateTime::DayEnd($end_period));
							return $this;
						}

					protected function InitItem($index)
						{
							$item = new TOrganizationSearch($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TOrganizationSearchesList_DEFINED", 1);
		}

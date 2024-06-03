<?php

	if (!defined("TGoogleSearchList_DEFINED"))
		{
			Class TGoogleSearchList extends TGenericList
				{
					/** @var TGoogleSearch[] $items */
					public $items;
					protected $tablename='google_searches';
					protected $idfield='id';

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

					function SetFilterCustomPeriod(int $start_period, int $end_period): self
						{
							$this->SetFilterIntValuesBetween('addedtime', $start_period, SMDateTime::DayEnd($end_period));
							return $this;
						}

					function SetFilterWeek()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" addedtime>=".SMDateTime::WeekStart(). " AND addedtime<=".time();
						}

					protected function InitItem($index)
						{
							$item = new TGoogleSearch($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TGoogleSearchList_DEFINED", 1);
		}

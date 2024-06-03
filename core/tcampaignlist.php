<?php

	if (!defined("TCampaignList_DEFINED"))
		{
			Class TCampaignList extends TGenericList
				{
					/** @var TCampaign[] $items */
					public $items;
					protected $tablename = 'campaigns';
					protected $idfield = 'id';
					protected $timefield = 'addedtime';
					protected $titlefield = 'note';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterSystemCampaignID($id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_system_sequence='.intval($id);
						}

					function SetFilterSceduled()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status='scheduled'";
						}
					function SetFilterSceduledBefore($timestamp)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status='scheduled' AND starttime<".intval($timestamp);
						}
					function SetFilterSendBefore($timestamp)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " starttime<".intval($timestamp);
						}

					function SetFilterAddedTimeBetween($from, $to)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' addedtime BETWEEN '.intval($from).' AND '.intval($to);
						}

					function SetFilterSceduledAfter($timestamp)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status='scheduled' AND starttime>".intval($timestamp);
						}

					function ExcludeStatusesArray($array)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status NOT IN (".implode(', ', Cleaner::ArrayQuotedAndDBEscaped($array)).")";
						}
					function OrderByAddedTime($asc=true)
						{
							$this->orderby=$this->timefield;
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}
					protected function InitItem($index)
						{
							$item = new TCampaign($this->itemsinfo[$index]);
							return $item;
						}

					function SetFilterDay()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" starttime>=".SMDateTime::DayStart(time()). " AND starttime<=".SMDateTime::DayEnd(time());
						}

					function SetFilterWeek()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" starttime>=".SMDateTime::WeekStart(). " AND starttime<=".time();
						}

					function SetFilter14days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" starttime>=".SMDateTime::DayStart(time()-14*24*3600). " AND starttime<=".time();
						}

					function SetFilterMonth()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" starttime>=".SMDateTime::MonthStart(). " AND starttime<=".time();
						}
				}

			define("TCampaignList_DEFINED", 1);
		}

<?php

	if (!defined("TMessagesLogList_DEFINED"))
		{
			Class TMessagesLogList extends TGenericList
				{
					/** @var TMessagesLog[] $items */
					public $items;
					protected $tablename='messagelog';
					protected $idfield='id';

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

					function SetFilterTimeSent($time)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' timesent='.intval($time);
						}

					function SetFilterEmail()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' type="email"';
						}

					function SetFilterReply($reply_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_reply='.intval($reply_id);
						}

					function SetFilterNotReply()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_reply=0';
						}

					function SetFilterGreaterThenID($last_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id > '.$last_id;
						}

					protected function InitItem($index)
						{
							$item = new TMessagesLog($this->itemsinfo[$index]);
							return $item;
						}
					function OrderByTimestamp($asc=true)
						{
							$this->orderby='timesent';
							if (!$asc)
								$this->orderby.=' DESC';
						}

					function SetFilterAddedTimeBetween($from, $to)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' timesent BETWEEN '.intval($from).' AND '.intval($to);
						}

					function SetFilterMessages()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' type<>"call"';
						}

					function SetFilterCalls()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' type="call"';
						}

					function SetFilterOutgoing()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' incoming=0';
						}

					function SetFilterIncoming()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' incoming=1';
						}

					function SetFilterUnread()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' timeread="0"';
						}

					function SetFilterDay()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" timesent>=".SMDateTime::DayStart(). " AND timesent<=".SMDateTime::DayEnd();
						}

					function SetFilterMonth()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" timesent>=".SMDateTime::MonthStart(). " AND timesent<=".time();
						}

					function SetFilter7days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" timesent>=".SMDateTime::DayStart(time()-7*24*3600). " AND timesent<=".time();
						}

					function SetFilter14days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" timesent>=".SMDateTime::DayStart(time()-14*24*3600). " AND timesent<=".time();
						}

					function SetFilter30days()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" timesent>=".SMDateTime::DayStart(time()-30*24*3600). " AND timesent<=".time();
						}

					function SetFilterCustomPeriod(int $start_period, int $end_period): self
						{
							$this->SetFilterIntValuesBetween('timesent', $start_period, SMDateTime::DayEnd($end_period));
							return $this;
						}
				}

			define("TMessagesLogList_DEFINED", 1);
		}

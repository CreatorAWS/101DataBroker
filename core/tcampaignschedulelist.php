<?php

	if (!defined("TCampaignScheduleList_DEFINED"))
		{
			Class TCampaignScheduleList extends TGenericList
				{
					/** @var TCampaignSchedule[] $items */
					public $items;
					protected $tablename = 'campaigns_schedule';
					protected $idfield = 'id';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCampaign($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_campaign='.Cleaner::IntObjectID($object_or_id);
						}

					function ExtractCampaignsIDsArray()
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->CampaignID();
								}
							return $r;
						}

					function ExtractCustomerIDsArray()
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->CustomerID();
								}
							return $r;
						}

					function SetFilterCustomer($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_customer='.Cleaner::IntObjectID($object_or_id);
						}

					function SetFilterCustomersIDs($arrayids)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							if (!is_array($arrayids) || count($arrayids)==0)
								$this->sql .= " 1=2 ";
							else
								$this->sql .= " id_customer IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
						}

					function SetFilterSequence($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_sequence='.Cleaner::IntObjectID($object_or_id);
						}

					function SetFilterStatus($mode)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " status='".dbescape($mode)."'";
						}

					function SetFilterNextActionTimeBefore($timestamp)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " scheduledtime<".intval($timestamp);
						}

					function OrderByActionTime($asc=true)
						{
							$this->orderby='scheduledtime';
							if (!$asc)
								$this->orderby.=' DESC';
							$this->orderby.=', id';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					protected function InitItem($index)
						{
							$item = new TCampaignSchedule($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCampaignScheduleList_DEFINED", 1);
		}

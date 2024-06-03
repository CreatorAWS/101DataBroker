<?php

	use_api('tcustomer');

	class TCustomerList
		{
			var $info;
			var $company;
			/** @var TCustomer[] */
			var $items;
			private $itemsinfo;
			var $limit;
			var $offset;
			private $sql;
			private $orderby;
			private $showdeleted=false;
			private $totalcount=NULL;
			private $db_result;
			function __construct()
				{
					$this->limit=0;
					$this->offset=0;
					$this->OrderByID();
				}
			public static function LoadCustomers()
				{

				}
			private function GetSQL($sql='')
				{
					global $sm, $userinfo;
					if (!empty($sql))
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=$sql;
						}
					if (!$this->showdeleted)
						$this->sql.=(empty($this->sql)?'':' AND ').' deleted=0';
					if (empty($this->sql))
						$this->sql=' 1=2';
					$sql = " FROM customers WHERE ".$this->sql;
					return $sql;
				}
			function Load($sql='')
				{
					$sql.="SELECT * ".$this->GetSQL();
					if (!empty($this->orderby))
						$sql.=' ORDER BY '.$this->orderby;
					if (!empty($this->limit))
						$sql.=' LIMIT '.intval($this->limit);
					if (!empty($this->offset))
						$sql.=' OFFSET '.intval($this->offset);
					$itemsinfo=getsqlarray($sql);
					$this->InitWithItemsData($itemsinfo);
				}
			function TotalCount($rewritecache=false)
				{
					if ($rewritecache || $this->totalcount===NULL)
						{
							$sql .= "SELECT count(*) ".$this->GetSQL();
							$this->totalcount = intval(getsqlfield($sql));
						}
					return intval($this->totalcount);
				}
			function InitWithItemsData($itemsinfo)
				{
					$this->itemsinfo=$itemsinfo;
					for ($i=0; $i<count($this->itemsinfo); $i++)
						$this->items[$i]=new TCustomer($this->itemsinfo[$i]);
				}
			function Count()
				{
					return count($this->items);
				}
			function Limit($limit)
				{
					$this->limit=intval($limit);
				}
			function Offset($offset)
				{
					$this->offset=intval($offset);
				}
			function OrderBySMSAcceptPendingTimestamp($asc=true)
				{
					$this->orderby='sms_pending_time';
					if (!$asc)
						$this->orderby.=' DESC';
				}
			function OrderByUnreadOrLastUpdate($asc=true)
				{
					if (!$asc)
						$this->orderby=' if(unread>0, 0, 1), unread DESC, lastupdate DESC';
					else
						$this->orderby=' if(unread>0, 0, 1), unread ASC, lastupdate ASC';
				}

			function SetFilterStatus($status_tag)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" `status`='".dbescape($status_tag)."'";
				}
			function SetFilterEnabled()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" `is_enabled`=1";
				}

			function SetFilterDay()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded>=".SMDateTime::DayStart(time()). " AND timeadded<=".SMDateTime::DayEnd(time());
				}

			function SetFilterWeek()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded>=".SMDateTime::WeekStart(). " AND timeadded<=".time();
				}

			function SetFilter14days()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded>=".SMDateTime::DayStart(time()-14*24*3600). " AND timeadded<=".time();
				}

			function SetFilterMonth()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded>=".SMDateTime::MonthStart(). " AND timeadded<=".time();
				}
			function SetFilter10DaysOld()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" `lastupdate`>'".(time() - 240*60*60)."'";
				}

			function OrderByID($asc=true)
				{
					$this->orderby='id';
					if (!$asc)
						$this->orderby.=' DESC';
				}
			function OrderByName($asc=true)
				{
					$this->orderby="last_name";
					if (!$asc)
						$this->orderby.=' DESC';
					$this->orderby.=", first_name";
					if (!$asc)
						$this->orderby.=' DESC';
				}
			function SetFilterIDs($array)
				{
					use_api('cleaner');
					if (!empty($this->sql))
						$this->sql.=' AND ';
					if (!is_array($array) || count($array)==0)
						$this->sql.=" 1=2";
					else
						$this->sql.=" id IN (".implode(', ', Cleaner::ArrayIntval($array)).")";
				}
			function SetFilterCompany($id_company)
				{
					if (is_object($id_company))
						$id_company=$id_company->ID();
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" id_company=".intval($id_company);
				}
			function SetFilterNotDeleted()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" deleted <> 1";
				}
			function SetFilterSMSAcceptedTag($tag)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" sms_accepted='".dbescape($tag)."'";
				}
			function SetFilterCellPhone($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" cellphone='".dbescape(Cleaner::USPhone($val))."'";
				}
			function SetFilterRegisteredFrom($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded>=".intval($val);
				}
			function SetFilterUnread()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" unread>0";
				}
			function SetFilterRegisteredTo($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" timeadded<=".intval($val);
				}
			function SetFilterName($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" (first_name LIKE '%".dbescape($val)."%' OR last_name LIKE '%".dbescape($val)."%')";
				}
			function SetFilterVehicleMake($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" vehicle_make LIKE '%".dbescape($val)."%'";
				}
			function SetFilterVehicleModel($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" vehicle_model LIKE '%".dbescape($val)."%'";
				}
			function SetFilterVehicleCondition($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" vehicle_condition LIKE '%".dbescape($val)."%'";
				}
			function SetFilterSMSAcceptPendingTimeLTE($time=NULL)
				{
					if ($time===NULL)
						$time=time();
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" sms_pending_time<=".intval($time);
				}
			function SetFilterSMSAcceptPending1()
				{
					$this->SetFilterSMSAcceptedTag('pending1');
				}
			function SetFilterSMSAcceptPending2()
				{
					$this->SetFilterSMSAcceptedTag('pending2');
				}
			function SetFilterSMSAcceptNoResponse()
				{
					$this->SetFilterSMSAcceptedTag('noresponse');
				}
			function SetFilterHasAppointments()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" has_appointment=1";
				}
			function ShowDeleted()
				{
					$this->showdeleted=true;
				}
			function OrderBySortOrder()
				{
					$this->orderby='sort_order, lastupdate DESC';

				}
			function SetFilterExcludeIDs($arrayids)
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';
					if (!is_array($arrayids))
						$this->sql .= " 1=2 ";
					else
						$this->sql .= " id NOT IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
				}

			function Open()
				{
					$sql="SELECT * ".$this->GetSQL();
					$this->db_result=execsql($sql);
					return $this;
				}
			function Fetch()
				{
					if ($data=database_fetch_assoc($this->db_result))
						{
							$object=new TCustomer($data);
							return $object;
						}
					else
						return false;
				}

			function ExtractIDsArray()
				{
					$r=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							$r[]=$this->items[$i]->ID();
						}
					return $r;
				}
			function ExtractNamesArray($addrolename=false)
				{
					$r=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							$r[]=$this->items[$i]->Name().($addrolename?' ('.$this->items[$i]->PrimaryRoleTitle().')':'');
						}
					return $r;
				}
		}

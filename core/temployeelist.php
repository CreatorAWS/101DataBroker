<?php

	use_api('temployee');

	class TEmployeeList
		{
			var $info;
			var $company;
			/** @var TEmployee[] */
			var $items;
			private $itemsinfo;
			var $limit;
			var $offset;
			private $sql;
			private $orderby;
			function __construct($company_id=NULL)
				{
					if ($company_id==NULL)
						$this->company=TCompany::CurrentCompany()->ID();
					elseif($company_id != 'all')
						$this->company=intval($company_id);
					$this->limit=0;
					$this->offset=0;
					$this->OrderByID();
				}
			private function GetSQL($sql='')
				{
					global $sm, $userinfo;
					if (!empty($this->company))
						$this->sql="id_company=".intval($this->company).(empty($this->sql)?'':" AND ".$this->sql);
					if (!empty($sql))
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=$sql;
						}
					$sql = " FROM sm_users WHERE ".$this->sql;
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
			function TotalCount($sql='')
				{
					$sql.="SELECT count(*) ".$this->GetSQL();
					return intval(getsqlfield($sql));
				}
			function InitWithItemsData($itemsinfo)
				{
					$this->itemsinfo=$itemsinfo;
					for ($i=0; $i<count($this->itemsinfo); $i++)
						$this->items[$i]=new TEmployee($this->itemsinfo[$i]);
				}
			function Count()
				{
					return count($this->items);
				}

			function Item(int $index): TEmployee
				{
					return $this->items[$index];
				}

			function Limit($limit)
				{
					$this->limit=intval($limit);
				}
			function Offset($offset)
				{
					$this->offset=intval($offset);
				}
			function OrderByID($asc=true)
				{
					$this->orderby='id_user';
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
			function OrderBySelectedRoleAndName($first_role_tag)
				{
					$this->orderby=" IF (primary_role='".dbescape($first_role_tag)."', 0, 1), last_name, first_name";
				}
			function SetFilterRole($role_tag)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" primary_role='".dbescape($role_tag)."'";
				}
			function SetFilterNotDeleted()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" deleted=0";
				}
			function SetFilterHaveNewMessageFromCustomerNotificationsEnabled()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" new_msg_from_member_notif<>'no'";
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
			function SetFilterNotDeletedOr($ids)
				{
					use_api('cleaner');
					$ids=Cleaner::ArrayIntval($ids);
					if (!empty($this->sql))
						$this->sql.=' AND ';
					if (count($ids)>0)
						$this->sql.=" (deleted=0 OR id_user IN (".implode(',', $ids)."))";
					else
						$this->sql.=" deleted=0";
				}

			function SetFilterOnline()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" online_time >= ".(time() - 12);
				}

			function SetFilterHasCellPhone()
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" cellphone<>''";
				}

			function SetFilterIDs($array)
				{
					use_api('cleaner');
					if (!empty($this->sql))
						$this->sql.=' AND ';
					if (!is_array($array) || count($array)==0)
						$this->sql.=" 1=2";
					else
						$this->sql.=" id_user IN (".implode(', ', Cleaner::ArrayIntval($array)).")";
				}

			function SetFilterFirstName($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
						$this->sql.=" first_name LIKE '%".dbescape($val)."%'";
				}

			function SetFilterLastName($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
						$this->sql.=" last_name LIKE '%".dbescape($val)."%'";
				}

			function SetFilterName($val)
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" (first_name LIKE '%".dbescape($val)."%' OR last_name LIKE '%".dbescape($val)."%')";
				}
		}

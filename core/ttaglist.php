<?php

	use_api('ttag');

	class TTagList
		{
			var $info;
			var $company;
			/** @var TTag[] */
			var $items;
			private $itemsinfo;
			var $limit;
			var $offset;
			private $sql;
			private $orderby;
			function TTagList($company_id=NULL)
				{
					if ($company_id==NULL)
						$this->company=TCompany::CurrentCompany()->ID();
					else
						$this->company=intval($company_id);
					$this->limit=0;
					$this->offset=0;
					$this->OrderByID();
				}
			private function GetSQL($sql='')
				{
					global $sm, $userinfo;
					$this->sql="id_company=".intval($this->company).(empty($this->sql)?'':" AND ".$this->sql);
					if (!empty($sql))
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=$sql;
						}
					$sql = " FROM company_tags WHERE ".$this->sql;
					if (!empty($this->orderby))
						$sql.=' ORDER BY '.$this->orderby;
					if (!empty($this->limit))
						$sql.=' LIMIT '.intval($this->limit);
					if (!empty($this->offset))
						$sql.=' OFFSET '.intval($this->offset);
					return $sql;
				}
			function Load($sql='')
				{
					$sql.="SELECT * ".$this->GetSQL();
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
						$this->items[$i]=new TTag($this->itemsinfo[$i]);
				}
			function Count()
				{
					return count($this->items);
				}
			function Limit($limit)
				{
					$this->limit=intval($limit);
				}
			function Item($index)
				{
					return $this->items[$index];
				}
			function Offset($offset)
				{
					$this->offset=intval($offset);
				}
			function OrderByID($asc=true)
				{
					$this->orderby='id';
					if (!$asc)
						$this->orderby.=' DESC';
				}
			function OrderByName($asc=true)
				{
					$this->orderby="tag";
					if (!$asc)
						$this->orderby.=' DESC';
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
							$r[]=$this->items[$i]->Name();
						}
					return $r;
				}
			function SetFilterIDs($ids)
				{
					use_api('cleaner');
					$ids=Cleaner::ArrayIntval($ids);
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" (id IN (".implode(',', $ids)."))";
				}
			function GetCustomerIDsArray()
				{
					$ids=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							$tmp=$this->items[$i]->GetCustomerIDsArray();
							$ids=array_merge($ids, $tmp);
						}
					return array_values(array_unique($ids));
				}

			/** @return TTag */
			function GetTagByName($tag_name)
				{
					for ($i = 0; $i < $this->Count(); $i++)
						{
							if (strcmp(strtolower(trim($this->items[$i]->Name())), strtolower(trim($tag_name)))==0)
								return $this->items[$i];
						}
					return false;
				}

			function HasTagName($tag_name)
				{
					for ($i = 0; $i < $this->Count(); $i++)
						{
							if (strcmp(strtolower(trim($this->items[$i]->Name())), strtolower(trim($tag_name)))==0)
								return true;
						}
					return false;
				}
		}

?>
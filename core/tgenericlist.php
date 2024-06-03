<?php

	class TGenericList
		{
			var $info;
			var $items;
			var $itemsinfo;
			var $limit;
			var $offset;
			var $inititems=true;
			var $sqlappend='';
			var $groupby;
			protected $showallitemsifnofilters=false;
			protected $sql;
			protected $orderby;
			protected $tablename='table';
			protected $idfield='id';
			protected $titlefield='title';
			protected $totalcount=NULL;

			function __construct()
				{
					$this->limit=0;
					$this->offset=0;
					$this->OrderByID();
				}
			private function GetSQL()
				{
					global $sm, $userinfo;
					$sql=$this->sql;
					if (!empty($this->sqlappend))
						{
							if (!empty($sql))
								$sql.=' AND ';
							$sql.='('.$this->sqlappend.')';
						}
					if (empty($sql))
						{
							if ($this->showallitemsifnofilters)
								$sql = ' 1=1';
							else
								$sql = ' 1=2';
						}
					$sql = " FROM ".$this->tablename." WHERE ".$sql;
					return $sql;
				}

			function ShowAllItemsIfNoFilters()
				{
					$this->showallitemsifnofilters=true;
				}

			function Item($index)
				{
					return $this->items[$index];
				}

			function Load()
				{
					$sql = "SELECT * ".$this->GetSQL();
					if (!empty($this->groupby))
						$sql .= ' GROUP BY '.$this->groupby;
					if (!empty($this->orderby))
						$sql.=' ORDER BY '.$this->orderby;
					if (!empty($this->limit))
						$sql.=' LIMIT '.intval($this->limit);
					if (!empty($this->offset))
						$sql.=' OFFSET '.intval($this->offset);
					$this->itemsinfo=getsqlarray($sql);
					if ($this->inititems)
						{
							for ($i = 0; $i < count($this->itemsinfo); $i++)
								{
									$this->items[$i] = $this->InitItem($i);
								}
						}
					return $this;
				}
			function TotalCount($reloadcache=false)
				{
					if ($reloadcache || $this->totalcount===NULL)
						{
							$sql = "SELECT count(*) ".$this->GetSQL();
							$this->totalcount=intval(getsqlfield($sql));
						}
					return intval($this->totalcount);
				}
			function GroupBy($groupbyfileds)
				{
					if (is_array($groupbyfileds))
						$groupbyfileds=implode(', ', $groupbyfileds);
					$this->groupby = $groupbyfileds;
					return $this;
				}
			protected function InitItem($index)
				{
					exit('Create loader InitItem');
					$item=new TGenericObject($this->itemsinfo[$index]);
					return $item;
				}
			function Count()
				{
					return count($this->itemsinfo);
				}
			function Limit($limit)
				{
					$this->limit=intval($limit);
					return $this;
				}
			function Offset($offset)
				{
					$this->offset=intval($offset);
					return $this;
				}
			function OrderByID($asc=true)
				{
					$this->orderby=$this->idfield;
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}
			function OrderByTitle($asc=true)
				{
					$this->orderby=$this->titlefield;
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}
			function NoInitItems()
				{
					$this->inititems=false;
					return $this;
				}
			function AppendWhereCondition($sql)
				{
					$this->sqlappend=$sql;
				}
			function SetFilterIDs($arrayids)
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';
					if (!is_array($arrayids))
						$this->sql .= " 1=2 ";
					else
						$this->sql .= " ".$this->idfield." IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
				}

			function SetFilterExcludeIDs($arrayids)
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';
					if (!is_array($arrayids))
						$this->sql .= " 1=2 ";
					else
						$this->sql .= " ".$this->idfield." NOT IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
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
			function ExtractTitlesArray()
				{
					$r=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							$r[]=$this->items[$i]->Title();
						}
					return $r;
				}
			function SetFilterStrValues($field, $array_values)
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';
					if (!is_array($array_values) || count($array_values)==0)
						$this->sql .= " 1=2 ";
					else
						$this->sql .= " ".$field." IN (".implode(',', Cleaner::ArrayQuotedAndDBEscaped($array_values)).") ";
				}

            function SetFilterIntValuesBetween($field, int $min_value, int $max_value)
                {
                    if (!empty($this->sql))
                        $this->sql .= ' AND ';
                    if (!is_int($min_value) || !is_int($max_value) || !isset( $min_value) || !isset($max_value))
                        $this->sql .= " 1=2 ";
                    else
                        {
                            if ($min_value > $max_value)
                                $this->sql .= " ".$field." BETWEEN ". $max_value ." AND ". $min_value ." ";
                            else
                                $this->sql .= " ".$field." BETWEEN ". $min_value ." AND ". $max_value ." ";
                        }
                }
		}

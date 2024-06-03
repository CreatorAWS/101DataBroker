<?php

	namespace GS\Common\DB;

	class DBQuery
		{
			protected $fields=[];
			protected $values=[];
			protected $operations=[];
			protected $tableprefix;
			protected $tablename;
			protected $noquote;
			protected $iswhere;
			protected $selectfields;
			protected $limit;
			protected $offset;
			protected $orderby;
			protected $groupby;
			protected $leftjoin;
			protected $sqlgenerationmode;
			protected $having;
			public $items=[];
			public $row;
			public $sql;
			private $result;

			function __construct($tablename, $tableprefix='')
				{
					$this->tableprefix=$tableprefix;
					$this->tablename=$tablename;
					$this->sqlgenerationmode=false;
				}

			public static function ForTable($tablename)
				{
					$query=new DBQuery($tablename);
					return $query;
				}

			function SQLGenerationModeOn()
				{
					$this->sqlgenerationmode=true;
					return $this;
				}

			function AddInt($fieldname, $value)
				{
					$this->Add($fieldname, intval($value));
					return $this;
				}

			function AddString($fieldname, $unescaped_value)
				{
					$this->Add($fieldname, dbescape($unescaped_value));
					return $this;
				}

		//Add($expression)
		//Add($fieldname, $value='')
			protected function Add($fieldname, $value=NULL, $operation='=')
				{
					if (func_num_args()==0)
						return $this;
					$fieldname=func_get_arg(0);
					if (func_num_args()>1)
						{
							$value=func_get_arg(1);
							if ($value===NULL)
								$value='';
						}
					if (func_num_args()>2)
						{
							$operation=func_get_arg(2);
							if (!in_array($operation, ['=','<','<=','>','>=','<>']))
								$operation='=';
						}
					$this->fields[count($this->fields)]=$fieldname;
					$this->values[count($this->values)]=$value;
					$this->operations[count($this->operations)]=$operation;
					$this->noquote[count($this->fields)-1]=false;
					$this->iswhere[count($this->fields)-1]=false;
					return $this;
				}

			function AddWhere($fieldname, $value=NULL, $operation='=')
				{
					if (func_num_args()==0)
						return;
					$fieldname=func_get_arg(0);
					if (func_num_args()>1)
						{
							$value=func_get_arg(1);
							if ($value===NULL)
								$value='';
						}
					if (func_num_args()>2)
						{
							$operation=func_get_arg(2);
							if (!in_array($operation, ['=','<','<=','>','>=','<>']))
								$operation='=';
						}
					$this->fields[count($this->fields)]=$fieldname;
					$this->values[count($this->values)]=$value;
					$this->operations[count($this->operations)]=$operation;
					$this->noquote[count($this->fields)-1]=false;
					$this->iswhere[count($this->fields)-1]=true;
					return $this;
				}

			function INStrings($fieldname, $values_array, $trim=false, $operator_type='IN')
				{
					if (is_array($values_array) AND count($values_array)>0)
						{
							$sql='`'.$fieldname.'` '.$operator_type.' (';
							for ($i=0; $i<count($values_array); $i++)
								{
									if ($trim)
										$values_array[$i]=trim($values_array[$i]);
									$sql.=($i>0?',':'')."'".dbescape($values_array[$i])."'";
								}
							$sql.=")";
							$this->AddWhere($sql);
						}
					else
						$this->AddWhere('1=2');
					return $this;
				}

			function INIntegers($fieldname, $values_array, $operator_type='IN')
				{
					if (is_array($values_array) AND count($values_array)>0)
						{
							$sql='`'.$fieldname.'` '.$operator_type.' (';
							for ($i=0; $i<count($values_array); $i++)
								{
									$sql.=($i>0?',':'').intval($values_array[$i]);
								}
							$sql.=")";
							$this->AddWhere($sql);
						}
					else
						$this->AddWhere('1=2');
					return $this;
				}

			function AddNotEmpty($fieldname, $value)
				{
					if (!empty($value))
						$this->Add($fieldname, $value);
					return $this;
				}

			function AddFunction($fieldname, $function)
				{
					$this->Add($fieldname, $function);
					$this->noquote[count($this->fields)-1]=true;
					return $this;
				}

			function AddExpression($fieldname, $expression)
				{
					$this->Add($fieldname, $expression);
					$this->noquote[count($this->fields)-1]=true;
					return $this;
				}

			function AddNumeric($fieldname, $value)
				{
					$this->Add($fieldname, floatval($value));
					$this->noquote[count($this->fields)-1]=true;
					return $this;
				}

			function AddPost($fieldname, $prefix='')
				{
					global $_postvars;
					$this->Add($fieldname, dbescape($_postvars[$prefix.$fieldname]));
					return $this;
				}

			function Insert()
				{
					$sqlf='';
					$sqlv='';
					for ($i=0; $i<count($this->fields); $i++)
						{
							if ($i!=0)
								{
									$sqlf.=', ';
									$sqlv.=', ';
								}
							$sqlf.='`'.$this->fields[$i].'`';
							if ($this->noquote[$i])
								$sqlv.=$this->values[$i];
							else
								$sqlv.='\''.$this->values[$i].'\'';
						}
					$this->sql="INSERT INTO ".$this->tableprefix.$this->tablename." (".$sqlf.") VALUES (".$sqlv.")";
					if (!$this->sqlgenerationmode)
						return insertsql($this->sql);
				}

		//Update($keyfield, $keyvalue)
		//Update($statement)
			function Update()
				{
					if (func_num_args()>0)
						$keyfield=func_get_arg(0);
					if (func_num_args()>1)
						$keyvalue=func_get_arg(1);
					$sql=$this->GetPairs(', ', 'notwhere');
					$this->sql="UPDATE ".$this->tableprefix.$this->tablename." SET ".$sql." WHERE ";
					if (func_num_args()==1)
						$this->sql.=$keyfield;
					elseif (func_num_args()!=0)
						$this->sql.="`".$keyfield."` = '".$keyvalue."'";
					$sql=$this->GetPairs(' AND ', 'where');
					if (!empty($sql))
						{
							if (func_num_args()!=0)
								$this->sql.=' AND ';
							$this->sql.='('.$sql.')';
						}
					elseif (func_num_args()==0)
						return;
					if (!empty($this->limit))
						$this->sql.=' LIMIT '.$this->limit;
					if (!$this->sqlgenerationmode)
						execsql($this->sql);
					return $this;
				}

			function Remove($addsql='')
				{
					$sql=$this->GetPairs();
					if (!empty($sql))
						$this->sql="DELETE FROM ".$this->tableprefix.$this->tablename." WHERE (".$sql.")";
					else
						$this->sql="DELETE FROM ".$this->tableprefix.$this->tablename;
					if (!empty($addsql))
						$this->sql.=' '.$addsql;
					if (!$this->sqlgenerationmode)
						execsql($this->sql);
				}

			private function GetPairs($combine_with=' AND ', $filter='no')
				{
					$sql='';
					for ($i=0; $i<count($this->fields); $i++)
						{
							if ($filter=='where' && !$this->iswhere[$i])
								continue;
							elseif ($filter=='notwhere' && $this->iswhere[$i])
								continue;
							if (!empty($sql))
								{
									$sql.=$combine_with;
								}
							if ($this->values[$i]===NULL)
								$sql.=$this->fields[$i];
							elseif ($this->noquote[$i])
								$sql.='`'.$this->fields[$i].'` = '.$this->values[$i];
							elseif (is_array($this->values[$i]) && count($this->values[$i])>0)
								{
									$list='';
									for ($j=0; $j<count($this->values[$i]); $j++)
										{
											if ($j>0)
												$list.=', ';
											$list.="'".dbescape($this->values[$i][$j])."'";
										}
									$sql.='`'.$this->fields[$i].'` IN ('.$list.')';
								}
							else
								$sql.='`'.$this->fields[$i].'` '.$this->operations[$i].' \''.$this->values[$i].'\'';
						}
					return $sql;
				}

		//Return matches count
			function TotalCount($addsql='')
				{
					$sql=$this->GetPairs(' AND ');
					if (!empty($sql))
						$this->sql="SELECT count(*) FROM ".$this->tableprefix.$this->tablename." WHERE (".$sql.")";
					else
						$this->sql="SELECT count(*) FROM ".$this->tableprefix.$this->tablename;
					if (!empty($addsql))
						$this->sql.=' '.$addsql;
					if (!$this->sqlgenerationmode)
						{
							$r=getsql($this->sql, 'r');
							return intval($r[0]);
						}
				}

			function ChangeValue($fieldname, $value)
				{
					$u=0;
					for ($i=0; $i<count($this->fields); $i++)
						{
							if ($this->fields[$i]==$fieldname)
								{
									$u=1;
									$this->values[$i]=$value;
								}
						}
					if ($u!=1)
						$this->Add($fieldname, $value);
					return $this;
				}

			function Clear($tablename='', $tableprefix='')
				{
					$this->fields=Array();
					$this->values=Array();
					$this->noquote=Array();
					$this->sql='';
					if (!empty($tableprefix))
						{
							$this->tableprefix=$tableprefix;
							$this->tablename=$tablename;
						}
					return $this;
				}

			function Limit($count)
				{
					$this->limit=$count;
					return $this;
				}

			function Offset($count)
				{
					$this->offset=$count;
					return $this;
				}

			function OrderBy($orderbyfileds)
				{
					if (is_array($orderbyfileds))
						$orderbyfileds=implode(', ', $orderbyfileds);
					$this->orderby=$orderbyfileds;
					return $this;
				}

		/**
		 * $fields_array ['field_name'=>'true/false as asc/desc']
		 * @param array $fields_fieldname_boolsort_array
		 * @return $this
		 */
			function OrderByFieldsArray(array $fields_fieldname_boolsort_array)
				{
					$this->orderby='';
					$i=0;
					foreach ($fields_fieldname_boolsort_array as $key=>$val)
						{
							$i++;
							$this->orderby.='`'.$key.'`';
							if ($val!==true)
								$this->orderby.=' DESC';
							if ($i<countarr($fields_fieldname_boolsort_array))
								{
									$this->orderby.=', ';
								}
						}
					return $this;
				}

			function GroupBy($groupbyfileds)
				{
					if (is_array($groupbyfileds))
						$groupbyfileds=implode(', ', $groupbyfileds);
					$this->groupby=$groupbyfileds;
					return $this;
				}

			function LeftJoin($table, $on_statement)
				{
					$this->leftjoin=$table.' ON '.$on_statement;
					return $this;
				}

			function SelectFields($list='*')
				{
					if (is_array($list))
						$list=implode(', ', $list);
					$this->selectfields=$list;
					return $this;
				}

			function Having($sql)
				{
					$this->having=$sql;
					return $this;
				}

			private function SelectStatement($addsql='')
				{
					if (empty($this->selectfields))
						$this->SelectFields();
					$sql=$this->GetPairs(' AND ');
					$this->sql="SELECT ".$this->selectfields." FROM ".$this->tableprefix.$this->tablename;
					if (!empty($this->leftjoin))
						$this->sql.=" LEFT JOIN ".$this->leftjoin;
					if (!empty($sql))
						$this->sql.=" WHERE (".$sql.")";
					if (!empty($addsql))
						{
							if (empty($sql))
								$this->sql.=" WHERE ";
							$this->sql.=' '.$addsql;
						}
					if (!empty($this->groupby))
						$this->sql.=' GROUP BY '.$this->groupby;
					if (!empty($this->having))
						$this->sql.=' HAVING '.$this->having;
					if (!empty($this->orderby))
						$this->sql.=' ORDER BY '.$this->orderby;
					if (!empty($this->limit))
						$this->sql.=' LIMIT '.$this->limit;
					if (!empty($this->offset))
						$this->sql.=' OFFSET '.$this->offset;
				}

			function Select($addsql='', $type='a')
				{
					$this->SelectStatement($addsql);
					if (!$this->sqlgenerationmode)
						{
							$this->items=getsqlarray($this->sql, $type);
						}
					return $this;
				}

			function Open($addsql='')
				{
					$this->SelectStatement($addsql);
					$this->result=execsql($this->sql);
					return $this;
				}

			function Fetch($type='a')
				{
					if ($type=='r')
						$this->row=database_fetch_row($this->result);
					elseif ($type=='o')
						$this->row=database_fetch_object($this->result);
					elseif ($type=='b')
						$this->row=database_fetch_array($this->result);
					else
						$this->row=database_fetch_assoc($this->result);
					return $this->row;
				}

			function Get($addsql='', $type='a')
				{
					$this->Limit(1);
					$this->Select($addsql, $type);
					return $this->items[0] ?? [];
				}

			function GetField($field)
				{
					$this->SelectFields($field);
					$row=$this->Get();
					return $row[$field] ?? '';
				}

			function Count()
				{
					return count($this->items);
				}

			function isEmpty()
				{
					return $this->Count()==0;
				}

			function ColumnValues($column_name)
				{
					$result=[];
					for ($i=0; $i<$this->Count(); $i++)
						{
							$result[]=$this->items[$i][$column_name];
						}
					return $result;
				}
		}


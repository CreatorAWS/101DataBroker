<?php /** @noinspection PhpUnhandledExceptionInspection */

	namespace GS\ORM;

	/*
	 * @method TGenericObject Item($index)
	 * @method TGenericObject|bool Fetch()
	 * @method TGenericObject[] EachItem()
	 * @method TGenericObject|NULL FirstItem()
	 * @method TGenericObject|NULL LastItem()
	 */

	use Cleaner;
	use GS\Common\Legacy\LoadDistinctValuesTrait;

	class GenericList extends SimpleList
		{
			protected $classname;
			protected $itemsinfo=[];
			protected $limit;
			protected $offset;
			protected $inititems=true;
			protected $sqlappend='';
			protected $showallitemsifnofilters=false;
			protected $wasfiletrsapplied=false;
			protected $sql;
			protected $orderby;
			protected $tablename='table';
			protected $idfield='id';
			protected $titlefield='title';
			protected $totalcount=NULL;
			protected $db_result=NULL;
			protected $next_index=0;
			protected $was_load_executed=false;
			protected $was_db_select_processed=false;
			protected $was_items_added_manually=false;
			protected $level_condition_index=[0];
			protected $level_index=0;
			protected $level_concat_confition=['AND'];
			private $sql_generated=NULL;

			use LoadDistinctValuesTrait;

			protected function InitObjectWithData($data)
				{
					$item=new $this->classname($data);
					return $item;
				}
			
			function __construct($items_classname, $tablename, $idfield, $titlefield=NULL)
				{
					if ($titlefield===NULL)
						$titlefield=$idfield;
					$this->classname=$items_classname;
					$this->tablename=$tablename;
					$this->idfield=$idfield;
					$this->titlefield=$titlefield;
					$this->limit=0;
					$this->offset=0;
					$this->OrderByID();
				}

			protected function FinalConditions()
				{}

			protected function GetSQL()
				{
					if ($this->sql_generated===NULL)
						{
							$this->FinalConditions();
							$sql=$this->sql;
							if (!empty($this->sqlappend))
								{
									if (!empty($sql))
										$sql.=' AND ';
									$sql.='('.$this->sqlappend.')';
								}
							$this->wasfiletrsapplied=!empty($sql);
							if (empty($sql))
								{
									if ($this->showallitemsifnofilters)
										$sql=' 1=1';
									else
										$sql=' 1=2';
								}
							$this->sql_generated=" FROM ".$this->tablename." WHERE ".$sql;
						}
					return $this->sql_generated;
				}
			function ShowAllItemsIfNoFilters()
				{
					$this->showallitemsifnofilters=true;
				}
			function WasFiltersApplied()
				{
					return $this->wasfiletrsapplied;
				}
			protected function GetSelectSQL()
				{
					$sql="SELECT * ".$this->GetSQL();
					if (!empty($this->orderby))
						$sql.=' ORDER BY '.$this->orderby;
					if (!empty($this->limit))
						$sql.=' LIMIT '.intval($this->limit);
					if (!empty($this->offset))
						$sql.=' OFFSET '.intval($this->offset);
					return $sql;
				}
			function Load()
				{
					$this->was_load_executed=true;
					$this->was_db_select_processed=true;
					$this->was_items_added_manually=false;
					$this->itemsinfo=getsqlarray($this->GetSelectSQL());
					if ($this->inititems)
						{
							for ($i = 0; $i < count($this->itemsinfo); $i++)
								{
									$this->items[$i] = $this->InitItem($i);
								}
						}
					return $this;
				}
			function Open()
				{
					$this->was_load_executed=false;
					$this->was_db_select_processed=true;
					$this->was_items_added_manually=false;
					$this->db_result=execsql($this->GetSelectSQL());
					return $this;
				}

		/**
		 * @deprecated
		 */
			function EachItemOfLoaded()
				{
					foreach ($this->EachItem() as $item)
						yield $item;
				}

			function EachItem()
				{
					if (!$this->was_db_select_processed && !$this->was_items_added_manually)
						$this->Open();
					if ($this->was_load_executed || $this->was_items_added_manually)
						{
							for ($i=0; $i<$this->Count(); $i++)
								{
									yield $this->Item($i);
								}
						}
					else
						{
							while ($item=$this->Fetch())
								yield $item;
						}
				}
			protected function FetchData()
				{
					$data=database_fetch_assoc($this->db_result);
					return $data;
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
			function AddObjectToList($object)
				{
					$this->was_items_added_manually=true;
					$this->items[]=$object;
					$this->itemsinfo[]=$object->GetRawData();
					return $this;
				}
			function RemoveObjectWithIndex($index)
				{
					if ($index<$this->Count())
						{
							array_splice($this->items, $index, 1);
							array_splice($this->itemsinfo, $index, 1);
						}
					return $this;
				}

			public function RemoveObjectWithID($object_or_id): void
				{
					$index=$this->GetIndexForItemWithID(Cleaner::IntOrObjectID($object_or_id));
					$this->RemoveObjectWithIndex($index);
				}

			protected function InitItem($index)
				{
					$item=$this->InitObjectWithData($this->itemsinfo[$index]);
					return $item;
				}
			function Fetch()
				{
					if ($this->was_load_executed)
						{
							if ($this->next_index+1>$this->Count())
								return false;
							else
								{
									$this->next_index++;
									return $this->Item($this->next_index-1);
								}
						}
					else
						{
							if ($data=$this->FetchData())
								{
									$item=$this->InitObjectWithData($data);
									return $item;
								}
							else
								return false;
						}
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
			protected function OrderByField($field_name, $asc=true)
				{
					$this->orderby='`'.$field_name.'`';
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}
			protected function OrderByFieldAppend($field_name, $asc=true)
				{
					if (strlen($this->orderby)>0)
						$this->orderby.=', ';
					$this->orderby.='`'.$field_name.'`';
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}

			protected function OrderByExpressionAppend(string $unescaped_expression, bool $asc=true): self
				{
					if (strlen($this->orderby)>0)
						$this->orderby.=', ';
					$this->orderby.=$unescaped_expression;
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}

		/**
		 * $fields_array ['field_name'=>'true/false as asc/desc']
		 * @param array $fields_array
		 * @return $this
		 */
			protected function OrderByFields(array $fields_array): self
				{
					$this->NoOrder();
					$i=0;
					foreach ($fields_array as $key=>$val)
						{
							$i++;
							if (Strings::Contains($key, 'if('))
								$this->orderby.=$key;
							else
								$this->orderby.='`'.$key.'`';
							if ($val!==true)
								$this->orderby.=' DESC';
							if ($i<count($fields_array))
								{
									$this->orderby.=', ';
								}
						}
					return $this;
				}

			function OrderByID($asc=true)
				{
					$this->orderby='`'.$this->idfield.'`';
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}
			function OrderByTitle($asc=true)
				{
					$this->orderby='`'.$this->titlefield.'`';
					if (!$asc)
						$this->orderby.=' DESC';
					return $this;
				}
			function NoOrder()
				{
					$this->orderby='';
					return $this;
				}
			protected function AppendWhereCondition($sql)
				{
					$this->sqlappend=$sql;
					return $this;
				}
			protected function SetFilterFieldBetweenIntValues($field, $int_start, $int_end)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."` BETWEEN ".intval($int_start)." AND ".intval($int_end));
					return $this;
				}
			protected function SetFilterCustomCondition($escaped_condition)
				{
					$this->ConcatCondition();
					$this->Condition($escaped_condition);
					return $this;
				}
			protected function SetFilterFieldIntValue($field, $int_value)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`=".intval($int_value));
					return $this;
				}
			protected function SetFilterFieldIntValueGreaterThan($field, $int_value, $include_equal=false)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`>".($include_equal?'=':'').intval($int_value));
					return $this;
				}
			protected function SetFilterFieldIntValueLessThan($field, $int_value, $include_equal=false)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`<".($include_equal?'=':'').intval($int_value));
					return $this;
				}
			protected function SetFilterFieldIntValueExclude($field, $int_value)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`<>".intval($int_value));
					return $this;
				}
			protected function SetFilterFieldFloatValue($field, $float_value)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`=".floatval($float_value));
					return $this;
				}
			protected function SetFilterFieldLike($field, $like_condition)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."` LIKE '".dbescape($like_condition)."'");
					return $this;
				}
			protected function SetFilterFieldNotLike($field, $like_condition)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."` NOT LIKE '".dbescape($like_condition)."'");
					return $this;
				}
			protected function SetFilterFieldStringValue($field, $string_value)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`='".dbescape($string_value)."'");
					return $this;
				}

			protected function SetFilterFieldBetweenStringDateValues(string $field, string $string_start, string $string_end): self
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."` BETWEEN '".dbescape($string_start)."' AND '".dbescape($string_end)."'");
					return $this;
				}
			protected function SetFilterFieldDateStringValueLessThan($field, $string_value, $include_equal=false)
				{
					$this->ConcatCondition();
					$this->Condition("'".dbescape($string_value)."'<".($include_equal?'=':'').'`'.$field.'`');
					return $this;
				}
			protected function SetFilterFieldDateStringValueGreaterThan($field, $string_value, $include_equal=false)
				{
					$this->ConcatCondition();
					$this->Condition("'".dbescape($string_value)."'>".($include_equal?'=':'').'`'.$field.'`');
					return $this;
				}

			protected function SetFilterFieldStringValueExclude($field, $string_value)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."`<>'".dbescape($string_value)."'");
					return $this;
				}
			protected function SetFilterArrayIntValues($field, $array_int_values, $exclude_values=false)
				{
					$this->ConcatCondition();
					if (!is_array($array_int_values) || countarr($array_int_values)==0)
						$this->Condition(" 1=2 ");
					else
						$this->Condition('`'.$field."` ".($exclude_values?'NOT IN':'IN')." (".implode(',', \Cleaner::ArrayIntval($array_int_values)).") ");
					return $this;
				}
			protected function SetFilterArrayFloatValues($field, $array_float_values, $exclude_values=false)
				{
					$this->ConcatCondition();
					if (!is_array($array_float_values) || countarr($array_float_values)==0)
						$this->Condition(" 1=2 ");
					else
						$this->Condition('`'.$field."` ".($exclude_values?'NOT IN':'IN')." (".implode(',', \Cleaner::ArrayFloatval($array_float_values)).") ");
					return $this;
				}
			protected function SetFilterArrayStringValues($field, $array_string_values, $exclude_values=false)
				{
					$this->ConcatCondition();
					if (!is_array($array_string_values) || countarr($array_string_values)==0)
						$this->Condition(" 1=2 ");
					else
						$this->Condition('`'.$field."` ".($exclude_values?'NOT IN':'IN')." (".implode(',', \Cleaner::ArrayQuotedAndDBEscaped($array_string_values)).") ");
					return $this;
				}

			function SetFilterTitle($title): self
				{
					$this->SetFilterFieldStringValue($this->titlefield, $title);
					return $this;
				}

			function SetFilterIDs($arrayids)
				{
					$this->SetFilterArrayIntValues($this->idfield, $arrayids);
					return $this;
				}

			function SetFilterExcludeIDs($arrayids)
				{
					if (is_array($arrayids) && count($arrayids)==0)
						return $this;
					$this->SetFilterArrayIntValues($this->idfield, $arrayids, true);
					return $this;
				}

			function SetFilterIDLessThen($id)
				{
					$this->ConcatCondition();
					$this->Condition($this->idfield.'<'.intval($id));
					return $this;
				}

			function SetFilterIDGreaterThen($id)
				{
					$this->ConcatCondition();
					$this->Condition($this->idfield.'>'.intval($id));
					return $this;
				}

			protected function ANDCondition()
				{
					if ($this->level_condition_index[$this->level_index]>0)
						$this->sql .= ' AND ';
					return $this;
				}
			protected function ORCondition()
				{
					if ($this->level_condition_index[$this->level_index]>0)
						$this->sql .= ' OR ';
					return $this;
				}
			protected function ConcatCondition()
				{
					if (empty($this->level_concat_confition[$this->level_index]))
						throw new \Exception('No concat condition');
					if ($this->level_condition_index[$this->level_index]>0)
						$this->sql .= ' '.$this->level_concat_confition[$this->level_index].' ';
					return $this;
				}

			protected function Condition(string $sql)
				{
					$this->level_condition_index[$this->level_index]++;
					$this->sql .= $sql;
				}
			protected function GroupANDStart()
				{
					$this->GroupStart();
					$this->level_concat_confition[$this->level_index]='AND';
				}
			protected function GroupORStart()
				{
					$this->GroupStart();
					$this->level_concat_confition[$this->level_index]='OR';
				}
			protected function GroupStart()
				{
					$this->ConcatCondition();
					$this->sql .= ' (';
					$this->level_condition_index[$this->level_index]++;
					$this->level_index++;
					$this->level_condition_index[$this->level_index]=0;
					return $this;
				}
			private function CheckGroupCorrection()
				{
					if ($this->level_index<0)
						throw new \Exception('Too much closed groups');
				}
			protected function GroupEnd()
				{
					$this->sql .= ' )';
					unset($this->level_condition_index[$this->level_index]);
					$this->level_index--;
					if ($this->level_index<0)
						$this->CheckGroupCorrection();
					return $this;
				}
			function FilterZeroResults()
				{
					$this->SetFilterIDs([0]);
					return $this;
				}

			protected function SetFilterFieldLikeAny($field, $like_condition)
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field."` LIKE ('%".dbescape($like_condition)."%')");
					return $this;
				}

			protected function SetFilterFieldsNotEqual(string $field1, string $field2): self
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field1.'`<>`'.$field2.'`');
					return $this;
				}

			protected function SetFilterFieldsEqual(string $field1, string $field2): self
				{
					$this->ConcatCondition();
					$this->Condition('`'.$field1.'`=`'.$field2.'`');
					return $this;
				}

		}

<?php

	namespace GS\ORM;

	use GS\Common\DB\DBQuery;

	Class GenericObject
		{
			protected $info;
			protected $id_field;
			protected $title_field;
			protected $table_name;

			function __construct($id_or_cachedinfo, $table_name, $id_field, $title_field=NULL)
				{
					$this->id_field=$id_field;
					$this->table_name=$table_name;
					$this->title_field=$title_field;
					if ($id_or_cachedinfo===NULL)
						$this->info=[];
					elseif (is_array($id_or_cachedinfo))
						{
							$this->info=$id_or_cachedinfo;
							$this->OnLoad();
						}
					else
						{
							if (!$this->isValidIDValueForConstuctor($id_or_cachedinfo))
								$this->info=[];
							else
								$this->LoadData($id_or_cachedinfo);
						}
				}

			protected function isValidIDValueForConstuctor($id_value): bool
				{
					if (intval($id_value)>0)
						return true;
					else
						return false;
				}

			protected function LoadData($id=NULL): void
				{
					if ($id===NULL)
						$id=$this->ID();
					$this->info=DBQuery::ForTable($this->table_name)->AddWhere($this->id_field, intval($id))->Get();
					$this->OnLoad();
				}

			protected function ReloadData(): void
				{
					$this->info=DBQuery::ForTable($this->table_name)->AddWhere($this->id_field, $this->ID())->Get();
					$this->OnLoad();
				}

			function GetRawData(): array
				{
					return $this->info;
				}

			public static function UsingCache($id): self
				{
					global $sm;
					$class_name=static::class;
					$class_name_str='class_'.str_replace('\\', '__', static::class);
					if (!isset($sm['cache'][$class_name_str][$id]) || !is_array($sm['cache'][$class_name_str][$id]))
						{
							/** @var self $object */
							$object=new $class_name($id);
							if ($object->Exists())
								$sm['cache'][$class_name_str][$id]=$object->GetRawData();
						}
					else
						$object=new $class_name($sm['cache'][$class_name_str][$id]);
					return $object;
				}

			function ID(): int
				{
					return $this->FieldIntValue($this->id_field);
				}

			function Title(): string
				{
					return $this->FieldStringValue($this->title_field);
				}

			function SetTitle(string $val): void
				{
					$this->UpdateValues([$this->title_field=>$val]);
				}

			function Exists(): bool
				{
					return !empty($this->info[$this->id_field] ?? 0);
				}

			function UpdateValues(array $params_array)
				{
					unset($params_array['id']);
					if (empty($params_array) || !is_array($params_array))
						return;
					$q=new DBQuery($this->table_name);
					foreach ($params_array as $key=>$val)
						{
							$this->info[$key]=$val;
							$q->AddString($key, $this->info[$key]);
						}
					$q->Update($this->id_field, intval($this->ID()));
					$this->OnUpdateCompleted();
				}

			protected function OnCreate(): void
				{
				}

			protected function OnUpdateCompleted(): void
				{
				}

			protected function OnLoad(): void
				{
				}

			protected static function CreateObjectWithParams(string $tablename, array $params_array)
				{
					$class_name=static::class;
					$sql=new DBQuery($tablename);
					foreach ($params_array as $key=>$val)
						{
							$sql->AddString($key, $val);
						}
					/** @var self $object */
					$object=new $class_name($sql->Insert());
					$object->OnCreate();
					return $object;
				}

			protected static function initWithParams(string $tablename, array $params_array, array $order_fieldname_boolsort_array=[])
				{
					$class_name=static::class;
					$sql=new DBQuery($tablename);
					foreach ($params_array as $key=>$val)
						{
							$sql->AddWhere($key, dbescape($val));
						}
					if (!empty($order_fieldname_boolsort_array))
						{
							$sql->OrderByFieldsArray($order_fieldname_boolsort_array);
						}
					$object=new $class_name($sql->Get());
					return $object;
				}

			static function initNotExistent()
				{
					$class_name=static::class;
					return new $class_name([]);
				}

			protected function OnRemoveBeforeStart()
				{
				}

			function Remove()
				{
					$this->OnRemoveBeforeStart();
					DBQuery::ForTable($this->table_name)->AddWhere($this->id_field, intval($this->ID()))->Remove();
				}

			protected function Increment(string $field_name, int $val=1)
				{
					$sql=dbescape($field_name)."=".dbescape($field_name).'+'.intval($val);
					$sql="UPDATE ".$this->table_name." SET ".$sql." WHERE ".$this->id_field."=".intval($this->ID());
					execsql($sql);
					$this->info[$field_name]=intval($this->info[$field_name])+intval($val);//кешовані дані, які можуть бути неактуальними
				}

			protected function Decrement(string $field_name, int $val=1)
				{
					$this->Increment($field_name, -$val);
				}

			protected function IncrementFloat(string $field_name, float $val=1)
				{
					$sql=dbescape($field_name)."=".dbescape($field_name).'+'.floatval($val);
					$sql="UPDATE ".$this->table_name." SET ".$sql." WHERE ".$this->id_field."=".intval($this->ID());
					execsql($sql);
					$this->info[$field_name]=intval($this->info[$field_name])+floatval($val);//кешовані дані, які можуть бути неактуальними
				}

			protected function DecrementFloat(string $field_name, float $val=1)
				{
					$this->IncrementFloat($field_name, -$val);
				}

			private function FieldRawValue(string $field_name)
				{
					//info[] повинен бути в об’єкті
					return $this->info[$field_name] ?? '';
				}

			protected function FieldFloatValue(string $field_name): float
				{
					return floatval($this->FieldRawValue($field_name));
				}

			protected function FieldIntValue(string $field_name): int
				{
					return intval($this->FieldRawValue($field_name));
				}

			protected function FieldBoolValue(string $field_name): bool
				{
					return $this->FieldIntValue($field_name)>0;
				}

			protected function FieldStringValue(string $field_name): string
				{
					return $this->FieldRawValue($field_name);
				}

			static function initSingleton($id)
				{
					global $sm;
					$class_name=static::class;
					if (empty($sm['singleton'][$class_name][$id]))
						{
							$sm['singleton'][$class_name][$id]=new $class_name($id);
						}
					return $sm['singleton'][$class_name][$id];
				}
		}

<?php

	namespace GS\Common\Legacy;

	use Cleaner;

	trait SetFilterField
		{
			protected function SetFilterArrayIntValues(string $field, array $array_int_values, bool $exclude_values=false): void
				{
					$array_int_values=Cleaner::ArrayIntval($array_int_values);
					if (!empty($this->sql))
						$this->sql.=' AND ';
					if (count($array_int_values)>0)
						$this->sql.=" ".$field." ".($exclude_values?'NOT ':'')."IN (".implode(', ', $array_int_values).")";
					else
						$this->sql.=" 1=2 ";
				}

			protected function SetFilterArrayStringValues(string $field, array $array_string_values, bool $exclude_values=false): void
				{
					$array_string_values=Cleaner::ArrayQuotedAndDBEscaped($array_string_values);
					if (!empty($this->sql))
						$this->sql.=' AND ';
					if (count($array_string_values)>0)
						$this->sql.=" ".$field." ".($exclude_values?'NOT ':'')."IN (".implode(', ', $array_string_values).")";
					else
						$this->sql.=" 1=2 ";
				}

			protected function SetFilterFieldIntValue(string $field, int $int_value): void
				{
					if (!empty($this->sql))
						$this->sql.=' AND ';
					$this->sql.=" ".$field."=".intval($int_value);
				}

		}

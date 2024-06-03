<?php

	namespace GS\Common\Legacy;

	trait LoadDistinctValuesTrait
		{

			protected function LoadDistinctValues(string $field_name): array
				{
					$sql = "SELECT DISTINCT ".$field_name." ".$this->GetSQL();
					$r=[];
					$result=execsql($sql);
					while ($row=database_fetch_assoc($result))
						{
							$r[]=$row[$field_name];
						}
					return $r;
				}

		}

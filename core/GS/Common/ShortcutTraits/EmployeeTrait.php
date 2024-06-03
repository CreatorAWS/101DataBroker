<?php

	namespace GS\Common\ShortcutTraits;

	use TEmployee;

	trait EmployeeTrait
		{
			private $employee_cached_object=NULL;

			abstract public function EmployeeID(): int;

			public function HasEmployee(): bool
				{
					return $this->EmployeeID()>0;
				}

			public function Employee(): TEmployee
				{
					if ($this->employee_cached_object===NULL)
						{
							$this->employee_cached_object=TEmployee::usingCache($this->EmployeeID());
						}
					return $this->employee_cached_object;
				}

		}

<?php

	namespace GS\Company\Employees\Decorator;

	/* Для вказання типів методів вставити наступні значення в DocBlock нащадків
	 * @method static self Init($employee)
	 */

	use TEmployee;

	class EmployeeDecoratorPrototype
		{
			private $employee;

			public function __construct(TEmployee $employee)
				{
					$this->employee=$employee;
				}

			public function Employee(): TEmployee
				{
					return  $this->employee;
				}

			public static function Init(TEmployee $employee)
				{
					return new static($employee);
				}

			protected function GetMetaData(string $key): string
				{
					return $this->Employee()->GetMetaData($key);
				}

			protected function HasMetaData(string $key): bool
				{
					return $this->Employee()->HasMetaData($key);
				}
			
			protected function SetMetaData(string $key, ?string $value): void
				{
					$this->Employee()->SetMetaData($key, $value);
				}

		}

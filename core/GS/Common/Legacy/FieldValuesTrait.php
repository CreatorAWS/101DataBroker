<?php

	namespace GS\Common\Legacy;

	trait FieldValuesTrait
		{
			private function FieldRawValue(string $field_name)
				{
					//info[] reqired
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
		}

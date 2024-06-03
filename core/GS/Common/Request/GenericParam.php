<?php

	namespace GS\Common\Request;

	use Cleaner;
	use GS\Common\Strings;

	class GenericParam
		{

			private $value;

			function __construct(&$value)
				{
					$this->value=&$value;
				}

			protected function RawValue()
				{
					return $this->value ?? NULL;
				}

			function AsString(): string
				{
					return (string)$this->RawValue();
				}

			function AsBool(): bool
				{
					return (bool)$this->RawValue();
				}

			function ProtectedCleanedString(): string
				{
					return Cleaner::CleanWebStr($this->AsString());
				}

			function UrlencodedString(): string
				{
					return urlencode($this->AsString());
				}

			function EscapedString(): string
				{
					return Cleaner::HTMLEscape($this->AsString());
				}

			function isStringEqual(string $string_to_compare): bool
				{
					return Strings::isEqual($this->AsString(), $string_to_compare);
				}

			function AsFloat(): float
				{
					return floatval($this->RawValue());
				}

			function AsFloatPriceCleaned(): float
				{
					return Cleaner::PriceFloat($this->AsString());
				}

			function AsFloatCleaned(): float
				{
					return Cleaner::Float($this->AsString());
				}

			function AsInt(): int
				{
					return intval($this->RawValue());
				}

			function AsAbsInt(): int
				{
					return abs($this->AsInt());
				}

			function AsArray(): array
				{
					if ($this->RawValue()===NULL)
						return [];
					elseif (!is_array($this->RawValue()))
						return [$this->RawValue()];
					else
						return $this->RawValue();
				}

			function SetValue($new_value): void
				{
					$this->value=$new_value;
				}

			function isEmpty(): bool
				{
					return empty($this->RawValue());
				}

		}

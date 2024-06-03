<?php

	namespace GS\Support;

	class SupportPriorityReadable
		{

			public const LOW = 'low';
			public const MEDIUM = 'medium';
			public const HIGH = 'high';

			public static function ReadableTagForSupportPriority(string $support_priority_value): string
				{
					if ($support_priority_value===SupportPriority::LOW)
						return self::LOW;
					elseif ($support_priority_value===SupportPriority::MEDIUM)
						return self::MEDIUM;
					elseif ($support_priority_value===SupportPriority::HIGH)
						return self::HIGH;
					else
						return '';
				}

			public static function ValueForSupportPriorityTag(string $support_priority_value): int
				{
					if ($support_priority_value===self::LOW)
						return 0;
					elseif ($support_priority_value===self::MEDIUM)
						return 1;
					elseif ($support_priority_value===self::HIGH)
						return 2;
					else
						return 0;
				}

			public static function ListAvailable(): array
				{
					return [
						self::LOW,
						self::MEDIUM,
						self::HIGH,
					];
				}
		}

<?php

	namespace GS\Support;

	class SupportPriority
		{

			public const LOW = '0';
			public const MEDIUM = '1';
			public const HIGH = '2';


			public static function ListAvailable(): array
				{
					return [
						self::LOW,
						self::MEDIUM,
						self::HIGH,
					];
				}
		}

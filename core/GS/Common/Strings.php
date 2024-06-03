<?php

	namespace GS\Common;

	class Strings
		{
			public static function isEqualCaseInsensitive(string $str1, string $str2): bool
				{
					return strcmp(strtolower($str1), strtolower($str2))==0;
				}

			public static function isEqual(?string $str1, ?string $str2): bool
				{
					return strcmp((string)$str1, (string)$str2)==0;
				}

			public static function isBeginningEquals(string $beginning, string $full_string): bool
				{
					if (strlen($full_string)<strlen($beginning))
						return false;
					else
						return Strings::isEqual($beginning, substr($full_string, 0, strlen($beginning)));
				}

			public static function isEndingEquals(string $ending, string $full_string): bool
				{
					if (strlen($full_string)<strlen($ending))
						return false;
					else
						return Strings::isEqual($ending, substr($full_string, -strlen($ending)));
				}

			public static function RemoveEnding(string $ending, string $full_string): string
				{
					if (!Strings::isEndingEquals($ending, $full_string))
						return $full_string;
					else
						return substr($full_string, 0, -strlen($ending));
				}

			public static function Contains(?string $haystack, ?string $needle, $start_position=0): bool
				{
					return strpos((string)$haystack, (string)$needle, $start_position)!==false;
				}

			public static function isEmpty(string $string): bool
				{
					return strlen($string)==0;
				}

			public static function Replace(string $search, string $replace, string $string): string
				{
					return str_replace($search, $replace, $string);
				}

			public static function GetTextBetween(string $search, string $start_string, string $end_string): string
				{
					$start_position=strpos($search, $start_string);
					if ($start_position===false)
						return '';
					$start_position+=strlen($start_string);
					$end_position=strpos($search, $end_string, $start_position);
					if ($end_position===false)
						return '';
					return substr($search, $start_position, $end_position-$start_position);
				}

			public static function NumericSymbolsCount(string $str): int
				{
					$count=0;
					foreach (str_split($str) as $character)
						{
							if (is_numeric($character))
								$count++;
						}
					return $count;
				}

			public static function NonNumericSymbolsCount(string $str): int
				{
					return strlen($str)-self::NumericSymbolsCount($str);
				}

			public static function Uppercase(string $str): string
				{
					return mb_convert_case($str, MB_CASE_UPPER, 'utf8');
				}

			public static function Lowercase(string $str): string
				{
					return mb_convert_case($str, MB_CASE_LOWER, 'utf8');
				}
		}

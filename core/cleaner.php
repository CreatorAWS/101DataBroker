<?php

	class Cleaner
		{
			public static function RemoveEmoji($string)
				{
					// Match Enclosed Alphanumeric Supplement
					$regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
					$clear_string = preg_replace($regex_alphanumeric, '', $string);

					// Match Miscellaneous Symbols and Pictographs
					$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
					$clear_string = preg_replace($regex_symbols, '', $clear_string);

					// Match Emoticons
					$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
					$clear_string = preg_replace($regex_emoticons, '', $clear_string);

					// Match Transport And Map Symbols
					$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
					$clear_string = preg_replace($regex_transport, '', $clear_string);

					// Match Supplemental Symbols and Pictographs
					$regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
					$clear_string = preg_replace($regex_supplemental, '', $clear_string);

					// Match Miscellaneous Symbols
					$regex_misc = '/[\x{2600}-\x{26FF}]/u';
					$clear_string = preg_replace($regex_misc, '', $clear_string);

					// Match Dingbats
					$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
					$clear_string = preg_replace($regex_dingbats, '', $clear_string);

					$regex_dingbats = '/[\x{1F44C}-\x{1FAF8}]/u';
					$clear_string = preg_replace($regex_dingbats, '', $clear_string);

					return $clear_string;
				}

			public static function DomainName($url)
				{
					$host = parse_url($url, PHP_URL_HOST);
					$host_parts = explode(".", $host);
					$domain = $host_parts[count($host_parts)-2] . "." . $host_parts[count($host_parts)-1];
					return $domain;
				}

			public static function Phone($str)
				{
					$allowed='0123456789';
					$result='';
					for ($i=0; $i<strlen($str); $i++)
						{
							$c=substr($str, $i, 1);
							if (strpos($allowed, $c)!==false)
								$result.=$c;
						}
					return $result;
				}
			public static function USPhone($str)
				{
					$result=Cleaner::Phone($str);
					if (strlen($result)==10)
						$result='1'.$result;
					return $result;
				}
			public static function USPhone10($str)
				{
					$result=Cleaner::USPhone($str);
					if (strlen($result)==11)
						$result=substr($result, 1);
					return $result;
				}
			public static function Float($str)
				{
					$str=str_replace('/', '.', $str);
					$str=str_replace('?', '.', $str);
					$str=str_replace('>', '.', $str);
					$str=str_replace('<', '.', $str);
					$allowed='0123456789.-+';
					$result='';
					for ($i=0; $i<strlen($str); $i++)
						{
							$c=substr($str, $i, 1);
							if (strpos($allowed, $c)!==false)
								$result.=$c;
						}
					return floatval($result);
				}
			public static function ArrayIntval($array)
				{
					if (is_array($array))
						{
							foreach ($array as &$val)
								$val=intval($val);
							return $array;
						}
					else
						return Array();
				}
			public static function ArrayQuotedAndDBEscaped($array)
				{
					if (is_array($array))
						{
							foreach ($array as &$val)
								$val="'".dbescape($val)."'";
							return $array;
						}
					else
						return Array();
				}
			public static function ArrayUniqueValues($array)
				{
					if (is_array($array))
						{
							return array_values(array_unique($array));
						}
					else
						return Array();
				}
			public static function ArrayNotEmptyValues($array)
				{
					if (is_array($array))
						{
							$result=Array();
							foreach ($array as $key=>$val)
								if (!empty($val))
									$result[$key]=$val;
							return $result;
						}
					else
						return Array();
				}
			public static function SplitLongWords($text, $maxcharacters=50)
				{
					$text=explode(' ', $text);
					for ($i = 0; $i<count($text); $i++)
						{
							if (strlen($text[$i])>$maxcharacters)
								$text[$i]=implode(' ', str_split($text[$i], $maxcharacters));
						}
					return implode(' ', $text);
				}
			public static function MaskWithAsterisks($str)
				{
					$r=substr($str, 0, 1);
					for ($i = 1; $i < strlen($str)-2; $i++)
						{
							if (substr($str, $i, 1)==' ')
								$r.=' ';
							else
								$r.='*';
						}
					$r.=substr($str, -1);
					return $r;
				}
			public static function IntObjectID($object_or_int)
				{
					if (is_object($object_or_int))
						$object_or_int=$object_or_int->ID();
					return intval($object_or_int);
				}

			public static function FloatMoney($str)
				{
					return round(Cleaner::Float($str), 2);
				}
		}

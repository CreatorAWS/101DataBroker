<?php

	class Cleaner
		{
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
		}

?>
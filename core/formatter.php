<?php

	class Formatter
		{
			public static function Money($float)
				{
					return number_format($float, 2, '.', '');
				}
			public static function MoneyFine($float)
				{
					return number_format($float, 2, '.', ',');
				}
			public static function FloatFine($float)
				{
					return number_format($float, 2, '.', ' ');
				}
			public static function IntFine($float)
				{
					return number_format($float, 0, '.', ',');
				}
			public static function Date($unixtimestamp)
				{
					global $lang;
					return strftime('%m/%d/%Y', $unixtimestamp);
				}
			public static function DateTime($unixtimestamp)
				{
					global $lang;
					return strftime('%b %d, %Y %I:%M %p', $unixtimestamp);
				}
			public static function Time($unixtimestamp)
				{
					global $lang;
					return strftime('%I:%M %p', $unixtimestamp);
				}

			public static function Phone( $phone_number)
				{
					$phone_number = Cleaner::Phone($phone_number);

					switch(strlen($phone_number)) {
						case 10:
								$format = "+1 xxx-xxx-xxxx";
								break;
						case 11:
								$format = "+x xxx-xxx-xxxx";
								break;
						case 12:
								$format = "+xx xxxx-xxxxx";
								break;
						case 13:
								$format = "+xx xxx xxx-xxxx";
								break;
						default:
								return null; // Invalid phone number length
						}

					$formatted_phone_number = "";
					$format_index = 0;
					for($i = 0; $i < strlen($format); $i++)
						{
							if($format[$i] == "x")
								{
									$formatted_phone_number .= $phone_number[$format_index];
									$format_index++;
								}
							else
								$formatted_phone_number .= $format[$i];
						}

					return $formatted_phone_number;
				}

			public static function USPhone($phone)
				{
					use_api('cleaner');
					$phone=Cleaner::USPhone($phone);
					if (strlen($phone)==11)
						$phone=substr($phone, 1);
					return '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6);
				}
			public static function OrdinalNumber($number, $divider='')
				{
					$last=substr($number, -1);
					if (intval($last)==1)
						return $number.$divider.'st';
					elseif (intval($last)==2)
						return $number.$divider.'nd';
					elseif (intval($last)==3)
						return $number.$divider.'rd';
					else
						return $number.$divider.'th';
				}

			public static function DurationDHM($time_seconds, $days_suffix='d', $hours_suffix='h', $minutes_suffix='m', $divider=' ')
				{
					$days=floor($time_seconds/(24*3600));
					$time_seconds-=$days*24*3600;
					$hours=floor($time_seconds/3600);
					$time_seconds-=$hours*3600;
					$mins=floor($time_seconds/60);
					if ($mins==0)
						$mins=1;
					$time_seconds-=$mins*60;
					$str='';
					$str.=$days.$days_suffix.$divider;
					$str.=$hours.$hours_suffix.$divider;
					$str.=$mins.$minutes_suffix;
					return $str;
				}
			public static function DurationDH($time_seconds, $days_suffix='d', $hours_suffix='h', $divider=' ')
				{
					$days=floor($time_seconds/(24*3600));
					$time_seconds-=$days*24*3600;
					$hours=floor($time_seconds/3600);
					if ($hours==0)
						$hours=1;
					$time_seconds-=$hours*3600;
					$str='';
					$str.=$days.$days_suffix.$divider;
					$str.=$hours.$hours_suffix;
					return $str;
				}
			public static function DurationDHMSWithoutZeros($time_seconds, $days_suffix='d', $hours_suffix='h', $minutes_suffix='m', $seconds_suffix='s', $divider=' ')
				{
					$days=floor($time_seconds/(24*3600));
					$time_seconds-=$days*24*3600;
					$hours=floor($time_seconds/3600);
					$time_seconds-=$hours*3600;
					$mins=floor($time_seconds/60);
					$time_seconds-=$mins*60;
					$str='';
					if ($days > 0)
						$str.=$days.$days_suffix.$divider;
					if ($hours > 0)
						$str.=$hours.$hours_suffix.$divider;
					if ($mins > 0)
						$str.=$mins.$minutes_suffix.$divider;
					$str.=$time_seconds.$seconds_suffix;
					return $str;
				}
		}


<?php

	use_api('cleaner');
	class Validator
		{
			public static function ValidateEmail($str)
				{
					$towerdata_apikey = sm_settings('towerdata_ApiKey');
					$api = new TowerDataApi($towerdata_apikey);
					try
						{
							$response = $api -> query_by_email($str, $hash_email = false);
							return ($response['email_validation']['status']=='valid');
						}
					catch (\Exception $e) {

					}
				}
			public static function Email($str)
				{
					return is_email($str);
				}
			public static function USPhone($str)
				{
					$phone=Cleaner::Phone($str);
					if (strlen($phone)!=11 && strlen($phone)!=10)
						return false;
					if (strlen($phone)==11 && substr($phone, 0, 1)!=='1')
						return false;
					return true;
				}

			public static function isPhone(string $string): bool
				{
					$phone = Cleaner::Phone($string);
					if ((strlen($phone) == 12 && substr($phone, 0, 2) == '61'))
						return true;
					if (((strlen($phone) == 11 || strlen($phone) == 10) && substr($phone, 0, 2) == '64'))
						return true;
					if ((strlen($phone) == 13 && substr($phone, 0, 2) == '55'))
						return true;
					if ((strlen($phone) == 12 && substr($phone, 0, 2) == '54'))
						return true;
					if ((strlen($phone) == 12 && substr($phone, 0, 2) == '33'))
						return true;
					if ((strlen($phone) == 12 && substr($phone, 0, 2) == '44'))
						return true;
					if ((strlen($phone) == 12 && substr($phone, 0, 2) == '49'))
						return true;
					if ((strlen($phone) == 11 && substr($phone, 0, 1) == '1'))
						return true;

					return false;
				}

			public static function URL($url)
				{
					return preg_match("#((http|https|ftp)://(\\S*?\\.\\S*?))(\\s|\\;|\\)|\\]|\\[|\\{|\\}|,|\"|'|:|\\<|$|\\.\\s)#ie", $url);
				}
			public static function ImageExtension($filename)
				{
					return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
						Array(
							'jpg', 'jpeg', 'gif', 'png'
						)
					);
				}
			public static function NotEmptyArrayKeys($array, $keys_array)
				{
					if (is_array($keys_array) && is_array($array))
						{
							foreach ($keys_array as $key)
								if (empty($array[$key]))
									return false;
							return true;
						}
					else
						return false;
				}
		}

?>
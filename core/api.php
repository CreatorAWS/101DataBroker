<?php

	function use_api($library)
		{
			global $sm;
			$file=dirname(__FILE__).'/'.$library.'.php';
			if (file_exists($file))
				include_once($file);
		}
	use_api('globalsettings');
	use_api('tcompany');
	
	function is_debug_environment()
		{
			return (strcmp(substr(__FILE__, 0, 5), '/www/')==0);
		}

	function main_domain()
		{
			$string = getsqlfield("SELECT value_settings FROM sm_settings WHERE name_settings = 'resource_url' AND mode='default' LIMIT 1");
			if(substr($string, -1) == '/')
				{
					$string = substr($string, 0, -1);
				}
			return $string;
		}

	function resource_name()
		{
			$string = getsqlfield("SELECT value_settings FROM sm_settings WHERE name_settings = 'resource_title' AND mode='default' LIMIT 1");
			return $string;
		}

	function mail_domain()
		{
			$string = sm_get_settings('maildomain');
			if (empty($string))
				{
					$string = getsqlfield("SELECT value_settings FROM sm_settings WHERE name_settings = 'resource_url' AND mode='default' LIMIT 1");
					if(substr($string, -1) == '/')
						{
							$string = substr($string, 0, -1);
						}
					return $string;
				}
			else
				return $string;
		}

	function frontend_domain()
		{
			$string = sm_get_settings('frontend_domain');

			return $string;
		}

	function image_domain()
		{
			return main_domain();
		}

	function rd_api_class_loader($classname)
		{
			global $sm;
			if (strcmp(substr($classname, 0, 3), 'GS\\')===0)
				include_once(dirname(__FILE__).'/'.str_replace('\\', '/', $classname).'.php');
			elseif (strcmp(substr($classname, 0, 13), 'APIV1Modules\\')===0)
				{
					include_once(dirname(__FILE__, 2).'/httpdocs/api/v1/'.str_replace('\\', '/', substr($classname, 13)).'.php');
				}
			else
				{
					$classname=strtolower($classname);
					use_api($classname);
				}
		}

	function HasGooglePlacesAPIKey()
		{
			if (TCompany::CurrentCompany()->HasGoogleApiKey())
				return true;
			else
				return !empty(GooglePlacesAPIKey());
		}

	function GooglePlacesAPIKey()
		{
			if (TCompany::CurrentCompany()->HasGoogleApiKey())
				return TCompany::CurrentCompany()->GoogleApiKey();

			return sm_get_settings('google_places_api_key');
		}

	function url_validator($url)
		{
			$redirectionlink = strtolower($url);
			$redirectionlink = str_replace( 'http://', '', $redirectionlink );
			$redirectionlink = str_replace( 'https://', '', $redirectionlink );
			if(strpos($redirectionlink, '/'))
				$redirectionlink = substr($redirectionlink, 0, strpos($redirectionlink, '/'));

			$pattern = '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,30}$/i';
			if (!preg_match($pattern, $redirectionlink))
				{
					$error_message = $url. ' is not a valid URL';
				}
			return $error_message;
		}

	function url_cleaner($url)
		{
			$redirectionlink = strtolower($url);
			$redirectionlink = str_replace( 'http://', '', $redirectionlink );
			$redirectionlink = str_replace( 'https://', '', $redirectionlink );


			return $redirectionlink;
		}

	function BuiltWithAPIKey()
		{
			if ( TCompany::CurrentCompany()->HasBuiltWithApiKey() )
				return TCompany::CurrentCompany()->BuiltWithApiKey();

			return sm_settings('builtwith_api_key');
		}


	function SearchSectionAvailableForCompanies()
		{
			return sm_settings('search_section_for_companies') == 1;
		}


	spl_autoload_register('rd_api_class_loader');


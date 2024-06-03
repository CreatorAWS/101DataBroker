<?php

	if (!defined("system_DEFINED"))
		{
			class System
				{
					/**
					 * @return TEmployee
					 */
					public static function MyAccount()
						{
							global $myaccount;
							return $myaccount;
						}

					/**
					 * @return TCompany
					 */
					public static function MyCompany()
						{
							return TCompany::CurrentCompany();
						}

					public static function HasBuiltWithInstalled()
						{
							if ( !TCompany::CurrentCompany()->isBuiltWithApiEnabled() )
								return false;
							elseif ( !TCompany::CurrentCompany()->isBuiltWithApiEnabled() )
								return false;
							if ( TCompany::CurrentCompany()->HasBuiltWithApiKey() )
								return true;
							elseif ( !empty(sm_settings('builtwith_api_key')) )
								return true;
							else
								return false;
						}

					public static function HasSicCodesSearchInstalled()
						{
							if (TCompany::CurrentCompany()->SicCodesSearchEnabled())
								return true;
							else
								return false;
						}
					public static function HasStatesSearchInstalled()
						{
							if (TCompany::CurrentCompany()->StatesSearchEnabled())
								return true;
							else
								return false;
						}
					public static function HasGoogleSearchInstalled()
						{
							if ( !TCompany::CurrentCompany()->isGoogleApiEnabled() )
								return false;
							elseif ( TCompany::CurrentCompany()->HasGoogleApiKey() )
								return true;
							elseif ( !empty(sm_settings('google_places_api_key')) )
								return true;
							else
								return false;
						}

					public static function InitMyAccount()
						{
							global $sm, $myaccount;
							$myaccount = new TEmployee($sm['u']['info']['employee_id']);
							if (!$myaccount->Exists())
								{
									exit('Error EU-214876');
								}
						}

					public static function LoggedIn()
						{
							/** @var $myaccount TEmployee */
							global $myaccount;
							return is_object($myaccount) && $myaccount->Exists();
						}
				}

			define("system_DEFINED", 1);
		}

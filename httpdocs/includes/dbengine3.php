<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.13
	//#revision 2017-01-16
	//==============================================================================

	function database_get_fn_name($fn)
		{
			if (strcmp($fn, 'rand') == 0)
				return 'rand';
			else
				return $fn;
		}

	function database_connect($host, $user, $password, $database = 'siman')
		{
			global $serverDB, $lnkDB;
			$lnkDB = mysqli_connect($host, $user, $password);
			if (!mysqli_select_db($lnkDB, $database))
				{
					return false;
				}
			return $lnkDB;
		}

	function database_query($sql, $lnkDB)
		{
			global $special, $_settings, $lnkDB;
			if ($_settings['show_script_info'] == 'on')
				{
					$timestart=microtime(true);
					$special['sql']['count']++;
					$special['sql']['last_query']=$sql;
					$special['sql']['queries'][count($special['sql']['queries'])]=$sql;
				}
			$r = mysqli_query($lnkDB, $sql);
			if ($_settings['show_script_info'] == 'on')
				{
					$special['sql']['time'][count($special['sql']['time'])]=microtime(true)-$timestart;
				}
			if (!$r)
				{
					if ($_settings['show_script_info'] == 'on')
						print('<hr />'.mysqli_error($lnkDB).'<br /> ====&gt;<br />'.$sql.'<hr />');
				}
			return $r;
		}

	function database_fetch_object($result)
		{
			return mysqli_fetch_object($result);
		}

	function database_fetch_row($result)
		{
			return mysqli_fetch_row($result);
		}

	function database_fetch_array($result)
		{
			return mysqli_fetch_array($result);
		}

	function database_fetch_assoc($result)
		{
			return mysqli_fetch_assoc($result);
		}

	function database_insert_id($tbl, $nameDB, $lnkDB)
		{
			return mysqli_insert_id($lnkDB);
		}

	function database_real_escape_string($unescaped_string, $lnkDB)
		{
			return mysqli_real_escape_string($lnkDB, $unescaped_string);
		}


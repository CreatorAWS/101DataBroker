<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.5
	//#revision 2013-10-07
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
			$lnkDB = mysql_connect($host, $user, $password);
			if (!mysql_select_db($database, $lnkDB))
				{
					return false;
				}
			return $lnkDB;
		}

	function database_query($sql, $lnkDB)
		{
			return database_db_query('', $sql, $lnkDB);
		}

	function database_db_query($nameDB /*DEPRECATED*/, $sql, $lnkDB)
		{
			global $special, $_settings;
			if ($_settings['show_script_info'] == 'on')
				{
					$special['sql']['count']++;
					$special['sql']['last_query']=$sql;
					$special['sql']['queries'][count($special['sql']['queries'])]=$sql;
				}
			$r = mysql_query($sql);
			if (!$r)
				{
					if ($_settings['show_script_info'] == 'on')
						print('<hr />'.mysql_error($lnkDB).'<br /> ====&gt;<br />'.$sql.'<hr />');
				}
			return $r;
		}

	function database_fetch_object($result)
		{
			return mysql_fetch_object($result);
		}

	function database_fetch_row($result)
		{
			return mysql_fetch_row($result);
		}

	function database_fetch_array($result)
		{
			return mysql_fetch_array($result);
		}

	function database_fetch_assoc($result)
		{
			return mysql_fetch_assoc($result);
		}

	function database_insert_id($tbl, $nameDB, $lnkDB)
		{
			return mysql_insert_id($lnkDB);
		}

	function database_real_escape_string($unescaped_string, $lnkDB)
		{
			return mysql_real_escape_string($unescaped_string, $lnkDB);
		}


?>
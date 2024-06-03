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
				return 'random';
			else
				return $fn;
		}

	function database_connect($host, $user, $password, $database = 'siman')
		{
			global $serverDB, $lnkDB;
			if (empty($host))
				$host='.';
			$lnkDB = new  SQLite3($host.'/'.$database, SQLITE3_OPEN_READWRITE);
			if (!empty($error))
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
			$r = $lnkDB->query($sql);
			if (!$r)
				{
					if ($_settings['show_script_info'] == 'on')
						print('<hr />'.$lnkDB->lastErrorMsg().'<br /> ====&gt;<br />'.$sql.'<hr />');
				}
			return $r;
		}

	function database_fetch_object($result)
		{
			if (!class_exists('DATABASEFieldObject'))
				{
					class DATABASEFieldObject
						{
							function SetField($f, $v)
								{
									$this->$f=$v;
								}
						}
				}
			if (!$row=database_fetch_assoc($result))
				return false;
			$data=new DATABASEFieldObject();
			if (is_array($row))
				{
					foreach ($row as $key=>$val)
						{
							$data->SetField($key, $val);
						}
				}
			return $data;
		}

	function database_fetch_row($result)
		{
			return $result->fetchArray(SQLITE3_NUM);
		}


	function database_fetch_array($result)
		{
			return $result->fetchArray(SQLITE3_BOTH);
		}

	function database_fetch_assoc($result)
		{
			return $result->fetchArray(SQLITE3_ASSOC);
		}

	function database_insert_id($tbl, $nameDB, $lnkDB)
		{
			return $lnkDB->lastInsertRowID();
		}

	function database_real_escape_string($unescaped_string, $lnkDB)
		{
			return $lnkDB->escapeString($unescaped_string);
		}

?>
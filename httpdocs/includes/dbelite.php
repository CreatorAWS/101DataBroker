<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.5
	//#revision 2013-10-07
	//==============================================================================

	function execsql($sql)
		{
			global $lnkDB;
			return database_query($sql, $lnkDB);
		}

	function insertsql($sql)
		{
			global $nameDB, $lnkDB;
			database_query($sql, $lnkDB);
			return database_insert_id('', $nameDB, $lnkDB);
		}

	function deletesql($tablename, $idname, $idval)
		{
			execsql("DELETE FROM $tablename WHERE `$idname` = '".dbescape($idval)."'");
		}

	function getsql($sql, $type = 'a')
		{
			$result = execsql($sql);
			if ($type == 'r')
				return database_fetch_row($result);
			elseif ($type == 'o')
				return database_fetch_object($result);
			elseif ($type == 'b')
				return database_fetch_array($result);
			else
				return database_fetch_assoc($result);
		}

	function getsqlarray($sql, $type = 'a')
		{
			$result = execsql($sql);
			$i = 0;
			$r = Array();
			if ($type == 'r')
				{
					while ($row = database_fetch_row($result))
						{
							$r[$i] = $row;
							$i++;
						}
				}
			elseif ($type == 'o')
				{
					while ($row = database_fetch_object($result))
						{
							$r[$i] = $row;
							$i++;
						}
				}
			elseif ($type == 'b')
				{
					while ($row = database_fetch_array($result))
						{
							$r[$i] = $row;
							$i++;
						}
				}
			else
				{
					while ($row = database_fetch_assoc($result))
						{
							$r[$i] = $row;
							$i++;
						}
				}
			return $r;
		}

	function getsqlfield($sql)
		{
			$result = getsql($sql, 'r');
			return $result[0];
		}

	function dbescape($unescaped_string)
		{
			global $lnkDB;
			return database_real_escape_string($unescaped_string, $lnkDB);
		}

?>
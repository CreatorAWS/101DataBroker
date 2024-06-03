<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.11
	//#revision 2016-05-06
	//==============================================================================

	require_once("includes/dbsettings.php");
	require_once("includes/dbengine".$serverDB.".php");
	require_once("includes/dbelite.php");

	$url = '';

	$lnkDB = database_connect($hostNameDB, $userNameDB, $userPasswordDB, $nameDB);
	if ($lnkDB != false)
		{
			if (!empty($initialStatementDB))
				$result = database_query($initialStatementDB, $lnkDB);
			$replaced = 0;
			if (strcmp($_GET['rewrittenquery'], 'robots.txt') == 0)
				{
					@header('Content-type: text/plain; charset=utf-8');
					print(@getsqlfield("SELECT value_settings FROM ".$tableprefix."settings WHERE name_settings='robots_txt' AND `mode`='seo'"));
					exit();
				}
			if (substr($_GET['rewrittenquery'], -1) == '/')
				$_GET['rewrittenquery'] = substr($_GET['rewrittenquery'], 0, -1);
			$tmp=dbescape($_GET['rewrittenquery']);
			$url = @getsqlfield("SELECT url_fs FROM ".$tableprefix."filesystem WHERE `filename_fs`='".$tmp."' OR `filename_fs`='".$tmp."/' LIMIT 1");
		}

	if (empty($url))
		$url = 'index.php?m=404';

	$query = substr($url, 10);

	$options = explode('&', $query);

	for ($i = 0; $i < count($options); $i++)
		{
			$tmp = explode('=', $options[$i]);
			$_GET[$tmp[0]] = $tmp[1];
		}

	require(dirname(__FILE__).'/index.php');

?>
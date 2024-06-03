<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-07-16
	//==============================================================================

	if (!empty($siman_cache) && file_exists('files/temp/cache_'.md5($_SERVER['REQUEST_URI'])))
		{
			if (filectime('files/temp/cache_'.md5($_SERVER['REQUEST_URI']))+$siman_cache<time())
				unlink('files/temp/cache_'.md5($_SERVER['REQUEST_URI']));
			else
				{
					$fh = fopen('files/temp/cache_'.md5($_SERVER['REQUEST_URI']), 'rb');
					fpassthru($fh);
					exit;
				}
		}

	if (!$sm['disable_session'])
		session_start();

	$_getvars = $_GET;
	$_postvars = $_POST;
	$_cookievars = $_COOKIE;
	$_servervars = $_SERVER;
	$_uplfilevars = $_FILES;
	if (!$sm['disable_session'])
		{
			if (is_array($_SESSION))
				while (list($key, $val) = each($_SESSION))
					{
						if (strcmp(substr($key, 0, strlen($session_prefix)), $session_prefix) == 0)
							{
								$key = substr($key, strlen($session_prefix));
								$_sessionvars[$key] = $val;
							}
					}
		}

	$special['main_tpl'] = 'index';
	$special['page_url'] = 'index.php';
	if (!empty($_servervars['QUERY_STRING']))
		$special['page_url'] .= '?'.$_servervars['QUERY_STRING'];
	$singleWindow = 0;

	$special['printmode'] = 'off';
	if (!empty($_getvars['printmode']))
		{
			if ($_getvars['printmode'] == 'on' || $_getvars['printmode'] == 1)
				{
					$special['printmode'] = 'on';
					$special['main_tpl'] = 'indexprint';
				}
		}
	if (!empty($_getvars['ajax']))
		{
			if ($_getvars['ajax'] == 1 || $_getvars['ajax'] == 'on')
				{
					$special['ajax'] = 1;
					$special['main_tpl'] = 'simpleout';
					$singleWindow = 1;
				}
		}
	if ($_getvars['theonepage'] == 1 || $_getvars['theonepage'] == 'on')
		{
			$special['main_tpl'] = 'theonepage';
			$special['no_blocks'] = true;
			$special['no_borders_main_block'] = true;
		}
	if (!empty($_getvars['chngdsrc']))
		{
			if (is_numeric($_getvars['chngdsrc']))
				{
					if (!empty($_settings['allowed_db_prefixes'][$_getvars['chngdsrc']]))
						$_sessionvars['overwritedbprefix'] = $_settings['allowed_db_prefixes'][$_getvars['chngdsrc']];
				}
		}
	if (!empty($_sessionvars['overwritedbprefix']))
		{
			if ($tableusersprefix == $tableprefix)
				$tableusersprefix = $_sessionvars['overwritedbprefix'];
			$tableprefix = $_sessionvars['overwritedbprefix'];
		}

	$sm['g'] =& $_getvars;
	$sm['p'] =& $_postvars;
	$sm['server'] =& $_servervars;
	$sm['cookies'] =& $_cookievars;
	$sm['files'] =& $_uplfilevars;
	$sm['session'] =& $_sessionvars;
	$sm['s'] =& $special;
	$sm['t'] =& $tableprefix;
	$sm['tu'] =& $tableusersprefix;

?>
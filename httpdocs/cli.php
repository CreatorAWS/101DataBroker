<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-04-23
	//==============================================================================

	if (!in_array(php_sapi_name(), Array('cli', 'cgi-fcgi')))
		exit('Access denied');

	function parseCLIArgs($argv)
		{
			array_shift($argv);
			$out = array();
			foreach ($argv as $arg)
				{
					if (substr($arg, 0, 2) == '--')
						{
							$eqPos = strpos($arg, '=');
							if ($eqPos === false)
								{
									$key = substr($arg, 2);
									$out[$key] = isset($out[$key]) ? $out[$key] : true;
								}
							else
								{
									$key = substr($arg, 2, $eqPos-2);
									$out[$key] = substr($arg, $eqPos+1);
								}
						}
					else if (substr($arg, 0, 1) == '-')
						{
							if (substr($arg, 2, 1) == '=')
								{
									$key = substr($arg, 1, 1);
									$out[$key] = substr($arg, 3);
								}
							else
								{
									$chars = str_split(substr($arg, 1));
									foreach ($chars as $char)
										{
											$key = $char;
											$out[$key] = isset($out[$key]) ? $out[$key] : true;
										}
								}
						}
					else
						{
							$out[] = $arg;
						}
				}
			return $out;
		}

	chdir(dirname(__FILE__));

	$_SERVER['SERVER_NAME'] = 'localhost';
	$_SERVER['REQUEST_URI'] = 'cli.php';
	$_GET = parseCLIArgs($argv);
	$_GET['ajax'] = 1;
	$special['cli']=true;
	$special['nosmarty']=true;
	include('index.php');

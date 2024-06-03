<?php

//------------------------------------------------------------------------------
//|                                                                            |
//|         Система керування вмістом сайту SiMan CMS                          |
//|            Content Management System SiMan CMS                             |
//|                                                                            |
//|               (c) Aged Programmer's Group                                  |
//|                http://www.apserver.org.ua                                  |
//|                                                                            |
//------------------------------------------------------------------------------

//==============================================================================
//#ver 1.3.0.0                                                                 |
//==============================================================================

$modules[$modules_index]["module"]='msgbox';
$modules[$modules_index]["title"]=$_msgbox['title'];
$modules[$modules_index]["mode"]=$_msgbox['mode'];

if (strcmp($modules[$modules_index]["mode"], 'yesno')==0)
	{
		$modules[$modules_index]["message"]=$_msgbox['msg'];
		$modules[$modules_index]["url_yes"]=$_msgbox['yes'];
		$modules[$modules_index]["url_no"]=$_msgbox['no'];
		if (empty($modules[$modules_index]["url_no"]))
			{
				$modules[$modules_index]["url_no"]='index.php';
			}
	}

?>
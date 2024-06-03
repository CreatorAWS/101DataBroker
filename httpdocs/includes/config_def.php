<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//Use protect code (captcha). 1 - on, 0 - off
	$_settings['use_protect_code']=1;
	
	$_settings['disallowed_upload_extensions']='php|phps|php3|php4|php5|htm|xhtml|html|exe|sh|pl|py|bash|zsh|csh|ksh|cmd|wsf|bat|bin|com|lnk|pif|vb|vbs|vbscript|ws';
	
	$_settings['packages_upload_allowed']=true;

	$_settings['show_script_info'] = 'off';
	
	if (empty($_settings['htmlescapecharset']))
		{
			if (strpos($initialStatementDB, '1251'))
				$_settings['htmlescapecharset']='cp1251';
			elseif (strpos($initialStatementDB, '1252'))
				$_settings['htmlescapecharset']='cp1252';
			else
				$_settings['htmlescapecharset']='UTF-8';
		}

?>
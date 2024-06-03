<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Simple Shortcodes
	Module URI: http://simancms.org/modules/simpleshortcodes/
	Description: Simple shortcodes to use in your texts
	Version: 2013-10-17
	Author: SiMan CMS Team
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (!defined("simpleshortcodes_FUNCTIONS_DEFINED"))
		{
			define("simpleshortcodes_FUNCTIONS_DEFINED", 1);
		}

	if ($userinfo['level'] == 3)
		{
			sm_include_lang('simpleshortcodes');
			if (sm_action('admin'))
				{
					$m["module"] = 'simpleshortcodes';
					add_path_modules();
					add_path($lang['module_simpleshortcodes']['module_simpleshortcodes'], 'index.php?m=simpleshortcodes&d=admin');
					$m['title'] = $lang['settings'];
				}
			if (sm_action('install'))
				{
					$m['title'] = $lang['common']['install'];
					$m["module"] = 'simpleshortcodes';
					sm_register_module('simpleshortcodes', $lang['module_simpleshortcodes']['module_simpleshortcodes']);
					sm_register_autoload('simpleshortcodes');
					sm_register_postload('simpleshortcodes');
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('uninstall'))
				{
					$m['title'] = $lang['common']['install'];
					$m["module"] = 'simpleshortcodes';
					sm_unregister_module('simpleshortcodes');
					sm_unregister_autoload('simpleshortcodes');
					sm_unregister_postload('simpleshortcodes');
					sm_redirect('index.php?m=admin&d=modules');
				}
		}

?>
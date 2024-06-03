<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Navigation
	Module URI: http://simancms.org/modules/menu/
	Description: Navigation management module. Base CMS module
	Version: 1.6.7
	Revision: 2013-10-17
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	sm_default_action('view');

	if (sm_action('view'))
		{
			if (!empty($m["bid"]))
				$menu_id = intval($m["bid"]);
			else
				$menu_id = intval($_getvars['mid']);
			$result = execsql("SELECT * FROM ".$tableprefix."menus WHERE id_menu_m=".intval($menu_id));
			while ($row = database_fetch_assoc($result))
				{
					$m["module"] = 'menu';
					sm_title($row['caption_m']);
					$m['menu'] = siman_load_menu($menu_id);
					siman_add_modifier_menu($m['menu']);
					sm_page_viewid('menu-view-'.$menu_id);
				}
		}

	if ($userinfo['level'] == 3)
		include('modules/inc/adminpart/menu.php');

?>
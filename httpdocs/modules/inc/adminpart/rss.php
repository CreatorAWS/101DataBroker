<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2014-05-26
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if ($userinfo['level'] == 3)
		{
			sm_include_lang('rss');
			if (sm_action('postsettings'))
				{
					$cnt=intval($_postvars['rss_itemscount']);
					if ($cnt<=0)
						$cnt=15;
					sm_update_settings('rss_itemscount', $cnt);
					sm_update_settings('rss_showfulltext', intval($_postvars['rss_showfulltext']));
					sm_update_settings('rss_shownewsctgs', intval($_postvars['rss_shownewsctgs']));
					sm_update_settings('rss_shownimagetag', intval($_postvars['rss_shownimagetag']));
					sm_redirect('index.php?m=rss&d=admin');
				}
			if (sm_action('admin'))
				{
					add_path_modules();
					add_path($lang['module_rss']['module_rss'], 'index.php?m=rss&d=admin');
					sm_title($lang['settings']);
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					$f = new TForm('index.php?m=rss&d=postsettings');
					$f->AddText('rss_itemscount', $lang['module_rss']['settings']['rss_itemscount']);
					$f->AddCheckbox('rss_showfulltext', $lang['module_rss']['settings']['rss_showfulltext']);
					$f->AddCheckbox('rss_shownewsctgs', $lang['module_rss']['settings']['rss_shownewsctgs']);
					$f->AddCheckbox('rss_shownimagetag', $lang['module_rss']['settings']['rss_shownimagetag']);
					$f->LoadValuesArray($_settings);
					$ui->AddForm($f);
					$ui->Output(true);
				}
			if (sm_action('install'))
				{
					sm_register_module('rss', $lang['module_rss']['module_rss']);
					sm_register_autoload('rss');
					sm_new_settings('rss_itemscount', 15);
					sm_new_settings('rss_showfulltext', 0);
					sm_new_settings('rss_shownewsctgs', 0);
					sm_new_settings('rss_shownimagetag', 0);
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('uninstall'))
				{
					sm_unregister_module('rss');
					sm_unregister_autoload('rss');
					sm_delete_settings('rss_itemscount');
					sm_delete_settings('rss_showfulltext');
					sm_delete_settings('rss_shownewsctgs');
					sm_delete_settings('rss_shownimagetag');
					sm_redirect('index.php?m=admin&d=modules');
				}
		}

?>
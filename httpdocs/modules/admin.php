<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-06-06
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("ADMIN_FUNCTIONS_DEFINED"))
		{
			function sm_get_module_info($filename)
				{
					$fh = fopen($filename, 'r');
					$info = fread($fh, 2048);
					fclose($fh);
					$start = strpos($info, 'Module Name:');
					if ($start !== false && strpos($info, '*/', $start) !== false)
						{
							$info = substr($info, $start, strpos($info, '*/', $start) - $start);
							$items=nllistToArray($info, true);
							for ($i=0; $i<count($items); $i++)
								{
									$item=explode(':', $items[$i]);
									$key=sm_getnicename(trim($item[0]));
									$value='';
									for ($j=1; $j<count($item); $j++)
										$value.=($j>1?':'.$item[$j]:''.ltrim($item[$j]));
									$result[$key]=$value;
								}
							return $result;
						}
					else
						return false;
				}
			function siman_clean_resource_url($url)
				{
					$url = trim($url);
					if (strcmp($url, '/')==0)
						$url = '';
					if (substr($url, -1) != '/' && strlen($url)>0)
						$url .= '/';
					if (strpos($url, '://')!==false)
						return substr($url, strpos($url, '://')+3);
					return $url;
				}

			define("ADMIN_FUNCTIONS_DEFINED", 1);
		}

	sm_default_action('view');

	if ($userinfo['level'] == 3)
		{
			$m["module"] = 'admin';
			if (sm_actionpost('postsettings'))
				{
					if (!empty($_getvars['viewmode']))
						$m['mode_settings'] = $_getvars['viewmode'];
					else
						$m['mode_settings'] = 'default';
					//------- Common settings ------------------------------------------------------------------------------
					sm_update_settings('resource_title', $_postvars['p_title'], $m['mode_settings']);
					sm_update_settings('resource_url', siman_clean_resource_url($_postvars['p_url']), $m['mode_settings']);
					sm_update_settings('resource_url_mobile', siman_clean_resource_url($_postvars['resource_url_mobile']), $m['mode_settings']);
					sm_update_settings('resource_url_tablet', siman_clean_resource_url($_postvars['resource_url_tablet']), $m['mode_settings']);
					sm_update_settings('resource_url_rewrite', intval($_postvars['resource_url_rewrite'])==1 ? '1' : '0', $m['mode_settings']);
					sm_update_settings('logo_text', $_postvars['p_logo'], $m['mode_settings']);
					sm_update_settings('copyright_text', $_postvars['p_copyright'], $m['mode_settings']);
					sm_update_settings('meta_keywords', $_postvars['p_keywords'], $m['mode_settings']);
					sm_update_settings('meta_description', $_postvars['p_description'], $m['mode_settings']);
					sm_update_settings('default_language', $_postvars['p_lang'], $m['mode_settings']);
					sm_update_settings('default_theme', $_postvars['p_theme'], $m['mode_settings']);
					sm_update_settings('sidepanel_count', (intval($_postvars['p_sidepanel_count']) <= 0) ? 1 : intval($_postvars['p_sidepanel_count']), $m['mode_settings']);
					sm_update_settings('default_module', $_postvars['p_module'], $m['mode_settings']);
					sm_update_settings('cookprefix', $_postvars['p_cook'], $m['mode_settings']);
					sm_update_settings('max_upload_filesize', intval($_postvars['p_maxfsize']), $m['mode_settings']);
					sm_update_settings('admin_items_by_page', intval($_postvars['p_adminitems_per_page'])<=0 ? 10 : intval($_postvars['p_adminitems_per_page']), $m['mode_settings']);
					sm_update_settings('search_items_by_page', intval($_postvars['p_searchitems_per_page'])<=0 ? 10 : intval($_postvars['p_searchitems_per_page']), $m['mode_settings']);
					sm_update_settings('ext_editor', $_postvars['p_exteditor'], $m['mode_settings']);
					sm_update_settings('noflood_time', intval($_postvars['p_floodtime'])<=0 ? 600 : intval($_postvars['p_floodtime']), $m['mode_settings']);
					sm_update_settings('blocks_use_image', intval($_postvars['p_blocks_use_image'])==1?1:0, $m['mode_settings']);
					sm_update_settings('rewrite_index_title', $_postvars['p_rewrite_index_title'], $m['mode_settings']);
					sm_update_settings('log_type', intval($_postvars['p_log_type'])<=0 ? 0 : intval($_postvars['p_log_type']), $m['mode_settings']);
					sm_update_settings('log_store_days', intval($_postvars['p_log_store_days'])<=0 ? 0 : intval($_postvars['p_log_store_days']), $m['mode_settings']);
					sm_update_settings('image_generation_type', ($_postvars['p_image_generation_type'] == 'static') ? 'static' : 'dynamic', $m['mode_settings']);
					sm_update_settings('title_delimiter', $_postvars['p_title_delimiter'], $m['mode_settings']);
					sm_update_settings('meta_resource_title_position', intval($_postvars['p_meta_resource_title_position']), $m['mode_settings']);
					//------- Menu settings ------------------------------------------------------------------------------
					sm_update_settings('upper_menu_id', $_postvars['p_uppermenu'], $m['mode_settings']);
					sm_update_settings('bottom_menu_id', $_postvars['p_bottommenu'], $m['mode_settings']);
					sm_update_settings('users_menu_id', $_postvars['p_usersmenu'], $m['mode_settings']);
					sm_update_settings('menus_use_image', intval($_postvars['p_menus_use_image'])==1?1:0, $m['mode_settings']);
					sm_update_settings('menuitems_use_image', intval($_postvars['p_menuitems_use_image'])==1?1:0, $m['mode_settings']);
					//------- Text settings ------------------------------------------------------------------------------
					sm_update_settings('content_use_preview', intval($_postvars['p_content_use_preview'])==1?1:0, $m['mode_settings']);
					sm_update_settings('content_per_page_multiview', intval($_postvars['content_per_page_multiview'])<=0?10:intval($_postvars['content_per_page_multiview']), $m['mode_settings']);
					sm_update_settings('allow_alike_content', intval($_postvars['p_allow_alike_content'])==1?1:0, $m['mode_settings']);
					sm_update_settings('alike_content_count', intval($_postvars['alike_content_count'])<=0 ? 5 : intval($_postvars['alike_content_count']), $m['mode_settings']);
					sm_update_settings('content_use_path', ($_postvars['p_content_use_path'] == '1') ? '1' : '0', $m['mode_settings']);
					sm_update_settings('content_attachments_count', abs(intval($_postvars['p_content_attachments_count'])), $m['mode_settings']);
					sm_update_settings('content_use_image', ($_postvars['p_content_use_image'] == '1') ? '1' : '0', $m['mode_settings']);
					sm_update_settings('content_image_preview_width', $_postvars['p_content_image_preview_width'], $m['mode_settings']);
					sm_update_settings('content_image_preview_height', $_postvars['p_content_image_preview_height'], $m['mode_settings']);
					sm_update_settings('content_image_fulltext_width', $_postvars['p_content_image_fulltext_width'], $m['mode_settings']);
					sm_update_settings('content_image_fulltext_height', $_postvars['p_content_image_fulltext_height'], $m['mode_settings']);
					sm_update_settings('content_editor_level', intval($_postvars['content_editor_level']), $m['mode_settings']);
					if ($m['mode_settings'] == 'default')
						{
							sm_update_settings('autogenerate_content_filesystem', intval($_postvars['autogenerate_content_filesystem']), 'content');
						}
					//------- News settings ------------------------------------------------------------------------------
					sm_update_settings('news_use_title', intval($_postvars['p_news_use_title'])==1?1:0, $m['mode_settings']);
					sm_update_settings('news_use_time', intval($_postvars['p_news_use_time'])==1?1:0, $m['mode_settings']);
					sm_update_settings('news_use_image', intval($_postvars['p_news_use_image'])==1?1:0, $m['mode_settings']);
					sm_update_settings('news_image_preview_width', $_postvars['p_news_image_preview_width'], $m['mode_settings']);
					sm_update_settings('news_image_preview_height', $_postvars['p_news_image_preview_height'], $m['mode_settings']);
					sm_update_settings('news_image_fulltext_width', $_postvars['p_news_image_fulltext_width'], $m['mode_settings']);
					sm_update_settings('news_image_fulltext_height', $_postvars['p_news_image_fulltext_height'], $m['mode_settings']);
					sm_update_settings('news_by_page', intval($_postvars['p_news_per_page'])<=0?10:intval($_postvars['p_news_per_page']), $m['mode_settings']);
					sm_update_settings('news_use_preview', intval($_postvars['p_news_use_preview'])==1?1:0, $m['mode_settings']);
					sm_update_settings('news_anounce_cut', intval($_postvars['p_news_cut'])<=0?300:intval($_postvars['p_news_cut']), $m['mode_settings']);
					sm_update_settings('short_news_count', intval($_postvars['p_news_short'])<=0?3:intval($_postvars['p_news_short']), $m['mode_settings']);
					sm_update_settings('short_news_cut', intval($_postvars['p_short_news_cut'])<=0?100:intval($_postvars['p_short_news_cut']), $m['mode_settings']);
					sm_update_settings('allow_alike_news', intval($_postvars['p_allow_alike_news'])==1?1:0, $m['mode_settings']);
					sm_update_settings('alike_news_count', intval($_postvars['p_alike_news_count']), $m['mode_settings']);
					sm_update_settings('news_attachments_count', abs(intval($_postvars['p_news_attachments_count'])), $m['mode_settings']);
					sm_update_settings('news_full_list_longformat', intval($_postvars['news_full_list_longformat']), $m['mode_settings']);
					sm_update_settings('news_editor_level', intval($_postvars['news_editor_level']), $m['mode_settings']);
					if ($m['mode_settings'] == 'default')
						{
							sm_update_settings('autogenerate_news_filesystem', intval($_postvars['autogenerate_news_filesystem']), 'news');
						}
					//------ User settings ----------------------------------------------------------------
					sm_update_settings('allow_register', intval($_postvars['p_allowregister'])==1?1:0, $m['mode_settings']);
					sm_update_settings('allow_forgot_password', intval($_postvars['p_allowforgotpass'])==1?1:0, $m['mode_settings']);
					sm_update_settings('user_activating_by_admin', intval($_postvars['p_adminactivating'])==1?1:0, $m['mode_settings']);
					sm_update_settings('return_after_login', intval($_postvars['p_return_after_login'])==1?1:0, $m['mode_settings']);
					sm_update_settings('allow_private_messages', intval($_postvars['p_allow_private_messages'])==1?1:0, $m['mode_settings']);
					sm_update_settings('use_email_as_login', ($_postvars['p_use_email_as_login'] == '1') ? '1' : '0', $m['mode_settings']);
					sm_update_settings('signinwithloginandemail', intval($_postvars['signinwithloginandemail']), $m['mode_settings']);
					sm_update_settings('redirect_after_login_1', $_postvars['p_redirect_after_login_1'], $m['mode_settings']);
					sm_update_settings('redirect_after_login_2', $_postvars['p_redirect_after_login_2'], $m['mode_settings']);
					sm_update_settings('redirect_after_login_3', $_postvars['p_redirect_after_login_3'], $m['mode_settings']);
					sm_update_settings('redirect_after_register', $_postvars['p_redirect_after_register'], $m['mode_settings']);
					sm_update_settings('redirect_after_logout', $_postvars['p_redirect_after_logout'], $m['mode_settings']);
					sm_update_settings('redirect_on_success_change_usrdata', $_postvars['redirect_on_success_change_usrdata'], $m['mode_settings']);
					//------ Security settings ----------------------------------------------------------------
					sm_update_settings('banned_ip', $_postvars['p_banned_ip'], $m['mode_settings']);
					//------ Static texts settings ----------------------------------------------------------------
					sm_update_settings('meta_header_text', $_postvars['p_meta_header_text'], $m['mode_settings']);
					sm_update_settings('header_static_text', $_postvars['p_htext'], $m['mode_settings']);
					sm_update_settings('footer_static_text', $_postvars['p_ftext'], $m['mode_settings']);
					//---- Setup mail settings ------------------------------------------------------------
					sm_update_settings('administrators_email', $_postvars['p_admemail'], $m['mode_settings']);
					sm_update_settings('email_signature', $_postvars['p_esignature'], $m['mode_settings']);
					//-------------------------------------------------------------------------------------

					include('includes/config.php');
					sm_notify($lang['settings_saved_successful']);
					sm_redirect('index.php?m=admin&d=settings&viewmode='.$m['mode_settings']);
				}
			if (sm_action('postchgttl'))
				{
					$q=new TQuery($tableprefix."modules");
					$q->AddPost('module_title');
					$q->Update('id_module', intval($_getvars['mid']));
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('postuplimg'))
				{
					sm_extcore();
					$fs = $_uplfilevars['userfile']['tmp_name'];
					if (empty($_postvars['p_optional']))
						{
							$fd = basename($_uplfilevars['userfile']['name']);
						}
					else
						{
							$fd = $_postvars['p_optional'];
						}
					$fd = './files/img/'.$fd;
					$m['fs'] = $fs;
					$m['fd'] = $fd;
					if (!sm_is_allowed_to_upload($fd))
						{
							$m['error_message']=$lang['error_file_upload_message'];
							sm_set_action('uplimg');
						}
					elseif (!move_uploaded_file($fs, $fd))
						{
							$m['error_message']=$lang['error_file_upload_message'];
							sm_set_action('uplimg');
						}
					else
						{
							sm_event('afteruploadedimagesaveadmin', array($fd));
							sm_notify($lang['operation_complete']);
							sm_redirect('index.php?m=admin&d=listimg');
						}
				}
			if (sm_action('view'))
				{
					if (intval(sm_settings('ignore_update'))!=1)
						{
							if (file_exists('includes/update.php'))
								{
									sm_update_settings('install_not_erased', 1);
								}
						}
					sm_event('beforeadmindashboard');
					sm_title($lang['control_panel']);
					sm_use('admindashboard');
					sm_use('admininterface');
					$ui = new TInterface();
					sm_event('onadmindashboardstart');
					$ui->AddBlock($lang['control_panel']);
					$dashboard=new TDashBoard();
					sm_event('onadmindashboardcommonstart');
					$dashboard->AddItem($lang['modules_mamagement'], 'index.php?m=admin&d=modules', 'applications');
					$dashboard->AddItem($lang['blocks_mamagement'], 'index.php?m=blocks', 'blocks');
					$dashboard->AddItem($lang['module_admin']['virtual_filesystem'], 'index.php?m=admin&d=filesystem', 'folder');
					//<a href="index.php?m=admin&d=filesystemexp">{$lang.module_admin.virtual_filesystem_regexp}</a><br /> [Unsupported]
					//<a href="index.php?m=admin&d=listmodes">{$lang.module_admin.modes_management}</a><br /> [Temporary unsupported]
					$dashboard->AddItem($lang['module_admin']['images_list'], 'index.php?m=admin&d=listimg', 'photo');
					$dashboard->AddItem($lang['upload_image'], 'index.php?m=admin&d=uplimg', 'photoadd');
					$dashboard->AddItem($lang['module_admin']['optimize_database'], 'index.php?m=admin&d=tstatus', 'databasechecked');
					if (intval(sm_settings('log_type'))>0)
						$dashboard->AddItem($lang['module_admin']['view_log'], 'index.php?m=admin&d=viewlog', 'log');
					if (is_writeable('./') && sm_settings('packages_upload_allowed'))
						$dashboard->AddItem($lang['module_admin']['upload_package'], 'index.php?m=admin&d=package', 'upload');
					$dashboard->AddItem('robots.txt', 'index.php?m=admin&d=robotstxt', 'directions.png');
					$dashboard->AddItem($lang['settings'], 'index.php?m=admin&d=settings', 'settings.png');
					sm_event('onadmindashboardcommonend');
					$ui->AddDashboard($dashboard);
					unset($dashboard);
					$ui->AddBlock($lang['user_settings']);
					$dashboard=new TDashBoard();
					sm_event('onadmindashboardusersstart');
					$dashboard->AddItem($lang['register_user'], 'index.php?m=account&d=register', 'useradd');
					$dashboard->AddItem($lang['user_list'], 'index.php?m=account&d=usrlist', 'user');
					$dashboard->AddItem($lang['module_account']['groups_management'], 'index.php?m=account&d=listgroups', 'usersettings');
					$dashboard->AddItem($lang['module_admin']['mass_email'], 'index.php?m=admin&d=massemail', 'email');
					sm_event('onadmindashboardusersend');
					$ui->AddDashboard($dashboard);
					unset($dashboard);
					sm_event('onadmindashboardend');
					$ui->Output(true);
					sm_event('afteradmindashboard');
				}
			if (sm_action('uplimg'))
				{
					$m["title"] = $lang['upload_image'];
					add_path_control();
					add_path($lang['module_admin']['images_list'], 'index.php?m=admin&d=listimg');
					add_path($lang['upload_image'], 'index.php?m=admin&d=uplimg');
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($m['error_message']))
						$ui->NotificationError($m['error_message']);
					$f = new TForm('index.php?m=admin&d=postuplimg');
					$f->AddFile('userfile', $lang['file_name']);
					$f->AddText('p_optional', $lang['optional_file_name']);
					$f->SaveButton($lang['upload']);
					$ui->AddForm($f);
					$ui->Output(true);
				}
			if (sm_action('addmodule'))
				{
					add_path_modules();
					$m['title'] = $lang['module_admin']['add_module'];
					sm_use('admininterface');
					sm_use('admintable');
					$ui = new TInterface();
					$t=new TGrid();
					$t->AddCol('title', $lang['module'], '20%');
					$t->AddCol('information', $lang['common']['information'], '25%');
					$t->AddCol('description', $lang['common']['description'], '50%');
					$t->AddCol('action', $lang['action'], '5%');
					$t->SetAsMessageBox('action', $lang['common']['are_you_sure']);
					$dir = dir('./modules/');
					$i = 0;
					while ($entry = $dir->read())
						{
							if (strpos($entry, '.php') > 0)
								{
									if (in_array($entry, Array('admin.php', 'content.php', 'account.php', 'blocks.php', 'refresh.php', 'menu.php', 'news.php', 'download.php', 'search.php')))
										continue;
									if (in_array(substr($entry, 0, -4), nllistToArray(sm_settings('installed_packages'))))
										continue;
									$info = sm_get_module_info('./modules/'.$entry);
									if (
										!file_exists('./themes/'.$_settings['default_theme'].'/'.substr($entry, 0, -4).'.tpl')
										&&
										!file_exists('./themes/default/'.substr($entry, 0, -4).'.tpl')
										&&
										$info == false
									)
										continue;
									if (!empty($info[sm_getnicename('Module Name')]))
										$t->Label('title', $info[sm_getnicename('Module Name')]);
									else
										$t->Label('title', substr($entry, 0, -4));
									$information='';
									if (!empty($info[sm_getnicename('Version')]))
										$information=$lang['module_admin']['version'].': '.$info[sm_getnicename('Version')].'<br />';
									if (!empty($info[sm_getnicename('Author')]))
										$information.=$lang['module_admin']['author'].': '.$info[sm_getnicename('Author')].'<br />';
									$t->Label('information', $information);
									if (!empty($info[sm_getnicename('Description')]))
										$t->Label('description', $info[sm_getnicename('Description')]);
									if (!empty($info[sm_getnicename('Author URI')]))
										$t->URL('title', $info[sm_getnicename('Author URI')], true);
									if (!empty($info[sm_getnicename('Module URI')]))
										$t->URL('description', $info[sm_getnicename('Module URI')], true);
									$t->Label('action', $lang['common']['install']);
									$t->URL('action', 'index.php?m='.substr($entry, 0, -4).'&d=install');
									$t->NewRow();
									$i++;
								}
						}
					$dir->close();
					$ui->AddGrid($t);
					$ui->Output(true);
				}
			if (sm_action('modules'))
				{
					add_path_modules();
					$m["title"] = $lang['modules_mamagement'];
					sm_use('admininterface');
					sm_use('admintable');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$b = new TButtons();
					$b->AddButton('add', $lang['module_admin']['add_module'], 'index.php?m=admin&d=addmodule');
					$ui->AddButtons($b);
					$t = new TGrid();
					$t->AddCol('title', $lang['module']);
					$t->AddCol('information', $lang['common']['information'], '25%');
					$t->AddCol('description', $lang['common']['description'], '50%');
					$t->AddEdit();
					$t->AddCol('delete', '', '16');
					$t->SetHeaderImage('delete', 'transparent.gif');
					$sql = "SELECT * FROM ".$tableprefix."modules";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$info = sm_get_module_info('./modules/'.$row->module_name.'.php');
							if (!empty($info[sm_getnicename('Module Name')]))
								$t->Label('title', $info[sm_getnicename('Module Name')]);
							else
								$t->Label('title', substr($entry, 0, -4));
							$information='';
							if (!empty($info[sm_getnicename('Version')]))
								$information=$lang['module_admin']['version'].': '.$info[sm_getnicename('Version')].'<br />';
							if (!empty($info[sm_getnicename('Author')]))
								{
									if (!empty($info[sm_getnicename('Author URI')]))
										$information.=$lang['module_admin']['author'].': <a href="'.$info[sm_getnicename('Author URI')].'" target="_blank">'.$info[sm_getnicename('Author')].'</a><br />';
									else
										$information.=$lang['module_admin']['author'].': '.$info[sm_getnicename('Author')].'<br />';
								}
							$t->Label('information', $information);
							if (!empty($info[sm_getnicename('Description')]))
								$t->Label('description', $info[sm_getnicename('Description')]);
							if (!empty($info[sm_getnicename('Module URI')]))
								$t->URL('description', $info[sm_getnicename('Module URI')], true);
							$t->Label('title', $row->module_title);
							$t->Url('title', 'index.php?m='.$row->module_name.'&d=admin');
							$t->Url('edit', 'index.php?m=admin&d=chgttl&mid='.$row->id_module);
							if (!in_array($row->module_name, Array('content', 'news', 'download', 'menu', 'search', 'media')))
								{
									$t->Image('delete', 'delete.gif');
									$t->Url('delete', 'index.php?m='.$row->module_name.'&d=uninstall');
									$t->CustomMessageBox('delete', $lang['common']['are_you_sure']);
								}
							$t->NewRow();
							$i++;
						}
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('chgttl'))
				{
					add_path_control();
					add_path_current();
					sm_title($lang['change_title']);
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					$f = new TForm('index.php?m=admin&d=postchgttl&mid='.intval($_getvars['mid']));
					$f->AddText('module_title', $lang['title']);
					$q=new TQuery($tableprefix."modules");
					$q->Add('id_module', intval($_getvars['mid']));
					$f->LoadValuesArray($q->Get());
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('module_title');
				}
			if (sm_action('copysettings'))
				{
					if (!empty($_getvars['destmode']) && !empty($_getvars['name']))
						{
							$q = new TQuery($tableprefix."settings");
							$q->Add('name_settings', dbescape($_getvars['name']));
							$q->Add('value_settings', addslashes($_settings[$_getvars['name']]));
							$q->Add('mode', dbescape($_getvars['destmode']));
							$q->Insert();
						}
					sm_redirect('index.php?m=admin&d=settings');
				}
			if (sm_action('remsettings'))
				{
					if (!empty($_getvars['destmode']) && !empty($_getvars['name']))
						{
							$q = new TQuery($tableprefix."settings");
							$q->Add('name_settings', dbescape($_getvars['name']));
							$q->Add('mode', dbescape($_getvars['destmode']));
							$q->Remove();
						}
					sm_redirect('index.php?m=admin&d=settings');
				}
			if (sm_action('settings'))
				{
					$m["title"] = $lang['settings'];
					if (!empty($_getvars['viewmode']))
						$m['mode_settings'] = $_getvars['viewmode'];
					else
						$m['mode_settings'] = 'default';
					$m['list_modes'][0]['mode'] = 'mobile';
					$m['list_modes'][0]['shortcut'] = 'M';
					$m['list_modes'][0]['hint'] = $lang['common']['device'].': '.$lang['common']['mobile_device'];
					$m['list_modes'][0]['profile'] = $lang['common']['mobile_device'];
					$m['list_modes'][1]['mode'] = 'tablet';
					$m['list_modes'][1]['shortcut'] = 'T';
					$m['list_modes'][1]['hint'] = $lang['common']['device'].': '.$lang['common']['tablet_device'];
					$m['list_modes'][1]['profile'] = $lang['common']['tablet_device'];
					add_path_control();
					add_path($lang['settings'], 'index.php?m=admin&d=settings');
					if ($m['mode_settings'] == 'default')
						{
							$m['available_modes'] = $m['list_modes'];
							add_path($lang['common']['general'], 'index.php?m=admin&d=settings');
						}
					elseif ($m['mode_settings'] == 'mobile')
						add_path($lang['common']['mobile_device'], 'index.php?m=admin&d=settings&viewmode=mobile');
					elseif ($m['mode_settings'] == 'tablet')
						add_path($lang['common']['tablet_device'], 'index.php?m=admin&d=settings&viewmode=tablet');
					$sql = "SELECT * FROM ".$tableprefix."settings WHERE mode='".$m['mode_settings']."'";
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$m['edit_settings'][$row->name_settings] = $row->value_settings;
							$m['show_settings'][$row->name_settings] = 1;
						}
					for ($i = 0; $i < count($m['available_modes']); $i++)
						{
							$sql = "SELECT * FROM ".$tableprefix."settings WHERE mode='".$m['available_modes'][$i]['mode']."'";
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$m['extmodes'][$m['available_modes'][$i]['mode']]['show_settings'][$row->name_settings] = 1;
								}
						}
					$dir = dir('./lang/');
					$i = 0;
					while ($entry = $dir->read())
						{
							if (strcmp($entry, '.') != 0 && strcmp($entry, '..') != 0 && strcmp($entry, 'index.html') != 0 && strpos($entry, '.php'))
								{
									$m['lang'][$i] = substr($entry, 0, strpos($entry, '.'));
									$i++;
								}
						}
					$dir->close();
					$dir = dir('./themes/');
					$i = 0;
					while ($entry = $dir->read())
						{
							if (strcmp($entry, '.') != 0 && strcmp($entry, '..') != 0 && strcmp($entry, 'default') != 0 && strcmp($entry, 'index.html') != 0)
								{
									if (!file_exists('./files/themes/'.$entry)) continue;
									$m['themes'][$i] = $entry;
									$i++;
								}
						}
					$dir->close();
					$dir = dir('./ext/editors/');
					$i = 0;
					while ($entry = $dir->read())
						{
							if (strcmp($entry, '.') != 0 && strcmp($entry, '..') != 0 && strcmp($entry, 'index.html') != 0)
								{
									$m['exteditors'][$i] = $entry;
									$i++;
								}
						}
					$dir->close();
					$q=new TQuery($tableprefix."modules");
					$q->Open();
					while ($q->Fetch())
						$m['modules'][]=Array(
							'title' => $q->row['module_title'].(empty($q->row['module_title'])?$q->row['module_name']:''),
							'name' => $q->row['module_name'],
							'id' => $q->row['id_module']
						);
					unset($q);
					$q=new TQuery($tableprefix."menus");
					$q->Open();
					while ($q->Fetch())
						$m['menus'][]=Array(
							'title' => $q->row['caption_m'],
							'id' => $q->row['id_menu_m']
						);
					unset($q);
					if ($m['mode_settings'] == 'default')
						{
							$m['edit_settings']['autogenerate_content_filesystem'] = sm_get_settings('autogenerate_content_filesystem', 'content');
							$m['show_settings']['autogenerate_content_filesystem'] = 1;
							$m['edit_settings']['autogenerate_news_filesystem'] = sm_get_settings('autogenerate_news_filesystem', 'news');
							$m['show_settings']['autogenerate_news_filesystem'] = 1;
						}
				}
			if (sm_action('tstatus'))
				{
					add_path_control();
					add_path_current();
					sm_title($lang['module_admin']['optimize_database']);
					sm_use('admininterface');
					sm_use('admintable');
					$ui = new TInterface();
					if ($serverDB == 0)
						{
							$t = new TGrid();
							$t->AddCol('table_name', $lang['module_admin']['table_name'], '25%');
							$t->AddCol('table_rows', $lang['module_admin']['table_rows'], '25%');
							$t->AddCol('table_size', $lang['module_admin']['table_size'], '25%');
							$t->AddCol('table_not_optimized', $lang['module_admin']['table_not_optimized'], '20%');
							$t->AddCol('table_optimize', $lang['module_admin']['table_optimize'], '5%');
							$sql = "SHOW TABLE STATUS FROM ".$nameDB;
							$result = execsql($sql);
							$i = 0;
							while ($row = database_fetch_object($result))
								{
									$t->Label('table_name', $row->Name);
									$t->Label('table_rows', $row->Rows);
									$t->Label('table_not_optimized', $row->Data_free);
									$t->Label('table_size', $row->Data_length + $row->Index_length);
									$t->Checkbox('table_optimize', 'p_opt_'.$i, $row->Name, $row->Data_free>0);
									$t->NewRow();
									$i++;
								}
							$ui->html('<form action="index.php?m=admin&d=optimize" method="post">');
							$ui->html('<input type="hidden" name="p_table_count" value="'.$i.'" />');
							$ui->AddGrid($t);
							$ui->div('<input type="submit" value="'.$lang['module_admin']['optimize_tables'].'" />', '', '', 'text-align:right;');
						}
					else
						{
							$ui->NotificationWarning($lang['module_admin']['message_no_tables_in_DB']);
						}
					$ui->Output(true);
				}
			if (sm_action('optimize'))
				{
					$m["title"] = $lang['module_admin']['optimize_database'];
					$tc = $_postvars['p_table_count'];
					if ($serverDB == 0)
						{
							for ($i = 0; $i < $tc; $i++)
								{
									if (isset($_postvars['p_opt_'.$i]))
										{
											$sql = "OPTIMIZE TABLE `".dbescape($_postvars['p_opt_'.$i])."`";
											$result = execsql($sql);
										}
								}
							sm_notify($lang['module_admin']['message_optimize_successfull']);
							sm_redirect('index.php?m=admin&d=tstatus');
						}
				}
			if (sm_action('viewimg'))
				{
					sm_title($lang['common']['image']);
					sm_use('admininterface');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$ui->html('<div align="center">');
					$ui->html('<img src="files/img/'.$_getvars['path'].'" width="400" />');
					$b=new TButtons();
					$b->AddMessageBox('del', $lang['common']['delete'], 'index.php?m=admin&d=postdelimg&imgn='.urlencode($_getvars['path']), $lang['module_admin']['really_want_delete_image'].'?');
					$ui->AddButtons($b);
					$ui->html('</div>');
					$ui->Output(true);
				}
			if (sm_action('listimg'))
				{
					sm_title($lang['module_admin']['images_list']);
					sm_use('admintable');
					add_path_control();
					add_path($lang['module_admin']['images_list'], 'index.php?m=admin&d=listimg');
					$t=new TGrid();
					$t->AddCol('thumb', $lang['common']['thumbnail'], '10');
					$t->AddCol('title', $lang['module_admin']['image_file_name'], '90%');
					$t->AddEdit();
					$t->AddDelete();
					$t->SetAsMessageBox('delete', $lang['module_admin']['really_want_delete_image']);
					$i = 0;
					$j = -1;
					$files = load_file_list('./files/img/');
					$offset=abs(intval($_getvars['from']));
					$limit=intval($_settings['admin_items_by_page']);
					while ($j + 1 < count($files))
						{
							$j++;
							$entry = $files[$j];
							if (!empty($_getvars['filter']))
								if (strpos($entry, $_getvars['filter']) !== 0)
									continue;
							if (strcmp($entry, '.') != 0 && strcmp($entry, '..') != 0 && strcmp($entry, 'index.html') != 0)
								{
									if ($i>=$offset && $i<$limit+$offset)
										{
											$t->Image('thumb', sm_thumburl($entry, 50, 50));
											$t->Label('title', $entry);
											$t->URL('title', 'index.php?m=admin&d=viewimg&path='.urlencode($entry));
											$t->URL('edit', 'index.php?m=admin&d=renimg&imgn='.urlencode($entry));
											$t->URL('delete', 'index.php?m=admin&d=postdelimg&imgn='.urlencode($entry));
											$t->NewRow();
										}
									$i++;
								}
						}
					$m['table']=$t->Output();
					$m['pages']['url'] = sm_this_url('from', '');
					$m['pages']['interval'] = $limit;
					$m['pages']['selected'] = ceil(($offset+1)/$m['pages']['interval']);
					$m['pages']['records']=$i;
					$m['pages']['pages'] = ceil($m['pages']['records'] / $m['pages']['interval']);
				}
			if (sm_action('postdelimg'))
				{
					sm_title($lang['module_admin']['delete_image']);
					$img = $_getvars["imgn"];
					if (!strpos($img, '..') && !strpos($img, '/') && !strpos($img, '\\'))
						unlink('./files/img/'.$img);
					sm_notify($lang['module_admin']['message_delete_image_successful']);
					sm_redirect('index.php?m=admin&d=listimg');
				}
			if (sm_action('postrenimg'))
				{
					sm_extcore();
					$img1 = $_getvars["on"];
					$img2 = $_getvars["nn"];
					if (!(!strpos($img1, '..') && !strpos($img1, '/') && !strpos($img1, '\\') && !strpos($img2, '..') && !strpos($img2, '/') && !strpos($img2, '\\')) || empty($img1) || empty($img2) || !sm_is_allowed_to_upload($img2))
						{
							$m["error_message"] = $lang['module_admin']['message_wrong_file_name'];
						}
					else
						{
							if (!rename('files/img/'.$img1, 'files/img/'.$img2))
								$m["error_message"] = $lang['module_admin']['message_cant_reaname'];
						}
					if (empty($m["error_message"]))
						{
							sm_notify($lang['module_admin']['message_rename_image_successful']);
							sm_redirect('index.php?m=admin&d=listimg');
						}
					else
						{
							$m['mode'] = 'renimg';
							$_getvars["imgn"] = $img1;
						}
				}
			if (sm_action('renimg') && !empty($_getvars["imgn"]))
				{
					sm_title($lang['module_admin']['rename_image']);
					$m['image']['old_name'] = $_getvars["imgn"];
					add_path_control();
					add_path($lang['module_admin']['images_list'], "index.php?m=admin&d=listimg");
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($m['error_message']))
						$ui->div($m['error_message'], '', 'errormessage');
					$f = new TForm('index.php', '', 'get');
					$f->AddText('nn', $lang['file_name']);
					$f->LoadValuesArray($_getvars);
					if (empty($_getvars['nn']))
						$f->SetValue('nn', $_getvars["imgn"]);
					$f->AddHidden('m', 'admin');
					$f->AddHidden('d', 'postrenimg');
					$f->AddHidden('on', $_getvars["imgn"]);
					$f->SaveButton($lang['rename']);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('nn');
				}
			if (sm_action('postmassemail'))
				{
					if (empty($_postvars['subject']) || empty($_postvars['message']))
						{
							$error=$lang['message_set_all_fields'];
							sm_set_action('massemail');
						}
					else
						{
							$result = execsql("SELECT * FROM ".$sm['tu']."users WHERE get_mail=1");
							while ($row = database_fetch_assoc($result))
								{
									send_mail($_settings['resource_title']." <".$_settings['administrators_email'].">", $row['email'], $_postvars['subject'], $_postvars['message']);
								}
							sm_notify($lang['module_admin']['message_mass_email_successfull']);
							sm_redirect('index.php?m=admin');
						}
				}
			if (sm_action('massemail'))
				{
					add_path_control();
					add_path_current();
					sm_title($lang['module_admin']['mass_email']);
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->div($error, '', 'errormessage error-message');
					$f = new TForm('index.php?m=admin&d=postmassemail');
					$f->AddText('subject', $lang['module_admin']['mass_email_theme']);
					$f->AddEditor('message', $lang['module_admin']['mass_email_message']);
					$f->LoadValuesArray($_postvars);
					if (count($_postvars)==0)
						$f->SetValue('message', $_settings['email_signature']);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('subject');
				}
			if (sm_action('filesystem'))
				{
					add_path_control();
					add_path($lang['module_admin']['virtual_filesystem'], 'index.php?m=admin&d=filesystem');
					$m["title"] = $lang['module_admin']['virtual_filesystem'];
					sm_use('admintable');
					sm_use('admininterface');
					sm_use('adminbuttons');
					$offset=abs(intval($_getvars['from']));
					$limit=intval($_settings['admin_items_by_page']);
					$ui = new TInterface();
					$t=new TGrid();
					$t->AddCol('ico', '', '16');
					$t->AddCol('url', $lang['url'], '50%');
					$t->HeaderDropDownItem('url', $lang['common']['sortingtypes']['asc'], sm_this_url(Array('orderby'=>'urlasc', 'from'=>'')));
					$t->HeaderDropDownItem('url', $lang['common']['sortingtypes']['desc'], sm_this_url(Array('orderby'=>'urldesc', 'from'=>'')));
					$t->AddCol('title', $lang['common']['title'], '50%');
					$t->HeaderDropDownItem('title', $lang['common']['sortingtypes']['asc'], sm_this_url(Array('orderby'=>'titleasc', 'from'=>'')));
					$t->HeaderDropDownItem('title', $lang['common']['sortingtypes']['desc'], sm_this_url(Array('orderby'=>'titledesc', 'from'=>'')));
					$t->AddEdit();
					$t->AddDelete();
					$t->AddMenuInsert();
					$q=new TQuery($tableprefix."filesystem");
					if ($_getvars['orderby']=='urldesc')
						$q->OrderBy('filename_fs DESC');
					elseif ($_getvars['orderby']=='titleasc')
						$q->OrderBy('comment_fs');
					elseif ($_getvars['orderby']=='titledesc')
						$q->OrderBy('comment_fs DESC');        
					else
						$q->OrderBy('filename_fs');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<$q->Count(); $i++)
						{
							if (substr($q->items[$i]['filename_fs'], -1) == '/')
								$t->Image('ico', 'folder.gif');
							else
								$t->Image('ico', 'file.gif');
							$t->Hint('ico', $q->items[$i]['id_fs']);
							$t->Label('url', $q->items[$i]['filename_fs']);
							$t->Label('title', empty($q->items[$i]['comment_fs'])?'-----':$q->items[$i]['comment_fs']);
							$t->URL('url', $q->items[$i]['filename_fs'], true);
							$t->URL('title', $q->items[$i]['url_fs'], true);
							$t->URL('edit', 'index.php?m=admin&d=editfilesystem&id='.$q->items[$i]['id_fs'].'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=admin&d=postdeletefilesystem&id='.$q->items[$i]['id_fs'].'&returnto='.urlencode(sm_this_url()));
							$t->Menu($q->items[$i]['comment_fs'], $q->items[$i]['filename_fs']);
							$t->NewRow();
						}
					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=admin&d=addfilesystem');
					$ui->AddButtons($b);
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->Output(true);
				}
			if (sm_action('postdeletefilesystem'))
				{
					$m['title'] = $lang['common']['delete'];
					$sql = "DELETE FROM ".$tableprefix."filesystem WHERE id_fs=".intval($_getvars["id"]);
					execsql($sql);
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m=admin&d=filesystem');
				}
			if (sm_action('postaddfilesystem', 'posteditfilesystem'))
				{
					$q=new TQuery($sm['t'].'filesystem');
					$q->Add('filename_fs', dbescape($_postvars['filename_fs']));
					$q->Add('url_fs', dbescape($_postvars['url_fs']));
					$q->Add('comment_fs', dbescape($_postvars['comment_fs']));
					if (sm_action('postaddfilesystem'))
						$q->Insert();
					else
						$q->Update('id_fs', intval($_getvars['id']));
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m=admin&d=filesystem');
				}
			if (sm_action('addfilesystem', 'editfilesystem'))
				{
					add_path_control();
					add_path($lang['module_admin']['virtual_filesystem'], 'index.php?m=admin&d=filesystem');
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->div($error, '', 'error alert-error');
					if (sm_action('editfilesystem'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m=admin&d=posteditfilesystem&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m=admin&d=postaddfilesystem&returnto='.urlencode($_getvars['returnto']));
						}
					$f->AddText('filename_fs', $lang['common']['url']);
					$f->AddText('url_fs', $lang['module_admin']['true_url']);
					$f->AddText('comment_fs', $lang['common']['comment']);
					if (sm_action('editfilesystem'))
						{
							$q=new TQuery($sm['t'].'filesystem');
							$q->Add('id_fs', intval($_getvars['id']));
							$f->LoadValuesArray($q->Get());
							unset($q);
						}
					if (is_array($_postvars))
						$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('filename_fs');
				}
			/*
			if (sm_action('filesystemexp'))
				{
					$m["title"] = $lang['module_admin']['virtual_filesystem_regexp'];
					sm_use('admintable');
					$m['table']['columns']['regexp']['caption'] = $lang['module_admin']['regexp'];
					$m['table']['columns']['regexp']['width'] = '50%';
					$m['table']['columns']['replace']['caption'] = $lang['module_admin']['regexp_replace'];
					$m['table']['columns']['replace']['width'] = '50%';
					$m['table']['columns']['edit']['caption'] = '';
					$m['table']['columns']['edit']['hint'] = $lang['common']['edit'];
					$m['table']['columns']['edit']['replace_text'] = $lang['common']['edit'];
					$m['table']['columns']['edit']['replace_image'] = 'edit.gif';
					$m['table']['columns']['edit']['width'] = '16';
					$m['table']['columns']['delete']['caption'] = '';
					$m['table']['columns']['delete']['hint'] = $lang['common']['delete'];
					$m['table']['columns']['delete']['replace_text'] = $lang['common']['delete'];
					$m['table']['columns']['delete']['replace_image'] = 'delete.gif';
					$m['table']['columns']['delete']['width'] = '16';
					$m['table']['columns']['delete']['messagebox'] = 1;
					$m['table']['columns']['delete']['messagebox_text'] = addslashes($lang['module_admin']['really_want_delete_filesystem_item']);
					$m['table']['default_column'] = 'edit';
					$sql = "SELECT * FROM ".$tableprefix."filesystem_regexp ORDER BY regexpr ASC";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m['table']['rows'][$i]['regexp']['data'] = $row->regexpr;
							$m['table']['rows'][$i]['replace']['data'] = $row->url;
							$m['table']['rows'][$i]['edit']['url'] = 'index.php?m=admin&d=editfilesystemexp&id='.$row->id_fsr;
							$m['table']['rows'][$i]['delete']['url'] = 'index.php?m=admin&d=postdeletefilesystemexp&id='.$row->id_fsr;
							$i++;
						}
				}
			if (sm_action('postdeletefilesystemexp'))
				{
					$m['title'] = $lang['common']['delete'];
					$sql = "DELETE FROM ".$tableprefix."filesystem_regexp WHERE id_fsr='".intval($_getvars["id"])."'";
					$result = execsql($sql);
					sm_redirect('index.php?m=admin&d=filesystemexp');
				}
			if (sm_action('addfilesystemexp'))
				{
					$m['title'] = $lang['common']['add'];
					add_path($lang['control_panel'], "index.php?m=admin");
					add_path($lang['module_admin']['virtual_filesystem_regexp'], "index.php?m=admin&d=filesystemexp");
					sm_use('adminform');
					$f = new TForm('index.php?m=admin&d=postaddfilesystemexp');
					$f->AddText('regexpr', $lang['module_admin']['regexp']);
					$f->AddText('url', $lang['module_admin']['regexp_replace']);
					$m['form'] = $f->Output();
				}
			if (sm_action('editfilesystemexp'))
				{
					$m['title'] = $lang['common']['edit'];
					add_path($lang['control_panel'], "index.php?m=admin");
					add_path($lang['module_admin']['virtual_filesystem_regexp'], "index.php?m=admin&d=filesystemexp");
					sm_use('adminform');
					$f = new TForm('index.php?m=admin&d=posteditfilesystemexp&id='.$_getvars['id']);
					$f->AddText('regexpr', $lang['module_admin']['regexp']);
					$f->AddText('url', $lang['module_admin']['regexp_replace']);
					$f->LoadValues("SELECT * FROM ".$tableprefix.'filesystem_regexp WHERE id_fsr='.intval($_getvars['id']));
					$m['form'] = $f->Output();
				}
			if (sm_action('posteditfilesystemexp'))
				{
					$m['title'] = $lang['common']['edit'];
					$q = new TQuery($tableprefix.'filesystem_regexp');
					$q->AddPost('regexpr');
					$q->AddPost('url');
					$q->Update('id_fsr', intval($_getvars['id']));
					sm_redirect('index.php?m=admin&d=filesystemexp');
				}
			if (sm_action('postaddfilesystemexp'))
				{
					$m['title'] = $lang['common']['add'];
					$q = new TQuery($tableprefix.'filesystem_regexp');
					$q->AddPost('regexpr');
					$q->AddPost('url');
					$q->Insert();
					sm_redirect('index.php?m=admin&d=filesystemexp');
				}
			*/
			if (sm_action('viewlog'))
				{
					add_path_control();
					add_path($lang['module_admin']['view_log'], 'index.php?m=admin&d=viewlog');
					if (intval($_settings['log_store_days'])>0)
						{
							$q = new TQuery($sm['t'].'log');
							$q->Add('object_name', 'system');
							$q->Add('time<'.(time()-intval($_settings['log_store_days'])*3600*24));
							$q->Remove();
						}
					sm_title($lang['module_admin']['view_log']);
					sm_use('admintable');
					sm_use('admininterface');
					$limit=100;
					$offset=abs(intval($_getvars['from']));
					$ui = new TInterface();
					$t=new TGrid();
					$t->AddCol('time', $lang['common']['time'], '20%');
					$t->AddCol('description', $lang['description']['description'], '60%');
					$t->AddCol('ip', 'IP', '10%');
					$t->AddCol('user', $lang['user'], '10%');
					$q=new TQuery($sm['t']."log");
					$q->Add('object_name', dbescape('system'));
					$q->OrderBy('id_log DESC');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<count($q->items); $i++)
						{
							$t->Label('time', strftime($lang["datetimemask"], $q->items[$i]['time']));
							$t->Label('description', htmlescape($q->items[$i]['description']));
							$t->Label('ip', inet_ntop($q->items[$i]['ip']));
							$t->Label('user', $q->items[$i]['user']);
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->Output(true);
				}
			if (sm_action('postpackage') && $_settings['packages_upload_allowed'])
				{
					if (empty($_getvars['typeupload']))
						{
							$fs = $_uplfilevars['userfile']['tmp_name'];
							$fd = basename($_uplfilevars['userfile']['name']);
							$fd = './'.$fd;
							$m['fs'] = $fs;
							$m['fd'] = $fd;
							if (!move_uploaded_file($fs, $fd))
								{
									$m['error_message'] = $lang['error_file_upload_message'];
									sm_set_action('package');
								}
						}
					elseif (function_exists('curl_init'))
						{
							$ch = curl_init($_postvars['urlupload']);
							if (file_exists('urlupload.zip'))
								unlink('urlupload.zip');
							$fp = fopen("urlupload.zip", "w");
							curl_setopt($ch, CURLOPT_FILE, $fp);
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_FAILONERROR, 1);
							curl_exec($ch);
							$tmperr = curl_error($ch);
							curl_close($ch);
							fclose($fp);
							if (!empty($tmperr))
								{
									$m['error_message'] = $tmperr;
									sm_set_action('package');
									unlink('urlupload.zip');
								}
							else
								$fd = 'urlupload.zip';
						}
					if (sm_action('postpackage'))
						{
							require_once('ext/package/unarchiver.php');
							$zip = new PclZip($fd);
							$ext = $zip->extract(PCLZIP_OPT_SET_CHMOD, 0777);
							unlink($fd);
							if (intval(sm_settings('ignore_update'))!=1)
								{
									if (file_exists('includes/update.php'))
										{
											include('includes/update.php');
											@unlink('includes/update.php');
											if (file_exists('includes/update.php') && empty($refresh_url))
												sm_update_settings('install_not_erased', 1);
										}
								}
							if (empty($refresh_url))
								sm_redirect('index.php?m=admin&d=view');
						}
				}
			if (sm_action('package') && $_settings['packages_upload_allowed'])
				{
					sm_title($lang['module_admin']['upload_package']);
					add_path_control();
					add_path_current();
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.tabs');
					$ui = new TInterface();
					if (!empty($m['error_message']))
						{
							$ui->AddBlock($lang['error']);
							$ui->NotificationError($m['error_message']);
						}
					$tabs=new TTabs();
					$tabs->AddBlock($lang['module_admin']['upload_package'].' ('.$lang['common']['file'].')');
					$f = new TForm('index.php?m=admin&d=postpackage');
					$f->AddFile('userfile', $lang['file_name']);
					$f->SaveButton($lang['upload']);
					$tabs->AddForm($f);
					if (function_exists('curl_init'))
						{
							$tabs->AddBlock($lang['module_admin']['upload_package'].' ('.$lang['common']['url'].')');
							$f = new TForm('index.php?m=admin&d=postpackage&typeupload=url');
							$f->AddText('urlupload', $lang['common']['url']);
							$f->SaveButton($lang['upload']);
							if ($_getvars['typeupload']=='url')
								{
									$tabs->SetActiveIndex(1);
									sm_setfocus('urlupload');
								}
							$tabs->AddForm($f);
						}
					$ui->Add($tabs);
					$ui->Output(true);
				}
			if (sm_actionpost('saverobotstxt'))
				{
					sm_update_settings('robots_txt', $_postvars['robotstxtcontent'], 'seo');
					sm_redirect('index.php?m=admin');
				}
			if (sm_action('robotstxt'))
				{
					add_path_control();
					add_path_current();
					sm_title('robots.txt');
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					$f = new TForm('index.php?m=admin&d=saverobotstxt');
					$f->AddTextarea('robotstxtcontent', '');
					$f->MergeColumns();
					$f->SetValue('robotstxtcontent', sm_get_settings('robots_txt', 'seo'));
					$ui->AddForm($f);
					$ui->Output(true);
				}
		}

?>
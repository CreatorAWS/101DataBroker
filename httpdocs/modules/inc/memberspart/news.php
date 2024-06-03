<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-05-01
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (sm_actionpost('postadd') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			$m['post'] = $_postvars;
			if ($userinfo['level'] < intval(sm_settings('news_editor_level')))
				$extsql = '('.convert_groups_to_sql($userinfo['groups'], 'groups_modify').') AND id_category='.intval($_postvars["p_id_category_n"]);
			else
				$extsql = '';
			$sql = "SELECT * FROM ".$tableprefix."categories_news";
			if (!empty($extsql))
				$sql .= ' WHERE '.$extsql;
			$result = execsql($sql);
			$i = 0;
			while ($row = database_fetch_object($result))
				{
					$m["ctgid"][$i][0] = $row->id_category;
					$m["ctgid"][$i][1] = $row->title_category;
					$i++;
				}
			if ($i > 0)
				{
					if (!checkdate($_postvars['p_date_month'], $_postvars['p_date_day'], $_postvars['p_date_year']))
						{
							$m['error_message'] = $lang['news_wrong_date_message'];
							$m['mode'] = 'add';
							$m['ctgidselected'] = $_postvars['p_id_category_n'];
							$m['date_news'] = time();
							$m['date'] = getdate($m['date_news']);
							$m['title_news'] = $_postvars['p_title_news'];
							$m['preview_news'] = $_postvars['p_preview_news'];
							$m['text_news'] = $_postvars['p_text_news'];
							$m['type_news'] = $_postvars['p_type_news'];
							$m['filename_news'] = $_postvars['p_filename'];
							$m['keywords_news'] = $_postvars['keywords_news'];
							$m['description_news'] = $_postvars['description_news'];
						}
					else
						{
							sm_event('startpostaddnews', array(0));
							$m["module"] = 'news';
							sm_title($lang['add_news']);
							$id_category_n = $_postvars["p_id_category_n"];
							$preview_news = dbescape($_postvars['p_preview_news']);
							$text_news = dbescape($_postvars["p_text_news"]);
							$title_news = dbescape($_postvars['p_title_news']);
							$m['date'] = getdate(time());
							$type_news = $_postvars["p_type_news"];
							$filename = dbescape($_postvars["p_filename"]);
							$keywords_news = dbescape($_postvars["keywords_news"]);
							$description_news = dbescape($_postvars["description_news"]);
							$img_copyright_news = dbescape($_postvars["img_copyright_news"]);
							if ($_settings['news_use_time'])
								{
									$m['date']['hours'] = $_postvars['p_time_hours'];
									$m['date']['minutes'] = $_postvars['p_time_minutes'];
								}
							$date_news = mktime($m['date']['hours'], $m['date']['minutes'], $m['date']['seconds'], $_postvars['p_date_month'], $_postvars['p_date_day'], $_postvars['p_date_year']);
							$sql = "INSERT INTO ".$tableprefix."news (id_category_n, date_news, title_news, preview_news, text_news, type_news, keywords_news, description_news, id_author_news, img_copyright_news) VALUES ('$id_category_n', '$date_news', '$title_news', '$preview_news', '$text_news', '$type_news', '$keywords_news', '$description_news', '".intval($userinfo['id'])."', '$img_copyright_news')";
							$id_news = insertsql($sql);
							sm_set_metadata('news', $id_news, 'author_id', $sm['u']['id']);
							sm_set_metadata('news', $id_news, 'news_template', $_postvars['tplnews']);
							if ($_settings['news_use_image'] == 1)
								{
									if ($_settings['image_generation_type'] == 'static' && file_exists($_uplfilevars['userfile']['tmp_name']))
										{
											include_once('includes/smcoreext.php');
											move_uploaded_file($_uplfilevars['userfile']['tmp_name'], 'files/temp/news'.$id_news.'.jpg');
											sm_resizeimage(
												'files/temp/news'.$id_news.'.jpg',
												'files/thumb/news'.$id_news.'.jpg',
												$_settings['news_image_preview_width'],
												$_settings['news_image_preview_height'],
												0, 100, 1);
											sm_resizeimage(
												'files/temp/news'.$id_news.'.jpg',
												'files/fullimg/news'.$id_news.'.jpg',
												$_settings['news_image_fulltext_width'],
												$_settings['news_image_fulltext_height'],
												0, 100, 1);
											unlink('files/temp/news'.$id_news.'.jpg');
										}
									else
										{
											siman_upload_image($id_news, 'news');
										}
								}
							if (!empty($filename))
								{
									$nid = $id_news;
									$title_news2 = (empty($title_news)) ? $_postvars['p_date_day'].'.'.$_postvars['p_date_month'].'.'.$_postvars['p_date_year'] : $title_news;
									$urlid = register_filesystem('index.php?m=news&d=view&nid='.$nid, $filename, $title_news2);
									$sql = "UPDATE ".$tableprefix."news SET filename_news='$urlid' WHERE id_news=".$nid;
									$result = execsql($sql);
								}
							for ($i = 0; $i < $_settings['news_attachments_count']; $i++)
								{
									sm_upload_attachment('news', $id_news, $_uplfilevars['attachment'.$i]);
								}
							if ($userinfo['level'] == 3)
								sm_redirect('index.php?m=news&d=list&ctg='.$id_category_n);
							else
								sm_redirect('index.php?m=news&d=listnews&ctg='.$id_category_n);
							sm_notify($lang['add_news_successful']);
							sm_event('postaddnews', array($id_news));
						}
				}
		}


	if (sm_action('add') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			if ($userinfo['level'] < intval(sm_settings('news_editor_level')))
				$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
			else
				$extsql = '';
			$sql = "SELECT * FROM ".$tableprefix."categories_news";
			if (!empty($extsql))
				$sql .= ' WHERE '.$extsql;
			$m["ctgidselected"] = intval($_getvars['ctg']);
			$result = execsql($sql);
			$i = 0;
			while ($row = database_fetch_object($result))
				{
					$m["ctgid"][$i][0] = $row->id_category;
					$m["ctgid"][$i][1] = $row->title_category;
					$i++;
				}
			if ($i > 0)
				{
					$m["module"] = 'news';
					if (!empty($_settings['ext_editor']) && $_getvars['exteditor'] != 'off')
						{
							$special['ext_editor_on'] = 1;
						}
					else
						{
							$m['type_news'] = $_settings['default_news_text_style'];
						}
					sm_title($lang['add_news']);
					add_path($lang['control_panel'], "index.php?m=admin");
					add_path($lang['modules_mamagement'], "index.php?m=admin&d=modules");
					add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
					add_path($lang['module_news']['list_news'], "index.php?m=news&d=list");
					$m['date'] = getdate(time());
					$m['images'] = load_file_list('./files/img/', 'jpg|gif|jpeg|png');
					if (count($sm['themeinfo']['alttpl']['news'])>0)
						{
							$m['alttpl']['news']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
							for ($i = 0; $i < count($sm['themeinfo']['alttpl']['news']); $i++)
								$m['alttpl']['news'][]=$sm['themeinfo']['alttpl']['news'][$i];
						}
					sm_setfocus('title_news');
					sm_event('onaddnews', array($m['date']));
				}
		}


	if (sm_action('delete') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			$candelete = 0;
			if ($userinfo['level']>=intval(sm_settings('news_editor_level')))
				{
					$candelete = 1;
				}
			elseif (!empty($userinfo['groups']))
				{
					$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."categories_news.* FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE ".$tableprefix."news.id_category_n=".$tableprefix."categories_news.id_category AND id_news='".intval($_getvars["nid"])."'";
					$sql .= ' AND '.$extsql;
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$candelete = 1;
						}
				}
			if ($candelete == 1)
				{
					$m["module"] = 'news';
					$_msgbox['mode'] = 'yesno';
					$_msgbox['title'] = $lang['delete_news'];
					$_msgbox['msg'] = $lang['really_want_delete_news'];
					$_msgbox['yes'] = 'index.php?m=news&d=postdelete&nid='.$_getvars["nid"].'&ctg='.$_getvars['ctg'];
					if ($userinfo['level'] == 3)
						$_msgbox['no'] = 'index.php?m=news&d=list';
					else
						$_msgbox['no'] = 'index.php?m=news&d=view&nid='.$_getvars["nid"];
				}
		}

	if (sm_action('postdelete') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			$candelete = 0;
			if ($userinfo['level']>=intval(sm_settings('news_editor_level')))
				{
					$candelete = 1;
				}
			elseif (!empty($userinfo['groups']))
				{
					$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."categories_news.* FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE ".$tableprefix."news.id_category_n=".$tableprefix."categories_news.id_category AND id_news='".intval($_getvars["nid"])."'";
					$sql .= ' AND '.$extsql;
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$candelete = 1;
						}
				}
			if ($candelete == 1)
				{
					sm_title($lang['delete_news']);
					$m["module"] = 'news';
					$id_news = intval($_getvars["nid"]);
					$sql = "SELECT * FROM ".$tableprefix."news WHERE id_news=".intval($id_news);
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$fname = $row->filename_news;
						}
					$sql = "DELETE FROM ".$tableprefix."news WHERE id_news=".intval($id_news);
					$result = execsql($sql);
					sm_extcore();
					sm_saferemove('index.php?m=news&d=view&nid='.$id_news);
					if ($fname != 0)
						{
							delete_filesystem($fname);
						}
					sm_delete_attachments('news', intval($id_news));
					if (file_exists('files/thumb/news'.$id_news.'.jpg'))
						unlink('files/thumb/news'.$id_news.'.jpg');
					if (file_exists('files/fullimg/news'.$id_news.'.jpg'))
						unlink('files/fullimg/news'.$id_news.'.jpg');
					if (file_exists('files/img/news'.$id_news.'.jpg'))
						unlink('files/img/news'.$id_news.'.jpg');
					sm_notify($lang['delete_news_successful']);
					sm_event('onnewsdeleted', array($id_news));
					if ($userinfo['level'] == 3)
						sm_redirect('index.php?m=news&d=list&ctg='.intval($_getvars['ctg']));
					else
						sm_redirect('index.php?m=news&d=listnews&ctg='.intval($_getvars['ctg']));
				}
		}


	if (sm_actionpost('postedit') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			$canedit = 0;
			if ($userinfo['level']>=intval(sm_settings('news_editor_level')))
				{
					$canedit = 1;
				}
			elseif (!empty($userinfo['groups']))
				{
					$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."categories_news.* FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE ".$tableprefix."news.id_category_n=".$tableprefix."categories_news.id_category AND id_news='".intval($_postvars["p_id_category_n"])."'";
					$sql .= ' AND '.$extsql;
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$canedit = 1;
						}
				}
			if ($canedit == 1)
				{
					if (!checkdate($_postvars['p_date_month'], $_postvars['p_date_day'], $_postvars['p_date_year']))
						{
							$m['error_message'] = $lang['news_wrong_date_message'];
							$m['mode'] = 'edit';
							$m['ctgidselected'] = $_postvars['p_id_category_n'];
							$m['date_news'] = $_postvars['p_date_prev'];
							$m['date'] = getdate($m['date_news']);
							$m['title_news'] = $_postvars['p_title_news'];
							$m['preview_news'] = $_postvars['p_preview_news'];
							$m['text_news'] = $_postvars['p_text_news'];
							$m['type_news'] = $_postvars['p_type_news'];
							$m["id_news"] = $_getvars["nid"];
							$m['filename_news'] = $_postvars['p_filename'];
							$m['keywords_news'] = $_postvars['keywords_news'];
							$m['description_news'] = $_postvars['description_news'];
						}
					else
						{
							$id_news = intval($_getvars["nid"]);
							sm_event('startposteditnews', array($id_news));
							sm_title($lang['edit_news']);
							$m["module"] = 'news';
							$id_category_n = $_postvars["p_id_category_n"];
							$m['date'] = getdate($_postvars["p_date_prev"]);
							if ($_settings['news_use_time'])
								{
									$m['date']['hours'] = $_postvars['p_time_hours'];
									$m['date']['minutes'] = $_postvars['p_time_minutes'];
								}
							$date_news = mktime($m['date']['hours'], $m['date']['minutes'], $m['date']['seconds'], $_postvars['p_date_month'], $_postvars['p_date_day'], $_postvars['p_date_year']);
							$title_news = dbescape($_postvars['p_title_news']);
							$preview_news = dbescape($_postvars['p_preview_news']);
							$text_news = dbescape($_postvars["p_text_news"]);
							$type_news = dbescape($_postvars["p_type_news"]);
							$filename = dbescape($_postvars["p_filename"]);
							$keywords_news = dbescape($_postvars["keywords_news"]);
							$description_news = dbescape($_postvars["description_news"]);
							$sql = "UPDATE ".$tableprefix."news SET id_category_n='$id_category_n', title_news='$title_news', preview_news='$preview_news', text_news='$text_news', type_news='$type_news', date_news='$date_news', keywords_news='$keywords_news', description_news='$description_news' WHERE id_news='".$id_news."'";
							$result = execsql($sql);
							if ($_settings['news_use_image'] == 1)
								{
									if ($_settings['image_generation_type'] == 'static' && file_exists($_uplfilevars['userfile']['tmp_name']))
										{
											include_once('includes/smcoreext.php');
											move_uploaded_file($_uplfilevars['userfile']['tmp_name'], 'files/temp/news'.$id_news.'.jpg');
											sm_resizeimage(
												'files/temp/news'.$id_news.'.jpg',
												'files/thumb/news'.$id_news.'.jpg',
												$_settings['news_image_preview_width'],
												$_settings['news_image_preview_height'],
												0, 100, 1);
											sm_resizeimage(
												'files/temp/news'.$id_news.'.jpg',
												'files/fullimg/news'.$id_news.'.jpg',
												$_settings['news_image_fulltext_width'],
												$_settings['news_image_fulltext_height'],
												0, 100, 1);
											unlink('files/temp/news'.$id_news.'.jpg');
										}
									else
										{
											siman_upload_image($id_news, 'news');
										}
								}
							sm_set_metadata('news', $id_news, 'news_template', $_postvars['tplnews']);
							$title_news2 = (empty($title_news)) ? $_postvars['p_date_day'].'.'.$_postvars['p_date_month'].'.'.$_postvars['p_date_year'] : $title_news;
							$sql = "SELECT * FROM ".$tableprefix."news WHERE id_news='".$id_news."'";
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$fname = $row->filename_news;
								}
							if ($fname == 0 && !empty($filename))
								{
									$urlid = register_filesystem('index.php?m=news&d=view&nid='.$id_news, $filename, $title_news2);
									$sql = "UPDATE ".$tableprefix."news SET filename_news='$urlid' WHERE id_news=".$id_news;
									$result = execsql($sql);
								}
							else
								{
									if (empty($filename))
										{
											$sql = "UPDATE ".$tableprefix."news SET filename_news='0' WHERE id_news=".$id_news;
											$result = execsql($sql);
											delete_filesystem($fname);
										}
									else
										update_filesystem($fname, 'index.php?m=news&d=view&nid='.$id_news, $filename, $title_news2);
								}
							for ($i = 0; $i < $_settings['news_attachments_count']; $i++)
								{
									sm_upload_attachment('news', $id_news, $_uplfilevars['attachment'.$i]);
								}
							if ($userinfo['level'] == 3)
								sm_redirect('index.php?m=news&d=list&ctg='.$id_category_n);
							else
								sm_redirect('index.php?m=news&d=listnews&ctg='.$id_category_n);
							sm_notify($lang['edit_news_successful']);
							sm_event('posteditnews', array($id_news));
						}
				}
		}


	if (sm_action('edit') && ($userinfo['level']>=intval(sm_settings('news_editor_level')) || !empty($userinfo['groups'])))
		{
			$canedit = 0;
			if ($userinfo['level']>=intval(sm_settings('news_editor_level')))
				{
					$canedit = 1;
				}
			elseif (!empty($userinfo['groups']))
				{
					$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."categories_news.* FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE ".$tableprefix."news.id_category_n=".$tableprefix."categories_news.id_category AND id_news='".intval($_getvars["nid"])."'";
					$sql .= ' AND '.$extsql;
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$canedit = 1;
						}
				}
			if ($canedit == 1)
				{
					if (!empty($_settings['ext_editor']) && $_getvars['exteditor'] != 'off')
						{
							$special['ext_editor_on'] = 1;
							require_once('ext/editors/'.$_settings['ext_editor'].'/siman_config.php');
						}
					sm_title($lang['edit_news']);
					$m["module"] = 'news';
					$sql = "SELECT * FROM ".$tableprefix."categories_news";
					if (!empty($extsql))
						$sql .= ' WHERE '.$extsql;
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m["ctgid"][$i][0] = $row->id_category;
							$m["ctgid"][$i][1] = $row->title_category;
							$i++;
						}
					$sql = "SELECT * FROM ".$tableprefix."news WHERE id_news='".intval($_getvars["nid"])."' LIMIT 1";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_array($result))
						{
							$m["post"] = $row;
							$m["type_news"] = $row['type_news'];
							if ($m["type_news"] != 1)
								$special['ext_editor_on'] = 0;
							$m["ctgidselected"] = $row['id_category_n'];
							$m["date_news"] = $row['date_news'];
							$m['date'] = getdate($m['date_news']);
							$m["title_news"] = htmlescape($row['title_news']);
							$m["preview_news"] = $row['preview_news'];
							$m["text_news"] = $row['text_news'];
							$m["filename_news"] = get_filename($row['filename_news']);
							$m["keywords_news"] = $row['keywords_news'];
							$m["description_news"] = $row['description_news'];
							if ($special['ext_editor_on'] == 1)
								{
									$m["text_news"] = siman_prepare_to_exteditor($m["text_news"]);
									$m["preview_news"] = siman_prepare_to_exteditor($m["preview_news"]);
								}
							$m["id_news"] = $row['id_news'];
							$m['attachments'] = sm_get_attachments('news', $row['id_news']);
							$i++;
						}
					if (!empty($m["id_news"]))
						{
							if (count($sm['themeinfo']['alttpl']['news'])>0)
								{
									$m['alttpl']['news']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
									for ($i = 0; $i < count($sm['themeinfo']['alttpl']['news']); $i++)
										$m['alttpl']['news'][]=$sm['themeinfo']['alttpl']['news'][$i];
								}
							$tmp=sm_load_metadata('news', $m["id_news"]);
							if (!isset($sm['p']['tplnews']))
								$sm['p']['tplnews']=$tmp['news_template'];
							sm_event('oneditnews', array($m["id_news"]));
						}
				}
			$m['images'] = load_file_list('./files/img/', 'jpg|gif|jpeg|png');
			add_path($lang['control_panel'], "index.php?m=admin");
			add_path($lang['modules_mamagement'], "index.php?m=admin&d=modules");
			add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
			add_path($lang['module_news']['list_news'], "index.php?m=news&d=list&ctg=".$m['ctgidselected']."");
		}

	if ($userinfo['level'] == 3)
		include('modules/inc/adminpart/news.php');

?>
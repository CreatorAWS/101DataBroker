<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-10-14
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if ($userinfo['level'] > 0)
		{

			if (sm_action('add') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level'] < intval(sm_settings('content_editor_level')))
						$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					else
						$extsql = '';
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0)
						{
							sm_title($lang['add_content']);
							$m["module"] = 'content';
							add_path($lang['control_panel'], "index.php?m=admin");
							add_path($lang['modules_mamagement'], "index.php?m=admin&d=modules");
							add_path($lang['module_content_name'], "index.php?m=content&d=admin");
							add_path($lang['list_content'], "index.php?m=content&d=list");
							if (!empty($_settings['ext_editor']) && $_getvars['exteditor'] != 'off')
								{
									$special['ext_editor_on'] = 1;
								}
							$m['ctgidselected'] = intval($_getvars['ctg']);
							$m['images'] = load_file_list('./files/img/', 'jpg|gif|jpeg|png');
							if (count($sm['themeinfo']['alttpl']['main'])>0)
								{
									$m['alttpl']['main']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
									for ($i = 0; $i < count($sm['themeinfo']['alttpl']['main']); $i++)
										$m['alttpl']['main'][]=$sm['themeinfo']['alttpl']['main'][$i];
								}
							if (count($sm['themeinfo']['alttpl']['content'])>0)
								{
									$m['alttpl']['content']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
									for ($i = 0; $i < count($sm['themeinfo']['alttpl']['content']); $i++)
										$m['alttpl']['content'][]=$sm['themeinfo']['alttpl']['content'][$i];
								}
							sm_event('onaddcontent', array($m['selected_ctg']));
						}
				}

			if (sm_action('postadd') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level']<intval(sm_settings('content_editor_level')))
						$extsql = '('.convert_groups_to_sql($userinfo['groups'], 'groups_modify').') AND id_category='.intval($_postvars["p_id_category_c"]);
					else
						$extsql = '';
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0)
						{
							sm_event('startpostaddcontent', array(0));
							$id_category_c = $_postvars["p_id_category_c"];
							$title_content = dbescape($_postvars["p_title_content"]);
							$preview_content = dbescape($_postvars["p_preview_content"]);
							$text_content = dbescape($_postvars["p_text_content"]);
							$type_content = $_postvars["p_type_content"];
							$keywords_content = dbescape($_postvars["p_keywords_content"]);
							$description_content = dbescape($_postvars["p_description_content"]);
							$filename = dbescape($_postvars["p_filename"]);
							$refuse_direct_show = intval($_postvars["p_refuse_direct_show"]);
							$sql = "INSERT INTO ".$tableprefix."content (id_category_c, title_content, preview_content, text_content, type_content, keywords_content, refuse_direct_show, description_content) VALUES ('$id_category_c', '$title_content', '$preview_content', '$text_content', '$type_content', '$keywords_content', '$refuse_direct_show', '$description_content')";
							$cid = insertsql($sql);
							sm_set_metadata('content', $cid, 'author_id', $sm['u']['id']);
							sm_set_metadata('content', $cid, 'main_template', $_postvars['tplmain']);
							sm_set_metadata('content', $cid, 'content_template', $_postvars['tplcontent']);
							if (!empty($filename))
								{
									$urlid = register_filesystem('index.php?m=content&d=view&cid='.$cid, $filename, $title_content);
									$sql = "UPDATE ".$tableprefix."content SET filename_content='$urlid' WHERE id_content=".$cid;
									$result = execsql($sql);
								}
							$sql = "UPDATE ".$tableprefix."content SET priority_content='$cid' WHERE id_content=".$cid;
							$result = execsql($sql);
							for ($i = 0; $i < $_settings['content_attachments_count']; $i++)
								{
									sm_upload_attachment('content', $cid, $_uplfilevars['attachment'.$i]);
								}
							if ($_settings['content_use_image'] == 1)
								{
									if ($_settings['image_generation_type'] == 'static' && file_exists($_uplfilevars['userfile']['tmp_name']))
										{
											include_once('includes/smcoreext.php');
											move_uploaded_file($_uplfilevars['userfile']['tmp_name'], 'files/temp/content'.$cid.'.jpg');
											sm_resizeimage(
												'files/temp/content'.$cid.'.jpg',
												'files/thumb/content'.$cid.'.jpg',
												$_settings['content_image_preview_width'],
												$_settings['content_image_preview_height'],
												0, 100, 1);
											sm_resizeimage(
												'files/temp/content'.$cid.'.jpg',
												'files/fullimg/content'.$cid.'.jpg',
												$_settings['content_image_fulltext_width'],
												$_settings['content_image_fulltext_height'],
												0, 100, 1);
											unlink('files/temp/content'.$cid.'.jpg');
										}
									else
										{
											siman_upload_image($cid, 'content');
										}
								}
							sm_notify($lang['add_content_successful']);
							if ($userinfo['level'] < 3)
								sm_redirect('index.php?m=content&d=viewctg&ctgid='.$id_category_c);
							else
								sm_redirect('index.php?m=content&d=list&ctg='.$id_category_c);
							sm_event('postaddcontent', array($cid));
						}
				}

			if (sm_actionpost('postedit') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level']<intval(sm_settings('content_editor_level')))
						$extsql = '('.convert_groups_to_sql($userinfo['groups'], 'groups_modify').') AND id_category='.intval($_postvars["p_id_category_c"]);
					else
						{
							$extsql = '';
							$canedit = 1;
						}
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0 && $canedit != 1)
						{
							$sql = "SELECT * FROM ".$tableprefix."content  LEFT JOIN ".$tableprefix."categories ON ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category  WHERE id_content='".intval($_getvars["cid"])."'";
							$sql .= " AND (".$extsql.')';
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$canedit = 1;
								}
						}
					if ($canedit == 1)
						{
							sm_event('startposteditcontent', array(intval($_getvars["cid"])));
							$id_category_c = $_postvars["p_id_category_c"];
							$title_content = dbescape($_postvars["p_title_content"]);
							$preview_content = dbescape($_postvars["p_preview_content"]);
							$text_content = dbescape($_postvars["p_text_content"]);
							$type_content = dbescape($_postvars["p_type_content"]);
							$keywords_content = dbescape($_postvars["p_keywords_content"]);
							$description_content = dbescape($_postvars["p_description_content"]);
							$filename = dbescape($_postvars["p_filename"]);
							$refuse_direct_show = intval($_postvars["p_refuse_direct_show"]);
							if ($_settings['content_use_preview'] == 1)
								$tmp_preview_sql = "preview_content='$preview_content',";
							$sql = "UPDATE ".$tableprefix."content SET id_category_c='".intval($id_category_c)."', title_content='$title_content', $tmp_preview_sql text_content='$text_content', type_content='$type_content', keywords_content = '$keywords_content', refuse_direct_show = '$refuse_direct_show', description_content='$description_content' WHERE id_content='".intval($_getvars["cid"])."'";
							$result = execsql($sql);
							if ($_settings['content_use_image'] == 1)
								{
									$id_content = intval($_getvars["cid"]);
									if ($_settings['image_generation_type'] == 'static' && file_exists($_uplfilevars['userfile']['tmp_name']))
										{
											include_once('includes/smcoreext.php');
											move_uploaded_file($_uplfilevars['userfile']['tmp_name'], 'files/temp/content'.$id_content.'.jpg');
											sm_resizeimage(
												'files/temp/content'.$id_content.'.jpg',
												'files/thumb/content'.$id_content.'.jpg',
												$_settings['content_image_preview_width'],
												$_settings['content_image_preview_height'],
												0, 100, 1);
											sm_resizeimage(
												'files/temp/content'.$id_content.'.jpg',
												'files/fullimg/content'.$id_content.'.jpg',
												$_settings['content_image_fulltext_width'],
												$_settings['content_image_fulltext_height'],
												0, 100, 1);
											unlink('files/temp/content'.$id_content.'.jpg');
										}
									else
										{
											siman_upload_image($id_content, 'content');
										}
								}
							$sql = "SELECT * FROM ".$tableprefix."content WHERE id_content='".intval($_getvars["cid"])."'";
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$fname = $row->filename_content;
								}
							if ($fname == 0 && !empty($filename))
								{
									$urlid = register_filesystem('index.php?m=content&d=view&cid='.$_getvars["cid"], $filename, $title_content);
									$sql = "UPDATE ".$tableprefix."content SET filename_content='$urlid' WHERE id_content=".intval($_getvars["cid"]);
									$result = execsql($sql);
								}
							else
								{
									if (empty($filename))
										{
											$sql = "UPDATE ".$tableprefix."content SET filename_content='0' WHERE id_content=".intval($_getvars["cid"]);
											$result = execsql($sql);
											delete_filesystem($fname);
										}
									else
										update_filesystem($fname, 'index.php?m=content&d=view&cid='.intval($_getvars["cid"]), $filename, $title_content);
								}
							$result = execsql($sql);
							sm_set_metadata('content', intval($_getvars["cid"]), 'main_template', $_postvars['tplmain']);
							sm_set_metadata('content', intval($_getvars["cid"]), 'content_template', $_postvars['tplcontent']);
							for ($i = 0; $i < $_settings['content_attachments_count']; $i++)
								{
									sm_upload_attachment('content', intval($_getvars["cid"]), $_uplfilevars['attachment'.$i]);
								}
							sm_notify($lang['edit_content_successful']);
							if ($userinfo['level'] < 3)
								sm_redirect('index.php?m=content&d=viewctg&ctgid='.$id_category_c);
							else
								sm_redirect('index.php?m=content&d=list&ctg='.$id_category_c);
							sm_event('posteditcontent', array(intval($_getvars["cid"])));
						}
				}

			if (sm_action('edit') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level']<intval(sm_settings('content_editor_level')))
						$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					else
						$extsql = '';
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0)
						{
							if (!empty($_settings['ext_editor']) && $_getvars['exteditor'] != 'off')
								{
									$special['ext_editor_on'] = 1;
									require_once('ext/editors/'.$_settings['ext_editor'].'/siman_config.php');
								}
							$sql = "SELECT * FROM ".$tableprefix."content  LEFT JOIN ".$tableprefix."categories ON ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category  WHERE id_content='".intval($_getvars["cid"])."'";
							if (!empty($extsql))
								$sql .= " AND (".$extsql.')';
							$result = execsql($sql);
							$i = 0;
							while ($row = database_fetch_object($result))
								{
									$m["type_content"] = $row->type_content;
									if ($m["type_content"] != 1)
										$special['ext_editor_on'] = 0;
									$m["title_content"] = htmlescape($row->title_content);
									$m["keywords_content"] = htmlescape($row->keywords_content);
									$m["description_content"] = htmlescape($row->description_content);
									$m["ctgidselected"] = $row->id_category_c;
									if ($special['ext_editor_on'] != 1)
										{
											$m["preview_content"] = htmlescape($row->preview_content);
											$m["text_content"] = htmlescape($row->text_content);
										}
									else
										{
											$m["preview_content"] = $row->preview_content;
											$m["text_content"] = $row->text_content;
										}
									if ($special['ext_editor_on'] == 1)
										{
											$m["text_content"] = siman_prepare_to_exteditor($m["text_content"]);
											$m["preview_content"] = siman_prepare_to_exteditor($m["preview_content"]);
										}
									$m["id_content"] = $row->id_content;
									if (!empty($row->filename_content))
										{
											$m['filesystem'] = get_filesystem($row->filename_content);
											$m["filename_content"] = $m['filesystem']['filename'];
										}
									$m['refuse_direct_show'] = $row->refuse_direct_show;
									$m['attachments'] = sm_get_attachments('content', $row->id_content);
									$i++;
								}
							sm_title($lang['edit_content']);
							add_path($lang['control_panel'], "index.php?m=admin");
							add_path($lang['modules_mamagement'], "index.php?m=admin&d=modules");
							add_path($lang['module_content_name'], "index.php?m=content&d=admin");
							add_path($lang['list_content'], "index.php?m=content&d=list&ctg=".$modules[$modules_index]['ctgidselected']."");
							if ($i > 0)
								$m["module"] = 'content';
							$m['images'] = load_file_list('./files/img/', 'jpg|gif|jpeg|png');
							//$m["ctgid"]=siman_load_ctgs_content();
							if (count($sm['themeinfo']['alttpl']['main'])>0)
								{
									$m['alttpl']['main']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
									for ($i = 0; $i < count($sm['themeinfo']['alttpl']['main']); $i++)
										$m['alttpl']['main'][]=$sm['themeinfo']['alttpl']['main'][$i];
								}
							if (count($sm['themeinfo']['alttpl']['content'])>0)
								{
									$m['alttpl']['content']=Array(Array('tpl'=>'', 'name'=>$lang['common']['default']));
									for ($i = 0; $i < count($sm['themeinfo']['alttpl']['content']); $i++)
										$m['alttpl']['content'][]=$sm['themeinfo']['alttpl']['content'][$i];
								}
							$tmp=sm_load_metadata('content', intval($_getvars["cid"]));
							if (!isset($sm['p']['tplmain']))
								$sm['p']['tplmain']=$tmp['main_template'];
							if (!isset($sm['p']['tplcontent']))
								$sm['p']['tplcontent']=$tmp['content_template'];
							if (!empty($m["id_content"]))
								sm_event('oneditcontent', array($m["id_content"]));
						}
				}

			if (sm_action('delete') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level']<intval(sm_settings('content_editor_level')))
						$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					else
						{
							$extsql = '';
							$candelete = 1;
						}
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0 && $candelete != 1)
						{
							$sql = "SELECT * FROM ".$tableprefix."content  LEFT JOIN ".$tableprefix."categories ON ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category  WHERE id_content='".intval($_getvars["cid"])."'";
							$sql .= " AND (".$extsql.')';
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$candelete = 1;
								}
						}
					if ($candelete == 1)
						{
							$m["module"] = 'content';
							$_msgbox['mode'] = 'yesno';
							$_msgbox['title'] = $lang['delete_content'];
							$_msgbox['msg'] = $lang['module_content']['really_want_delete_text'];
							$_msgbox['yes'] = 'index.php?m=content&d=postdelete&cid='.$_getvars["cid"].'&ctg='.$_getvars['ctg'];
							if ($userinfo['level'] < 3)
								$_msgbox['no'] = 'index.php?m=content&d=viewctg&ctgid='.$_getvars['ctg'];
							else
								$_msgbox['no'] = 'index.php?m=content&d=list&ctg='.$_getvars['ctg'];
						}
				}
			if (sm_action('postdelete') && ($userinfo['level']>=intval(sm_settings('content_editor_level')) || !empty($userinfo['groups'])))
				{
					if ($userinfo['level']<intval(sm_settings('content_editor_level')))
						$extsql = convert_groups_to_sql($userinfo['groups'], 'groups_modify');
					else
						{
							$extsql = '';
							$candelete = 1;
						}
					$m['ctgid'] = siman_load_ctgs_content(-1, $extsql);
					if (count($m['ctgid']) > 0 && $candelete != 1)
						{
							$sql = "SELECT * FROM ".$tableprefix."content  LEFT JOIN ".$tableprefix."categories ON ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category  WHERE id_content='".intval($_getvars["cid"])."'";
							$sql .= " AND (".$extsql.')';
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$candelete = 1;
								}
						}
					if ($candelete == 1)
						{
							sm_title($lang['delete_content']);
							$m["module"] = 'content';
							$fname=0;
							$sql = "SELECT * FROM ".$tableprefix."content WHERE id_content=".intval($_getvars["cid"]);
							$result = execsql($sql);
							while ($row = database_fetch_object($result))
								{
									$fname = $row->filename_content;
								}
							$sql = "DELETE FROM ".$tableprefix."content WHERE id_content=".intval($_getvars["cid"])." AND id_content<>1";
							$result = execsql($sql);
							sm_extcore();
							sm_saferemove('index.php?m=content&d=view&cid='.intval($_getvars["cid"]));
							if ($fname != 0)
								{
									delete_filesystem($fname);
								}
							sm_delete_attachments('content', intval($_getvars["cid"]));
							if (file_exists('files/thumb/content'.intval($_getvars["cid"]).'.jpg'))
								unlink('files/thumb/content'.intval($_getvars["cid"]).'.jpg');
							if (file_exists('files/fullimg/content'.intval($_getvars["cid"]).'.jpg'))
								unlink('files/fullimg/content'.intval($_getvars["cid"]).'.jpg');
							if (file_exists('files/img/content'.intval($_getvars["cid"]).'.jpg'))
								unlink('files/img/content'.intval($_getvars["cid"]).'.jpg');
							sm_notify($lang['delete_content_successful']);
							if ($userinfo['level'] < 3)
								sm_redirect('index.php?m=content&d=viewctg&ctgid='.$_getvars['ctg']);
							else
								sm_redirect('index.php?m=content&d=list&ctg='.$_getvars['ctg']);
							sm_event('postdeletecontent', array(intval($_getvars["cid"])));
						}
				}

			if ($userinfo['level'] > 2)
				include('modules/inc/adminpart/content.php');
		}

?>
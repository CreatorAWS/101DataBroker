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
		exit('Hacking attempt!');

	if ($userinfo['level'] == 3)
		{
			if (sm_action('admin'))
				{
					$m['title'] = $lang['settings'];
					$m["module"] = 'news';
					add_path_modules();
				}
			if (sm_action('editctg'))
				{
					$m['title'] = $lang['edit_category'];
					$m["module"] = 'news';
					add_path_modules();
					add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
					$sql = "SELECT * FROM ".$tableprefix."categories_news WHERE id_category='".$_getvars['ctgid']."'";
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$m["id_ctg"] = $row->id_category;
							$m["title_category"] = $row->title_category;
							$m['modify_groups_category'] = get_array_groups($row->groups_modify);
							$m['category_no_alike_news'] = $row->no_alike_news;
							if (!empty($row->filename_category))
								{
									$m['filesystem'] = get_filesystem($row->filename_category);
									$m["filename_category"] = $m['filesystem']['filename'];
								}
						}
					$m['groups_list'] = get_groups_list();
				}
			if (sm_actionpost('postaddctg'))
				{
					$title_category = dbescape($_postvars["p_title_category"]);
					$filename = dbescape($_postvars["p_filename"]);
					$groups_modify = create_groups_str($_postvars['p_groups_modify']);
					$no_alike_news = intval($_postvars['p_no_alike_news']);
					$sql = "INSERT INTO ".$tableprefix."categories_news (title_category, groups_modify, no_alike_news) VALUES ('$title_category', '$groups_modify', '$no_alike_news')";
					$result = execsql($sql);
					$ctgid = database_insert_id('categories_news', $nameDB, $lnkDB);
					if (!empty($filename))
						{
							$urlid = register_filesystem('index.php?m=news&d=listnews&ctg='.$ctgid, $filename, $title_category);
							$sql = "UPDATE ".$tableprefix."categories_news SET filename_category='$urlid' WHERE id_category=".$ctgid;
							$result = execsql($sql);
						}
					sm_notify($lang['add_content_category_successful']);
					sm_redirect('index.php?m=news&d=listctg');
					sm_event('postaddctgnews', array($ctgid));
				}
			if (sm_action('addctg'))
				{
					$m['title'] = $lang['add_category'];
					$m["module"] = 'news';
					add_path_modules();
					add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
					$m['groups_list'] = get_groups_list();
				}
			if (sm_actionpost('posteditctg'))
				{
					$title_category = dbescape($_postvars["p_title_category"]);
					$filename = dbescape($_postvars["p_filename"]);
					$groups_modify = create_groups_str($_postvars['p_groups_modify']);
					$id_ctg = intval($_getvars['ctgid']);
					$no_alike_news = intval($_postvars['p_no_alike_news']);
					$sql = "UPDATE ".$tableprefix."categories_news SET title_category = '$title_category', groups_modify='$groups_modify', no_alike_news='$no_alike_news' WHERE id_category='$id_ctg'";
					$result = execsql($sql);
					$sql = "SELECT * FROM ".$tableprefix."categories_news WHERE id_category='$id_ctg'";
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$fname = $row->filename_category;
						}
					if ($fname == 0 && !empty($filename))
						{
							$urlid = register_filesystem('index.php?m=news&d=listnews&ctg='.$id_ctg, $filename, $title_category);
							$sql = "UPDATE ".$tableprefix."categories_news SET filename_category='$urlid' WHERE id_category=".$id_ctg;
							$result = execsql($sql);
						}
					else
						{
							if (empty($filename))
								{
									$sql = "UPDATE ".$tableprefix."categories_news SET filename_category='0' WHERE id_category=".$id_ctg;
									$result = execsql($sql);
									delete_filesystem($fname);
								}
							else
								update_filesystem($fname, 'index.php?m=news&d=listnews&ctg='.$id_ctg, $filename, $title_category);
						}
					sm_notify($lang['edit_content_category_successful']);
					sm_redirect('index.php?m=news&d=listctg');
					sm_event('posteditctgnews', array($id_ctg));
				}
			if (sm_action('deletectg'))
				{
					$m["module"] = 'news';
					$_msgbox['mode'] = 'yesno';
					$_msgbox['title'] = $lang['delete_category'];
					$_msgbox['msg'] = $lang['really_want_delete_category_news'];
					$_msgbox['yes'] = 'index.php?m=news&d=postdeletectg&ctgid='.$_getvars["ctgid"];
					$_msgbox['no'] = 'index.php?m=news&d=listctg';
				}
			if (sm_action('postdeletectg'))
				{
					$id_ctg = intval($_getvars['ctgid']);
					$sql = "SELECT * FROM ".$tableprefix."categories_news WHERE id_category='".$id_ctg."'";
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$fname = $row->filename_category;
						}
					sm_extcore();
					sm_saferemove('index.php?m=news&d=listnews&ctg='.$id_ctg);
					if ($fname != 0)
						{
							delete_filesystem($fname);
						}
					$sql = "DELETE FROM ".$tableprefix."categories_news WHERE id_category='$id_ctg' AND id_category<>1";
					$result = execsql($sql);
					$q=new TQuery($sm['t'].'news');
					$q->Add('id_category_n', 1);
					$q->Update('id_category_n', intval($id_ctg));
					sm_notify($lang['delete_content_category_successful']);
					sm_redirect('index.php?m=news&d=listctg');
					sm_event('postdeletectgnews', array($id_ctg));
				}
			if (sm_action('listctg'))
				{
					sm_extcore();
					$m['title'] = $lang['list_news_categories'];
					$m["module"] = 'news';
					add_path_modules();
					add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
					$sql = "SELECT * FROM ".$tableprefix."categories_news ORDER BY title_category";
					$result = execsql($sql);
					sm_use('admintable');
					sm_use('admininterface');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$t=new TGrid('edit');
					$t->AddCol('title', $lang['common']['title'], '100%');
					$t->AddCol('search', '', '16', $lang['search'], '', 'search.gif');
					$t->AddEdit();
					$t->AddDelete();
					$t->AddCol('stick', '', '16', $lang["set_as_block"], '', 'stick.gif');
					$t->AddMenuInsert();
					$i = 0;
					while ($row = database_fetch_assoc($result))
						{
							$t->Label('title', $row['title_category']);
							$url = sm_fs_url('index.php?m=news&d=listnews&ctg='.$row['id_category']);
							$t->URL('title', $url);
							$t->URL('search', 'index.php?m=news&d=listnews&ctg='.$row['id_category']);
							if ($row['id_category'] != 1)
								$t->URL('delete', 'index.php?m=news&d=postdeletectg&ctgid='.$row['id_category']);
							$t->URL('edit', 'index.php?m=news&d=editctg&ctgid='.$row['id_category']);
							$t->URL('tomenu', sm_tomenuurl($row['title_category'], $url, sm_this_url()));
							$t->URL('stick', 'index.php?m=blocks&d=add&b=news&id='.$row['id_category'].'&db=shortnews&c='.$row['title_category']);
							$t->NewRow();
							$i++;
						}
					$b=new TButtons();
					$b->AddButton('add', $lang['add_category'], 'index.php?m=news&d=addctg');
					$ui->AddButtons($b);
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('list'))
				{
					$m["module"] = 'news';
					sm_extcore();
					add_path_modules();
					add_path($lang['module_news']['module_news_name'], "index.php?m=news&d=admin");
					$from_record = abs(intval($_getvars['from']));
					if (empty($from_record)) $from_record = 0;
					$from_page = ceil(($from_record + 1) / $_settings['admin_items_by_page']);
					$m['pages']['url'] = 'index.php?m=news&d=list';
					$m['pages']['selected'] = $from_page;
					$m['pages']['interval'] = $_settings['admin_items_by_page'];
					$ctg_id = $_getvars['ctg'];
					$m['ctg_id'] = $ctg_id;
					$sql = "SELECT * FROM ".$tableprefix."categories_news";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m['ctg'][$i]['title'] = $row->title_category;
							$m['ctg'][$i]['id'] = $row->id_category;
							$i++;
						}
					$m['title'] = $lang['list_news'];
					$q=new TQuery($sm['t']."news");
					if (!empty($ctg_id))
						$q->Add('id_category_n', intval($ctg_id));
					$q->OrderBy('date_news DESC');
					$q->Limit($_settings['admin_items_by_page']);
					$q->Offset($from_record);
					$q->Select();
					sm_use('admintable');
					$t=new TGrid('edit');
					$t->AddCol('date', $lang['date_news'], '10%');
					$t->AddCol('title', $lang['common']['title'], '90%');
					$t->AddEdit();
					$t->AddCol('html', '', '16', $lang['common']['edit'].' ('.$lang['common']['html'].')', '', 'edit_html.gif');
					$t->AddDelete();
					$t->AddCol('stick', '', '16', $lang["set_as_block"], '', 'stick.gif');
					$t->AddMenuInsert();
					$result = execsql($sql);
					$have_title = 0;
					for ($i = 0; $i<count($q->items); $i++)
						{
							$t->Label('date', strftime($lang["datemask"], $q->items[$i]['date_news']));
							$t->Label('title', $q->items[$i]['title_news']);
							$t->Hint('title', $q->items[$i]['title_news']);
							if (empty($q->items[$i]['preview_news']))
								$t->Hint('title', htmlescape(cut_str_by_word(nl2br(strip_tags($q->items[$i]['text_news'])), 100, '...')));
							else
								$t->Hint('title', htmlescape(cut_str_by_word(nl2br(strip_tags($q->items[$i]['preview_news'])), 100, '...')));
							$url=sm_fs_url('index.php?m=news&d=view&nid='.$q->items[$i]['id_news']);
							$t->URL('title', $url, true);
							$t->URL('date', $url, true);
							$t->URL('edit', 'index.php?m=news&d=edit&nid='.$q->items[$i]['id_news']);
							$t->URL('html', 'index.php?m=news&d=edit&nid='.$q->items[$i]['id_news'].'&exteditor=off');
							$t->URL('delete', 'index.php?m=news&d=postdelete&nid='.$q->items[$i]['id_news'].'&ctg='.$q->items[$i]['id_category_n']);
							$t->URL('tomenu', sm_tomenuurl(!empty($q->items[$i]['title_news'])?$q->items[$i]['title_news']:strftime($lang["datemask"], $q->items[$i]['date_news']), $url, sm_this_url()));
							$t->URL('stick', 'index.php?m=blocks&d=add&b=news&id='.$q->items[$i]['id_news'].'&db=view&c='.(!empty($q->items[$i]['title_news'])?$q->items[$i]['title_news']:strftime($lang["datemask"], $q->items[$i]['date_news'])));
							if (!empty($q->items[$i]['title_news']))
								$have_title = 1;
							$t->NewRow();
						}
					if ($have_title != 1)
						{
							$t->HeaderHideCol('title');
						}
					$m['table']=$t->Output();
					$m['pages']['records'] = $q->Find();
					$m['pages']['pages'] = ceil($m['pages']['records'] / $_settings['admin_items_by_page']);
					$m['short_news'] = 0;
				}
		}

?>
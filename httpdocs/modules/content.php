<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Content
	Module URI: http://simancms.org/modules/content/
	Description: Pages management. Base CMS module
	Version: 1.6.8
	Revision: 2015-02-13
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("CONTENT_FUNCTIONS_DEFINED"))
		{
			function siman_getfilename_ctg_content($idctg, $filenameid)
				{
					$tmpurl = get_filename($filenameid);
					if (empty($tmpurl))
						$tmpurl = 'index.php?m=content&d=viewctg&ctgid='.$idctg;
					return $tmpurl;
				}

			function siman_load_ctgs_content($id_mainctg = -1, $extsql = '')
				{
					global $sm;
					if (!empty($extsql))
						$addsql = ' WHERE '.$extsql;
					if ($id_mainctg >= 0)
						{
							if (empty($addsql))
								$addsql .= " WHERE ";
							else
								$addsql .= " AND ";
							$addsql .= " id_maincategory=".intval($id_mainctg);
						}
					$sql = "SELECT * FROM ".$sm['t']."categories $addsql";
					$sql .= " ORDER BY id_maincategory, IF(id_category=1, 0, 1), title_category";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_assoc($result))
						{
							$ctg[$i]['id'] = $row['id_category'];
							$ctg[$i]['title'] = $row['title_category'];
							$ctg[$i]['can_view'] = $row['can_view'];
							$ctg[$i]['main_ctg'] = $row['id_maincategory'];
							$ctg[$i]['sorting_category'] = $row['sorting_category'];
							$ctg[$i]['preview_category'] = $row['preview_category'];
							$ctg[$i]['groups_view'] = $row['groups_view'];
							$ctg[$i]['groups_modify'] = $row['groups_modify'];
							$ctg[$i]['level'] = 1;
							$ctg[$i]['filename'] = siman_getfilename_ctg_content($row['id_category'], $row['filename_category']);
							$i++;
						}

					for ($i = 0; $i < count($ctg); $i++)
						{
							$pos[$i] = 0;
						}
					for ($i = 0; $i < count($ctg); $i++)
						{
							if ($ctg[$i]['main_ctg'] == 0)
								{
									$maxpos = 0;
									for ($j = 0; $j < count($ctg); $j++)
										{
											if ($maxpos < $pos[$j])
												$maxpos = $pos[$j];
										}
									$pos[$i] = $maxpos + 1;
								}
							else
								{
									$rootpos = 0;
									$childpos = -1;
									for ($j = 0; $j < count($ctg); $j++)
										{
											if ($ctg[$j]['id'] == $ctg[$i]['main_ctg'])
												{
													$rootpos = $pos[$j];
													$ctg[$i]['level'] = $ctg[$j]['level'] + 1;
													$ctg[$j]['is_mainctg'] = 1;
												}
											if ($ctg[$j]['main_ctg'] == $ctg[$i]['main_ctg'] && $j != $i && $childpos < $pos[$j])
												$childpos = $pos[$j];
										}
									$pos[$i] = ($rootpos > $childpos) ? ($rootpos + 1) : ($childpos + 1);
									for ($j = 0; $j < count($ctg); $j++)
										{
											if ($pos[$j] >= $pos[$i] && $j != $i)
												$pos[$j]++;
										}
								}
						}
					for ($i = 0; $i < count($ctg); $i++)
						{
							$rctg[$pos[$i] - 1] = $ctg[$i];
						}

					return $rctg;
				}

			define("CONTENT_FUNCTIONS_DEFINED", 1);
		}

	$tmp_load_preview_only = 0;
	$tmp_dont_set_title = 0;

	sm_default_action('view');
	if (sm_action('view'))
		{
			if (!empty($m["bid"])) $m["cid"] = intval($m["bid"]);
			$content_id = intval($m["cid"]);
			if (empty($content_id) && $modules_index == 0)
				{
					$content_id = intval($_getvars["cid"]);
					if ($sm['s']['is_index_page'] && empty($content_id))
						$content_id=1;
				}
			if (empty($content_id))
				{
					$m["title"] = $lang["error"];
					$m["text"] = $lang["error_cannot_found"];
					$content_error = 1;
				}
			else
				{
					$m["module"] = 'content';
					sm_page_viewid('content-view-'.$content_id);
					$sql = "SELECT ".$tableprefix."content.*, ".$tableprefix."categories.* FROM ".$tableprefix."content, ".$tableprefix."categories WHERE ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category AND id_content=".intval($content_id);
					if ($modules_index == 0)
						$sql .= " AND refuse_direct_show <> 1";
				}
			if ($_settings['allow_alike_content'] != 1)
				$tmp_no_alike_content = 1;
		}

	if (sm_action('viewlast') || sm_action('viewfirst'))
		{
			sm_page_viewid('content-viewlast');
			$m["module"] = 'content';
			$tmp_ctg = intval($_getvars['ctg']);
			$sql = "SELECT ".$tableprefix."content.*, ".$tableprefix."categories.* FROM ".$tableprefix."content, ".$tableprefix."categories WHERE ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category";
			if (!empty($tmp_ctg))
				$sql .= " AND ".$tableprefix."content.id_category_c=".intval($tmp_ctg);
			if (sm_action('viewlast'))
				$sql .= " ORDER BY ".$tableprefix."content.id_content DESC LIMIT 1";
			else
				$sql .= " ORDER BY ".$tableprefix."content.id_content ASC LIMIT 1";
			$m["mode"] = 'view';
			if ($_settings['allow_alike_content'] != 1)
				$tmp_no_alike_content = 1;
		}

	if (sm_action('multiview'))
		{
			$m["module"] = 'content';
			if (!empty($m["bid"]))
				$ctg_id = intval($m["bid"]);
			else
				$ctg_id = intval($_getvars["ctgid"]);
			if (!empty($ctg_id))
				{
					sm_page_viewid('content-multiview-'.$ctg_id);
					$m['subcategories'] = siman_load_ctgs_content($ctg_id);
					$m['subcategories_present'] = 1;
					$sql = "SELECT * FROM ".$tableprefix."categories WHERE id_category=".intval($ctg_id);
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$m['category']['id_ctg'] = $row->id_category;
							$m['category']['title_category'] = $row->title_category;
							$m['category']['category_can_view'] = $row->can_view;
							$m['category']['main_ctg'] = $row->id_maincategory;
							$m['category']['preview_ctg'] = $row->preview_category;
						}
				}
			else
				sm_page_viewid('content-multiview');
			$sql = "SELECT ".$tableprefix."content.*, ".$tableprefix."categories.* FROM ".$tableprefix."content, ".$tableprefix."categories WHERE ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category";
			$sql .= ' AND '.$tableprefix.'categories.can_view<='.intval($userinfo['level']);
			if (!empty($ctg_id))
				$sql .= ' AND '.$tableprefix.'content.id_category_c='.intval($ctg_id);
			$sql .= ' ORDER BY '.$tableprefix.'content.priority_content DESC"';
			if ($_settings['content_multiview'] == 'off')
				{
					$sql .= ' LIMIT 1';
				}
			else
				{
					if (!empty($_getvars['count']))
						{
							if (is_numeric($_getvars['count']))
								$sql .= ' LIMIT '.intval($_getvars['count']);
							else
								$sql .= ' LIMIT '.intval($_settings['content_per_page_multiview']);
						}
					else
						{
							$sql .= ' LIMIT '.intval($_settings['content_per_page_multiview']);
						}
				}
			$tmp_dont_set_title = 1;
			$tmp_load_preview_only = 1;
			$tmp_no_alike_content = 1;
			$m["mode"] = 'view';
		}

	if (sm_action('rndctgview'))
		{
			sm_page_viewid('content-rndctgview');
			$m["module"] = 'content';
			if (!empty($m["bid"]))
				$ctg_id = intval($m["bid"]);
			else
				$ctg_id = intval($_getvars["ctgid"]);
			$sql = "SELECT ".database_get_fn_name('rand')."() as rndrow,".$tableprefix."content.*, ".$tableprefix."categories.* FROM ".$tableprefix."content, ".$tableprefix."categories WHERE ".$tableprefix."content.id_category_c=".$tableprefix."categories.id_category";
			$sql .= ' AND '.$tableprefix.'categories.can_view<='.intval($userinfo['level']);
			if (!empty($ctg_id))
				$sql .= ' AND '.$tableprefix.'content.id_category_c='.intval($ctg_id);
			$sql .= ' ORDER BY rndrow LIMIT 1';
			$m["mode"] = 'view';
			$tmp_no_alike_content = 1;
		}

	if (sm_action('view'))
		{
			if ($content_error != 1)
				{
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_assoc($result))
						{
							if ($row['type_content'] == 2)
								{
									@header('Content-type: text/plain; charset='.$lang["charset"]);
									print($row['text_content']);
									exit();
								}
							sm_event('onbeforecontentprocessing', $i);
							if ($modules_index==0 && $i==0 && !empty($content_id))
								sm_meta_canonical(sm_fs_url('index.php?m=content&d=view&cid='.$content_id));
							$m['content'][$i]["title"] = $row['title_content'];
							sm_add_title_modifier($m['content'][$i]["title"]);
							if ($tmp_dont_set_title != 1)
								$m["title"] = $m['content'][$i]["title"];
							if ($tmp_load_preview_only == 1)
								{
									$m['content'][$i]["text"] = $row['preview_content'];
									if (empty($m['content'][$i]["text"]))
										$m['content'][$i]["text"] = cut_str_by_word($row['text_content'], 300, '...');
									if (!empty($row['filename_content']))
										$m['content'][$i]["fullink"] = get_filename($row['filename_content']);
									else
										$m['content'][$i]["fullink"] = 'index.php?m=content&d=view&cid='.$row['id_content'];
								}
							else
								$m['content'][$i]["text"] = $row['text_content'];
							sm_add_content_modifier($m['content'][$i]["text"]);
							$m['content'][$i]["id_category"] = $row['id_category_c'];
							if ($special['categories']['getctg'] == 1)
								$special['categories']['id'] = $row['id_category_c'];
							$m['content'][$i]["title_category"] = $row['title_category'];
							if ($modules_index == 0 && $i == 0 && $_settings['content_use_path'] == 1 && $row['no_use_path'] != 1)
								{
									$tmppath = sm_get_path_tree($tableprefix."categories", 'id_category', 'id_maincategory', $row['id_category_c']);
									add_path_home();
									for ($tmpi = 0; $tmpi < count($tmppath); $tmpi++)
										{
											$tmpurl = siman_getfilename_ctg_content($tmppath[$tmpi]['id_category'], $tmppath[$tmpi]['filename_category']);
											add_path($tmppath[$tmpi]['title_category'], $tmpurl);
										}
								}
							if ($modules_index == 0 && $i == 0)
								$m['content'][$i]['attachments'] = sm_get_attachments('content', $row['id_content']);
							if ($tmp_no_alike_content != 1)
								if ($row['no_alike_content'] == 1)
									$tmp_no_alike_content = 1;
							if ($row['can_view'] <= $userinfo['level'])
								$m['content'][$i]["can_view"] = 1;
							else
								{
									if (!empty($userinfo['groups']))
										{
											if (compare_groups($userinfo['groups'], $row['groups_view']))
												$m['content'][$i]["can_view"] = 1;
											else
												$m['content'][$i]["can_view"] = 0;
										}
									else
										$m['content'][$i]["can_view"] = 0;
									if ($m['content'][$i]["can_view"] == 0)
										{
											$m['content'][$i]["title"] = $lang['access_denied'];
										}
								}
							if ($row['type_content'] == 0)
								{
									$m['content'][$i]["text"] = nl2br($m['content'][$i]["text"]);
								}
							if ($userinfo['level']>=intval(sm_settings('content_editor_level')))
								{
									$m['content'][$i]["can_edit"] = 1;
									$m['content'][$i]["can_delete"] = 1;
								}
							elseif (!empty($userinfo['groups']))
								{
									if (compare_groups($userinfo['groups'], $row['groups_modify']))
										{
											$m['content'][$i]["can_edit"] = 1;
											$m['content'][$i]["can_delete"] = 1;
										}
								}
							$m['content'][$i]["cid"] = $content_id;
							if ($_settings['content_use_image'] == 1)
								{
									if (file_exists('files/fullimg/content'.$content_id.'.jpg'))
										{
											if ($tmp_load_preview_only == 1)
												$m['content'][$i]['image'] = 'files/thumb/content'.$content_id.'.jpg';
											else
												$m['content'][$i]['image'] = 'files/fullimg/content'.$content_id.'.jpg';
										}
									elseif (file_exists('files/img/content'.$content_id.'.jpg'))
										{
											$m['content'][$i]['image'] = 'ext/showimage.php?img=content'.$content_id;
											if ($tmp_load_preview_only == 1)
												{
													if (!empty($_settings['content_image_preview_width']))
														$m['content'][$i]['image'] .= '&width='.$_settings['content_image_preview_width'];
													if (!empty($_settings['content_image_preview_height']))
														$m['content'][$i]['image'] .= '&height='.$_settings['content_image_preview_height'];
												}
											else
												{
													if (!empty($_settings['content_image_fulltext_width']))
														$m['content'][$i]['image'] .= '&width='.$_settings['content_image_fulltext_width'];
													if (!empty($_settings['content_image_fulltext_height']))
														$m['content'][$i]['image'] .= '&height='.$_settings['content_image_fulltext_height'];
												}
										}
								}
							if ($modules_index == 0)
								{
									if (!empty($special['meta']['keywords']) && !empty($row['keywords_content']))
										{
											$special['meta']['keywords'] = ($row['keywords_content']).', '.$special['meta']['keywords'];
										}
									elseif (!empty($row['keywords_content']))
										{
											$special['meta']['keywords'] = $row['keywords_content'];
										}
									if (!empty($row['description_content']))
										$special['meta']['description'] = $row['description_content'];
								}
							if ($tmp_no_alike_content != 1 && $modules_index == 1 && $m['panel'] == 'center' && $m['content'][$i]["can_view"] != 0)
								{
									$tmpsql = "SELECT * FROM ".$tableprefix."content WHERE id_content<>".intval($m['content'][$i]["cid"])." AND id_category_c=".intval($m['content'][$i]['id_category'])." ORDER BY priority_content DESC LIMIT ".intval($_settings['alike_content_count']);
									$tmpresult = database_db_query($nameDB, $tmpsql, $lnkDB);
									$j = 0;
									while ($tmprow = database_fetch_assoc($tmpresult))
										{
											$m['content'][$i]['alike_texts'][$j]['id'] = $tmprow['id_content'];
											$m['content'][$i]['alike_texts'][$j]['title'] = $tmprow['title_content'];
											if (!empty($tmprow['filename_content']))
												$m['content'][$i]['alike_texts'][$j]["fullink"] = get_filename($tmprow['filename_content']);
											else
												$m['content'][$i]['alike_texts'][$j]["fullink"] = 'index.php?m=content&d=view&cid='.$tmprow['id_content'];
											$m['content'][$i]['alike_texts'][$j]['preview'] = $tmprow['preview_content'];
											if (empty($m['content'][$i]['alike_texts'][$j]['preview']))
												$m['content'][$i]['alike_texts'][$j]['preview'] = cut_str_by_word($tmprow['text_content'], 300, '...');
											sm_add_title_modifier($m['content'][$i]['alike_texts'][$j]['title']);
											sm_add_content_modifier($m['content'][$i]['alike_texts'][$j]['preview']);
											$j++;
										}
									$m['content'][$i]['alike_texts_present'] = $j;
								}
							else
								$m['content'][$i]['alike_texts_present'] = 0;
							$m['content'][$i]['data']=$row;
							$tmp=sm_load_metadata('content', $row['id_content']);
							if (!empty($tmp['main_template']) && $modules_index==0)
								sm_use_template($tmp['main_template']);
							if (!empty($tmp['content_template']) && $i==0)
								$m['module']=$tmp['content_template'];
							sm_event('oncontentprocessed', $i);
							$i++;
						}
					if ($i == 0)
						$m["module"] = '';
					elseif ($modules_index == 0)
						sm_event('onviewcontent', array($m['content'][0]["cid"]));
				}
		}

	if (sm_action('viewctg'))
		{
			$m["module"] = 'content';
			if (empty($_getvars["ctgid"]) && !empty($_getvars["ctg"]))
				$_getvars["ctgid"] = $_getvars["ctg"];
			$ctg_id = intval($_getvars["ctgid"]);
			sm_page_viewid('content-viewctg-'.$ctg_id);
			$sql = "SELECT * FROM ".$tableprefix."categories WHERE id_category=".intval($ctg_id);
			$result = execsql($sql);
			while ($row = database_fetch_assoc($result))
				{
					sm_event('onbeforecontentcategoriespathprocessing', $i);
					if ($modules_index == 0 && $_settings['content_use_path'] == 1 && $row['no_use_path'] != 1)
						{
							$tmppath = sm_get_path_tree($tableprefix."categories", 'id_category', 'id_maincategory', $row['id_maincategory']);
							add_path_home();
							for ($tmpi = 0; $tmpi < count($tmppath); $tmpi++)
								{
									$tmpurl = siman_getfilename_ctg_content($tmppath[$tmpi]['id_category'], $tmppath[$tmpi]['filename_category']);
									add_path($tmppath[$tmpi]['title_category'], $tmpurl);
								}
						}
					if ($special['categories']['getctg'] == 1)
						$special['categories']['id'] = $row['id_category'];
					$m['title'] = $row['title_category'];
					$m['preview_category'] = $row['preview_category'];
					$m['sorting_category'] = $row['sorting_category'];
					if ($row['can_view'] <= $userinfo['level'])
						$m['category']['can_view'] = 1;
					else
						{
							if (!empty($userinfo['groups']))
								{
									if (compare_groups($userinfo['groups'], $row['groups_view']))
										$m['category']['can_view'] = 1;
									else
										$m['category']['can_view'] = 0;
								}
							else
								$m['category']['can_view'] = 0;
							if ($m['category']['can_view'] == 0)
								$m['title'] = $lang['access_denied'];
						}
					$m['subcategories'] = siman_load_ctgs_content($row['id_category']);
					sm_add_title_modifier($m['title']);
					sm_add_content_modifier($m['preview_category']);
				}
			$sql = "SELECT ".$tableprefix."content.*, ".$tableprefix."filesystem.* FROM ".$tableprefix."content LEFT JOIN ".$tableprefix."filesystem ON ".$tableprefix."content.filename_content=".$tableprefix."filesystem.id_fs WHERE ".$tableprefix."content.id_category_c=".intval($ctg_id);
			if ($m['sorting_category'] == 3)
				$sql .= " ORDER BY priority_content DESC";
			elseif ($m['sorting_category'] == 1)
				$sql .= " ORDER BY title_content DESC";
			elseif ($m['sorting_category'] == 2)
				$sql .= " ORDER BY priority_content ASC";
			else
				$sql .= " ORDER BY title_content ASC";
			//$sql="SELECT * FROM ".$tableprefix."content WHERE id_category_c='$ctg_id'";
			$result = execsql($sql);
			$i = 0;
			while ($row = database_fetch_assoc($result))
				{
					sm_event('onbeforeviewctgcontentprocessing', $i);
					$m['category']['ctg'][$i]['title'] = $row['title_content'];
					$m['category']['ctg'][$i]['id'] = $row['id_content'];
					if ($row['filename_content'] != 0)
						{
							$m['category']['ctg'][$i]['url'] = $row['filename_fs'];
						}
					else
						{
							$m['category']['ctg'][$i]['url'] = 'index.php?m=content&d=view&cid='.$row['id_content'];
						}
					if ($_settings['content_use_preview'] == 1)
						{
							$m['category']['ctg'][$i]['preview'] = $row['preview_content'];
						}
					if ($_settings['content_use_image'] == 1)
						{
							if (file_exists('files/thumb/content'.$m['category']['ctg'][$i]['id'].'.jpg'))
								{
									$m['category']['ctg'][$i]['image'] = 'files/thumb/content'.$m['category']['ctg'][$i]['id'].'.jpg';
								}
							elseif (file_exists('files/img/content'.$m['category']['ctg'][$i]['id'].'.jpg'))
								{
									$m['category']['ctg'][$i]['image'] = 'ext/showimage.php?img=content'.$m['category']['ctg'][$i]['id'];
									if (!empty($_settings['content_image_preview_width']))
										$m['category']['ctg'][$i]['image'] .= '&width='.$_settings['content_image_preview_width'];
									if (!empty($_settings['content_image_preview_height']))
										$m['category']['ctg'][$i]['image'] .= '&height='.$_settings['content_image_preview_height'];
								}
						}
					sm_add_title_modifier($m['category']['ctg'][$i]['title']);
					sm_add_content_modifier($m['category']['ctg'][$i]['preview']);
					$i++;
				}
		}

	if (sm_action('blockctgview'))
		{
			$m["module"] = 'content';
			$ctg_id = intval($modules[0]['content'][0]["id_category"]);
			if (empty($ctg_id) || $ctg_id == 1)
				$m['mode'] = 'donotshow';
			else
				{
					$sql = "SELECT * FROM ".$tableprefix."categories WHERE id_category=".intval($ctg_id);
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							sm_event('onbeforeblockctgviewcontentprocessing', $i);
							$m['title'] = $row['title_category'];
							$m['sorting_category'] = $row['sorting_category'];
							if ($row['can_view'] <= $userinfo['level'])
								$m['category']['can_view'] = 1;
							else
								{
									if (!empty($userinfo['groups']))
										{
											if (compare_groups($userinfo['groups'], $row['groups_view']))
												$m['category']['can_view'] = 1;
											else
												$m['category']['can_view'] = 0;
										}
									else
										$m['category']['can_view'] = 0;
									if ($m['category']['can_view'] == 0)
										$m['mode'] = 'donotshow';
								}
							sm_add_title_modifier($m['title']);
						}
					$sql = "SELECT ".$tableprefix."content.*, ".$tableprefix."filesystem.* FROM ".$tableprefix."content LEFT JOIN ".$tableprefix."filesystem ON ".$tableprefix."content.filename_content=".$tableprefix."filesystem.id_fs WHERE ".$tableprefix."content.id_category_c=".intval($ctg_id);
					if ($m['sorting_category'] == 3)
						$sql .= " ORDER BY priority_content DESC";
					elseif ($m['sorting_category'] == 1)
						$sql .= " ORDER BY title_content DESC";
					elseif ($m['sorting_category'] == 2)
						$sql .= " ORDER BY priority_content ASC";
					else
						$sql .= " ORDER BY title_content ASC";
					$result = execsql($sql);
					$i = 0;
					$m['menu'] = Array();
					while ($row = database_fetch_object($result))
						{
							$m['category']['ctg'][$i]['title'] = $row['title_content'];
							$m['category']['ctg'][$i]['id'] = $row['id_content'];
							if ($row['filename_content'] != 0)
								{
									$m['category']['ctg'][$i]['url'] = $row['filename_fs'];
								}
							else
								{
									$m['category']['ctg'][$i]['url'] = 'index.php?m=content&d=view&cid='.$row['id_content'];
								}
							sm_add_menuitem($m['menu'], $row['title_content'], $m['category']['ctg'][$i]['url']);
							sm_add_title_modifier($m['category']['ctg'][$i]['title']);
							$i++;
						}
					if ($i > 0)
						{
							$m["module"] = 'menu';
							$m["mode"] = 'view';
						}
					else
						$m['mode'] = 'donotshow';
				}
		}
	
	if ($userinfo['level']>0)
		include('modules/inc/memberspart/content.php');

?>
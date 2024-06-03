<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: News
	Module URI: http://simancms.org/modules/news/
	Description: News management. Base CMS module
	Version: 1.6.7
	Revision: 2014-09-16
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (!defined("NEWS_FUNCTIONS_DEFINED"))
		{
			function sm_news_url($id, $timestamp)
				{
					return 'news/'.strftime('%Y/%m/%d/', $timestamp).$id.'.html';
				}
			
			define("NEWS_FUNCTIONS_DEFINED", 1);
		}

	if ($userinfo['level']>=(isset($_settings['news_view_level']) ? $_settings['news_view_level'] : 0))
		{ // user level view restrictions start
			$tmp_view_ctg = '';

			if (strpos($m["mode"], '|') !== false)
				{
					$tmp = explode('|', $m["mode"]);
					$m["mode"] = $tmp[0];
					$m['limitnews'] = $tmp[1];
					if (!empty($tmp[2]))
						$m["bid"] = $tmp[2];
					unset($tmp);
				}

			if ($m["bid"] == 1 && empty($m["mode"]))
				{
					$m["mode"] = 'shortnews';
					$m["bid"] = '';
				}

			if (empty($m["mode"])) $m["mode"] = 'listnews';

			$tmp_short_news = 0;


			if (sm_action('listnews') || sm_action('listdate'))
				{
					$tmp_view_ctg = $_getvars['ctg'];
				}
			elseif (sm_action('shortnews'))
				{
					$tmp_short_news = 1;
					$m["mode"] = 'listnews';
					$tmp_view_ctg = $m["bid"];
					//$_getvars['ctg']='';
				}
			elseif (sm_action('viewctg'))
				{
					$_getvars['ctg'] = $_getvars["ctgid"];
					$tmp_view_ctg = $_getvars['ctg'];
					$m["mode"] = 'listnews';
				}

			if (sm_action('listnews') || sm_action('listdate'))
				{
					$m["module"] = 'news';
					if (!empty($tmp_view_ctg))
						{
							$tmp_view_ctg_first = explode(',', $tmp_view_ctg);
							$tmp_view_ctg_first = $tmp_view_ctg_first[0];
							$sql = "SELECT * FROM ".$tableprefix."categories_news WHERE id_category=".intval($tmp_view_ctg_first);
							$result = execsql($sql);
							while ($row = database_fetch_assoc($result))
								{
									$m['title'] = $row['title_category'];
									if ($special['categories']['getctg'] == 1)
										$special['categories']['id'] = $row['id_category'];
								}
						}
					$ctg_id = $tmp_view_ctg;
					if (sm_action('listnews'))
						{
							if (!empty($ctg_id))
								sm_page_viewid('news-'.$m["mode"].'-'.$ctg_id);
							else
								sm_page_viewid('news-'.$m["mode"]);
						}
					else
						{
							sm_page_viewid('news-'.$m["mode"].'-'.$_getvars['dy'].'-'.$_getvars['dm'].'-'.$_getvars['dd']);
						}
					$from_record = abs(intval($_getvars['from']));
					$from_page = ceil(($from_record+1)/$_settings['news_by_page']);
					if (sm_action('listdate'))
						$m['pages']['url'] = 'index.php?m=news&d=listdate&dy='.$_getvars['dy'].'&dm='.$_getvars['dm'].'&dd='.$_getvars['dd'];
					elseif (!empty($tmp_view_ctg))
						$m['pages']['url'] = 'index.php?m=news&d=listnews&ctg='.$tmp_view_ctg;
					else
						$m['pages']['url'] = 'index.php?m=news&d=listnews';
					$m['pages']['selected'] = $from_page;
					$m['pages']['interval'] = $_settings['news_by_page'];
					if (empty($m['title']))
						$m['title'] = $lang['news'];
					$sql2 = " WHERE date_news<=".time();
					if (sm_action('listdate'))
						{
							if (!empty($_getvars['dy']))
								{
									if (empty($_getvars['dm']))
										{
											$tmp['monthstart'] = 1;
											$tmp['monthend'] = intval(date('m', time()));
										}
									else
										{
											$tmp['monthstart'] = intval($_getvars['dm']);
											$tmp['monthend'] = intval($_getvars['dm']);
										}
									if (empty($_getvars['dd']))
										{
											$tmp['daystart'] = 1;
											$tmp['dayend'] = date('d', mktime(23, 59, 59, ($tmp['monthend']<12 ? 1 : $tmp['monthend'] = 1), 1, ($tmp['monthend']<12 ? $_getvars['dy'] : intval($_getvars['dy'])+1))-86400);
										}
									else
										{
											$tmp['daystart'] = intval($_getvars['dd']);
											$tmp['dayend'] = intval($_getvars['dd']);
										}
									$tmp_date_filter1 = mktime(0, 0, 0, $tmp['monthstart'], $tmp['daystart'], $_getvars['dy']);
									$tmp_date_filter2 = mktime(23, 59, 59, $tmp['monthend'], $tmp['dayend'], $_getvars['dy']);
									$sql2 = " AND date_news>=$tmp_date_filter1 AND date_news<=$tmp_date_filter2 ";
								}
							$m["mode"] = 'listnews';
						}
					$m['id_category_n'] = $tmp_view_ctg;
					if (!empty($ctg_id) /* && $tmp_short_news!=1*/)
						{
							if (strpos($ctg_id, ',') !== false)
								{
									$ctg_id = explode(',', $ctg_id);
									for ($i = 0; $i<count($ctg_id); $i++)
										{
											$ctg_id[$i] = intval($ctg_id[$i]);
										}
									$ctg_id = implode(', ', $ctg_id);
									$sql2 .= " AND id_category_n IN ($ctg_id) ";
								}
							else
								{
									$sql2 .= " AND id_category_n = ".intval($ctg_id);
								}
						}
					$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."filesystem.* FROM ".$tableprefix."news LEFT JOIN ".$tableprefix."filesystem ON ".$tableprefix."news.filename_news=".$tableprefix."filesystem.id_fs";
					$sql .= " $sql2 ORDER BY date_news DESC";
					if ($tmp_short_news == 0)
						{
							$sql .= " LIMIT ".intval($_settings['news_by_page'])." OFFSET ".intval($from_record);
						}
					elseif (!empty($m['limitnews']))
						{
							$sql .= " LIMIT ".intval($m['limitnews']);
						}
					else
						{
							$sql .= " LIMIT ".intval($_settings['short_news_count']);
						}
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_assoc($result))
						{
							sm_event('onbeforelistnewsprocessing', $i);
							$m["list"][$i]['id'] = $row['id_news'];
							$m["list"][$i]['date'] = strftime($lang["datemask"], $row['date_news']);
							$m["list"][$i]['time'] = strftime($lang["timemask"], $row['date_news']);
							$m["list"][$i]['text'] = $row['text_news'];
							$m["list"][$i]['title'] = $row['title_news'];
							if ($row['filename_news'] != 0)
								{
									$m["list"][$i]['url'] = $row['filename_fs'];
								}
							else
								{
									$m["list"][$i]['url'] = sm_fs_url('index.php?m=news&d=view&nid='.$row['id_news'], false, sm_news_url($row['id_news'], $row['date_news']));
								}
							if ($_settings['news_use_image'] == 1)
								{
									if (file_exists('files/thumb/news'.$row['id_news'].'.jpg'))
										{
											$m["list"][$i]['image'] = 'files/thumb/news'.$row['id_news'].'.jpg';
										}
									elseif (file_exists('files/img/news'.$row['id_news'].'.jpg'))
										{
											$m["list"][$i]['image'] = 'ext/showimage.php?img=news'.$row['id_news'];
											if (!empty($_settings['news_image_preview_width']))
												$m["list"][$i]['image'] .= '&width='.$_settings['news_image_preview_width'];
											if (!empty($_settings['news_image_preview_height']))
												$m["list"][$i]['image'] .= '&height='.$_settings['news_image_preview_height'];
										}
								}
							if ($tmp_short_news == 0)
								{
									$tmp_cut_news = $_settings['news_anounce_cut'];
								}
							else
								{
									$tmp_cut_news = $_settings['short_news_cut'];
								}
							if ($_settings['news_use_preview'] == 1 && !empty($row['preview_news']))
								{
									$m["list"][$i]['preview'] = $row['preview_news'];
									$m["list"][$i][4] = 1;
									$u = 1;
								}
							else
								{
									if (strlen($row['text_news'])>$tmp_cut_news && $u != 1)
										$m["list"][$i]['preview'] = cut_str_by_word($row['text_news'], $tmp_cut_news, '...');
									else
										$m["list"][$i]['preview'] = $row['text_news'];
									$u = 0;
								}
							if ($row['type_news'] == 0)
								{
									$m["list"][$i]['text'] = nl2br($m["list"][$i]['text']);
									$m["list"][$i]['preview'] = nl2br($m["list"][$i]['preview']);
								}
							sm_event('onlistnewsprocessed', $i);
							sm_add_title_modifier($m["list"][$i]['title']);
							sm_add_content_modifier($m["list"][$i]['text']);
							sm_add_content_modifier($m["list"][$i]['preview']);
							$i++;
						}
					if ($tmp_short_news == 0)
						{
							$m['pages']['records']=intval(getsqlfield("SELECT count(*) FROM ".$tableprefix."news".$sql2));
							$m['pages']['pages'] = ceil($m['pages']['records']/$_settings['news_by_page']);
							$m['short_news'] = 0;
							if ($i==0 && intval($_getvars['from'])>0)
								$m['module']='404';
						}
					else
						{
							$m['pages']['pages'] = 0;
							$m['short_news'] = 1;
						}
					sm_add_title_modifier($m['title']);
				}

			if (sm_action('view'))
				{
					$news_id = intval($m["bid"]);
					if ($_settings['allow_alike_news'] == 1)
						$tmp_no_alike_news = 0;
					else
						$tmp_no_alike_news = 1;
					if (empty($news_id) && $modules_index == 0) $news_id = intval($_getvars["nid"]);
					if (!empty($news_id))
						{
							$sql = "SELECT ".$tableprefix."news.*, ".$tableprefix."categories_news.* FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE ".$tableprefix."news.id_category_n=".$tableprefix."categories_news.id_category AND id_news=".intval($news_id)." LIMIT 1";
							$result = execsql($sql);
							while ($row = database_fetch_assoc($result))
								{
									$m["module"] = 'news';
									sm_event('onbeforenewsprocessing', 0);
									if ($modules_index==0 && $i==0)
										sm_meta_canonical(sm_fs_url('index.php?m=news&d=view&nid='.$row['id_news'], false, sm_news_url($row['id_news'], $row['date_news'])));
									sm_page_viewid('news-'.$m["mode"].'-'.$row['id_news']);
									$m['row'] = $row;
									$m['id'] = $row['id_news'];
									$tmp=sm_load_metadata('news', $row['id_news']);
									if (!empty($tmp['news_template']))
										$m['module']=$tmp['news_template'];
									if ($_settings['news_use_title'])
										{
											if (empty($row['title_news']))
												{
													$m["title"] = strftime($lang["datemask"], $row['date_news']);
													if ($_settings['news_use_time'] == '1')
														$m["title"] = strftime($lang["timemask"], $row['date_news']).' '.$m["title"];
												}
											else
												$m["title"] = $row['title_news'];
										}
									else
										$m["title"] = $lang['news'].' :: '.strftime($lang["datemask"], $row['date_news']);
									$m["date"] = $row['date_news'];
									$m["news_time"] = strftime($lang["timemask"], $row['date_news']);
									$m["news_date"] = strftime($lang["datemask"], $row['date_news']);
									$m["date"] = strftime($lang["datemask"], $m["date"]);
									$m["text"] = $row['text_news'];
									$m["preview"] = $row['preview_news'];
									if (empty($m["preview"]))
										$m["preview"] = cut_str_by_word($row['text_news'], sm_settings('news_anounce_cut'), '...');
									$m["id_category"] = intval($row['id_category_n']);
									if ($special['categories']['getctg'] == 1)
										$special['categories']['id'] = $row['id_category_n'];
									if ($row['no_alike_news'] == 1)
										$tmp_no_alike_news = 1;
									if ($_settings['news_use_image'] == 1)
										{
											if (file_exists('files/fullimg/news'.$m["id"].'.jpg'))
												{
													$m['news_image'] = 'files/fullimg/news'.$m["id"].'.jpg';
												}
											elseif (file_exists('files/img/news'.$m['id'].'.jpg'))
												{
													$m['news_image'] = 'ext/showimage.php?img=news'.$m["id"];
													if (!empty($_settings['news_image_fulltext_width']))
														$m['news_image'] .= '&width='.$_settings['news_image_fulltext_width'];
													if (!empty($_settings['news_image_fulltext_height']))
														$m['news_image'] .= '&height='.$_settings['news_image_fulltext_height'];
												}
										}
									if ($row['type_news'] == 0)
										{
											$m["text"] = nl2br($m["text"]);
										}
									if ($userinfo['level']>=intval(sm_settings('news_editor_level')))
										{
											$m["can_edit"] = 1;
											$m["can_delete"] = 1;
										}
									elseif (!empty($userinfo['groups']))
										{
											if (compare_groups($userinfo['groups'], $row['groups_modify']))
												{
													$m["can_edit"] = 1;
													$m["can_delete"] = 1;
												}
										}
									if ($tmp_no_alike_news != 1 && $m['panel'] == 'center')
										{
											$tmpsql = "SELECT * FROM ".$tableprefix."news WHERE id_category_n=".intval($m["id_category"])." AND id_news<>".intval($news_id)." ORDER BY date_news DESC LIMIT ".intval($_settings['alike_news_count']);
											$tmpresult = execsql($tmpsql);
											$j = 0;
											while ($tmprow = database_fetch_assoc($tmpresult))
												{
													$m['alike_news'][$j]['id'] = $tmprow['id_news'];
													$m['alike_news'][$j]['title'] = $tmprow['title_news'];
													$m['alike_news'][$j]["date"] = strftime($lang["datemask"], $tmprow['date_news']);
													if (!empty($tmprow['filename_news']))
														$m['alike_news'][$j]["fullink"] = get_filename($tmprow['filename_news']);
													else
														$m['alike_news'][$j]["fullink"] = sm_fs_url('index.php?m=news&d=view&nid='.$tmprow['id_news'], false, sm_news_url($tmprow['id_news'], $tmprow['date_news']));
													$m['alike_news'][$j]['preview'] = $tmprow['preview_news'];
													if (empty($m['alike_news'][$j]['preview']))
														$m['alike_news'][$j]['preview'] = cut_str_by_word($tmprow['text_news'], $_settings['news_anounce_cut'], '...');
													if (empty($m['alike_news'][$j]['title']))
														$m['alike_news'][$j]['title'] = $m['alike_news'][$j]['preview'];
													sm_add_title_modifier($m['alike_news'][$j]['title']);
													sm_add_content_modifier($m['alike_news'][$j]['preview']);
													$j++;
												}
											$m['alike_news_present'] = $j;
										}
									else
										$m['alike_news_present'] = 0;
									$m['attachments'] = sm_get_attachments('news', $row['id_news']);
									if ($modules_index == 0)
										{
											if (!empty($special['meta']['keywords']) && !empty($row['keywords_news']))
												{
													$special['meta']['keywords'] = ($row['keywords_news']).', '.$special['meta']['keywords'];
												}
											elseif (!empty($row['keywords_news']))
												{
													$special['meta']['keywords'] = $row['keywords_news'];
												}
											if (!empty($row['description_news']))
												$special['meta']['description'] = $row['description_news'];
										}
									sm_event('onnewsprocessed', $i);
									sm_event('onviewnews', array($m["id"]));
								}
							sm_add_content_modifier($m["text"]);
						}
					sm_add_title_modifier($m['title']);
				}

			if ($userinfo['level']>0)
				include('modules/inc/memberspart/news.php');
		}// user level view restrictions end

?>
<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-06-13
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if ($userinfo['level'] >= 3)
		{
			sm_default_action('view');

			if (sm_action('add'))
				{
					$m["module"] = 'blocks';
					sm_title($lang['static_blocks'].': '.$lang['common']['add']);
					add_path($lang['control_panel'], "index.php?m=admin");
					add_path($lang['static_blocks'], "index.php?m=blocks");
					add_path_current();
					$m["id"] = $_getvars['id'];
					$m["block"] = $_getvars['b'];
					$m["doing"] = $_getvars['db'];
					$m["caption_block"] = $_getvars['c'];
					$sql = "SELECT * FROM ".$tableprefix."modules ORDER BY module_name='content' ASC";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m['show_on'][$i]['caption'] = $lang['module'].': '.$row->module_title;
							$m['show_on'][$i]['value'] = $row->module_name.'|0';
							$i++;
						}
					$listeners = nllistToArray($_settings['autoload_modules']);
					for ($i = 0; $i < count($listeners); $i++)
						{
							$blockfn = 'siman_block_items_'.$listeners[$i];
							if (function_exists($blockfn))
								{
									$tmparr = call_user_func($blockfn, $m);
									if (is_array($tmparr))
										$m['show_on'] = array_merge($m['show_on'], $tmparr);
								}
						}
					$m['groups_all'] = get_groups_list();
				}
			if (sm_action('edit'))
				{
					$m["module"] = 'blocks';
					sm_title($lang['static_blocks'].': '.$lang['common']['edit']);
					add_path($lang['control_panel'], "index.php?m=admin");
					add_path($lang['static_blocks'], "index.php?m=blocks");
					add_path_current();
					$id_block = intval($_getvars["id"]);
					$sql = "SELECT * FROM ".$tableprefix."blocks WHERE id_block='$id_block'";
					$result = execsql($sql);
					while ($row = database_fetch_object($result))
						{
							$m["id"] = $row->id_block;
							$m["panel_block"] = $row->panel_block;
							$m["pos_block"] = $row->position_block;
							$m["caption_block"] = $row->caption_block;
							$m["level_block"] = $row->level;
							$m["show_on_module_block"] = $row->show_on_module;
							$m["show_on_ctg_block"] = $row->show_on_ctg;
							$m["dont_show_modif"] = $row->dont_show_modif;
							$m["no_borders"] = $row->no_borders;
							$m["rewrite_title"] = $row->rewrite_title;
							$m["block_groups_sel"] = get_array_groups($row->groups_view);
							$m["thislevelonly"] = $row->thislevelonly;
							$m["show_on_device"] = $row->show_on_device;
							$m["show_on_viewids"] = $row->show_on_viewids;
						}
					$m["show_on_all"] = 1;
					$sql = "SELECT * FROM ".$tableprefix."modules ORDER BY module_name='content' ASC";
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m['show_on'][$i]['caption'] = $lang['module'].': '.$row->module_title;
							$m['show_on'][$i]['value'] = $row->module_name.'|0';
							if (strcmp($m["show_on_module_block"], $row->module_name) == 0 && $m["show_on_ctg_block"] == 0)
								$m['show_on'][$i]['selected'] = 1;
							$i++;
						}
					$listeners = nllistToArray($_settings['autoload_modules']);
					for ($i = 0; $i < count($listeners); $i++)
						{
							$blockfn = 'siman_block_items_'.$listeners[$i];
							if (function_exists($blockfn))
								{
									$tmparr = call_user_func($blockfn, $m);
									if (is_array($tmparr))
										$m['show_on'] = array_merge($m['show_on'], $tmparr);
								}
						}
					$m['groups_all'] = get_groups_list();
				}
			if (sm_action('postedit'))
				{
					$id_block = intval($_postvars["p_id"]);
					$old_panel = $_postvars["p_old_pnl"];
					$old_position = $_postvars["p_old_pos"];
					$caption_block = dbescape($_postvars["p_caption"]);
					$panel_block = $_postvars["p_panel"];
					$level = $_postvars["p_level"];
					$arr_show_on = explode('|', $_postvars['p_show_on']);
					$module_block = $arr_show_on[0];
					$show_doing_block = $arr_show_on[2];
					$ctg_block = $arr_show_on[1];
					$no_borders = $_postvars['p_no_borders'];
					$dont_show_modif = $_postvars['p_dont_show'];
					$rewrite_title = $_postvars["p_rewrite_title"];
					$groups_view = create_groups_str($_postvars['p_groups']);
					$thislevelonly = intval($_postvars['p_thislevelonly']);
					$show_on_device = $_postvars['show_on_device'];
					$show_on_viewids = $_postvars['show_on_viewids'];
					if ($panel_block != $old_panel)
						{
							$sql = "SELECT max(position_block) FROM ".$tableprefix."blocks WHERE panel_block='".dbescape($panel_block)."'";
							$result = execsql($sql);
							$pos_block = 0;
							while ($row = database_fetch_row($result))
								{
									$pos_block = $row[0];
								}
							$pos_block++;
							$sql = "UPDATE ".$tableprefix."blocks SET level = '$level', panel_block='$panel_block', position_block='$pos_block', caption_block='$caption_block', show_on_module='$module_block', show_on_doing='$show_doing_block', show_on_ctg='$ctg_block', no_borders='$no_borders', dont_show_modif ='$dont_show_modif', rewrite_title = '$rewrite_title', groups_view = '$groups_view', thislevelonly='$thislevelonly', show_on_device='$show_on_device', show_on_viewids='$show_on_viewids' WHERE id_block = '$id_block'";
							$result = execsql($sql);
							$sql = "UPDATE ".$tableprefix."blocks SET position_block=position_block-1 WHERE position_block>='".($old_position)."' AND panel_block='$old_panel'";
							$result = execsql($sql);
						}
					else
						{
							$sql = "UPDATE ".$tableprefix."blocks SET level = '$level', caption_block = '$caption_block', show_on_module='$module_block', show_on_doing='$show_doing_block', show_on_ctg='$ctg_block', no_borders='$no_borders', dont_show_modif ='$dont_show_modif', rewrite_title = '$rewrite_title', groups_view = '$groups_view', thislevelonly='$thislevelonly', show_on_device='$show_on_device', show_on_viewids='$show_on_viewids'  WHERE id_block = '$id_block'";
							$result = execsql($sql);
						}
					if ($_settings['blocks_use_image'] == 1)
						{
							siman_upload_image($id_block, 'block');
						}
					sm_redirect('index.php?m=blocks&d=view');
				}
			if (sm_action('postadd'))
				{
					$id_block = $_postvars["p_id"];
					$name_block = $_postvars["p_block"];
					$caption_block = dbescape($_postvars["p_caption"]);
					$panel_block = $_postvars["p_panel"];
					$level = $_postvars["p_level"];
					$arr_show_on = explode('|', $_postvars['p_show_on']);
					$dont_show_modif = $_postvars["p_dont_show"];
					$doing_block = $_postvars["p_doing"];
					$rewrite_title = $_postvars["p_rewrite_title"];
					$module_block = $arr_show_on[0];
					$show_doing_block = $arr_show_on[2];
					$ctg_block = $arr_show_on[1];
					$no_borders = $_postvars['p_no_borders'];
					$show_on_device = $_postvars['show_on_device'];
					$groups_view = create_groups_str($_postvars['p_groups']);
					$thislevelonly = intval($_postvars['p_thislevelonly']);
					$show_on_viewids = $_postvars['show_on_viewids'];
					$sql = "SELECT max(position_block) FROM ".$tableprefix."blocks WHERE panel_block='$panel_block'";
					$result = execsql($sql);
					$pos_block = 0;
					while ($row = database_fetch_row($result))
						{
							$pos_block = $row[0];
						}
					$pos_block++;
					$sql = "INSERT INTO ".$tableprefix."blocks (level, panel_block, position_block, name_block, caption_block, showed_id, show_on_module, show_on_doing, show_on_ctg, dont_show_modif, doing_block, no_borders, rewrite_title, groups_view, thislevelonly, show_on_device, show_on_viewids) VALUES ('$level', '$panel_block', '$pos_block', '$name_block', '$caption_block', '$id_block', '$module_block', '$show_doing_block', '$ctg_block', '$dont_show_modif', '$doing_block', '$no_borders', '$rewrite_title', '$groups_view', '$thislevelonly', '$show_on_device', '$show_on_viewids')";
					$result = execsql($sql);
					if ($_settings['blocks_use_image'] == 1)
						{
							$id_block = database_insert_id('blocks', $nameDB, $lnkDB);
							siman_upload_image($id_block, 'block');
						}
					sm_redirect('index.php?m=blocks&d=view');
				}
			if (sm_action('postdelete'))
				{
					execsql("DELETE FROM ".$tableprefix."blocks  WHERE id_block=".intval($_getvars['id']));
					execsql("UPDATE ".$tableprefix."blocks SET position_block=position_block-1 WHERE position_block>=".intval($_getvars['pos'])." AND panel_block='".dbescape($_getvars['pnl'])."'");
					sm_redirect('index.php?m=blocks');
				}
			if (sm_action('up'))
				{
					$block=TQuery::ForTable($tableprefix."blocks")->Add('id_block', intval($_getvars['id']))->Get();
					$block2=TQuery::ForTable($tableprefix."blocks")->Add('panel_block', dbescape($block['panel_block']))->Add('position_block<'.intval($block['position_block']))->OrderBy('position_block DESC')->Get();
					if (!empty($block['position_block']) && !empty($block2['position_block']))
						{
							$q=new TQuery($tableprefix."blocks");
							$q->Add('position_block', intval($block2['position_block']));
							$q->Update('id_block', intval($block['id_block']));
							unset($q);
							$q=new TQuery($tableprefix."blocks");
							$q->Add('position_block', intval($block['position_block']));
							$q->Update('id_block', intval($block2['id_block']));
							unset($q);
						}
					sm_redirect('index.php?m=blocks');
				}
			if (sm_action('down'))
				{
					$block=TQuery::ForTable($tableprefix."blocks")->Add('id_block', intval($_getvars['id']))->Get();
					$block2=TQuery::ForTable($tableprefix."blocks")->Add('panel_block', dbescape($block['panel_block']))->Add('position_block>'.intval($block['position_block']))->OrderBy('position_block ASC')->Get();
					if (!empty($block['position_block']) && !empty($block2['position_block']))
						{
							$q=new TQuery($tableprefix."blocks");
							$q->Add('position_block', intval($block2['position_block']));
							$q->Update('id_block', intval($block['id_block']));
							unset($q);
							$q=new TQuery($tableprefix."blocks");
							$q->Add('position_block', intval($block['position_block']));
							$q->Update('id_block', intval($block2['id_block']));
							unset($q);
						}
					sm_redirect('index.php?m=blocks');
				}
			if (sm_action('view'))
				{
					$m["module"] = 'blocks';
					sm_title($lang['static_blocks']);
					add_path_control();
					add_path($lang['static_blocks'], "index.php?m=blocks");
					sm_use('admintable');
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					$q=new TQuery($sm['t']."blocks");
					$q->Add('panel_block', 'c');
					$q->OrderBy('panel_block, position_block');
					$q->Select();
					$t=new TGrid('edit');
					$t->AddCol('title', $lang['center_panel'], '100%');
					$t->AddCol('up', '', '16', $lang['up']);
					$t->AddCol('down', '', '16', $lang['down']);
					$t->AddEdit();
					$t->AddDelete();
					$v=Array(0);
					$l=Array($lang['first']);
					for ($i = 0; $i<$q->Count(); $i++)
						{
							if (intval(sm_settings('main_block_position'))==$i)
								{
									$t->Label('title', $lang['module_blocks']['main_block_position']);
									$t->OneLine('title');
									$t->NewRow();
								}
							$v[]=$i+1;
							$l[]=$lang['after'].': '.$q->items[$i]['caption_block'];
							$t->Label('title', $q->items[$i]['caption_block']);
							$t->URL('edit', 'index.php?m=blocks&d=edit&id='.$q->items[$i]['id_block']);
							if ($i>0)
								{
									$t->URL('up', 'index.php?m=blocks&d=up&id='.$q->items[$i]['id_block']);
									$t->Image('up', 'up.gif');
								}
							if ($i+1<$q->Count())
								{
									$t->URL('down', 'index.php?m=blocks&d=down&id='.$q->items[$i]['id_block']);
									$t->Image('down', 'down.gif');
								}
							$t->URL('delete', 'index.php?m=blocks&d=postdelete&id='.$q->items[$i]['id_block'].'&pos='.$q->items[$i]['position_block'].'&pnl='.$q->items[$i]['panel_block']);
							$t->NewRow();
						}
					if (intval(sm_settings('main_block_position')) >= $q->Count())
						{
							$t->Label('title', $lang['module_blocks']['main_block_position']);
							$t->OneLine('title');
							$t->NewRow();
							$_settings['main_block_position'] = $q->Count();
						}
					$f = new TForm('index.php?m=blocks&d=setmain');
					$f->AddSelectVL('p_mainpos', $lang['module_blocks']['main_block_position'], $v, $l);
					$f->SetValue('p_mainpos', $_settings['main_block_position']);
					$ui->AddForm($f);
					unset($f);
					$ui->br();
					$ui->AddGrid($t);
					unset($t);
					for ($panel = 1; $panel < intval($_settings['sidepanel_count']) + 1; $panel++)
						{
							$t=new TGrid('edit');
							$t->AddCol('title', $lang['panel'].' '.$panel, '100%');
							$t->AddCol('up', '', '16', $lang['up']);
							$t->AddCol('down', '', '16', $lang['down']);
							$t->AddEdit();
							$t->AddDelete();
							$q=new TQuery($sm['t']."blocks");
							$q->Add('panel_block', intval($panel));
							$q->OrderBy('panel_block, position_block');
							$q->Select();
							for ($i = 0; $i<$q->Count(); $i++)
								{
									$t->Label('title', $q->items[$i]['caption_block']);
									$t->URL('edit', 'index.php?m=blocks&d=edit&id='.$q->items[$i]['id_block']);
									if ($i>0)
										{
											$t->URL('up', 'index.php?m=blocks&d=up&id='.$q->items[$i]['id_block'].'&pos='.$q->items[$i]['position_block'].'&pnl='.$q->items[$i]['panel_block']);
											$t->Image('up', 'up.gif');
										}
									if ($i+1<$q->Count())
										{
											$t->URL('down', 'index.php?m=blocks&d=down&id='.$q->items[$i]['id_block'].'&pos='.$q->items[$i]['position_block'].'&pnl='.$q->items[$i]['panel_block']);
											$t->Image('down', 'down.gif');
										}
									$t->URL('delete', 'index.php?m=blocks&d=postdelete&id='.$q->items[$i]['id_block'].'&pos='.$q->items[$i]['position_block'].'&pnl='.$q->items[$i]['panel_block']);
									$t->NewRow();
								}
							$ui->br();
							$ui->AddGrid($t);
							unset($t);
						}
					$ui->Output(true);
				}
			if (sm_action('setmain'))
				{
					sm_update_settings('main_block_position', intval($_postvars['p_mainpos']));
					sm_redirect('index.php?m=blocks');
				}
		}

?>
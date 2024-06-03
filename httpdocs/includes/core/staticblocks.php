<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2015-09-14
	//==============================================================================

	for ($tmpdelayedactionindex = 0; $tmpdelayedactionindex<count($sm['delayed_actions']); $tmpdelayedactionindex++)
		{
			$modules_index++;
			$modules[$modules_index]['current_module'] = $sm['delayed_actions'][$tmpdelayedactionindex]['module'];
			$modules[$modules_index]['borders_off'] = $sm['delayed_actions'][$tmpdelayedactionindex]['no_borders'];
			$modules[$modules_index]['bid'] = $sm['delayed_actions'][$tmpdelayedactionindex]['bid'];
			$modules[$modules_index]['mode'] = $sm['delayed_actions'][$tmpdelayedactionindex]['action'];
			$modules[$modules_index]['params'] = $sm['delayed_actions'][$tmpdelayedactionindex]['params'];
			if (is_numeric($sm['delayed_actions'][$tmpdelayedactionindex]['panel']))
				$modules[$modules_index]['panel'] = $sm['delayed_actions'][$tmpdelayedactionindex]['panel'];
			else
				$modules[$modules_index]['panel'] = 'center';
			$m =& $modules[$modules_index];
			$sm['m'] =& $modules[$modules_index];
			include('modules/'.$sm['delayed_actions'][$tmpdelayedactionindex]['module'].'.php');
		}
	
	//Static blocks out
	$sql = "SELECT * FROM ".$tableprefix."blocks ORDER BY position_block ASC";
	$pnlresult = database_query($sql, $lnkDB);
	while ($pnlrow = database_fetch_object($pnlresult))
		{
			$dont_show_high_priority = 0;
			if ((intval($userinfo['level']) < intval($pnlrow->level) && $pnlrow->thislevelonly == 0) && !compare_groups($userinfo['groups'], $pnlrow->groups_view))
				$dont_show_high_priority = 1;
			elseif ((intval($userinfo['level']) > intval($pnlrow->level) && $pnlrow->thislevelonly == -1) && !compare_groups($userinfo['groups'], $pnlrow->groups_view))
				$dont_show_high_priority = 1;
			elseif (intval($userinfo['level']) != intval($pnlrow->level) && $pnlrow->thislevelonly == 1)
				$dont_show_high_priority = 1;
			elseif (!sm_is_index_page() && strcmp('#index#', $pnlrow->show_on_module) == 0 && $pnlrow->dont_show_modif != 1)
				$dont_show_high_priority = 1;
			if (!empty($pnlrow->showontheme) && $pnlrow->showontheme == sm_current_theme())
				$dont_show_high_priority = 1;
			if (!empty($pnlrow->showonlang) && $pnlrow->showonlang == $sm['s']['lang'])
				$dont_show_high_priority = 1;
			$show_panel = 1;
			if (!empty($pnlrow->show_on_module))
				{
					$show_panel = 0;
					if ((strcmp($pnlrow->show_on_module, $module) == 0 || (empty($pnlrow->show_on_doing) && empty($modules[0]["mode"]) || strcmp($pnlrow->show_on_doing, $modules[0]["mode"]) == 0)) || (sm_is_index_page() && strcmp('#index#', $pnlrow->show_on_module) == 0))
						{
							if ($pnlrow->show_on_ctg != 0)
								{
									if ($special['categories']['id'] == $pnlrow->show_on_ctg)
										$show_panel = 1;
								}
							else
								$show_panel = 1;
						}
				}
			if ($pnlrow->dont_show_modif == 1)
				$show_panel = abs($show_panel - 1);
			if ($show_panel == 0 && !empty($pnlrow->show_on_viewids) && !empty($special['page']['viewid']))
				{
					$tmpviewidslist = nllistToArray($pnlrow->show_on_viewids);
					for ($i = 0; $i < count($tmpviewidslist); $i++)
						{
							if ($tmpviewidslist[$i] == $special['page']['viewid'])
								{
									$show_panel = 1;
									break;
								}
						}
					unset($tmpviewidslist);
				}
			if ($show_panel == 1 && !empty($pnlrow->show_on_device))
				{
					if ($pnlrow->show_on_device == 'desktop' && !$special['deviceinfo']['is_desktop'])
						$show_panel = 0;
					elseif ($pnlrow->show_on_device == 'mobile' && !$special['deviceinfo']['is_mobile'])
						$show_panel = 0;
					elseif ($pnlrow->show_on_device == 'tablet' && !$special['deviceinfo']['is_tablet'])
						$show_panel = 0;
				}
			if ($dont_show_high_priority == 1)
				$show_panel = 0;
			if ($show_panel == 1)
				{
					$modules_index++;
					if ($pnlrow->panel_block == 'l')
						{
							$modules[$modules_index]["panel"] = "1";
						}
					elseif ($pnlrow->panel_block == 'r')
						{
							$modules[$modules_index]["panel"] = "2";
						}
					elseif (is_numeric($pnlrow->panel_block))
						{
							$modules[$modules_index]["panel"] = $pnlrow->panel_block;
						}
					else
						{
							$modules[$modules_index]["panel"] = "center";
						}
					$modules[$modules_index]["borders_off"] = $pnlrow->no_borders;
					$modules[$modules_index]["bid"] = $pnlrow->showed_id;
					$modules[$modules_index]["mode"] = $pnlrow->doing_block;
					if ($_settings['blocks_use_image'] == 1)
						{
							if (file_exists('./files/img/block'.$pnlrow->id_block.'.jpg'))
								{
									$modules[$modules_index]['block_image'] = 'files/img/block'.$pnlrow->id_block.'.jpg';
								}
						}
					$modules[$modules_index]['rewrite_title_to'] = $pnlrow->rewrite_title;
					$m =& $modules[$modules_index];
					$sm['m'] =& $modules[$modules_index];
					if (empty($pnlrow->name_block) && !empty($pnlrow->text_block))
						{
							$m['title'] = $pnlrow->rewrite_title;
							$m['mode'] = 'view';
							$m['module'] = 'content';
							$m['content'][0]['can_view'] = 1;
							$m['content'][0]["text"] = $pnlrow->text_block;
							sm_add_title_modifier($m['title']);
							sm_add_content_modifier($m['content'][0]["text"]);
						}
					else
						{
							$modules[$modules_index]['current_module'] = $pnlrow->name_block;
							include('modules/'.$pnlrow->name_block.'.php');
						}
				}
			else
				{
					$modules_index++;
					if ($pnlrow->panel_block == 'l')
						{
							$modules[$modules_index]["panel"] = "1";
						}
					elseif ($pnlrow->panel_block == 'r')
						{
							$modules[$modules_index]["panel"] = "2";
						}
					elseif (is_numeric($pnlrow->panel_block))
						{
							$modules[$modules_index]["panel"] = $pnlrow->panel_block;
						}
					else
						{
							$modules[$modules_index]["panel"] = "center";
						}
					$modules[$modules_index]["module"] = 'system_empty_block';
				}
		}

?>
<?php

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}


	function simpleshortcodes_replace_time($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $lang;
			$first = strpos($str, '[[time][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			if (empty($p[1]))
				$replacer = strftime($lang["timemask"], time() + intval($p[2]));
			else
				$replacer = strftime($p[1], time() + intval($p[2]));
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_date($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $lang;
			$first = strpos($str, '[[date][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			if (empty($p[1]))
				$replacer = strftime($lang["datemask"], time() + intval($p[2]));
			else
				$replacer = strftime($p[1], time() + intval($p[2]));
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_userinfo($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo;
			$first = strpos($str, '[[userinfo][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = $userinfo[$p[1]];
			if (empty($replacer) && !empty($userinfo['info'][$p[1]]))
				$replacer = $userinfo['info'][$p[1]];
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_settings($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo;
			$first = strpos($str, '[[settings][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			if ($p[1] == 'resource_url')
				$replacer = $_settings['resource_url'];
			elseif ($p[1] == 'resource_title')
				$replacer = $_settings['resource_title'];
			elseif ($p[1] == 'administrators_email')
				$replacer = $_settings['administrators_email'];
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_uppermenu($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special;
			$first = strpos($str, '[[uppermenu][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$special["uppermenu"] = siman_load_menu(intval($p[1]));
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_bottommenu($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special;
			$first = strpos($str, '[[bottommenu][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$special["bottommenu"] = siman_load_menu(intval($p[1]));
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_changetheme($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special, $smarty;
			$first = strpos($str, '[[changetheme][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$_settings['default_theme'] = $p[1];
			$smarty->template_dir = 'themes/'.$p[1].'/';
			$smarty->compile_dir = 'files/themes/'.$p[1].'/';
			$smarty->config_dir = 'themes/'.$p[1].'/';
			$smarty->cache_dir = 'files/temp/';
			$smarty->template_dir_default = 'themes/default/';
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_changelang($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special, $smarty;
			$first = strpos($str, '[[changetheme][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$_settings['default_theme'] = $p[1];
			$smarty->template_dir = 'themes/'.$p[1].'/';
			$smarty->compile_dir = 'files/themes/'.$p[1].'/';
			$smarty->config_dir = 'themes/'.$p[1].'/';
			$smarty->cache_dir = 'files/temp/';
			$smarty->template_dir_default = 'themes/default/';
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_keywords($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special;
			$first = strpos($str, '[[keywords][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$special['meta']['keywords'] = $p[1];
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}

	function simpleshortcodes_replace_description($str)
		{
			global $_settings, $_getvars, $_postvars, $modules, $tableprefix, $userinfo, $special;
			$first = strpos($str, '[[description][');
			$last = strpos($str, ']]', $first);
			$last += 2;
			$key = substr($str, $first + 2, $last - $first - 4);
			$p = explode('][', $key);
			$replacer = '';
			$special['meta']['description'] = $p[1];
			$str = substr($str, 0, $first).$replacer.substr($str, $last);
			return $str;
		}


	for ($i = 0; $i < count($special['contentmodifier']); $i++)
		{
			while (strpos($special['contentmodifier'][$i], '[[time]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_time($special['contentmodifier'][$i]);
				}
			while (strpos($special['titlemodifier'][$i], '[[time]['))
				{
					$special['titlemodifier'][$i] = simpleshortcodes_replace_time($special['titlemodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[date]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_date($special['contentmodifier'][$i]);
				}
			while (strpos($special['titlemodifier'][$i], '[[date]['))
				{
					$special['titlemodifier'][$i] = simpleshortcodes_replace_date($special['titlemodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[userinfo]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_userinfo($special['contentmodifier'][$i]);
				}
			while (strpos($special['titlemodifier'][$i], '[[userinfo]['))
				{
					$special['titlemodifier'][$i] = simpleshortcodes_replace_userinfo($special['titlemodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[settings]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_settings($special['contentmodifier'][$i]);
				}
			while (strpos($special['titlemodifier'][$i], '[[settings]['))
				{
					$special['titlemodifier'][$i] = simpleshortcodes_replace_settings($special['titlemodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[uppermenu]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_uppermenu($special['contentmodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[bottommenu]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_bottommenu($special['contentmodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[changetheme]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_changetheme($special['contentmodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[keywords]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_keywords($special['contentmodifier'][$i]);
				}
			while (strpos($special['contentmodifier'][$i], '[[description]['))
				{
					$special['contentmodifier'][$i] = simpleshortcodes_replace_description($special['contentmodifier'][$i]);
				}
		}

?>
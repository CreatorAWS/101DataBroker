<?php

//------------------------------------------------------------------------------
//|                                                                            |
//|            Content Management System SiMan CMS                             |
//|                                                                            |
//------------------------------------------------------------------------------

//==============================================================================
//#ver 1.6.4
//#revision 2013-04-09
//==============================================================================

if (!defined("SIMAN_DEFINED"))
	{
		print('Hacking attempt!');
		exit();
	}

function siman_add_modifier_menu(&$menu)
	{
		for ($i=0; $i<count($menu); $i++)
			sm_add_content_modifier($menu[$i]['caption']);
	}

if (!function_exists('siman_load_menu'))
	{
		function siman_menu_open_detect(&$menu, $opened_id)
			{
				if (intval($opened_id) == 0)
					return;
				for ($i = 0; $i < count($menu); $i++)
					{
						if (intval($menu[$i]['id']) == intval($opened_id) && intval($opened_id) != intval($menu[$i]['submenu_from']))
							{
								$menu[$i]['opened'] = true;
								siman_menu_open_detect($menu, $menu[$i]['submenu_from']);
							}
					}
			}

		function siman_load_menu($menu_id, $maxlevel=-1)
			{
				global $nameDB, $lnkDB, $_servervars, $tableprefix, $_settings, $special, $row;
				$i=0;
				$addsql='';
				if ($maxlevel>=0)
					$addsql.=' AND submenu_from=0 ';
				$sql="SELECT * FROM ".$tableprefix."menu_lines WHERE id_menu_ml=".intval($menu_id)." $addsql ORDER BY submenu_from, position";
				$result=execsql($sql);
				$tmp_index=strpos($_settings['resource_url'], '/');
				$main_suburl=substr($_settings['resource_url'], $tmp_index);
				while ($row=database_fetch_assoc($result))
					{
						sm_event('onbeforemenulineprocessing', $i);
						$menu[$i]['id']=$row['id_ml'];
						$menu[$i]['mid']=$menu_id;
						$menu[$i]['pos']=$row['position'];
						$menu[$i]['add_param']=$menu_id.'|'.$row['id_ml'];
						$menu[$i]['level']=1;
						$menu[$i]['submenu_from']=$row['submenu_from'];
						$menu[$i]['sublines_count']=0;
						$menu[$i]['url']=$row['url'];
						$menu[$i]['caption']=$row['caption_ml'];
						$menu[$i]['partial']=$row['partial_select'];
						$menu[$i]['alt']=$row['alt_ml'];
						$menu[$i]['attr']=$row['attr_ml'];
						$menu[$i]['newpage']=$row['newpage_ml'];
						$menu[$i]['databasefields']=$row;
						$line_id=$row['id_ml'];
						if ($_settings['menuitems_use_image']==1)
							{
								if (file_exists('./files/img/menuitem'.$line_id.'.jpg'))
									$menu[$i]['image']='files/img/menuitem'.$line_id.'.jpg';
							}
						if (
								(strcmp($main_suburl.$menu[$i]['url'], $_servervars['REQUEST_URI'])==0
									||
								strcmp($main_suburl.$menu[$i]['url'], $_servervars['REQUEST_URI'].'index.php')==0)
								|| ($special['is_index_page'] == 1 && strcmp($menu[$i]['url'], 'http://'.$_settings['resource_url'])==0)
							)
							$menu[$i]['active']='1';
						if ($menu[$i]['active']!='1' && $menu[$i]['partial']==1)
							{
								if (strpos($_servervars['REQUEST_URI'], $main_suburl.$menu[$i]['url'])===0)
									$menu[$i]['active']='1';
							}
						if (empty($menu[$i]['url']))
							$menu[$i]['active']='0';
						$i++;
					}
				$maxlev=0;
				for ($i=0; $i<count($menu); $i++)
					{
						$pos[$i]=0;
					}
				$fistlevelposition=0;
				$fistlevellastposition=0;
				for ($i=0; $i<count($menu); $i++)
					{
						if ($menu[$i]['submenu_from']==0)
							{
								$maxpos=0;
								for ($j=0; $j<count($menu); $j++)
									if ($maxpos<$pos[$j])
										$maxpos=$pos[$j];
								$pos[$i]=$maxpos+1;
								$fistlevelposition++;
								$menu[$i]['submenu_position']=$fistlevelposition;
								$fistlevellastposition=$i;
							}
						else
							{
								$rootpos=0;
								$childpos=-1;
								for ($j=0; $j<count($menu); $j++)
									{
										if ($menu[$j]['id']==$menu[$i]['submenu_from'])
											{
												$rootpos=$pos[$j];
												$menu[$i]['level']=$menu[$j]['level']+1;
												$menu[$j]['sublines_count']++;
												$menu[$j]['is_submenu']=1;
												$menu[$i]['submenu_position']=$menu[$j]['sublines_count'];
											}
										if ($menu[$j]['submenu_from']==$menu[$i]['submenu_from'] && $j!=$i && $childpos<$pos[$j])
											$childpos=$pos[$j];
									}
								$pos[$i]=($rootpos>$childpos) ? ($rootpos+1) : ($childpos+1) ;
								for ($j=0; $j<count($menu); $j++)
									{
										if ($pos[$j]>=$pos[$i] && $j!=$i)
											$pos[$j]++;
									}
							}
					}
				if (count($menu)>0)
					{
						$menu[0]['first']=1;
						$menu[$fistlevellastposition]['last']=1;
					}
				for ($i=0; $i<count($menu); $i++)
					{
						$rmenu[$pos[$i]-1]=$menu[$i];
					}
				for ($i=0; $i<count($rmenu); $i++)
					{
						if (intval($rmenu[$i]['active'])==1)
							{
								$rmenu[$i]['opened']=true;
								siman_menu_open_detect($rmenu, $rmenu[$i]['submenu_from']);
							}
					}
				return $rmenu;
			}
	}

function sm_add_menuitem(&$menu, $title, $url, $level=1, $partial_select='', $alt_text='', $newpage=0)
	{
		$i=count($menu);
		$menu[$i]['url']=$url;
		$menu[$i]['caption']=$title;
		$menu[$i]['partial']=$partial_select;
		$menu[$i]['level']=$level;
		$menu[$i]['alt']=$alt_text;
		$menu[$i]['newpage']=$newpage;
		if ($level==1)
			{
				$menu[$i]['last']=1;
				if ($i==0)
					$menu[$i]['first']=1;
				else
					$menu[$i-1]['last']=0;
			}
	}

if (!empty($_settings['upper_menu_id']))
	{
		$special["uppermenu"]=siman_load_menu($_settings['upper_menu_id']);
		siman_add_modifier_menu($special["uppermenu"]);
	}

if (!empty($_settings['bottom_menu_id']))
	{
		$special["bottommenu"]=siman_load_menu($_settings['bottom_menu_id']);
		siman_add_modifier_menu($special["bottommenu"]);
	}

?>
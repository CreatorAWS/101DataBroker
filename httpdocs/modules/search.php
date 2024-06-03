<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Search
	Module URI: http://simancms.org/modules/search/
	Description: Search module. Base CMS module
	Version: 1.6.7
	Revision: 2014-05-26
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	sm_default_action('search');

	if (sm_action('search'))
		{
			if (!empty($m["bid"]))
				{
					$m["mode"] = 'shortview';
				}
			else
				{
					$m["module"] = 'search';
					sm_event('beforesearch', array($_getvars['q']));
					$_getvars['q'] = trim($_getvars['q']);
					while (strpos($_getvars['q'], '  '))
						str_replace('  ', ' ', $_getvars['q']);
					$special['search_text'] = $_getvars['q'];
					if (empty($_getvars['q']))
						$m['title'] = $lang['module_search']['search'];
					else
						{
							$m['title'] = $lang['module_search']['search_results'];

							$sql = "SELECT * FROM ".$tableprefix."modules";
							$result = execsql($sql);
							$srch_elem = 0;
							$i = 0;
							while ($row = database_fetch_object($result))
								{
									$from_record = abs(intval($_getvars['from']));
									if (empty($from_record)) $from_record = 0;
									$from_page = ceil(($from_record+1)/$_settings['search_items_by_page']);
									$m['pages']['url'] = 'index.php?m=search&q='.urlencode($_getvars['q']);
									$m['pages']['selected'] = $from_page;
									$m['pages']['interval'] = $_settings['search_items_by_page'];
									if (empty($row->search_doing)) continue;
									$srch_table = $tableprefix.$row->search_table;
									$srch_module = $row->module_name;
									$srch_doing = $row->search_doing;
									$srch_var = $row->search_var;
									$srch_title = $row->search_title;
									$srch_fields = $row->search_fields;
									$srch_idfield = $row->search_idfield;
									$srch_text = $row->search_text;
									$srch_mode = ' AND ';
									$srch_comparefull = 0;
									$srch_fields = explode(' ', $srch_fields);
									$srch_query = explode(' ', $_getvars['q']);
									$filter = '';
									for ($j = 0; $j<count($srch_fields); $j++)
										{
											if ($j != 0) $filter .= ' OR ';
											$filter .= ' (';
											for ($k = 0; $k<count($srch_query); $k++)
												{
													if ($k != 0) $filter .= $srch_mode;
													if ($srch_comparefull == 1)
														{
															$filter .= $srch_fields[$j].'LIKE \''.dbescape($srch_query[$k]).'\'';
														}
													else
														{
															$filter .= $srch_fields[$j].' LIKE \'%'.dbescape($srch_query[$k]).'%\'';
														}
												}
											$filter .= ')';
										}
									if (strcmp($srch_module, 'content') == 0)
										$sql = 'SELECT '.$tableprefix.'content.* FROM '.$tableprefix.'content, '.$tableprefix.'categories WHERE '.$tableprefix.'content.id_category_c='.$tableprefix.'categories.id_category AND '.$tableprefix.'categories.can_view<='.$userinfo['level']." AND ($filter)";
									else
										$sql = "SELECT * FROM $srch_table WHERE $filter";
									$srresult = execsql($sql);
									while ($srrow = database_fetch_array($srresult))
										{
											if ($from_record<=$i && $i<$from_record+$_settings['search_items_by_page'])
												{
													$m['search'][$srch_elem]['title'] = $srrow[$srch_title];
													$m['search'][$srch_elem]['url'] = 'index.php?m='.$srch_module.'&d='.$srch_doing.'&'.$srch_var.'='.$srrow[$srch_idfield];
													$m['search'][$srch_elem]['text'] = strip_tags($srrow[$srch_text]);
													if (strlen($m['search'][$srch_elem]['text'])>250)
														$m['search'][$srch_elem]['text'] = substr($m['search'][$srch_elem]['text'], 0, 250).'...';
													if (empty($m['search'][$srch_elem]['title']))
														$m['search'][$srch_elem]['title'] = $row->module_title;
													$srch_elem++;
												}
											$i++;
										}
									$m['result_count'] = $i;
									$m['pages']['records'] = $m['result_count'];
									$m['pages']['pages'] = ceil($m['pages']['records']/$_settings['search_items_by_page']);
								}
						}
					sm_event('aftersearch', array($_getvars['q']));
				}
		}

	if (sm_action('shortview'))
		{
			$m["module"] = 'search';
			$m['title'] = $lang['module_search']['search'];
		}

	if ($userinfo['level']==3)
		include('modules/inc/adminpart/search.php');

?>
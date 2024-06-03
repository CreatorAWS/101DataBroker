<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: RSS-Feed
	Module URI: http://simancms.org/modules/content/
	Description: RSS-Export module
	Version: 1.6.7
	Revision: 2014-05-26
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	sm_default_action('news');

	if (sm_action('news'))
		{
			$m["module"] = 'rss';
			$rss['title'] = sm_settings('resource_title');
			$rss['link'] = 'http://'.sm_settings('resource_url');
			$rss['description'] = sm_settings('logo_text');
			$ctg = intval($_getvars['ctg']);
			$sql = "SELECT * FROM ".$tableprefix."news, ".$tableprefix."categories_news WHERE id_category_n=id_category ";
			if (!empty($ctg))
				{
					$sql .= " AND id_category_n=".intval($ctg);
				}
			$sql .= " AND (date_news<=".time().") ";
			$sql .= " ORDER BY date_news DESC LIMIT ".intval(sm_settings('rss_itemscount'));
			$result = execsql($sql);
			$i = 0;
			while ($row = database_fetch_object($result))
				{
					$rss['items'][$i]['title'] = $row->title_news;
					$rss['items'][$i]['pubDate'] = date('D, d M Y H:i:s O', $row->date_news);
					if (empty($row->preview_news))
						{
							$rss['items'][$i]['description'] = cut_str_by_word(strip_tags($row->text_news), sm_settings('news_anounce_cut'), '...');
						}
					else
						$rss['items'][$i]['description'] = strip_tags($row->preview_news);
					$rss['items'][$i]['description'] = preg_replace("/&#?[a-z0-9]+;/i", "", $rss['items'][$i]['description']);
					$rss['items'][$i]['description'] = str_replace('&', '&amp;', $rss['items'][$i]['description']);
					$rss['items'][$i]['description'] = str_replace('&nbsp;', ' ', $rss['items'][$i]['description']);
					$rss['items'][$i]['description'] = str_replace('nbsp;', ' ', $rss['items'][$i]['description']);
					$rss['items'][$i]['fulltext'] = strip_tags($row->text_news);
					$rss['items'][$i]['fulltext'] = preg_replace("/&#?[a-z0-9]+;/i", "", $rss['items'][$i]['fulltext']);
					$rss['items'][$i]['fulltext'] = str_replace('&', '&amp;', $rss['items'][$i]['fulltext']);
					$rss['items'][$i]['title'] = $row->title_news;
					$rss['items'][$i]['link'] = 'http://'.$_settings['resource_url'].'index.php?m=news&amp;d=view&amp;nid='.$row->id_news;
					$rss['items'][$i]['category'] = $row->title_category;
					$rss['items'][$i]['description'] = eregi_replace('&[a-zA-Z]+;', ' ', $rss['items'][$i]['description']);
					if (empty($rss['items'][$i]['title']))
						$rss['items'][$i]['title'] = $rss['items'][$i]['description'];
					$i++;
				}
			sm_event('generaterss', Array($rss));
		}

	if ($userinfo['level']==3)
		include('modules/inc/adminpart/rss.php');

?>
<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2014-05-26
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	function event_generaterss_rss($feed)
		{
			global $special, $singleWindow, $lang;
			$special['ajax'] = 1;
			$special['main_tpl'] = 'simpleout';
			$singleWindow = 1;
			out('<?xml version="1.0" encoding="'.$lang["charset"].'"?>');
			out('<rss version="2.0">');
			out('<channel>'."\n");
			if (!empty($feed['title']))
				out(' <title>'.$feed['title'].'</title>'."\n");
			if (!empty($feed['link']))
				out(' <link>'.$feed['link'].'</link>'."\n");
			if (!empty($feed['description']))
				out(' <description>'.$feed['description'].'</description>'."\n");
			if (sm_settings('rss_shownimagetag')==1)
				out(' <image><url>http://'.sm_settings('resource_url').'files/img/rss_logo.png</url><title>'.$feed['description'].'</title><link>'.$feed['link'].'</link></image>'."\n");
			for ($i = 0; $i < count($feed['items']); $i++)
				{
					out('   <item>'."\n");
					if (!empty($feed['items'][$i]['title']))
						out('    <title>'.$feed['items'][$i]['title'].'</title>');
					if (!empty($feed['items'][$i]['link']))
						out('    <link>'.$feed['items'][$i]['link'].'</link>');
					if (!empty($feed['items'][$i]['description']))
						out('    <description>'.$feed['items'][$i]['description'].'</description>');
					if (!empty($feed['items'][$i]['category']))
						out('    <category>'.$feed['items'][$i]['category'].'</category>');
					if (!empty($feed['items'][$i]['pubDate']))
						out('    <pubDate>'.$feed['items'][$i]['pubDate'].'</pubDate>');
					if (!empty($feed['items'][$i]['fulltext']) && sm_settings('rss_showfulltext')==1)
						out('    <yandex:full-text>'.$feed['items'][$i]['fulltext'].'</yandex:full-text>');
					out('   </item>'."\n");
				}
			out('</channel>');
			out('</rss>');
		}

	sm_html_headend('<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://'.$_settings['resource_url'].'index.php?m=rss">');
	if (sm_settings('rss_shownewsctgs') == 1)
		{
			$tmpnewsctgs = getsqlarray("SELECT * FROM ".$tableprefix."categories_news ORDER BY title_category");
			for ($tmpirss = 0; $tmpirss < count($tmpnewsctgs); $tmpirss++)
				{
					sm_html_headend('<link rel="alternate" type="application/rss+xml" title="'.htmlescape($tmpnewsctgs[$tmpirss]['title_category']).'" href="http://'.sm_settings('resource_url').'index.php?m=rss&ctg='.$tmpnewsctgs[$tmpirss]['id_category'].'">');
				}
			unset($tmpnewsctgs);
		}

?>
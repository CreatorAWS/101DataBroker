<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-05-31
	//==============================================================================

	//System cleanup
	if ($userinfo['level'] == 3 && $_settings['next_system_cleanup'] <= time())
		{
			sm_extcore();
			sm_update_settings('next_system_cleanup', time() + (empty($_settings['next_system_cleanup_interval']) ? 86400 : intval($_settings['next_system_cleanup_interval'])));
			sm_autobannedip_cleanup();
			sm_event('systemcleanup', Array());
		}

	//Postload modules begin
	sm_event('beforepostload', Array());
	$postloadmodules = nllistToArray($_settings['postload_modules']);
	for ($postloadmodulesindex = 0; $postloadmodulesindex < count($postloadmodules); $postloadmodulesindex++)
		{
			if (strpos($postloadmodules[$postloadmodulesindex], ':')!==false || strpos($postloadmodules[$postloadmodulesindex], '.')!==false || strpos($postloadmodules[$postloadmodulesindex], '/')!==false || strpos($postloadmodules[$postloadmodulesindex], '\\')!==false || empty($postloadmodules[$postloadmodulesindex]))
				continue;
			if (file_exists('modules/postload/'.$postloadmodules[$postloadmodulesindex].'.php'))
				include_once('modules/postload/'.$postloadmodules[$postloadmodulesindex].'.php');
			if ($userinfo['level']>=1 && file_exists('modules/postload/level1/'.$postloadmodules[$postloadmodulesindex].'.php'))
				include_once('modules/postload/level1/'.$postloadmodules[$postloadmodulesindex].'.php');
			if ($userinfo['level']>=2 && file_exists('modules/postload/level2/'.$postloadmodules[$postloadmodulesindex].'.php'))
				include_once('modules/postload/level2/'.$postloadmodules[$postloadmodulesindex].'.php');
			if ($userinfo['level']>=3 && file_exists('modules/postload/level3/'.$postloadmodules[$postloadmodulesindex].'.php'))
				include_once('modules/postload/level3/'.$postloadmodules[$postloadmodulesindex].'.php');
		}
	sm_event('afterpostload', Array());
	//Postload modules end

	//Head section generation start
	if (!$sm['s']['headgen']['custom_encoding'])
		$sm['s']['document']['headdef'].='<meta content="text/html; charset='.$sm['s']['charset'].'" http-equiv=Content-Type>';
	$sm['s']['document']['headdef'].='<title>';
	if (intval(sm_settings('meta_resource_title_position'))==1 || intval(sm_settings('meta_resource_title_position'))==0 && strcmp($sm['s']['pagetitle'], "")==0)
		{
			$sm['s']['document']['headdef'].=sm_settings('resource_title');
			if (strcmp($sm['s']['pagetitle'], "")!=0)
				$sm['s']['document']['headdef'].=sm_settings('title_delimiter');
		}
	$sm['s']['document']['headdef'].=strip_tags($sm['s']['pagetitle']);
	if (intval(sm_settings('meta_resource_title_position'))==2)
		{
			if (strcmp($sm['s']['pagetitle'], "")!=0)
				$sm['s']['document']['headdef'].=sm_settings('title_delimiter');
			$sm['s']['document']['headdef'].=sm_settings('resource_title');
		}
	$sm['s']['document']['headdef'].='</title>';
	if (!empty($sm['s']['meta']['description']))
		$sm['s']['document']['headdef'].='<meta name="description" content="'.htmlescape($sm['s']['meta']['description']).'">';
	if (!empty($sm['s']['meta']['keywords']))
		$sm['s']['document']['headdef'].='<meta name="keywords" content="'.htmlescape($sm['s']['meta']['keywords']).'">';
	$sm['s']['document']['headdef'].='<base href="'.$sm['s']['page']['scheme'].'://'.$sm['s']['resource_url'].'">';
	if (!empty($refresh_url))
		$sm['s']['document']['headdef'].='<script type="text/javascript">setTimeout(function() { document.location.href = "'.$refresh_url.'"; }, 3000)</script>';
	for ($i = 0; $i < count($sm['s']['customjs']); $i++)
		$sm['s']['document']['headend'].='<script type="text/javascript" src="'.$sm['s']['customjs'][$i].'"></script>';
	for ($i = 0; $i < count($sm['s']['cssfiles']); $i++)
		$sm['s']['document']['headend'].='<link href="themes/'.sm_current_theme().'/'.$sm['s']['customjs'][$i].'" type="text/css" rel=stylesheet>';
	for ($i = 0; $i < count($sm['s']['customcss']); $i++)
		$sm['s']['document']['headend'].='<link href="'.$sm['s']['customcss'][$i].'" type="text/css" rel="stylesheet" />';
	if (!empty($sm['s']['autofocus']))
		$sm['s']['document']['headend'].='<script type="text/javascript">$( document ).ready(function() {$("'.$sm['s']['autofocus'].'").focus().select();});</script>';
	//Head section generation end

	unset($sm['cache']);

	//System temp table cleaning
	if (intval($_settings['next_clean_temptable']) <= time())
		{
			sm_extcore();
			$clean_temptable_interval = sm_get_settings('clean_temptable_interval', 'general');
			sm_update_settings('next_clean_temptable', time() + (empty($clean_temptable_interval) ? 600 : intval($clean_temptable_interval)));
			sm_tempdata_clean();
		}

?>
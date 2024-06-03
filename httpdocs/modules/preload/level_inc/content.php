<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-09-25
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	function siman_contnet_nicename_generation($id)
		{
			global $sm;
			if (intval(sm_get_settings('autogenerate_content_filesystem', 'content'))==0)
				return;
			sm_extcore();
			$info=TQuery::ForTable($sm['t'].'content')->Add('id_content', intval($id))->Get();
			if (empty($info['filename_content']))
				{
					$urlid = register_filesystem('index.php?m=content&d=view&cid='.$id, sm_fs_autogenerate($info['title_content'], '.html'), $info['title_content']);
					TQuery::ForTable($sm['t'].'content')->Add('filename_content', intval($urlid))->Update('id_content', intval($id));
				}
		}

	sm_event_handler('postaddcontent', 'siman_contnet_nicename_generation');

?>
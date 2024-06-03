<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Download
	Module URI: http://simancms.org/modules/download/
	Description: Downloads management module. Base CMS module
	Version: 1.6.7
	Revision: 2013-10-17
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	sm_default_action('view');

	if (sm_action('attachment', 'showattachedfile'))
		{
			$att = getsql("SELECT * FROM ".$tableprefix."downloads WHERE userlevel_download<=".intval($userinfo['id'])." AND id_download=".intval($_getvars['id']));
			if (!empty($att['id_download']) && file_exists('files/download/attachment'.intval($_getvars['id'])))
				{
					$m["module"] = 'download';
					$special['main_tpl'] = '';
					$special['no_blocks'] = true;
					header("Content-type: ".$att['attachment_type']);
					if (!sm_action('showattachedfile'))
						header("Content-Disposition: attachment; filename=".basename($att['file_download']));
					$fp = fopen('files/download/attachment'.intval($_getvars['id']), 'rb');
					fpassthru($fp);
					fclose($fp);
				}
		}

	if (sm_action('view'))
		{
			sm_page_viewid('download-view');
			$m["module"] = 'download';
			sm_title($lang['module_download']['downloads']);
			$sql = "SELECT * FROM ".$tableprefix."downloads WHERE attachment_from='-' AND userlevel_download <= ".intval($userinfo["level"]);
			$i = 0;
			$result = database_db_query($nameDB, $sql, $lnkDB);
			while ($row = database_fetch_assoc($result))
				{
					$m['files'][$i]['id'] = $row['id_download'];
					$m['files'][$i]['file'] = $row['file_download'];
					$m['files'][$i]['description'] = $row['description_download'];
					$m['files'][$i]['sizeK'] = round(filesize('./files/download/'.$row['file_download'])/1024, 2);
					$m['files'][$i]['sizeM'] = round($m['files'][$i]['sizeK']/1024, 2);
					sm_add_content_modifier($m['files'][$i]['description']);
					$i++;
				}
		}
	if ($userinfo['level'] == 3 || (intval(sm_settings('perm_downloads_management_level'))>0 && sm_settings('perm_downloads_management_level')<=intval($userinfo['level'])))
		include('modules/inc/adminpart/download.php');

?>
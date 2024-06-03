<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.6
	//#revision 2014-04-15
	//==============================================================================

	$userinfo['sid'] = session_id();

	if (empty($_sessionvars['userinfo_id']))
		{
			$userinfo = Array(
				'level' => 0,
				'info' => Array()
			);
		}
	else
		{
			$userinfo['id'] = $_sessionvars['userinfo_id'];
			$userinfo['login'] = $_sessionvars['userinfo_login'];
			$userinfo['email'] = $_sessionvars['userinfo_email'];
			$userinfo['level'] = $_sessionvars['userinfo_level'];
			$userinfo['session'] = $userinfo['sid'];
			$userinfo['groups'] = $_sessionvars['userinfo_groups'];
			$userinfo['info'] = unserialize($_sessionvars['userinfo_allinfo']);
		}

	if (intval($userinfo['id']) == 1)
		{
			$userinfo['level'] = 3;
		}

?>
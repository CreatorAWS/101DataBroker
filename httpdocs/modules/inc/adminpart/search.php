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
	
	if ($userinfo['level'] == 3)
		{
			if (sm_action('admin'))
				{
					sm_extcore();
					add_path_modules();
					add_path_current();
					sm_title($lang['settings']);
					sm_use('admininterface');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$b=new TButtons();
					$b->Button($lang['set_as_block'].' "'.$lang['search'].'"', 'index.php?m=blocks&d=add&b=search&id=1&c='.urlencode($lang['search']));
					$b->Button($lang['add_to_menu'].' - '.$lang['search'], sm_tomenuurl($lang['search'], 'index.php?m=search'));
					$ui->AddButtons($b);
					$ui->Output(true);
				}
		}

?>
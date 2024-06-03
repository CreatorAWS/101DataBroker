<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Settings (Expert Mode)
	Module URI: http://simancms.org/modules/adminsettings/
	Description: Manage default settings in expert mode.
	Version: 2014-06-06
	Author: SiMan CMS Team
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if ($userinfo['level'] == 3)
		{
			sm_default_action('admin');
			sm_include_lang('adminsettings');
			$m["module"] = 'adminsettings';
			if (sm_action('addeditor', 'addhtml', 'edit', 'html'))
				{
					add_path_modules();
					add_path($lang['module_adminsettings']['module_adminsettings'], 'index.php?m=adminsettings&d=admin');
					add_path_current();
					if (sm_action('addeditor', 'addhtml'))
						sm_title($lang['common']['add']);
					else
						sm_title($lang['common']['edit']);
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (sm_action('addeditor', 'addhtml'))
						$f = new TForm('index.php?m=adminsettings&d=postadd');
					else
						$f = new TForm('index.php?m=adminsettings&d=postedit&param='.urlencode($_getvars['param']).'&mode='.urlencode($_getvars['mode']));
					$f->AddText('name_settings', 'name_settings');
					if (sm_action('addeditor', 'edit'))
						$f->AddEditor('value_settings', 'value_settings');
					else
						$f->AddTextarea('value_settings', 'value_settings');
					$f->AddText('mode', 'mode');
					if (sm_action('addeditor', 'addhtml'))
						{
							$f->SetValue('mode', 'default');
						}
					else
						{
							$q=new TQuery($tableprefix."settings");
							$q->Add('mode', empty($_getvars['mode'])?'default':dbescape($_getvars['mode']));
							$q->Add('name_settings', dbescape($_getvars['param']));
							$f->LoadValuesArray($q->Get());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('name_settings');
				}
			if (sm_action('admin'))
				{
					add_path_modules();
					add_path($lang['module_adminsettings']['module_adminsettings'], 'index.php?m=adminsettings&d=admin');
					sm_use('admintable');
					sm_use('adminbuttons');
					sm_use('admininterface');
					sm_title($lang['settings']);
					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('', $lang['common']['add'], 'index.php?m=adminsettings&d=addeditor');
					$b->AddButton('', $lang['common']['add'].'('.$lang['common']['html'].')', 'index.php?m=adminsettings&d=addhtml');
					$ui->AddButtons($b);
					$t=new TGrid('edit');
					$t->AddCol('title', $lang['common']['title'], '80%');
					$t->AddCol('mode', '', '20%');
					$t->AddEdit();
					$t->AddCol('html', '', '16', $lang['common']['edit'].' ('.$lang['common']['html'].')', '', 'edit_html.gif');
					$t->AddDelete();
					$sql = "SELECT * FROM ".$tableprefix."settings ORDER BY if(mode='default', 0, 1), mode, name_settings";
					$result = database_db_query($nameDB, $sql, $lnkDB);
					$i = 0;
					while ($row = database_fetch_assoc($result))
						{
							$t->Label('title', $row['name_settings']);
							$t->Hint('title', strip_tags($row['value_settings']));
							$t->Label('mode', $row['mode']);
							$t->URL('edit', 'index.php?m=adminsettings&d=edit&param='.urlencode($row['name_settings']).'&mode='.urlencode($row['mode']));
							$t->URL('html', 'index.php?m=adminsettings&d=html&param='.urlencode($row['name_settings']).'&mode='.urlencode($row['mode']));
							$t->URL('delete', 'index.php?m=adminsettings&d=postdelete&param='.urlencode($row['name_settings']).'&mode='.urlencode($row['mode']));
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('postadd', 'postedit'))
				{
					$q=new TQuery($tableprefix."settings");
					$q->Add('mode', empty($_postvars['mode'])?'default':dbescape($_postvars['mode']));
					$q->Add('name_settings', dbescape($_postvars['name_settings']));
					$q->Add('value_settings', dbescape($_postvars['value_settings']));
					if (sm_action('postedit'))
						{
							$q->AddWhere('mode', empty($_getvars['mode'])?'default':dbescape($_getvars['mode']));
							$q->AddWhere('name_settings', dbescape($_getvars['param']));
							$q->Update();
						}
					else
						$q->Insert();
					sm_redirect('index.php?m=adminsettings&d=admin');
				}
			if (sm_action('postdelete'))
				{
					$q=new TQuery($tableprefix."settings");
					$q->Add('mode', empty($_getvars['mode'])?'default':dbescape($_getvars['mode']));
					$q->Add('name_settings', dbescape($_getvars['param']));
					$q->Remove();
					sm_redirect('index.php?m=adminsettings&d=admin');
				}
			if (sm_action('install'))
				{
					sm_register_module('adminsettings', $lang['module_adminsettings']['module_adminsettings']);
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('uninstall'))
				{
					sm_unregister_module('adminsettings');
					sm_redirect('index.php?m=admin&d=modules');
				}
		}

?>
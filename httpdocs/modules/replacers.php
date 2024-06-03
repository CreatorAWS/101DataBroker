<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	/*
	Module Name: Replacers
	Module URI: http://simancms.org/
	Description: Template replacers for custom themes
	Version: 2014-02-13
	Author: SiMan CMS Team
	Author URI: http://simancms.org/
	*/

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (!defined("replacers_FUNCTIONS_DEFINED"))
		{
			define("replacers_FUNCTIONS_DEFINED", 1);
		}

	if ($userinfo['level'] == 3)
		{
			sm_include_lang('replacers');
			if (sm_action('postdelete'))
				{
					$q = new TQuery($sm['t']."replacers");
					$q->Add('id_r', intval($_getvars['id']));
					$q->Remove();
					sm_redirect('index.php?m=replacers&d=admin');
				}
			if (sm_action('postadd', 'postedit'))
				{
					$q = new TQuery($sm['t']."replacers");
					$q->AddPost('key_r');
					$q->AddPost('value_r');
					if (sm_action('postedit'))
						$q->Update('id_r='.intval($_getvars['id']));
					else
						$q->Insert();
					sm_redirect('index.php?m=replacers&d=admin');
				}
			if (sm_action('add', 'edit'))
				{
					sm_use('admininterface');
					sm_use('adminform');
					add_path_modules();
					add_path('Replacers', 'index.php?m=replacers&d=admin');
					add_path_current();
					if (sm_action('add'))
						sm_title($lang['common']['add']);
					else
						sm_title($lang['common']['edit']);
					$ui = new TInterface();
					$f = new TForm('index.php?m=replacers&d=post'.$m['mode'].'&id='.intval($_getvars['id']));
					$f->AddText('key_r', 'Key');
					$f->AddTextarea('value_r', 'Content');
					if (sm_action('edit'))
						{
							$q = new TQuery($sm['t']."replacers");
							$q->Add('id_r', intval($_getvars['id']));
							$f->LoadValuesArray($q->Get());
						}
					$f->LoadValuesArray($sm['p']);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('key_r');
				}
			if (sm_action('admin'))
				{
					sm_use('admininterface');
					sm_use('admintable');
					sm_use('adminbuttons');
					add_path_modules();
					add_path('Replacers', 'index.php?m=replacers&d=admin');
					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('', $lang['common']['add'], 'index.php?m=replacers&d=add');
					$ui->AddButtons($b);
					$t = new TGrid();
					$t->AddCol('key_r', 'Key', '100%');
					$t->AddEdit();
					$t->AddDelete();
					$q = new TQuery($sm['t']."replacers");
					$q->Select();
					for ($i = 0; $i < count($q->items); $i++)
						{
							$t->Label('key_r', $q->items[$i]['key_r']);
							$t->Url('edit', 'index.php?m=replacers&d=edit&id='.$q->items[$i]['id_r']);
							$t->Url('delete', 'index.php?m=replacers&d=postdelete&id='.$q->items[$i]['id_r']);
							$t->NewRow();
						}
					if (count($q->items) == 0)
						{
							$t->Label('key_r', 'Nothing found');
							$t->OneLine('key_r');
						}
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->Output(true);
					sm_title($lang['settings']);
				}
			if (sm_action('install'))
				{
					sm_register_module('replacers', 'Replacers');
					sm_register_autoload('replacers');
					execsql("CREATE TABLE ".$sm['t']."replacers (
								`id_r` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`key_r` VARCHAR( 255 ) NOT NULL ,
								`value_r` TEXT NOT NULL
							);");
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('uninstall'))
				{
					sm_unregister_module('replacers');
					sm_unregister_autoload('replacers');
					execsql("DROP TABLE ".$sm['t']."replacers");
					sm_redirect('index.php?m=admin&d=modules');
				}
		}

?>
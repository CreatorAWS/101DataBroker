<?php

	/*
	Module Name: Code Generator
	Module URI: http://simancms.org/
	Description: Code generator for UI
	Version: 1.6.9
	Revision: 2015-05-27
	Author URI: http://simancms.org/
	*/

	sm_default_action('prepare');
	
	function parse_mysql_create($modulename, $sql)
		{
			global $sm;
			preg_match_all('/`(.+)` (\w+)\(? ?(\d*) ?\)?/', $sql, $fields, PREG_SET_ORDER);
			$result['fields']=$fields;
			if (preg_match('/CREATE\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF NOT EXISTS\s+)?([^\s]+)/i', $sql, $matches))
				{
					$tableName = $matches[1];
				}
			$result['table']=str_replace('`', '', $tableName);
			if (strcmp(substr($result['table'], 0, strlen($sm['t'])), $sm['t'])==0)
				{
					$result['tableprefix']='$sm[\'t\'].';
					$result['table']=substr($result['table'], strlen($sm['t']));
				}
			if (preg_match('#.*PRIMARY\s+KEY\s+\(`(.*?)`\).*#i', $sql, $matches))
				{
					$result['id']=$matches[1];
				}
			return $result;
		}
	
	function get_postdelete_code($modulename, $sql, $moduletitle, $fields)
		{
			$info=parse_mysql_create($modulename, $sql);
			$str="
			if (sm_action('postdelete'))
				{
					\$q=new TQuery(".$info['tableprefix']."'".$info['table']."');
					\$q->Add('".$info['id']."', intval(\$_getvars['id']));
					\$q->Remove();
					sm_extcore();
					sm_saferemove('index.php?m='.sm_current_module().'&d=view&id='.intval(\$_getvars['id']));
					sm_redirect(\$_getvars['returnto']);
				}
			";
			return $str;
		}
	function get_postadd_code($modulename, $sql, $moduletitle, $fields)
		{
			$info=parse_mysql_create($modulename, $sql);
			$req='';
			for ($i = 0; $i<count($fields); $i++)
				{
					if ($fields[$i]['required'])
						{
							if (!empty($req))
								$req.=' || ';
							$req.="empty(\$_postvars['".$fields[$i]['name']."'])";
						}
				}
			$str="
			if (sm_action('postadd', 'postedit'))
				{
					\$error='';\n";
			if (!empty($req))
			$str.="\t\t\t\t\tif (".$req.")
						\$error=\$lang['messages']['fill_required_fields'];\n";
			$str.="\t\t\t\t\tif (empty(\$error))
						{
							\$q=new TQuery(".$info['tableprefix']."'".$info['table']."');\n";
			for ($i = 0; $i<count($fields); $i++)
				{
					if ($info['id']==$fields[$i]['name'])
						continue;
					if ($fields[$i]['control']=='disabled')
						continue;
					if ($fields[$i]['datatype']=='tinyint' || $fields[$i]['datatype']=='int')
						$str.="\t\t\t\t\t\t\t\$q->Add('".$fields[$i]['name']."', intval(\$_postvars['".$fields[$i]['name']."']));\n";
					elseif ($fields[$i]['datatype']=='decimal')
						$str.="\t\t\t\t\t\t\t\$q->Add('".$fields[$i]['name']."', floatval(\$_postvars['".$fields[$i]['name']."']));\n";
					else
						$str.="\t\t\t\t\t\t\t\$q->Add('".$fields[$i]['name']."', dbescape(\$_postvars['".$fields[$i]['name']."']));\n";
				}
			$str.="\t\t\t\t\t\t\tif (sm_action('postadd'))
								\$q->Insert();
							else
								\$q->Update('".$info['id']."', intval(\$_getvars['id']));
							sm_redirect(\$_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}
			";
			return $str;
		}
	
	function get_add_code($modulename, $sql, $moduletitle, $fields)
		{
			$info=parse_mysql_create($modulename, $sql);
			$setfocus='';
			$str="
			if (sm_action('add', 'edit'))
				{
					add_path_modules();
					add_path('".$moduletitle."', 'index.php?m='.sm_current_module().'&d=list');
					sm_use('ui.interface');
					sm_use('ui.form');
					\$ui = new TInterface();
					if (!empty(\$error))
						\$ui->NotificationError(\$error);
					if (sm_action('edit'))
						{
							sm_title(\$lang['common']['edit']);
							\$f=new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.intval(\$_getvars['id']).'&returnto='.urlencode(\$_getvars['returnto']));
						}
					else
						{
							sm_title(\$lang['common']['add']);
							\$f=new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode(\$_getvars['returnto']));
						}\n";
			for ($i = 0; $i<count($fields); $i++)
				{
					if ($info['id']==$fields[$i]['name'])
						continue;
					if ($fields[$i]['control']=='disabled')
						continue;
					if (empty($setfocus))
						$setfocus=$fields[$i]['name'];
					if ($fields[$i]['datatype']=='tinyint')
						$str.="\t\t\t\t\t\$f->AddCheckbox('".$fields[$i]['name']."', '".$fields[$i]['caption']."'".($fields[$i]['required']?', true':'').");\n";
					elseif ($fields[$i]['datatype']=='text')
						$str.="\t\t\t\t\t\$f->AddTextarea('".$fields[$i]['name']."', '".$fields[$i]['caption']."'".($fields[$i]['required']?', true':'').");\n";
					elseif ($fields[$i]['datatype']=='editor')
						$str.="\t\t\t\t\t\$f->AddEditor('".$fields[$i]['name']."', '".$fields[$i]['caption']."'".($fields[$i]['required']?', true':'').");\n";
					else
						$str.="\t\t\t\t\t\$f->AddText('".$fields[$i]['name']."', '".$fields[$i]['caption']."'".($fields[$i]['required']?', true':'').");\n";
				}
			$str.="\t\t\t\t\tif (sm_action('edit'))
						{
							\$q=new TQuery(".$info['tableprefix']."'".$info['table']."');
							\$q->Add('".$info['id']."', intval(\$_getvars['id']));
							\$f->LoadValuesArray(\$q->Get());
							unset(\$q);
						}
					\$f->LoadValuesArray(\$_postvars);
					\$ui->AddForm(\$f);
					\$ui->Output(true);
					sm_setfocus('".$setfocus."');
				}
			";
			return $str;
		}
	
	function get_list_code($modulename, $sql, $moduletitle, $fields)
		{
			$info=parse_mysql_create($modulename, $sql);
			$str="
			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					include_once('includes/adminbuttons.php');
					add_path_modules();
					add_path('".$moduletitle."', 'index.php?m='.sm_current_module().'&d=list');
					sm_title('".$moduletitle."');
					\$offset=abs(intval(\$_getvars['from']));
					\$limit=30;
					\$ui = new TInterface();
					\$b=new TButtons();
					\$b->AddButton('add', \$lang['common']['add'], 'index.php?m='.sm_current_module().'&d=add&returnto='.urlencode(sm_this_url()));
					\$ui->AddButtons(\$b);
					\$t=new TGrid();\n";
			for ($i = 0; $i<count($fields); $i++)
				$str.="\t\t\t\t\t\$t->AddCol('".$fields[$i]['name']."', '".$fields[$i]['caption']."');\n";
			$str.="\t\t\t\t\t\$t->AddEdit();
					\$t->AddDelete();
					\$q=new TQuery(".$info['tableprefix']."'".$info['table']."');
					\$q->Limit(\$limit);
					\$q->Offset(\$offset);
					\$q->Select();
					for (\$i = 0; \$i<count(\$q->items); \$i++)
						{\n";
			for ($i = 0; $i<count($fields); $i++)
				$str.="\t\t\t\t\t\t\t\$t->Label('".$fields[$i]['name']."', \$q->items[\$i]['".$fields[$i]['name']."']);\n";
			$str.="\t\t\t\t\t\t\t\$t->Url('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.\$q->items[\$i]['".$info['id']."'].'&returnto='.urlencode(sm_this_url()));
							\$t->Url('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.\$q->items[\$i]['".$info['id']."'].'&returnto='.urlencode(sm_this_url()));
							\$t->NewRow();
						}
					\$ui->AddGrid(\$t);
					\$ui->AddPagebarParams(\$q->Find(), \$limit, \$offset);
					\$ui->AddButtons(\$b);
					\$ui->Output(true);
				}
			";
			return ($str);
		}
	
	function get_admin_code($modulename, $sql, $moduletitle, $fields)
		{
			$info=parse_mysql_create($modulename, $sql);
			$str="
			if (\$userinfo['level']==3)
				{
					if (sm_action('admin'))
						{
							add_path_modules();
							sm_title('".$moduletitle."');
							sm_use('ui.interface');
							\$ui = new TInterface();
							\$ui->a('index.php?m='.sm_current_module().'&d=list', \$lang['common']['list']);
							\$ui->Output(true);
						}
					if (sm_action('install'))
						{
							sm_register_module('".$modulename."', '".$moduletitle."');
							//sm_register_autoload('".$modulename."');
							//sm_register_postload('".$modulename."');
							sm_redirect('index.php?m=admin&d=modules');
						}
					if (sm_action('uninstall'))
						{
							sm_unregister_module('".$modulename."');
							//sm_unregister_autoload('".$modulename."');
							//sm_unregister_postload('".$modulename."');
							sm_redirect('index.php?m=admin&d=modules');
						}
				}
			";
			return $str;
		}

	//$info=parse_mysql_create($modulename, $sql);
	if (sm_action('generate'))
		{
			$modulename=$_postvars['module'];
			$moduletitle=$_postvars['title'];
			$sql=$_postvars['sql'];
			$fields=Array();
			$info=parse_mysql_create($modulename, $sql);
			for ($i = 0; $i<count($info['fields']); $i++)
				{
					$fields[$i]['name']=$info['fields'][$i][1];
					$fields[$i]['datatype']=$info['fields'][$i][2];
					$fields[$i]['control']=$_postvars['field_'.$i];
					$fields[$i]['caption']=$_postvars['fieldcap_'.$i];
					$fields[$i]['required']=intval($_postvars['required_'.$i])==1;
				}
			$info='<'.'?'."php\n\n";
			$info.="/*\n";
			$info.="Module Name: ".$moduletitle."\n";
			$info.="Module URI: http://simancms.org/\n";
			$info.="Description: ".$moduletitle."\n";
			$info.="Version: 1.0\n";
			$info.="Revision: ".date('Y-m-d')."\n";
			$info.="Author URI: http://simancms.org/\n";
			$info.="*/\n\n";
			$info.='	if ($userinfo[\'level\']>0)'."\n\t\t{\n";;
			$info.=get_postdelete_code($modulename, $sql, $moduletitle, $fields);
			$info.=get_postadd_code($modulename, $sql, $moduletitle, $fields);
			$info.=get_add_code($modulename, $sql, $moduletitle, $fields);
			$info.=get_list_code($modulename, $sql, $moduletitle, $fields);
			$info.=get_admin_code($modulename, $sql, $moduletitle, $fields);
			$info.="\n\t\t}\n";
			$info.="\n?".'>';
			sm_title('Code Generator');
			sm_use('ui.interface');
			sm_use('ui.form');
			$ui = new TInterface();
			//$ui->html('<pre>'.$info.'</pre>');
			$f = new TForm(false);
			$f->AddTextarea('php', 'PHP Code');
			$f->SetFieldAttribute('php', 'wrap', 'off');
			$f->MergeColumns('php', 'wrap', 'off');
			$f->SetValue('php', $info);
			$ui->AddForm($f);
			$ui->style('#php{height:500px;}');
			$ui->Output(true);
		}
	
	if (sm_action('prepare'))
		{
			sm_title('Code Generator');
			sm_use('ui.interface');
			sm_use('ui.form');
			$ui = new TInterface();
			if ($_getvars['type']=='fields')
				$f = new TForm('index.php?m=modulegenerator&d=generate');
			else
				$f = new TForm('index.php?m=modulegenerator&d=prepare&type=fields');
			$f->AddText('module', 'Module ID')->SetFocus();
			$f->AddText('title', 'Module Title');
			$f->AddTextarea('sql', 'SQL Create Query');
			if ($_getvars['type']=='fields')
				{
					$info=parse_mysql_create($_postvars['module'], $_postvars['sql']);
					for ($i = 0; $i<count($info['fields']); $i++)
						{
							$f->Separator('Field: '.$info['fields'][$i][1]);
							$f->AddSelectVL('field_'.$i, 'Type', Array('text', 'textarea', 'editor', 'checkbox', 'disabled'), Array('Text', 'Textarea', 'WYSIWYG editor', 'Checkbox', 'Disabled'));
							if ($info['fields'][$i][2]=='tinyint')
								$f->WithValue('checkbox');
							elseif ($info['fields'][$i][2]=='text')
								$f->WithValue('textarea');
							else
								$f->WithValue('text');
							$f->AddText('fieldcap_'.$i, 'Caption');
							$cap=str_replace('_', ' ', $info['fields'][$i][1]);
							$cap=strtoupper(substr($cap, 0, 1)).substr($cap, 1);
							$f->WithValue($cap);
							$f->AddCheckbox('required_'.$i, 'Required');
						}
				}
			$f->LoadValuesArray($_postvars);
			$f->SaveButton('Next');
			$ui->AddForm($f);
			$ui->Output(true);
		}
	
	if ($userinfo['level'] == 3)
		{
			if (sm_action('admin'))
				{
					sm_redirect('index.php?m=modulegenerator&d=prepare');
				}
			if (sm_action('install'))
				{
					sm_register_module('modulegenerator', 'Code Generator');
					sm_redirect('index.php?m=admin&d=modules');
				}
			if (sm_action('uninstall'))
				{
					sm_unregister_module('modulegenerator');
					sm_redirect('index.php?m=admin&d=modules');
				}
		}

?>
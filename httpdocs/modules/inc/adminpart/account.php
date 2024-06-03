<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-06-06
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if ($userinfo['level'] == 3)
		{
			if (sm_action('postdelete') && intval($_getvars['uid'])>1)
				{
					TQuery::ForTable($tableusersprefix."users")->Add('id_user', intval($_getvars['uid']))->Remove();
					sm_event('deleteuser', array(intval($_getvars['uid'])));
					sm_redirect($_getvars['returnto']);
				}
			if (sm_action('setstatus') && intval($_getvars['uid'])>1)
				{
					$q=new TQuery($tableusersprefix."users");
					$q->Add('user_status', intval($_getvars['status']));
					$q->Update('id_user', intval($_getvars['uid']));
					sm_redirect($_getvars['returnto']);
				}
			if (sm_actionpost('postedituser'))
				{
					$info=TQuery::ForTable($sm['tu'].'users')->Add('id_user', intval($_getvars['id']))->Get();
					if (!is_email($_postvars['email']))
						{
							$error=$lang['messages']['wrong_email'];
							sm_set_action('edituser');
						}
					elseif (!empty($info['id_user']) && intval($info['id_user'])!=1)
						{
							if (strlen($_postvars['pwd'])>0)
								{
									sm_set_userfield($info['id_user'], 'password', md5($_postvars['pwd']));
								}
							sm_set_userfield($info['id_user'], 'email', $_postvars['email']);
							sm_set_userfield($info['id_user'], 'get_mail', intval($_postvars['get_mail']));
							sm_extcore();
							$q=new TQuery($sm['t'].'groups');
							$q->OrderBy('title_group');
							$q->Select();
							for ($i = 0; $i<$q->Count(); $i++)
								{
									if (intval($_postvars['group_'.$q->items[$i]['id_group']])==1)
										sm_set_group($q->items[$i]['id_group'], Array($info['id_user']));
									else
										sm_unset_group($q->items[$i]['id_group'], Array($info['id_user']));
								}
							sm_event('onchangeuserbyadmin', array($info['id_user']));
							if (!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto']);
							else
								sm_redirect('index.php?m=account&d=edituser&id='.$info['id_user']);
						}
				}
			if (sm_action('edituser'))
				{
					add_path_control();
					add_path($lang['user_list'], 'index.php?m=account&d=usrlist');
					add_path_current();
					sm_title($lang['change']);
					$info=TQuery::ForTable($sm['tu'].'users')->Add('id_user', intval($_getvars['id']))->Get();
					if (!empty($info['id_user']) && intval($info['id_user'])!=1)
						{
							sm_use('admininterface');
							sm_use('adminform');
							$ui = new TInterface();
							if (!empty($error))
								$ui->NotificationError($lang['messages']['wrong_email']);
							$f = new TForm('index.php?m=account&d=postedituser&id='.$info['id_user'].'&returnto='.urlencode($_getvars['returnto']));
							$f->AddStatictext('login', $lang['login_str']);
							$f->AddText('pwd', $lang['password']);
							$f->SetFieldBottomText('pwd', $lang['set_passwords_if_want_to_change']);
							$f->AddText('email', $lang['email']);
							$f->AddCheckbox('get_mail', $lang['module_account']['get_mail_from_admin']);
							$f->LabelAfterControl();
							$q=new TQuery($sm['t'].'groups');
							$q->OrderBy('title_group');
							$q->Select();
							if ($q->Count()>0)
								{
									$f->AddSeparator('groups', $lang['module_account']['groups']);
									for ($i = 0; $i<$q->Count(); $i++)
										{
											$f->AddCheckbox('group_'.$q->items[$i]['id_group'], $q->items[$i]['title_group'].(empty($q->items[$i]['description_group'])?'':' ('.$q->items[$i]['description_group'].')'));
											$f->LabelAfterControl();
											if (sm_isuseringroup($info['id_user'], $q->items[$i]['id_group']))
												$f->SetValue('group_'.$q->items[$i]['id_group'], 1);
										}
								}
							$f->LoadValuesArray($info);
							$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
						}
				}
			if (sm_action('usrlist'))
				{
					add_path_control();
					add_path($lang["user_list"], 'index.php?m=account&d=usrlist');
					$m["module"] = 'account';
					$m["title"] = $lang["user_list"];
					if (empty($_getvars['sellogin']))
						$_getvars['sellogin'] = $_postvars['sellogin'];
					sm_use('admininterface');
					sm_use('admintable');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$b = new TButtons();
					$b->AddButton('add', $lang['register_user'], 'index.php?m=account&d=register');
					$ui->AddButtons($b);
					$t = new TGrid();
					$limit = sm_settings('admin_items_by_page');
					$offset = abs(intval($_getvars['from']));
					$t->AddCol('user', $lang['user'], '60%');
					$t->AddCol('status', $lang['status'], '20%');
					$t->AddCol('action', $lang['action'], '20%');
					$q=new TQuery($tableusersprefix."users");
					if (!empty($_getvars['sellogin']))
						$q->Add("login LIKE '".dbescape($_getvars['sellogin'])."%'");
					if (!empty($_getvars['group']))
						{
							$groupinfo=TQuery::ForTable($sm['t'].'groups')->Add('id_group', intval($_getvars['group']))->Get();
							add_path($groupinfo['title_group'], 'index.php?m=account&d=usrlist&group='.$groupinfo['id_group']);
							$q->Add("id_user IN (SELECT object_id FROM ".$sm['t']."taxonomy WHERE object_name='usergroups' AND rel_id=".intval($groupinfo['id_group']).")");
						}
					$q->OrderBy("login");
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Open();
					$i = 0;
					while ($row = $q->Fetch($result))
						{
							$t->Label('user', $row['login'].'<br />'.$row['email']);
							if ($row['user_status'] == 0)
								$t->Label('status', $lang['module_account']['unactivated_user']);
							elseif ($row['user_status'] == 1)
								$t->Label('status', $lang['normal_user']);
							elseif ($row['user_status'] == 2)
								$t->Label('status', $lang['privileged_user']);
							elseif ($row['user_status'] == 3)
								$t->Label('status', $lang['super_user']);
							if ($row['user_status'] != 0 && $row['id_user']!=1)
								$t->DropDownItem('status', $lang['module_account']['unactivated_user'], 'index.php?m=account&d=setstatus&uid='.$row['id_user'].'&status=0&returnto='.urlencode(sm_this_url()));
							if ($row['user_status'] != 1 && $row['id_user']!=1)
								$t->DropDownItem('status', $lang['normal_user'], 'index.php?m=account&d=setstatus&uid='.$row['id_user'].'&status=1&returnto='.urlencode(sm_this_url()));
							if ($row['user_status'] != 2 && $row['id_user']!=1)
								$t->DropDownItem('status', $lang['privileged_user'], 'index.php?m=account&d=setstatus&uid='.$row['id_user'].'&status=2&returnto='.urlencode(sm_this_url()));
							if ($row['user_status'] != 3 && $row['id_user']!=1)
								$t->DropDownItem('status', $lang['super_user'], 'index.php?m=account&d=setstatus&uid='.$row['id_user'].'&status=3&returnto='.urlencode(sm_this_url()));
							$t->Label('action', $lang['details']);
							if ($row['id_user']!=1)
								{
									$t->DropDownItem('action', $lang['module_account']['set_password'], 'index.php?m=account&d=setpwd&uid='.$row['id_user'].'&returnto='.urlencode(sm_this_url()));
									$t->DropDownItem('action', $lang['common']['edit'], 'index.php?m=account&d=edituser&id='.$row['id_user'].'&returnto='.urlencode(sm_this_url()));
									$t->DropDownItem('action', $lang['delete'], 'index.php?m=account&d=postdelete&uid='.$row['id_user'].'&returnto='.urlencode(sm_this_url()), $lang['really_want_delete_user']);
								}
							$t->NewRow();
							$i++;
						}
					$ui->AddGrid($t);
					$ui->div('<form action="index.php"><input type="hidden" name="m" value="account"><input type="hidden" name="d" value="usrlist">'.$lang['search'].': <input type="text" name="sellogin" value="'.htmlescape($sm['g']['sellogin']).'"></form>');
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('postsetpwd'))
				{
					$usr = sm_userinfo(intval($_getvars['uid']));
					if (!empty($usr['id']))
						{
							if (empty($_postvars['newpwd']))
								{
									$errormessage=$lang['message_set_all_fields'];
									sm_set_action('setpwd');
								}
							else
								{
									$password = md5($_postvars['newpwd']);
									$random_code = md5($id_user.microtime().rand());
									execsql("UPDATE ".$tableusersprefix."users SET password = '".dbescape($password)."', random_code='".dbescape($random_code)."' WHERE id_user=".intval($usr['id'])." AND id_user>1");
									sm_notify($lang['module_account']['message_set_password_finish']);
									sm_redirect($_getvars['returnto']);
								}
						}
				}
			if (sm_action('setpwd'))
				{
					$usr=sm_userinfo(intval($_getvars['uid']));
					if (!empty($usr['id']))
						{
							add_path_control();
							add_path($lang['user_list'], 'index.php?m=account&d=usrlist');
							add_path_current($lang['set_password']);
							sm_title($lang['module_account']['type_new_password_for_user']);
							sm_use('ui.interface');
							sm_use('ui.form');
							$ui = new TInterface();
							if (!empty($errormessage))
								$ui->NotificationError($errormessage);
							$f = new TForm('index.php?m=account&d=postsetpwd&uid='.$usr['id'].'&returnto='.urlencode($_getvars['returnto']));
							$f->AddLabel('login', $lang['login_str'], $usr['login']);
							$f->AddText('newpwd', $lang['password']);
							$f->LoadValuesArray($_postvars);
							$f->SaveButton($lang['set_password']);
							$ui->AddForm($f);
							$ui->Output(true);
							sm_setfocus('newpwd');
						}
				}
			if (sm_action('listgroups'))
				{
					add_path_control();
					add_path($lang['module_account']['groups'], 'index.php?m=account&d=listgroups');
					$m['module'] = 'account';
					$m['title'] = $lang['module_account']['groups'];
					sm_use('admintable');
					sm_use('admininterface');
					sm_use('adminbuttons');
					$ui = new TInterface();
					$t=new TGrid();
					$t->AddCol('title', $lang['common']['title'], '100%');
					$t->AddCol('search', '', '16', $lang['search'], '', 'search.gif');
					$t->AddEdit();
					$t->AddDelete();
					$q=new TQuery($sm['t'].'groups');
					$q->OrderBy('title_group');
					$q->Open();
					$i = 0;
					while ($row = $q->Fetch())
						{
							$t->Label('title', $row['title_group']);
							$t->Hint('title', htmlescape($row['description_group']));
							$t->URL('search', 'index.php?m=account&d=usrlist&group='.$row['id_group']);
							$t->URL('edit', 'index.php?m=account&d=editgroup&id='.$row['id_group']);
							$t->URL('delete', 'index.php?m=account&d=postdeletegroup&id='.$row['id_group']);
							$t->NewRow();
							$i++;
						}
					$b=new TButtons();
					$b->AddButton('add', $lang['module_account']['add_group'], 'index.php?m=account&d=addgroup');
					$ui->AddButtons($b);
					$ui->AddGrid($t);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_actionpost('postaddgroup'))
				{
					$m['module'] = 'account';
					$m['title'] = $lang['module_account']['add_group'];
					$q = new TQuery($sm['t'].'groups');
					$q->AddPost('title_group');
					$q->AddPost('description_group');
					$q->Add('autoaddtousers_group', intval($_postvars['autoaddtousers_group']));
					$q->Insert();
					sm_redirect('index.php?m=account&d=listgroups');
				}
			if (sm_actionpost('posteditgroup'))
				{
					$m['module'] = 'account';
					$m['title'] = $lang['common']['edit'];
					$q = new TQuery($sm['t'].'groups');
					$q->AddPost('title_group');
					$q->AddPost('description_group');
					$q->Add('autoaddtousers_group', intval($_postvars['autoaddtousers_group']));
					$q->Update('id_group', intval($_getvars['id']));
					sm_redirect('index.php?m=account&d=listgroups');
				}
			if (sm_action('addgroup', 'editgroup'))
				{
					if (sm_action('addgroup'))
						sm_title($lang['module_account']['add_group']);
					else
						sm_title($lang['common']['edit']);
					sm_use('adminform');
					sm_use('admininterface');
					$ui = new TInterface();
					add_path_control();
					add_path($lang['module_account']['groups_management'], 'index.php?m=account&d=listgroups');
					add_path_current();
					if (sm_action('addgroup'))
						$f = new TForm('index.php?m=account&d=postaddgroup');
					else
						$f = new TForm('index.php?m=account&d=posteditgroup&id='.$_getvars['id']);
					$f->AddText('title_group', $lang['module_account']['title_group']);
					$f->AddTextarea('description_group', $lang['module_account']['description_group']);
					$f->AddCheckbox('autoaddtousers_group', $lang['module_account']['add_to_new_users'], 1);
					if (sm_action('editgroup'))
						$f->LoadValues('SELECT * FROM '.$sm['t'].'groups WHERE id_group='.intval($_getvars['id']));
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title_group');
				}
			if (sm_action('postdeletegroup'))
				{
					sm_extcore();
					sm_delete_group(intval($_getvars['id']));
					sm_redirect('index.php?m=account&d=listgroups');
				}
		}

?>
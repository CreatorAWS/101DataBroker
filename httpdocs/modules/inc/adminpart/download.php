<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.5
	//#revision 2013-10-29
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if ($userinfo['level'] == 3 || (intval(sm_settings('perm_downloads_management_level'))>0 && sm_settings('perm_downloads_management_level')<=intval($userinfo['level'])))
		{
			if (sm_action('deleteattachment'))
				{
					$m["module"] = 'download';
					sm_title($lang['common']['delete']);
					$_msgbox['mode'] = 'yesno';
					$_msgbox['title'] = $lang['common']['delete'];
					$_msgbox['msg'] = $lang['module_download']['really_want_delete_file'];
					$_msgbox['yes'] = 'index.php?m=download&d=postdeleteattachment&id='.intval($_getvars["id"]);
					$_msgbox['no'] = 'index.php?m=download';
				}
			if (sm_action('postdeleteattachment'))
				{
					sm_delete_attachment(intval($_getvars['id']));
					sm_event('postdeleteattachment', array(intval($_getvars['id'])));
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m=download&d=list');
				}
			if (sm_action('postdelete'))
				{
					$q=new TQuery('sm_downloads');
					$q->Add('id_download', intval($_getvars['id']));
					$info=$q->Get();
					$filename='files/download/'.basename($info['file_download']);
					$q->Remove();
					sm_extcore();
					if (file_exists($filename))
						unlink($filename);
					sm_saferemove($filename);
					//sm_notify($lang['module_download']['delete_file_successful']);
					if (!empty($_getvars['returnto']))
						sm_redirect($_getvars['returnto']);
					else
						sm_redirect('index.php?m=download&d=list');
				}
			if (sm_action('postadd'))
				{
					sm_extcore();
					$descr = dbescape($_postvars['p_shortdesc']);
					$fs = $_uplfilevars['userfile']['tmp_name'];
					if (empty($_postvars['p_optional']))
						{
							$fd = basename($_uplfilevars['userfile']['name']);
						}
					else
						{
							$fd = basename($_postvars['optional_name']);
						}
					$fd = './files/download/'.$fd;
					if (empty($fs))
						{
							$error = $lang["message_set_all_fields"];
							sm_set_action('add');
						}
					elseif (!sm_is_allowed_to_upload($fd))
						{
							$error = $lang['error_file_upload_message'];
							sm_set_action('upload');
						}
					elseif (file_exists($fd))
						{
							$error = $lang['module_download']['file_already_exists'];
							sm_set_action('add');
						}
					elseif (!move_uploaded_file($fs, $fd))
						{
							$error=$lang['error_file_upload_message'];
							sm_set_action('add');
						}
					else
						{
							$q=new TQuery($sm['t'].'downloads');
							$q->Add('file_download', dbescape(basename($fd)));
							$q->Add('description_download', dbescape($_postvars['description_download']));
							$q->Add('userlevel_download', intval($_postvars['userlevel_download']));
							$q->Insert();
							//sm_notify($lang['operation_complete']);
							if (!empty($_getvars['returnto']))
								sm_redirect($_getvars['returnto']);
							else
								sm_redirect('index.php?m=download&d=list');
						}
				}
			if (sm_action('postupload'))
				{
					sm_extcore();
					$q = new TQuery($sm['t'].'downloads');
					$q->Add('id_download', intval($_getvars['id']));
					$info = $q->Get();
					if (!empty($info['id_download']))
						{
							$fs = $_uplfilevars['userfile']['tmp_name'];
							$fd = './files/download/'.$info['file_download'];
							if (empty($fs))
								{
									$error = $lang["message_set_all_fields"];
									sm_set_action('upload');
								}
							elseif (!sm_is_allowed_to_upload($fd))
								{
									$error = $lang['error_file_upload_message'];
									sm_set_action('upload');
								}
							elseif (!file_exists($fs))
								{
									$error = $lang['error_file_upload_message'];
									sm_set_action('upload');
								}
							else
								{
									if (file_exists($fd))
										{
											$tmp['file'] = 'files/temp/'.md5(time()).rand(1, 9999);
											$tmp['tmpfilecreated'] = true;
											rename($fd, $tmp['file']);
										}
									if (!move_uploaded_file($fs, $fd))
										{
											$error = $lang['error_file_upload_message'];
											sm_set_action('upload');
											if ($tmp['tmpfilecreated'])
												rename($tmp['file'], $fd);
										}
									else
										{
											if ($tmp['tmpfilecreated'])
												unlink($tmp['file']);
											//sm_notify($lang['operation_complete']);
											if (!empty($_getvars['returnto']))
												sm_redirect($_getvars['returnto']);
											else
												sm_redirect('index.php?m=download&d=view');
										}
								}
						}
				}
			if (sm_action('upload'))
				{
					add_path_modules();
					add_path($lang['module_download']['downloads'], 'index.php?m=download&d=admin');
					add_path($lang['common']['list'], 'index.php?m=download&d=list');
					add_path_current();
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->div($error, '', 'errormessage error-message');
					sm_title($lang['module_download']['upload_file']);
					$f=new TForm('index.php?m=download&d=postupload&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
					$f->AddFile('userfile', $lang['file_name'], true);
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('userfile');
				}
			if (sm_action('postedit'))
				{
					sm_extcore();
					$q = new TQuery($sm['t'].'downloads');
					$q->Add('id_download', intval($_getvars['id']));
					$info = $q->Get();
					if (!empty($info['id_download']))
						{
							if (!empty($_postvars['optional_name']) && file_exists('files/download/'.basename($_postvars['optional_name'])))
								{
									$error = $lang['module_download']['file_already_exists'];
									sm_set_action('edit');
								}
							elseif (!empty($_postvars['optional_name']) && !sm_is_allowed_to_upload($_postvars['optional_name']))
								{
									$error = $lang['module_admin']['message_wrong_file_name'];
									sm_set_action('edit');
								}
							elseif (!empty($_postvars['optional_name']) && !rename('files/download/'.basename($info['file_download']), 'files/download/'.basename($_postvars['optional_name'])))
								{
									$error = $lang['error'];
									sm_set_action('edit');
								}
							else
								{
									$q = new TQuery($sm['t'].'downloads');
									if (!empty($_postvars['optional_name']))
										$q->Add('file_download', dbescape($_postvars['optional_name']));
									$q->AddPost('description_download');
									$q->Add('userlevel_download', intval($_postvars['userlevel_download']));
									$q->Update('id_download', intval($_getvars['id']));
									if (!empty($_getvars['returnto']))
										sm_redirect($_getvars['returnto']);
									else
										sm_redirect('index.php?m=downloads&d=list');
								}
						}
				}
			if (sm_action('edit'))
				{
					$q=new TQuery($tableprefix."downloads");
					$q->Add('id_download', intval($_getvars['id']));
					$info=$q->Get();
					if (!empty($info['id_download']))
						{
							add_path_modules();
							add_path($lang['module_download']['downloads'], 'index.php?m=download&d=admin');
							add_path($lang['common']['list'], 'index.php?m=download&d=list');
							add_path_current();
							sm_use('admininterface');
							sm_use('adminform');
							$ui = new TInterface();
							if (!empty($error))
								$ui->div($error, '', 'errormessage error-message');
							sm_title($lang['edit']);
							$f=new TForm('index.php?m=download&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
							$f->AddTextarea('description_download', $lang['module_download']['short_description_download']);
							$f->AddSelectVL('userlevel_download', $lang['can_view'], Array(0, 1, 2, 3), Array($lang['all_users'], $lang['logged_users'], $lang['power_users'], $lang['administrators']));
							$f->AddText('optional_name', $lang['optional_file_name']);
							$f->LoadValuesArray($info);
							$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
							sm_setfocus('description_download');
						}
				}
			if (sm_action('add'))
				{
					add_path_modules();
					add_path($lang['module_download']['downloads'], 'index.php?m=download&d=admin');
					add_path($lang['common']['list'], 'index.php?m=download&d=list');
					add_path_current();
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->div($error, '', 'errormessage error-message');
					sm_title($lang['module_download']['upload_file']);
					$f=new TForm('index.php?m=download&d=postadd&returnto='.urlencode($_getvars['returnto']));
					$f->AddFile('userfile', $lang['file_name'], true);
					$f->AddTextarea('description_download', $lang['module_download']['short_description_download']);
					$f->AddSelectVL('userlevel_download', $lang['can_view'], Array(0, 1, 2, 3), Array($lang['all_users'], $lang['logged_users'], $lang['power_users'], $lang['administrators']));
					$f->AddText('optional_name', $lang['optional_file_name']);
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('userfile');
				}
			if (sm_action('list'))
				{
					sm_use('admininterface');
					sm_use('adminform');
					sm_use('admintable');
					sm_use('adminbuttons');
					add_path_modules();
					add_path($lang['module_download']['downloads'], 'index.php?m=download&d=admin');
					add_path($lang['common']['list'], 'index.php?m=download&d=list');
					sm_title($lang['module_download']['downloads']);
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=download&d=add&returnto='.urlencode(sm_this_url()));
					$ui->AddButtons($b);
					$t=new TGrid();
					$t->AddCol('file_download', $lang['file_name']);
					$t->AddCol('description_download', $lang['common']['description']);
					$t->AddCol('userlevel_download', $lang['can_view']);
					$t->AddCol('upload', $lang['module_download']['upload_file']);
					$t->AddEdit();
					$t->AddDelete();
					$q=new TQuery($sm['t'].'downloads');
					$q->AddWhere('attachment_from', '-');
					$q->OrderBy('file_download');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<count($q->items); $i++)
						{
							$t->Label('file_download', $q->items[$i]['file_download']);
							$t->URL('file_download', 'files/download/'.$q->items[$i]['file_download'], true);
							$t->Label('description_download', $q->items[$i]['description_download']);
							if ($q->items[$i]['userlevel_download']==0)
								$t->Label('userlevel_download', $lang['all_users']);
							elseif ($q->items[$i]['userlevel_download']==1)
								$t->Label('userlevel_download', $lang['logged_users']);
							elseif ($q->items[$i]['userlevel_download']==2)
								$t->Label('userlevel_download', $lang['power_users']);
							else
								$t->Label('userlevel_download', $lang['administrators']);
							$t->Label('upload', $lang['module_download']['upload_file']);
							$t->Url('upload', 'index.php?m=download&d=upload&id='.$q->items[$i]['id_download'].'&returnto='.urlencode(sm_this_url()));
							$t->Url('edit', 'index.php?m=download&d=edit&id='.$q->items[$i]['id_download'].'&returnto='.urlencode(sm_this_url()));
							$t->Url('delete', 'index.php?m=download&d=postdelete&id='.$q->items[$i]['id_download'].'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('admin'))
				{
					add_path_modules();
					add_path_current();
					sm_extcore();
					sm_title($lang['control_panel'].' :: '.$lang['module_download']['downloads']);
					sm_use('admininterface');
					$ui = new TInterface();
					$ui->a('index.php?m=download&d=list', $lang['module_download']['downloads']);
					$ui->br();
					$ui->br();
					$ui->a('index.php?m=download&d=add', $lang['module_download']['upload_file']);
					$ui->br();
					$ui->br();
					$ui->a(sm_tomenuurl($lang['module_download']['downloads'], sm_fs_url('index.php?m=download&d=view')), $lang['add_to_menu'].' - '.$lang['module_download']['downloads']);
					$ui->br();
					$ui->br();
					$ui->Output(true);
				}
		}

?>
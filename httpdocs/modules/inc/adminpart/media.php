<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2014-06-07
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("MEDIAADMIN_FUNCTIONS_DEFINED"))
		{
			function siman_thumb_for_media($filepath)
				{
					$info=pathinfo($filepath);
					$filename=$info['dirname'].'/'.$info['filename'].'-small.'.$info['extension'];
					if (file_exists($filename))
						return $filename;
					else
						return $filepath;
				}
			function siman_update_media_category_count($id_ctg)
				{
					global $sm;
					if ($id_ctg==0)
						return;
					$count=TQuery::ForTable($sm['t'].'media')->Add('id_ctg', intval($id_ctg))->Find();
					TQuery::ForTable($sm['t'].'categories_media')->Add('items_count', intval($count))->Update('id_ctg', intval($id_ctg));
				}
			function siman_notemptyuintwithdefault($int, $default)
				{
					if (intval($int)<=0)
						return intval($default);
					return $int;
				}

			define("MEDIAADMIN_FUNCTIONS_DEFINED", 1);
		}
	
	if ($userinfo['level']==3)
		{
			sm_include_lang('media');

			if (sm_action('postdelete'))
				{
					$info=TQuery::ForTable($sm['t'].'media')->Add('id', intval($_getvars['id']))->Get();
					$q = new TQuery($sm['t'].'media');
					$q->Add('id', intval($_getvars['id']));
					$q->Remove();
					siman_update_media_category_count(intval($info['id_ctg']));
					sm_extcore();
					sm_saferemove('index.php?m=media&d=view&id='.intval($_getvars['id']));
					if (file_exists(siman_thumb_for_media($info['filepath'])))
						unlink(siman_thumb_for_media($info['filepath']));
					if (file_exists(siman_medium_for_media($info['filepath'])))
						unlink(siman_medium_for_media($info['filepath']));
					if (file_exists($info['filepath']))
						unlink($info['filepath']);
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd'))
				{
					sm_extcore();
					$ctg=TQuery::ForTable($sm['t'].'categories_media')->Add('id_ctg', intval($_getvars['ctg']))->Get();
					unset($error);
					for ($i = 0; $i < count($_uplfilevars['userfile']['name']); $i++)
						{
							if (empty($_uplfilevars['userfile']['name'][$i]))
								continue;
							$extension=strtolower(pathinfo($_uplfilevars['userfile']['name'][$i], PATHINFO_EXTENSION));
							if (!sm_is_allowed_to_upload($_uplfilevars['userfile']['name'][$i]) || !in_array($extension, nllistToArray(sm_settings('media_allowed_extensions'), true)))
								{
									$error[]=$lang['module_admin']['message_wrong_file_name'].' '.$_uplfilevars['userfile']['name'][$i];
								}
							elseif ($tmpfile=sm_upload_file('userfile', '', $i))
								{
									$q = new TQuery($sm['t'].'media');
									$q->Add('id_ctg', intval($ctg['id_ctg']));
									$q->Add('type', dbescape($_uplfilevars['userfile'][$i]['type']));
									$q->Add('title', dbescape(pathinfo($_uplfilevars['userfile']['name'][$i], PATHINFO_FILENAME)));
									$q->Add('originalname', dbescape($_uplfilevars['userfile']['name'][$i]));
									$q->Add('alt_text', dbescape($_postvars['alt_text']));
									$q->Add('description', dbescape($_postvars['description']));
									$id=$q->Insert();
									$filename='files/img/mediaimage'.$id.'.'.$extension;
									$filename_medium='files/img/mediaimage'.$id.'-medium.'.$extension;
									$filename_small='files/img/mediaimage'.$id.'-small.'.$extension;
									$q = new TQuery($sm['t'].'media');
									$q->Add('filepath', dbescape($filename));
									$q->Update('id', intval($id));
									rename($tmpfile, $filename);
									sm_extcore();
									sm_resizeimage($filename, $filename_small, sm_settings('media_thumb_width'), sm_settings('media_thumb_height'), 0, 100, 1);
									sm_resizeimage($filename, $filename_medium, sm_settings('media_medium_width'), sm_settings('media_meduim_height'));
									siman_update_media_category_count(intval($ctg['id_ctg']));
								}
							else
								{
									$error[]=$lang['error_file_upload_message'].' '.$_uplfilevars['userfile']['name'][$i];
								}
						}
					if (is_array($error))
						sm_set_action('add');
					else
						{
							if (intval(sm_get_settings('media_edit_after_upload', 'media'))==1)
								sm_redirect('index.php?m=media&d=edit&id='.intval($id).'&returnto='.urlencode($_getvars['returnto']));
							else
								sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('postedit'))
				{
					$info=TQuery::ForTable($sm['t'].'media')->Add('id', intval($_getvars['id']))->Get();
					if (!empty($info['id']))
						{
							$q = new TQuery($sm['t'].'media');
							$q->Add('id_ctg', intval($_postvars['id_ctg']));
							$q->Add('title', dbescape($_postvars['title']));
							$q->Add('alt_text', dbescape($_postvars['alt_text']));
							$q->Add('description', dbescape($_postvars['description']));
							if (sm_action('postadd'))
								$q->Insert();
							else
								$q->Update('id', intval($_getvars['id']));
							if (intval($info['id_ctg'])!=intval($_postvars['id_ctg']))
								siman_update_media_category_count(intval($info['id_ctg']));
							siman_update_media_category_count(intval($_postvars['id_ctg']));
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('edit'))
				{
					add_path_modules();
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					add_path($lang['module_galleies']['galleries'], 'index.php?m=media&d=libraries');
					add_path_current();
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					sm_title($lang['common']['edit']);
					$q=new TQuery($sm['t'].'categories_media');
					$q->OrderBy('title');
					$q->Select();
					$f = new TForm('index.php?m=media&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
					$f->AddStatictext('filepath', $lang['file_name']);
					$f->AddSelectVL('id_ctg', $lang['common']['category'], $q->ColumnValues('id_ctg'), $q->ColumnValues('title'));
					$f->SelectAddBeginVL('id_ctg', 0, $lang['common']['uncategorized']);
					$f->AddText('title', $lang['common']['title']);
					$f->AddText('alt_text', $lang['common']['alt_text']);
					$f->AddTextarea('description', $lang['common']['description']);
					if (sm_action('edit'))
						{
							$q = new TQuery($sm['t'].'media');
							$q->Add('id', intval($_getvars['id']));
							$f->LoadValuesArray($q->Get());
							unset($q);
						}
					if (is_array($_postvars))
						$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('id_ctg');
				}

			if (sm_action('detailinfo'))
				{
					$image=TQuery::ForTable($sm['t'].'media')->Add('id', intval($_getvars['id']))->Get();
					if (!empty($image['id']))
						{
							add_path_modules();
							add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
							add_path($lang['module_galleies']['galleries'], 'index.php?m=media&d=libraries');
							$ctg=TQuery::ForTable($sm['t'].'categories_media')->Add('id_ctg', intval($_getvars['ctg']))->Get();
							if (!empty($ctg['id_ctg']))
								add_path($ctg['title'], 'index.php?m=media&d=list&ctg='.$ctg['id_ctg']);
							else
								add_path($lang['common']['uncategorized'], 'index.php?m=media&d=list&ctg=0');
							add_path_current();
							sm_title($lang['common']['image'].' - '.$image['title']);
							sm_use('admininterface');
							sm_use('adminform');
							$ui = new TInterface();
							$ui->div_open('image-detail-'.$image['id'], 'image-detail');
							$ui->img($image['filepath']);
							$ui->div_close();
							$ui->Output(true);
						}
				}

			if (sm_action('add'))
				{
					add_path_modules();
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					add_path($lang['module_galleies']['galleries'], 'index.php?m=media&d=libraries');
					$ctg=TQuery::ForTable($sm['t'].'categories_media')->Add('id_ctg', intval($_getvars['ctg']))->Get();
					if (!empty($ctg['id_ctg']))
						add_path($ctg['title'], 'index.php?m=media&d=list&ctg='.$ctg['id_ctg']);
					else
						add_path($lang['common']['uncategorized'], 'index.php?m=media&d=list&ctg=0');
					add_path_current();
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (is_array($error))
						for ($i = 0; $i < count($error); $i++)
							$ui->NotificationError($error[$i]);
					sm_title($lang['common']['add']);
					$f = new TForm('index.php?m=media&d=postadd&ctg='.intval($ctg['id_ctg']).'&returnto='.urlencode($_getvars['returnto']));
					for ($i = 0; $i < 10; $i++)
						{
							$f->AddFile('userfile['.$i.']', $lang['common']['file']);
						}
					if (is_array($_postvars))
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
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					add_path($lang['module_galleies']['galleries'], 'index.php?m=media&d=libraries');
					add_path_current();
					$ctg=TQuery::ForTable($sm['t'].'categories_media')->Add('id_ctg', intval($_getvars['ctg']))->Get();
					if (!empty($ctg['id_ctg']))
						sm_title($ctg['title']);
					else
						sm_title($lang['common']['uncategorized']);
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$b = new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=media&d=add&ctg='.intval($_getvars['ctg']).'&returnto='.urlencode(sm_this_url()));
					$ui->AddButtons($b);
					$t = new TGrid();
					$t->AddCol('id', $lang['common']['id']);
					$t->AddCol('image', $lang['common']['image']);
					$t->AddCol('type', 'type');
					$t->AddCol('title', $lang['common']['title']);
					$t->AddCol('description', $lang['common']['description']);
					$t->AddEdit();
					$t->AddDelete();
					$q = new TQuery($sm['t'].'media');
					$q->Add('id_ctg', intval($_getvars['ctg']));
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i < count($q->items); $i++)
						{
							$t->Label('id', $q->items[$i]['id']);
							$t->Image('image', siman_thumb_for_media($q->items[$i]['filepath']));
							$t->URL('image', 'index.php?m=media&d=detailinfo&id='.$q->items[$i]['id']);
							$t->Label('type', $q->items[$i]['type']);
							$t->Label('title', $q->items[$i]['title']);
							$t->Label('description', $q->items[$i]['description']);
							$t->Url('edit', 'index.php?m=media&d=edit&id='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
							$t->Url('delete', 'index.php?m=media&d=postdelete&id='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

			if (sm_action('postdeletectg'))
				{
					$q=new TQuery($sm['t'].'categories_media');
					$q->Add('id_ctg', intval($_getvars['id']));
					$q->Remove();
					unset($q);
					sm_extcore();
					sm_saferemove('index.php?m=media&d=list&id='.intval($_getvars['id']));
					if (file_exists('files/img/mediagallery'.intval($_getvars['id']).'.jpg'))
						unlink('files/img/mediagallery'.intval($_getvars['id']).'.jpg');
					$q=new TQuery($sm['t'].'_media');
					$q->Add('id_ctg', 0);
					$q->Update('id_ctg', intval($_getvars['id']));
					unset($q);
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postaddctg', 'posteditctg'))
				{
					if (empty($_postvars['title']))
						{
							$error=	$lang['messages']['fill_required_fields']='Заповніть необхідні поля';
							if (sm_action('postaddctg'))
								sm_set_action('addctg');
							else
								sm_set_action('editctg');
						}
					else
						{
							$q=new TQuery($sm['t'].'categories_media');
							$q->Add('title', dbescape($_postvars['title']));
							$q->Add('public', intval($_postvars['public']));
							$q->Add('keywords', dbescape($_postvars['keywords']));
							$q->Add('description', dbescape($_postvars['description']));
							$q->Add('items_count', dbescape($_postvars['items_count']));
							$q->Add('lastupdate', time());
							if (sm_action('postaddctg'))
								$id=$q->Insert();
							else
								{
									$id=intval($_getvars['id']);
									$q->Update('id_ctg', $id);
								}
							if (sm_action('postaddctg'))
								sm_fs_update($lang['module_galleies']['gallery'].' - '.$_postvars['title'], 'index.php?m=media&d=gallery&id='.$id, 'media/galleries/'.$id.'-'.sm_getnicename($_postvars['title']).'.html');
							if ($file=sm_upload_file('userfile'))
								{
									sm_extcore();
									sm_resizeimage($file, 'files/img/mediagallery'.$id.'.jpg', sm_settings('gallery_thumb_width'), sm_settings('gallery_thumb_height'), 0, 100, 1);
									unlink($file);
								}
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('addctg', 'editctg'))
				{
					add_path_modules();
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					add_path_current();
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('editctg'))
						{
							sm_title($lang['module_galleies']['gallery'].' - '.$lang['common']['edit']);
							$f=new TForm('index.php?m=media&d=posteditctg&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['module_galleies']['gallery'].' - '.$lang['common']['add']);
							$f=new TForm('index.php?m=media&d=postaddctg&returnto='.urlencode($_getvars['returnto']));
						}
					$f->AddText('title', $lang["title"], true);
					$f->AddFile('userfile', $lang['common']['thumbnail']);
					$f->AddCheckbox('public', $lang['common']['public']);
					$f->AddText('keywords', $lang['common']['seo_keywords']);
					$f->AddTextarea('description', $lang['common']['seo_description']);
					if (sm_action('editctg'))
						{
							$q=new TQuery($sm['t'].'categories_media');
							$q->Add('id_ctg', intval($_getvars['id']));
							$f->LoadValuesArray($q->Get());
							unset($q);
						}
					else
						{
							$f->SetValue('public', 1);
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title');
				}

			if (sm_action('libraries'))
				{
					sm_use('admininterface');
					sm_use('adminform');
					sm_use('admintable');
					sm_use('adminbuttons');
					add_path_modules();
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					sm_title($lang['module_galleies']['galleries']);
					add_path_current();
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=media&d=addctg&returnto='.urlencode(sm_this_url()));
					$ui->AddButtons($b);
					$t=new TGrid();
					$t->AddCol('id_ctg', $lang['common']['id']);
					$t->AddCol('image', $lang['common']['image']);
					$t->AddCol('title', $lang['common']['title']);
					$t->AddCol('public', $lang['common']['public']);
					$t->AddCol('items_count', $lang['count']);
					$t->AddEdit();
					$t->AddDelete();
					$q=new TQuery($sm['t'].'categories_media');
					$q->OrderBy('lastupdate DESC');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<count($q->items); $i++)
						{
							$t->Label('id_ctg', $q->items[$i]['id_ctg']);
							if (file_exists('files/img/mediagallery'.$q->items[$i]['id_ctg'].'.jpg'))
								{
									$t->Image('image', 'files/img/mediagallery'.$q->items[$i]['id_ctg'].'.jpg');
									$t->Image('image', sm_thumburl('mediagallery'.$q->items[$i]['id_ctg'], 50, 50));
									$t->Url('image', 'index.php?m=media&d=list&ctg='.$q->items[$i]['id_ctg']);
								}
							$t->Label('title', $q->items[$i]['title']);
							$t->Url('title', 'index.php?m=media&d=list&ctg='.$q->items[$i]['id_ctg']);
							$t->Label('public', $q->items[$i]['public']==1?$lang['yes']:$lang['no']);
							$t->Label('items_count', $q->items[$i]['items_count']);
							$t->Url('edit', 'index.php?m=media&d=editctg&id='.$q->items[$i]['id_ctg'].'&returnto='.urlencode(sm_this_url()));
							$t->Url('delete', 'index.php?m=media&d=postdeletectg&id='.$q->items[$i]['id_ctg'].'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

			if (sm_actionpost('postsettings'))
				{
					sm_update_settings('gallery_thumb_width', siman_notemptyuintwithdefault($_postvars['gallery_thumb_width'], 150));
					sm_update_settings('gallery_thumb_height', siman_notemptyuintwithdefault($_postvars['gallery_thumb_height'], 150));
					sm_update_settings('gallery_default_view', $_postvars['gallery_default_view']);
					sm_update_settings('gallery_view_items_per_row', abs(intval($_postvars['gallery_view_items_per_row'])));
					sm_update_settings('galleries_view_items_per_row', abs(intval($_postvars['galleries_view_items_per_row'])));
					sm_update_settings('galleries_sort', $_postvars['galleries_sort']);
					sm_update_settings('media_thumb_width', siman_notemptyuintwithdefault($_postvars['media_thumb_width'], 150));
					sm_update_settings('media_thumb_height', siman_notemptyuintwithdefault($_postvars['media_thumb_height'], 150));
					sm_update_settings('media_medium_width', siman_notemptyuintwithdefault($_postvars['media_medium_width'], 600));
					sm_update_settings('media_meduim_height', siman_notemptyuintwithdefault($_postvars['media_meduim_height'], 600));
					sm_update_settings('media_allowed_extensions', $_postvars['media_allowed_extensions']);
					sm_update_settings('media_edit_after_upload', intval($_postvars['media_edit_after_upload']), 'media');
					sm_notify($lang['settings_saved_successful']);
					sm_redirect('index.php?m=media&d=settings');
				}

			if (sm_action('settings'))
				{
					add_path_modules();
					add_path($lang['module_galleies']['media_files'], 'index.php?m=media&d=admin');
					add_path_current();
					sm_title($lang['settings']);
					sm_use('admininterface');
					sm_use('adminform');
					$ui = new TInterface();
					$f=new TForm('index.php?m=media&d=postsettings');
					$f->AddText('gallery_thumb_width', $lang['module_galleies']['gallery_thumb_width']);
					$f->AddText('gallery_thumb_height', $lang['module_galleies']['gallery_thumb_height']);
					$f->AddSelectVL('gallery_default_view', $lang['module_galleies']['gallery_default_view'], Array('all'), Array($lang['module_galleies']['all_images_in_one_page']));
					$f->AddText('gallery_view_items_per_row', $lang['module_galleies']['gallery_view_items_per_row']);
					$f->SetFieldBottomText('gallery_view_items_per_row', '0 - '.$lang['common']['auto']);
					$f->AddText('galleries_view_items_per_row', $lang['module_galleies']['galleries_view_items_per_row']);
					$f->SetFieldBottomText('galleries_view_items_per_row', '0 - '.$lang['common']['auto']);
					$f->AddSelectVL('galleries_sort', $lang['module_galleies']['galleries_sort'], Array('lastupdate_desc'), Array($lang['common']['last_update']));
					$f->AddText('media_thumb_width', $lang['module_galleies']['media_thumb_width']);
					$f->AddText('media_thumb_height', $lang['module_galleies']['media_thumb_height']);
					$f->AddText('media_medium_width', $lang['module_galleies']['media_medium_width']);
					$f->AddText('media_meduim_height', $lang['module_galleies']['media_meduim_height']);
					$f->AddTextarea('media_allowed_extensions', $lang['module_galleies']['media_allowed_extensions']);
					$f->AddCheckbox('media_edit_after_upload', $lang['module_galleies']['media_edit_after_upload']);
					$f->LoadValuesArray($_settings);
					$f->SetValue('media_edit_after_upload', sm_get_settings('media_edit_after_upload', 'media'));
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
				}
			if (sm_action('admin'))
				{
					add_path_modules();
					sm_title($lang['module_galleies']['media_files']);
					add_path_current();
					sm_use('admininterface');
					sm_use('admindashboard');
					$ui = new TInterface();
					$dash=new TDashBoard();
					$dash->AddItem($lang['module_galleies']['galleries'], 'index.php?m=media&d=libraries', 'photo');
					$dash->AddItem($lang['settings'], 'index.php?m=media&d=settings', 'settings');
					$ui->AddDashboard($dash);
					$ui->Output(true);
				}
		}


?>
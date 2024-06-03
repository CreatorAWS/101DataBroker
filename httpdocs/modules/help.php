<?php

	/** @var $currentcompany TCompany */
	/** @var $myaccount TEmployee */

	if (is_object($myaccount))
		{

			sm_default_action('list');


			if ( $userinfo['level']==3 )
				{

					if (sm_action('postdelete'))
						{
							$hvideo = new THelpVideo(intval($_getvars['id']));
							if ( !$hvideo->Exists() )
								exit('Access Denied!');

							$q = new TQuery('help_videos');
							$q->Add('id', $hvideo->ID());
							$q->Remove();

							if (file_exists($hvideo->VideoPath()))
								unlink($hvideo->VideoPath());
							sm_redirect($_getvars['returnto']);
						}

					if (sm_action('postadd', 'postedit'))
						{
							$error = '';
							if (sm_action('postadd'))
								{
									$fs = $_uplfilevars['userfile']['tmp_name'];
									$tmp = sm_upload_file();
									if ($tmp === false || !file_exists($tmp))
										{
											$error = 'Error uploading file';
										} else
										{
											$filename = $_uplfilevars['userfile']['name'];
											$type = $_uplfilevars['userfile']['type'];
										}
								}
							if (empty($error))
								{
									if (sm_action('postadd'))
										{
											$hvideo = THelpVideo::Create();
										} else
										{
											$hvideo = new THelpVideo(intval($_getvars['id']));
										}
									$hvideo->SetTitle($_postvars['title']);
									$hvideo->SetComment($_postvars['comment']);
									$hvideo->SetOrder($_postvars['order']);

									if (sm_action('postadd'))
										{
											rename($tmp, 'files/video/help_video_' . $hvideo->ID() . '.mp4');
										}

									if (!empty($_getvars['returnto'])) sm_redirect($_getvars['returnto']); else
										sm_redirect(sm_homepage());
								} else
								sm_set_action(Array('postadd' => 'add', 'postedit' => 'edit'));
						}

					if (sm_action('add', 'edit'))
						{
							add_path_home();
							add_path('Help', 'index.php?m=' . sm_current_module() . '&d=list');
							sm_use('ui.interface');
							sm_use('ui.form');
							$ui = new TInterface();
							if (!empty($error)) $ui->NotificationError($error);
							if (sm_action('edit'))
								{
									sm_title($lang['common']['edit']);
									$f = new TForm('index.php?m=' . sm_current_module() . '&d=postedit&id=' . intval($_getvars['id']) . '&returnto=' . urlencode($_getvars['returnto']));
								} else
								{
									sm_title($lang['common']['add']);
									$f = new TForm('index.php?m=' . sm_current_module() . '&d=postadd&returnto=' . urlencode($_getvars['returnto']));
									$f->AddFile('userfile', 'Video');
								}
							$f->AddText('title', 'Title');
							$f->AddText('comment', 'Comment');
							$f->AddText('order', 'Order');
							if (sm_action('edit'))
								{
									$hvideo = new THelpVideo(intval($_getvars['id']));
									if (!$hvideo->Exists()) exit('Access Denied!');
									$f->LoadValuesArray($hvideo->info);

								}
							$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
							sm_setfocus('comment');
						}
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					include_once('includes/adminbuttons.php');
					add_path_home();
					add_path('Help', 'index.php?m=' . sm_current_module() . '&d=list');
					sm_title('Help');
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$b = new TButtons();
					if ($userinfo['level'] > 2) $b->AddButton('add', $lang['common']['add'], 'index.php?m=' . sm_current_module() . '&d=add&returnto=' . urlencode(sm_this_url()));
					if ($b->Count() > 0) $ui->AddButtons($b);

					$q = new TQuery('help_videos');
					$q->OrderBy('`order`');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					if ($userinfo['level'] > 2)
						{
							$data['isallowedtomanageassets'] = 1;
						}
					for ($i = 0; $i < $q->Count(); $i++)
						{
							$hvideo = new THelpVideo($q->items[$i]);


							if (file_exists($hvideo->VideoPath()))
								{
									$data['library'][$i]['video'] = $hvideo->VideoURL();
								}

							$data['library'][$i]['title'] = $hvideo->Title();
							$data['library'][$i]['comment'] = $hvideo->Comment();
							$data['library'][$i]['editlink'] = 'index.php?m=' . sm_current_module() . '&d=edit&id=' . $hvideo->ID() . '&returnto=' . urlencode(sm_this_url());
							$data['library'][$i]['deletelink'] = 'index.php?m=' . sm_current_module() . '&d=postdelete&id=' . $hvideo->ID() . '&returnto=' . urlencode(sm_this_url());
							unset($hvideo);
						}
					if ($q->Count() == 0)
						{
							$data['noinfo'] = 'Nothing Found';
						}
					$ui->AddTPL('helplibrary.tpl', '', $data);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);

					if ($b->Count() > 0) $ui->AddButtons($b);
					$ui->Output(true);
				}
			if (sm_action('applinks'))
				{
					add_path_home();
					add_path_current();
					sm_title('App Links');
					sm_use('ui.interface');
					$ui = new TInterface();
					$ui->AddTPL('applinks.tpl');
					$ui->Output(true);
				}
		}

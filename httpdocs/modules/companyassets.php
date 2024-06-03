<?php

	/*
	Module Name: Assets
	Description: Assets
	Version: 1.0
	Revision: 2015-08-23
	*/

	/** @var $currentcompany TCompany */
	/** @var $myaccount TEmployee */
	if (sm_action('twiliomms'))
		{
			$info = TQuery::ForTable('company_assets')->Add('id', intval($_getvars['id']))->Get();
			if ((!is_object($myaccount) || $myaccount->CompanyID() != $info['id_company']) && $info['access'] == 'private')
				exit;
			elseif (file_exists('files/download/ca_'.intval($info['id'])))
				{
					header("Content-type: ".$info['type']);
					header("Content-Disposition: inline; filename=".basename($info['filename']));
					$fp = fopen('files/download/ca_'.intval($info['id']), 'rb');
					fpassthru($fp);
					fclose($fp);
					exit;
				}
		}
	if (is_object($myaccount) && ($myaccount->isAllowedToManageAssets() || $myaccount->isAllowedToDownloadAssets()))
		{
			sm_default_action('list');
			if (sm_action('download'))
				{
					$info=TQuery::ForTable('company_assets')->Add('id', intval($_getvars['id']))->Add('id_company', intval(TCompany::CurrentCompany()->ID()))->Get();
					if (file_exists('files/download/ca_'.intval($info['id'])))
						{
							header("Content-type: ".$info['type']);
							header("Content-Disposition: attachment; filename=".basename($info['filename']));
							$fp = fopen('files/download/ca_'.intval($info['id']), 'rb');
							fpassthru($fp);
							fclose($fp);
							exit;
						}
				}
			if ($myaccount->isAllowedToManageAssets())
				{
					if (sm_action('postdelete'))
						{
							$info=TQuery::ForTable('company_assets')->Add('id', intval($_getvars['id']))->Get();
							if (intval($info['id_company'])==TCompany::CurrentCompany()->ID())
								{
									$q=new TQuery('company_assets');
									$q->Add('id', intval($_getvars['id']));
									$q->Remove();
									sm_redirect($_getvars['returnto']);
									$filename='files/download/ca_'.intval($info['id']);
									if (file_exists($filename))
										unlink($filename);
								}
						}
		
					if (sm_action('postadd', 'postedit'))
						{
							$error='';
							if (sm_action('postadd'))
								{
									$fs = $_uplfilevars['userfile']['tmp_name'];
									$tmp=sm_upload_file();
									if ($tmp===false || !file_exists($tmp))
										{
											$error='Error uploading file';
										}
									else
										{
											$filename=$_uplfilevars['userfile']['name'];
											$type=$_uplfilevars['userfile']['type'];
										}
								}
							if (empty($error))
								{
									$q=new TQuery('company_assets');
									if (sm_action('postadd'))
										{
											$q->Add('id_company', intval(TCompany::CurrentCompany()->ID()));
											$q->Add('type', dbescape($type));
											$q->Add('filename', dbescape($filename));
										}
									$q->Add('comment', dbescape($_postvars['comment']));
									$q->Add('access', dbescape($_postvars['access']));
									if (sm_action('postadd'))
										{
											$id=$q->Insert();
											rename($tmp, 'files/download/ca_'.$id);
											if (strpos($type, 'image')!==false)
												{
													sm_extcore();
													sm_resizeimage('files/download/ca_'.$id, 'files/img/caprv'.$id.'.jpg', 100, 100, 1, 100, 1);
												}
										}
									else
										$q->Update('id', intval($_getvars['id']));
									if (!empty($_getvars['returnto']))
										sm_redirect($_getvars['returnto']);
									else
										sm_redirect(sm_homepage());
								}
							else
								sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
						}
		
					if (sm_action('add', 'edit'))
						{
							add_path_home();
							add_path('Assets', 'index.php?m=companyassets&d=list');
							sm_use('ui.interface');
							sm_use('ui.form');
							$ui = new TInterface();
							if (!empty($error))
								$ui->NotificationError($error);
							if (sm_action('edit'))
								{
									sm_title($lang['common']['edit']);
									$f=new TForm('index.php?m=companyassets&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
								}
							else
								{
									sm_title($lang['common']['add']);
									$f=new TForm('index.php?m=companyassets&d=postadd&returnto='.urlencode($_getvars['returnto']));
									$f->AddFile('userfile', 'File');
								}
							$f->AddText('comment', 'Comment');
							$f->AddSelectVL('access', 'Permissions', ['public','private'], ['Public', 'Private']);
							if (sm_action('edit'))
								{
									$q=new TQuery('company_assets');
									$q->Add('id', intval($_getvars['id']));
									$f->LoadValuesArray($q->Get());
									unset($q);
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
					add_path_current();
					if ($_getvars['mode'] == 'voice')
						sm_title('Voice Messages');
					else
						sm_title('Documents');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();

					if ($_getvars['mode'] == 'voice')
						{
							$data['currmode'] = 'voice';
							$ui->AddTPL('templates_header.tpl', '', $data);
						}

					$b=new TButtons();
					if ($myaccount->isAllowedToManageAssets())
						$b->AddButton('add', $lang['common']['add'], 'index.php?m=companyassets&d=add&returnto='.urlencode(sm_this_url()).(!empty($_getvars['mode'])?'&mode='.$_getvars['mode']:''));
					if ($b->Count()>0)
						$ui->AddButtons($b);
					$t=new TGrid();
					$t->AddCol('filename', 'Filename');
					$t->AddCol('type', 'Type');
					$t->AddCol('mms', 'MMS');
					$t->AddCol('comment', 'Comment'); 
					$t->AddCol('url', 'URL');
					$t->AddCol('download', 'Download');
					$t->AddCol('access', 'Permissions');
					if ($myaccount->isAllowedToManageAssets())
						{
							$t->AddEdit();
							$t->AddDelete();
						}
					$assets=new TAssetList();
					$assets->SetFilterCompany(TCompany::CurrentCompany());
					if ($_getvars['mode'] == 'voice')
						$assets->SetFilterAudio();
					$assets->OrderByTitle();
					$assets->Offset($offset);
					$assets->Limit($limit);
					$assets->Load();

					for ($i = 0; $i<$assets->Count(); $i++)
						{
							$asset=$assets->items[$i];
							if (file_exists($asset->ThumbPath()))
								$t->Image('type', $asset->ThumbURL());
							else
								$t->Label('type', $asset->FileType());
							$t->Label('mms', $asset->isEligibleForMMS()?'Yes':'No');
							$t->Label('filename', $asset->FileName());
							$t->Label('comment', $asset->Comment());
							if (file_exists('files/download/ca_'.$asset->ID()))
								$t->Label('url', 'https://'.main_domain().'/flo'.$asset->ID());
							$t->Label('download', 'Download');
							$t->Label('access', ucfirst($asset->Access()));
							$t->URL('download', 'index.php?m=companyassets&d=download&id='.$asset->ID());
							$t->URL('edit', 'index.php?m=companyassets&d=edit&id='.$asset->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=companyassets&d=postdelete&id='.$asset->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
							unset($asset);
						}
					if ($assets->Count()==0)
						{
							$t->Label('filename', 'Nothing Found');
							$t->AttachEmptyCellsToLeft();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($assets->TotalCount(), $limit, $offset);
					if ($b->Count()>0)
						$ui->AddButtons($b);
					$ui->Output(true);
				}

			if ($userinfo['level']==3)
				{
					if (sm_action('admin'))
						{
							add_path_home();
							sm_title('Assets');
							sm_use('ui.interface');
							$ui = new TInterface();
							$ui->a('index.php?m=companyassets&d=list', $lang['common']['list']);
							$ui->Output(true);
						}
					if (sm_action('install'))
						{
							sm_register_module('companyassets', 'Assets');
							sm_redirect('index.php?m=admin&d=modules');
						}
					if (sm_action('uninstall'))
						{
							sm_unregister_module('companyassets');
							sm_redirect('index.php?m=admin&d=modules');
						}
					if (sm_action('sendtest'))
						{
							if (empty($_postvars['vimeo']))
								$error='Empty URl';
							if (empty($error))
								{
									$vimeo = TVimeo::withURL($_postvars['vimeo']);
									if (!$vimeo->Valid())
										$error='Wrong Vimeo URL';
									elseif (!$vimeo->LoadData())
										$error='Unable to get data from Vimeo server';
									else
										{
											$text=$_postvars['text'].' '.$vimeo->URL();
											send_sms($_postvars['to'], $text, '7023816677', $vimeo->ThumbLarge());
											sm_notify('Message sent');
											sm_redirect('index.php?m=companyassets&d=test');
										}
								}
							if (!empty($error))
								sm_set_action('test');
						}
					if (sm_action('test'))
						{
							sm_title('Test Vimeo MMS');
							sm_use('ui.interface');
							sm_use('ui.form');
							$ui = new TInterface();
							if (!empty($error))
								$ui->NotificationError($error);
							$f = new TForm('index.php?m='.sm_current_module().'&d=sendtest');
							$f->AddText('to', 'To')->WithValue('2485607182');
							$f->AddText('text', 'Text')->WithValue('Check this video');
							$f->AddText('vimeo', 'Vimeo URL')->WithValue('https://vimeo.com/81076508');
							$ui->Add($f);
							$ui->Output(true);
						}
				}
		}
	elseif ($userinfo['level']==0)
		sm_redirect('index.php?m=account');

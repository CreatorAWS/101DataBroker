<?php

/*
 Module Name: Tags
 Description: Tags
 Version: 1.0
 Revision: 2015-08-20
*/

	if ($userinfo['level']>0)
		{
			sm_default_action('list');

			if (sm_action('postdelete'))
				{
					$q=new TQuery('company_tags');
					$q->Add('id_company', TCompany::CurrentCompany()->ID());
					$q->Add('id', intval($_getvars['id']));
					$q->Remove();
					sm_extcore();
					sm_saferemove('index.php?m=tags&d=view&id='.intval($_getvars['id']));
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd', 'postedit'))
				{
					$error='';
					if (empty($_postvars['tag']))
						$error=$lang['messages']['fill_required_fields'];
					if (empty($error))
						{
							$q=new TQuery('company_tags');
							$q->Add('id_company', TCompany::CurrentCompany()->ID());
							$q->Add('tag', dbescape($_postvars['tag']));
							if (sm_action('postadd'))
								$q->Insert();
							else
								$q->Update('id', intval($_getvars['id']));
							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}

			if (sm_action('add', 'edit'))
				{
					add_path_home();
					add_path('Tags', 'index.php?m=tags&d=list');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('edit'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m=tags&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m=tags&d=postadd&returnto='.urlencode($_getvars['returnto']));
						}
					$f->AddText('tag', 'Tag Name', true);
					if (sm_action('edit'))
						{
							$q=new TQuery('company_tags');
							$q->Add('id_company', TCompany::CurrentCompany()->ID());
							$q->Add('id', intval($_getvars['id']));
							$f->LoadValuesArray($q->Get());
							unset($q);
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('tag');
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					include_once('includes/adminbuttons.php');
					add_path_home();
					add_path('Tags', 'index.php?m=tags&d=list');
					sm_title('Tags');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();

					$data['currmode'] = sm_current_module();
					$ui->AddTPL('settings_header.tpl', '', $data);

					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=tags&d=add&returnto='.urlencode(sm_this_url()));
					$ui->AddButtons($b);
					$t=new TGrid();
					$t->AddCol('tag', 'Tag', '80%');
					$t->AddCol('list', 'List', '10%');
					$t->AddCol('text', 'Text', '10%');
					$t->AddEdit();
					$t->AddDelete();
					$q=new TQuery('company_tags');
					$q->Add('id_company', TCompany::CurrentCompany()->ID());
					$q->OrderBy('tag');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i<$q->Count(); $i++)
						{
							$t->Label('tag', $q->items[$i]['tag']);
							$t->Label('list', 'List');
							$t->Label('text', 'Text');
							$t->URL('list', 'index.php?m=customers&d=list&tags_selected='.$q->items[$i]['id']);
							$t->URL('text', 'index.php?m=smsblast&d=start&tag='.$q->items[$i]['id']);
							$t->Url('edit', 'index.php?m=tags&d=edit&id='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
							$t->Url('delete', 'index.php?m=tags&d=postdelete&id='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($q->Count()==0)
						{
							$t->Label('tag', 'No tags yet');
							$t->OneLine('tag');
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}

			if ($userinfo['level']==3)
				{
					if (sm_action('admin'))
						{
							add_path_home();
							sm_title('Tags');
							sm_use('ui.interface');
							$ui = new TInterface();
							$ui->a('index.php?m=tags&d=list', $lang['common']['list']);
							$ui->Output(true);
						}
					if (sm_action('install'))
						{
							sm_register_module('tags', 'Tags');
							//sm_register_autoload('tags');
							//sm_register_postload('tags');
							sm_redirect('index.php?m=admin&d=modules');
						}
					if (sm_action('uninstall'))
						{
							sm_unregister_module('tags');
							//sm_unregister_autoload('tags');
							//sm_unregister_postload('tags');
							sm_redirect('index.php?m=admin&d=modules');
						}
				}

		}
	else
		sm_redirect('index.php');

?>
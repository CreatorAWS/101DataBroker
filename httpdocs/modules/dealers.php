<?php

	if ($userinfo['level']>0)
		{
			sm_default_action('titles');
			
			if (sm_actionpost('groupsassign'))
				{
					$q=new TQuery('dealers');
					$q->Add('Title', $_postvars['group']);
					$q->INStrings('Title', $_postvars['chk']);
					$q->Update();
					sm_redirect($_getvars['returnto']);
				}
			if (sm_actionpost('savegroups'))
				{
					if (!array_key_exists('dealers_groups', $_settings))
						sm_new_settings('dealers_groups', $_postvars['groups']);
					else
						sm_update_settings('dealers_groups', $_postvars['groups']);
					sm_redirect($_getvars['returnto']);
				}
			if (sm_action('titles'))
				{
					add_path_home();
					add_path_current();
					sm_title('Titles');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					$t=new TGrid();
					$t->AddCol('title', 'Title', '95%');
					$t->AddCol('qty', 'Qty', '5%');
					$t->AddCol('chk', '', '10');
					$t->HeaderBulkCheckbox('chk');
					$q=new TQuery('dealers');
					if (!empty($_getvars['q']))
						{
							$srch=str_replace('*', '%', $_getvars['q']);
							$q->Add("Title LIKE '".$srch."'");
						}
					$q->SelectFields('Title, count(*) as qty');
					$q->OrderBy('Title');
					$q->GroupBy('Title');
					$q->Select();
					for ($i = 0; $i < $q->Count(); $i++)
						{
							$t->Label('title', $q->items[$i]['Title']);
							$t->Label('qty', $q->items[$i]['qty']);
							$t->Checkbox('chk', 'chk[]', $q->items[$i]['Title']);
							$t->NewRow();
						}
					$f = new TForm('index.php', '', 'get');
					$f->AddHidden('m', 'dealers');
					$f->AddHidden('d', 'titles');
					$f->AddText('q', 'Search');
					$f->SaveButton('Search');
					$f->LoadValuesArray($_getvars);
					$ui->AddForm($f);
					$ui->html('<form action="index.php?m=dealers&d=groupsassign&returnto='.urlencode(sm_this_url()).'" method="post">');
					$ui->AddGrid($t);
					$ui->div_open('', '', 'text-align:right;');
					$ui->html('<select name="group">');
					$groups=nllistToArray(sm_settings('dealers_groups'));
					for ($i = 0; $i < count($groups); $i++)
						{
							$ui->html('<option value="'.$groups[$i].'">'.$groups[$i].'</option>');
						}
					$ui->html('</select>');
					$ui->html('<input type="submit" value="Rename Title To Group Name" />');
					$ui->div_close();
					$ui->html('</form>');
					$ui->p('&nbsp;');
					$ui->AddBlock('Groups');
					$f = new TForm('index.php?m=dealers&d=savegroups'.'&returnto='.urlencode(sm_this_url()));
					$f->AddTextarea('groups', 'Group Names (each in new line)');
					$f->SetValue('groups', sm_settings('dealers_groups'));
					$ui->AddForm($f);
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');

?>
<?php

	if (System::LoggedIn() && System::MyAccount()->isSuperAdmin())
		{
			sm_default_action('list');

			if (sm_action('postdelete'))
				{
					if (empty($_getvars['id']))
						exit('Invalid Employee ID');

					$employee = new TEmployee($_getvars['id']);
					if (!$employee->Exists())
						exit('Invalid Employee ID');

					$company = new TCompany($employee->CompanyID());
					if (!$company->Exists())
						exit('Invalid Company ID');

					$q=new TQuery('companies');
					$q->Add('expiration', time());
					$q->Update('id', $company->ID());

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('remove'))
				{
					sm_extcore();
					if (empty($_getvars['id']))
						exit('Invalid Employee ID');

					$employee = new TEmployee($_getvars['id']);
					if (!$employee->Exists())
						exit('Invalid Employee ID');

					$company = new TCompany($employee->CompanyID());
					if (!$company->Exists())
						exit('Invalid Company ID');

					$q = new TQuery('sm_users');
					$q->Add('id_user', $employee->ID());
					$q->Remove();
					unset($q);

					$q = new TQuery('companies');
					$q->Add('id', $company->ID());
					$q->Remove();
					unset($q);

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd', 'postedit'))
				{
					if (sm_action('postedit'))
						{
							if (empty($_getvars['id']))
								exit('Invalid Employee ID');

							$employee = new TEmployee($_getvars['id']);
							if (!$employee->Exists())
								exit('Invalid Employee ID');

							$company = new TCompany($employee->CompanyID());
							if (!$company->Exists())
								exit('Invalid Company ID');
						}

					$error_message = '';

					if (!empty($_postvars['email']))
						$usr1=sm_userinfo($_postvars['email'], 'email');

					if (!empty($_postvars['email']) && !is_email($_postvars['email']))
						$error_message = 'Wrong Email Address';
					elseif (empty($_postvars['first_name']) || empty($_postvars['last_name']) || empty($_postvars['email']))
						$error_message = 'Fill required fields';
					elseif (sm_action('postadd') && (empty($_postvars['email']) || empty($_postvars['password'])))
						$error_message = 'Fill required fields';
					elseif (sm_action('postadd') && !empty($usr['id']))
						$error_message = 'User with this email exists';
					else
						{
							$cur_email_check = TQuery::ForTable('sm_users')->Add('id_user', intval($_getvars['id']))->Get();
							if (!empty($email_check) && $cur_email_check['email'] != $_postvars['email'])
								$error = 'This email already exists';
						}

					if (empty($error_message))
						{
							if (sm_action('postadd'))
								{
									sm_extcore();
									$user_id = sm_add_user( $_postvars['email'], $_postvars['password'], $_postvars['email']);
									$employee = new TEmployee($user_id);
									$company = TCompany::Create();
								}

							$employee->SetFirstName($_postvars['first_name']);
							$employee->SetLastName($_postvars['last_name']);
							$employee->SetEmail($_postvars['email']);
							$employee->SetCellphone($_postvars['cellphone']);
							$employee->SetCompanyID($company->ID());
							if ($_postvars['expiration'] != 0 )
								$company->SetExpirationTimestamp(strtotime($_postvars['expiration']));
							if (!empty($_postvars['password']))
								$employee->SetPassword($_postvars['password']);

							$company->SetName( $employee->Name() );
							$company->SetSicCodesSearch( $_postvars['sic_code_search'] );
							$company->SetStatesSearch( $_postvars['state_search'] );
							$company->SetGoogleSearch( $_postvars['google_search'] );
							$company->SetBuiltWithSearch( $_postvars['builtwith_search'] );

							if(!empty($_postvars['password']))
								{
									$subject="Login details";
									$message="Hello ".$_postvars['first_name'].",<br><br>This is your login details<br><a href='".main_domain()."'>Link to your dashboard</a><br>Login: ".$employee->Email()."<br>Password: ".$_postvars['password'];
									if(!empty($company->EmailFrom()))
										$employee->SendEmail($subject, $message);
								}

							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}

			if (sm_action('add', 'edit'))
				{
					if (sm_action('edit'))
						{
							if (empty($_getvars['id']))
								exit('Invalid Employee ID');

							$employee = new TEmployee($_getvars['id']);
							if (!$employee->Exists())
								exit('Invalid Employee ID');
						}

					sm_use('ui.interface');
					sm_use('ui.form');

					add_path_home();
					add_path_current();

					if (sm_action('add'))
						sm_title('Add Client');
					else
						sm_title('Edit Client');

					$ui = new TInterface();

					if (!empty($error_message))
						$ui->NotificationError($error_message);

					if (sm_action('add'))
						$f = new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode($_getvars['returnto']));
					else
						$f = new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$_getvars['id'].'&returnto='.urlencode($_getvars['returnto']));

					$f->AddText('first_name', 'First Name', true);
					$f->AddText('last_name', 'Last Name', true);
					$f->AddText('email', 'Email', true);
					$f->AddPassword('password', 'Password');
					$f->AddText('cellphone', 'Phone');
					$f->AddText('expiration', 'Expiration Date');
					$f->Calendar('expiration', 'd/M/Y');
					$f->SetFieldBottomText('expiration', '0 - for newer expire');

					$f->Separator('SIC-Code Search');
					$f->AddCheckbox('sic_code_search', 'Turn On Sic-Code Search Service');

					$f->Separator('States Search');
					$f->AddCheckbox('state_search', 'Turn On States Search Service');

					$f->Separator('Google Search');
					$f->AddCheckbox('google_search', 'Turn On Google Search Service');

					$f->Separator('BuiltWith Search');
					$f->AddCheckbox('builtwith_search', 'Turn On BuiltWith Search Service');

					if (sm_action('edit'))
						{
							$f->SetValue('first_name', $employee->FirstName());
							$f->SetValue('last_name', $employee->LastName());
							$f->SetValue('email', $employee->Email());
							$f->SetValue('cellphone', $employee->Cellphone());
							$company = new TCompany($employee->CompanyID());
							if ($company->Exists())
								{
									if ($company->ExpirationTimestamp()!=0)
										$f->SetValue('expiration', Formatter::Date($company->ExpirationTimestamp()));
									else
										$f->SetValue('expiration', 0);

									$f->SetValue('sic_code_search', $company->SicCodesSearch());
									$f->SetValue('state_search', $company->StatesSearch());
									$f->SetValue('google_search', $company->GoogleSearch());
									$f->SetValue('builtwith_search', $company->BuiltWithSearch());

								}
						}


					$ui->AddForm($f);
					$ui->Output(true);
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					add_path_home();
					add_path_current();
					sm_title('Clients Management');

					if (isset($_getvars['from']))
						$offset = abs(intval( $_getvars['from'] ));
					else
						$offset = 0;
					$limit = 30;

					$ui = new TInterface();

					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m='.sm_current_module().'&d=add&returnto='.urlencode(sm_this_url()));

					$t=new TGrid();
					$t->AddCol('name', 'Name');
					$t->AddCol('limits', 'Services');
					$t->AddCol('expiration', 'Expiration');
					$t->AddCol('deactivate', '');
					$t->AddCol('login', 'Log in');
					$t->AddEdit();
					$t->AddDelete();

					$list = new TEmployeeList('all');
					$list->SetFilterNotDeleted();
					$list->Offset($offset);
					$list->Limit($limit);
					$list->Load();

					for ( $i = 0; $i < $list->Count(); $i++ )
						{
							$employee = $list->Item($i);

							if ($employee->ID() == 1)
								$company = TCompany::SystemCompany();
							else
								{
									$company = new TCompany($employee->CompanyID());
									if (!$company->Exists())
										continue;
								}

							$label_text = '';
							if ($employee->isSuperAdmin())
								$label_text = ' <span class="label label-warning">Super Admin</span>';

							$t->Label('name', $employee->Name().$label_text);

							$limits = '';
							$limits .= $company->GoogleSearchEnabled()? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="16px" height="16px" style="margin-right: 5px;"><path d="M 15.003906 3 C 8.3749062 3 3 8.373 3 15 C 3 21.627 8.3749062 27 15.003906 27 C 25.013906 27 27.269078 17.707 26.330078 13 L 25 13 L 22.732422 13 L 15 13 L 15 17 L 22.738281 17 C 21.848702 20.448251 18.725955 23 15 23 C 10.582 23 7 19.418 7 15 C 7 10.582 10.582 7 15 7 C 17.009 7 18.839141 7.74575 20.244141 8.96875 L 23.085938 6.1289062 C 20.951937 4.1849063 18.116906 3 15.003906 3 z"/></svg>' : '';
							$limits .= $company->BuiltWithSearchEnabled()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16px" height="16px" viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /></svg>' : '';
							$limits .= $company->SicCodesSearchEnabled()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none"  width="16px" height="16px" viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>' : '';
							$limits .= $company->StatesSearchEnabled()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16px" height="16px"  viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>' : '';

							$t->Label('limits', $limits);

							if ($company->ExpirationTimestamp()==0)
								$t->Label('expiration', 'Never Expire');
							else
								$t->Label('expiration', strftime('%m/%d/%Y', $company->ExpirationTimestamp()));
							if($company->ExpirationTimestamp()==0 || ($company->ExpirationTimestamp()!=0 && $company->ExpirationTimestamp()>time()))
								{
									$t->Label('deactivate', 'Deactivate');
									$t->CustomMessageBox('deactivate', 'Are you sure?');
									$t->URL('deactivate', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$employee->ID().'&returnto='.urlencode(sm_this_url()));
								}
							else
								{
									$t->Label('deactivate', 'Activate');
									$t->URL('deactivate', 'index.php?m='.sm_current_module().'&d=edit&id='.$employee->ID().'&returnto='.urlencode(sm_this_url()));
								}
							if ($company->ID() != System::MyAccount()->CompanyID())
								{
									$t->Label('login', '<span class="label label-primary">Log in to Client</span>');
									$t->URL('login', 'index.php?m=companiesmgmt&d=switchcompany&id='.$company->ID().'&returnto='.urlencode(sm_this_url()));
								}
							else
								$t->Label('login', '<span class="label label-success">Current Client</span>');

							$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.$employee->ID().'&returnto='.urlencode(sm_this_url()));
							if ($company->ID() != TCompany::SystemCompany()->ID())
								$t->URL('delete', 'index.php?m='.sm_current_module().'&d=remove&id='.$employee->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						{
							$t->SingleLineLabel('Nothing Found');
							$t->RowAddClass('no-appoint-schedule');
						}

					$ui->AddGrid($t);
					$ui->AddPagebarParams($list->TotalCount(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=dashboard');
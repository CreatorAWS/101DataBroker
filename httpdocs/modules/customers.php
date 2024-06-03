<?php

/*
 Module Name: Customers
 Description: Customers
 Revision: 2014-11-01
 */

	if ( $userinfo['level'] > 0 )
		{
			/** @var $currentcompany TCompany */

			use_api('temployee');
			use_api('tcustomer');
			sm_default_action('list');

			function replace_tags_campaign($template, $customer)
				{
					/** @var $customer TCustomer */
					$str=str_replace('{FIRST_NAME}', $customer->FirstName(), $template);
					$str=str_replace('{LAST_NAME}', $customer->LastName(), $str);
					$str=str_replace('{CONTACT_NAME}', $customer->FirstName().' '.$customer->LastName(), $str);
					$str=str_replace('{CONTACT_BUSINESS_NAME}', $customer->GetBusinessName(), $str);
					$str=str_replace('{EMAIL}', $customer->Email(), $str);
					$str=str_replace('{CELLPHONE}', $customer->Cellphone(), $str);
					$str=str_replace('{BUSINESS}', TCompany::CurrentCompany()->Name(), $str);
					$str=str_replace('{BUSINESS_CELLPHONE}', TCompany::CurrentCompany()->Cellphone(), $str);
					$str=str_replace('{OWNER}', '', $str);
					return $str;
				}

			if (sm_action('postdelete'))
				{
					if (sm_action('postdelete'))
						$customer=new TCustomer(intval($_getvars['id']));
					if (is_object($customer) && $customer->Exists())
						{
							$customer->UnsetCampaignContactIDs();
							$customer->UnsetContactIDs();
							$customer->Remove();
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('tag'))
				{
					$customer = new TCustomer(intval($_getvars['id']));

					if (is_object($customer) && $customer->Exists())
						{
							if (!empty($_getvars['set']))
								$customer->SetTagID($_getvars['set']);

							if (!empty($_getvars['unset']))
								$customer->UnsetTagID($_getvars['unset']);

							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('postadd', 'postedit'))
				{
					$error='';
					if (sm_action('postedit'))
						{
							$customer=new TCustomer(intval($_getvars['id']));
							if ( !$customer->Exists())
								exit('Access Denied!');
						}

					if (!empty($_postvars['cellphone']) && !Validator::USPhone($_postvars['cellphone']))
						$error='Wrong cellphone number';

					if ( sm_action('postadd') && !empty($_postvars['cellphone']) && TCustomer::initWithCustomerPhoneNotDeleted($_postvars['cellphone'], TCompany::CurrentCompany())->Exists())
						$error = 'Customer with this cellphone number already exists';
					elseif (!empty($_postvars['cellphone']) && TCustomer::initWithCustomerPhoneNotDeleted($_postvars['cellphone'], TCompany::CurrentCompany())->Exists() && $customer->Cellphone()!=Cleaner::USPhone($_postvars['cellphone']))
						$error = 'Customer with this cellphone number already exists';

					if(!empty($_postvars['facebookurl']) && empty($error) && url_validator($_postvars['facebookurl']))
						$error = url_validator($_postvars['facebookurl']);

					if(!empty($_postvars['twitterurl']) && empty($error) && url_validator($_postvars['twitterurl']))
						$error = url_validator($_postvars['twitterurl']);

					if(!empty($_postvars['linkedin']) && empty($error) && url_validator($_postvars['linkedin']))
						$error = url_validator($_postvars['linkedin']);

					if(!empty($_postvars['instagramurl']) && empty($error) && url_validator($_postvars['instagramurl']))
						$error = url_validator($_postvars['instagramurl']);

					if(!empty($_postvars['website']) && empty($error) && url_validator($_postvars['website']))
						$error = url_validator($_postvars['website']);

					if (!empty($_postvars['email']) && !Validator::Email($_postvars['email']))
						$error='Wrong email';

					if ( sm_action('postadd') && !empty($_postvars['email']) && TCustomer::initWithCustomerEmailNotDeleted($_postvars['email'], TCompany::CurrentCompany())->Exists())
						$error = 'Customer with this email already exists';
					elseif (!empty($_postvars['email']) && TCustomer::initWithCustomerEmailNotDeleted($_postvars['email'], TCompany::CurrentCompany())->Exists() && $customer->Email()!=$_postvars['email'])
						$error = 'Customer with this email already exists';

					if(empty($error))
						{
							if ( sm_action('postedit') )
								$customer=new TCustomer(intval($_getvars['id']));

							if ( sm_action('postadd') || is_object($customer) && $customer->Exists() && $currentcompany->ID()==$customer->CompanyID() )
								{
									if ( sm_action('postadd') )
										{
											$customer=TCustomer::CreateByStaff($currentcompany, $_postvars['first_name'], $_postvars['last_name'], Cleaner::USPhone($_postvars['cellphone']));
											$customer->SendInitialSMS();
										}

									$customer->SetFirstName($_postvars['first_name']);
									$customer->SetLastName($_postvars['last_name']);
									$customer->SetCellPhone(Cleaner::USPhone($_postvars['cellphone']));
									$customer->SetEmail($_postvars['email']);
									$customer->SetBusinessName($_postvars['business_name']);

									$fields_additional_tags = new TFieldsList();
									$fields_additional_tags->SetFilterCompany($currentcompany);
									$fields_additional_tags->SetFilterTemplate($currentcompany->CustomerFormTemplate());
									$fields_additional_tags->Load();
									$additional_fields=$fields_additional_tags->ExtractIDsArray();

									for( $i = 0; $i < count($additional_fields); $i++ )
										{
											$customer->SetMetaData('customfield_'.$additional_fields[$i], $_postvars['customfield_'.$additional_fields[$i]]);
										}
									$customer->SetFacebookURL(url_cleaner($_postvars['facebookurl']));
									$customer->SetTwitterURL(url_cleaner($_postvars['twitterurl']));
									$customer->SetLinkedin(url_cleaner($_postvars['linkedin']));
									$customer->SetInstagramURL(url_cleaner($_postvars['instagramurl']));
									$customer->SetWebsite(url_cleaner($_postvars['website']));
									$customer->SetAddress($_postvars['address']);
									$customer->SetCity($_postvars['city']);
									$customer->SetState($_postvars['state']);
									$customer->SetZip($_postvars['zip']);
									$customer->SetCountry($_postvars['country']);
									$customer->SetNote($_postvars['note']);
									$customer->SetLastUpdateTime();

									$tags = new TTagList();
									$tags->Load();

									if ( sm_action('postedit'))
										{
											for ( $i = 0; $i < $tags->Count(); $i++ )
												{
													/** @var  $tag TTag */
													$tag = $tags->Item($i);

													if ($customer->HasTagID($tag->ID()))
														$customer->UnsetTagID($tag->ID());
												}
											unset($tag);
										}

									for ($i = 0; $i < $tags->Count(); $i++)
										{
											for($j=0; $j<count($_postvars['tags_selected']); $j++)
												{
													if($_postvars['tags_selected'][$j]=='tag_'.$tags->items[$i]->ID())
														$customer->SetTagID($tags->items[$i]->ID());
												}
										}

									for($j=0; $j<count($_postvars['tags_selected']); $j++)
										{
											$exist=0;
											for ($i = 0; $i < $tags->Count(); $i++)
												{
													if('tag_'.$tags->items[$i]->ID()==$_postvars['tags_selected'][$j])
														{
															$exist=1;
															continue;
														}
												}
											if($exist!=1)
												{
													$newtag = TTag::Create(TCompany::CurrentCompany(), $_postvars['tags_selected'][$j]);
													$newtag->SetAddedBy(System::MyAccount()->ID());
													$customer->SetTagID($newtag->ID());
												}
										}

									if (sm_action('postadd'))
										{
											if(!empty($_postvars['list']))
												{
													$list = new TContactsList(intval($_postvars['list']));
													if (!$list->Exists())
														exit('Access Denied');
													else
														{
															if(!$list->HasContactID($customer->ID()))
																$list->SetContactID($customer->ID());
															sm_redirect($_getvars['returnto']);
														}
												}
											else
												sm_redirect('index.php?m=customerdetails&d=newmsg&id='.$customer->ID());
										}
									else
										{
											if (!empty($_getvars['returnto']))
												sm_redirect($_getvars['returnto']);
											else
												sm_redirect('index.php?m=customers&d=list&id='.$customer->ID());
										}
								}
						}
					if (!empty($error))
						{
							if (sm_action('postedit'))
								sm_set_action('edit');
							else
								sm_set_action('add');
						}
				}

			if (sm_action('add', 'edit'))
				{
                    use_api('temployeelist');
					add_path_home();
					add_path($currentcompany->LabelForCustomers(), 'index.php?m=customers&d=list');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();

					if (sm_action('edit'))
						$customer=new TCustomer(intval($_getvars['id']));

					if (sm_action('add') || is_object($customer) && $customer->Exists() && $currentcompany->ID()==$customer->CompanyID())
						{
							if (!empty($error))
								$ui->NotificationError($error);

							if (sm_action('edit'))
								{
									add_path($customer->Name(), 'index.php?m=customerdetails&d=info&id='.$customer->ID());
									sm_title($lang['common']['edit']);
									$f=new TForm('index.php?m=customers&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
								}
							else
								{
									sm_title($lang['common']['add']);
									if(!empty(intval($_getvars['contactlist'])))
										$f = new TForm('index.php?m=customers&d=postadd&contactlist='.intval($_getvars['contactlist']).'&returnto='.urlencode($_getvars['returnto']));
									else
										$f = new TForm('index.php?m=customers&d=postadd&returnto='.urlencode($_getvars['returnto']));
								}
                            $f->AddClassnameGlobal('two_column_form');
							$f->AddSeparator('contacts', 'Contacts');
							$f->AddText('first_name', 'First Name');
							$f->AddText('last_name', 'Last Name');
							$f->AddText('cellphone', 'Cellphone');

							$contactlist=new TContactsLists();
							$contactlist->SetFilterCompany(TCompany::CurrentCompany());
							$contactlist->OrderByTitle(false);
							$contactlist->Load();

							if (sm_action('add'))
								{
									$f->AddSelectVL('list', 'Contact List', $contactlist->ExtractIDsArray(), $contactlist->ExtractTitlesArray());
									$f->SelectAddBeginVL('list', '', '------------');

									if(!empty(intval($_getvars['contactlist'])))
										$f->SetValue('list', intval($_getvars['contactlist']));
								}

							$f->AddText('email', 'Email');
							$f->AddText('business_name', 'Business Name');
							$f->AddText('website', 'Website');
							$f->AddText('address', 'Address');
							$f->AddText('city', 'City');
							$f->AddText('state', 'Zip');
							$f->AddText('country', 'Country');

							$f->AddSeparator('social_media', 'Social Media Profiles');
							$f->AddText('facebookurl', 'Facebook');
							$f->AddText('twitterurl', 'Twitter');
							$f->AddText('linkedin', 'LinkedIn');
							$f->AddText('instagramurl', 'Instagram');

							$fieldslist_en = new TFieldsList();
							$fieldslist_en->SetFilterCategory(0);
							$fieldslist_en->SetFilterCompany($currentcompany);
							$fieldslist_en->SetFilterTemplate($currentcompany->CustomerFormTemplate());
							$fieldslist_en->SetFilterEnabled();
							$fieldslist_en->Load();

							if ($fieldslist_en->TotalCount() > 0)
								{
									$f->AddSeparator('staff', 'Staff');

									$additional_fields_ids=$fieldslist_en->ExtractIDsArray();
									$additional_fields_titles=$fieldslist_en->ExtractNamesArray();

									for ($j = 0; $j < $fieldslist_en->Count(); $j++)
										{
											if (sm_action('edit'))
												{
													$meta_val=$customer->GetMetaData('customfield_'.$additional_fields_ids[$j]);
													if(!empty($meta_val))
														$f->SetValue('customfield_'.$additional_fields_ids[$j], $meta_val);
												}

											$salesmanagers = new TEmployeeList();
											if (sm_action('add'))
												$salesmanagers->SetFilterNotDeleted();
											elseif(!empty($meta_val))
												$salesmanagers->SetFilterNotDeletedOr($meta_val);
											$salesmanagers->OrderBySelectedRoleAndName('salesmanager');
											$salesmanagers->Load();
											$f->AddSelectVL('customfield_'.$additional_fields_ids[$j], $additional_fields_titles[$j], $salesmanagers->ExtractIDsArray(), $salesmanagers->ExtractNamesArray());

										}
								}

							unset($fieldslist_en);

							$custom_fields_ctg = new TFieldsCategoriesList();
							$custom_fields_ctg->SetFilterCompany($currentcompany);
							$custom_fields_ctg->SetFilterTemplate($currentcompany->CustomerFormTemplate());
							$custom_fields_ctg->Load();

							for ($i = 0; $i < $custom_fields_ctg->Count(); $i++)
								{
									/** @var  $category TFieldsCategory */
									$category = $custom_fields_ctg->items[$i];
									$fieldslist_en = new TFieldsList();
									$fieldslist_en->SetFilterCategory($category);
									$fieldslist_en->SetFilterCompany($currentcompany);
									$fieldslist_en->SetFilterTemplate($currentcompany->CustomerFormTemplate());
									$fieldslist_en->SetFilterEnabled();
									$fieldslist_en->Load();

									if ($fieldslist_en->TotalCount() > 0)
										$f->AddSeparator($category->Category(), $category->Category());
									else
										continue;

									$additional_fields_ids=$fieldslist_en->ExtractIDsArray();
									$additional_fields_titles=$fieldslist_en->ExtractNamesArray();
									for ($j = 0; $j < $fieldslist_en->Count(); $j++)
										{
											$f->AddText('customfield_'.$additional_fields_ids[$j], $additional_fields_titles[$j]);
											if (sm_action('edit'))
												{
													$meta_val=$customer->GetMetaData('customfield_'.$additional_fields_ids[$j]);
													if(!empty($meta_val))
														$f->SetValue('customfield_'.$additional_fields_ids[$j], $meta_val);
												}
										}
								}

							$pre_selected_tags = [];
							if (sm_action('edit'))
								{
									$pre_selected_tags = $customer->GetTagIDsArray();
								}
							if (is_array($_postvars) && !empty($_postvars))
								{
									$pre_selected_tags = [];
									
									foreach ($_postvars['tags_selected'] as $tags_arr)
										$pre_selected_tags[] = str_replace('tag_', '', $tags_arr);
								}
                            $f->AddSeparator('tags', 'Tags');
							$tagData['tags'] = [];
							$tags = new TTagList();
							$tags->Load();
							$tagValuesArr = [];
							for ($i = 0; $i < $tags->Count(); $i++)
								{
									$tmp=$tags->items[$i];
									$tagData['tags'][$i]['value'] ='tag_'.$tmp->ID();
									$tagData['tags'][$i]['title'] = $tmp->Name();
									if($pre_selected_tags && in_array($tmp->ID(),$pre_selected_tags))
									{ 
										$tagData['tags'][$i]['checked'] = 1;
									}
								}
                            $f->AddLabel('tagbar', 'User Tags', '');
                            $f->InsertTPL('tags_select.tpl', $tagData, '', 'Tags', 'tagbar');
                            unset($tagData);
                            $f->AddSeparator('other', 'Other');
							$f->AddTextarea('note', 'Note');
							if (sm_action('edit'))
								{
									$f->LoadValuesArray($customer->info);
									$f->SetValue('business_name', $customer->GetBusinessName());
								}
							if (is_array($_postvars))
								$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
							if (empty($error_pwd))
								sm_setfocus('first_name');
							else
								sm_setfocus('password');
						}
				}

			if (sm_action('postsetphoto'))
				{
					use_api('temployeelist');
					add_path_home();
					add_path($currentcompany->LabelForCustomers(), 'index.php?m=customers&d=list');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();
					$customer=new TCustomer(intval($_getvars['id']));
					if ($customer->Exists() && $currentcompany->ID()==$customer->CompanyID())
						{
							$tmpfile=sm_upload_file();
							if (!file_exists($tmpfile))
								$error='Error uploading file';
							else
								{
									sm_extcore();
									sm_resizeimage($tmpfile, $customer->ProfilePhotoPath(), 400, 400, 0, 100, 1);
								}
							if (!empty($error))
								sm_set_action('setphoto');
							else
								sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('setphoto'))
				{
					use_api('temployeelist');
					add_path_home();
					add_path($currentcompany->LabelForCustomers(), 'index.php?m=customers&d=list');
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					$ui = new TInterface();
					$customer=new TCustomer(intval($_getvars['id']));
					if ($customer->Exists() && $currentcompany->ID()==$customer->CompanyID())
						{
							add_path($customer->Name(), 'index.php?m=customerdetails&d=info&id='.$customer->ID());
							if (!empty($error))
								$ui->NotificationError($error);
							sm_title('Update Profile Photo');
							$f=new TForm('index.php?m=customers&d=postsetphoto&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
							$f->AddFile('userfile', 'Image');
							if (is_array($_postvars))
								$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
						}
				}

			if (sm_action('getbriefinfo'))
				{
					header('Content-Type: application/json; charset=utf-8');

					if (!empty($_getvars['phone']))
						{
							$customer = TCustomer::initWithCustomerPhoneNotDeleted(Cleaner::USPhone($_getvars['phone']), TCompany::CurrentCompany()->ID());
							if ( is_object($customer) && $customer->Exists())
								{
									echo json_encode(array(
										'id' => $customer->ID(),
										'name' => $customer->Name(),
										'url' => 'index.php?m=customerdetails&id='.$customer->ID(),
									));
								}
							else
								{
									echo json_encode(array(
										'id' => '',
										'name' => '',
										'url' => '',
									));
								}
						}
					exit();
				}

			if (sm_action('setstatus'))
				{
					if (intval($_getvars['id'])>0)
						$customer=new TCustomer(intval($_getvars['id']));
					if (is_object($customer) && $customer->Exists())
						{
							if (in_array($_getvars['status'], TCustomer::StatusTagValues()))
								{
									$customer->SetStatusTag($_getvars['status']);

									$customerlist = new TCustomerList();
									$customerlist->SetFilterEnabled();
									$customerlist->SetFilterStatus($_getvars['status']);
									$customerlist->OrderBySortOrder();
									$customerlist->SetFilterExcludeIDs(array($customer->ID()));
									$customerlist->Load();
									$newposition = $_getvars['position'];
									for ($i=0; $i<$customerlist->Count(); $i++)
										{
											$customerlist->items[$i]->SetSortOrder($i);
											if( $customerlist->items[$i]->SortOrder() == $newposition)
												{
													$newposition = $newposition+1;
													$customerlist->items[$i]->SetSortOrder($newposition);
												}
										}
									$customer->SetSortOrder($_getvars['position']);
								}
						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('list'))
				{
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');

					sm_add_cssfile('leaddraganddrop.css');

					sm_add_jsfile('referrals.js');
					$extendedfilters=false;
					add_path_home();
					add_path($currentcompany->LabelForCustomers(), 'index.php?m=customers&d=list');
					sm_title($currentcompany->LabelForCustomers());

					$ui = new TInterface();
					$b=new TButtons();
					$b->AddButton('add', $lang['common']['add'], 'index.php?m=customers&d=add&returnto='.urlencode(sm_this_url()));
					$b->AddClassname('add_button_contacts');
					$b->AddButton('received', 'Table View', 'index.php?m='.sm_current_module().'&d=listview', '', 'margin-top:-50px; margin-bottom:-20px;');
					$b->AddClassname('add_asset_button ab-button pull-right', 'received');

					if (!$extendedfilters)
						$b->AddToggle('extsrch', 'Extended Search', 'ext-search');
						
					$ui->AddButtons($b);

					$m['statusarray'] = ['received', 'contact', 'appointment', 'sold', 'lost'];

					$tags=new TTagList();
					$tags->OrderByName();
					$tags->Load();

					$tagsfilter=Array();
					if(!empty($_getvars['tags_selected']))
						{
							$tags_array = explode(',', $_getvars['tags_selected']);
							$extendedfilters=true;
							for ($i = 0; $i < $tags->Count(); $i++)
								{
									for($j=0; $j<count($tags_array); $j++)
										{
											if($tags_array[$j]==$tags->items[$i]->ID())
												{
													$tmp=$tags->items[$i]->GetCustomerIDsArray();
													$tagsfilter=array_merge($tagsfilter, $tmp);
												}
										}
								}
						}

					$contactlist=new TContactsLists();
					$contactlist->SetFilterCompany(TCompany::CurrentCompany());
					$contactlist->OrderByTitle(false);
					$contactlist->Load();

					$ui->div_open('ext-search', '', $extendedfilters?'':'display:none');
					$ui->h(3, 'Extended Search');
					$f=new TForm('index.php', '', 'get');
					$f->AddHidden('m', 'customers');
					$f->AddHidden('d', 'list');

					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$data['tags'][$i]['title'] = $tags->items[$i]->Name();
							$data['tags'][$i]['value'] = $tags->items[$i]->ID();
						}

					if ($tags_array)
						{
							for($j=0; $j<count($tags_array); $j++)
								{
									for ($i = 0; $i < $tags->Count(); $i++)
										{
											if($tags_array[$j]==$data['tags'][$i]['value'])
												{
													if($j==0)
														$data['values_selected'].= $data['tags'][$i]['value'];
													else
														$data['values_selected'].= ','.$data['tags'][$i]['value'];
												}
										}
								}
						}

					$f->AddLabel('tagbar', 'Tags', '');
					$f->InsertTPL('tags_filter.tpl', $data, '', 'Tags', 'tagbar');
					unset($data);

					$f->AddSelectVL('list', 'Contact List', $contactlist->ExtractIDsArray(), $contactlist->ExtractTitlesArray());
					$f->SelectAddBeginVL('list', '', '----');
					$f->AddText('registeredfrom', 'Registered After');
					$f->Calendar();
					$f->AddText('registeredto', 'Registered Before');
					$f->Calendar();
					$f->LoadValuesArray($_getvars);
					$f->SaveButton('Search');
					$ui->AddForm($f);
					$ui->div_close();


					for ($j = 0; $j<count($m['statusarray']); $j++)
						{
							if($m['statusarray'][$j] == 'lost')
								$limit = 10;
							else
								$limit = 50;
							$list=new TCustomerList();
							$list->SetFilterEnabled();
							$list->SetFilterCompany($currentcompany);
							$list->OrderBySortOrder();
							$list->SetFilterStatus($m['statusarray'][$j]);

							if($m['statusarray'][$j] == 'lost')
								$list->SetFilter10DaysOld();

							if (!empty($_getvars['q']))
								{
									if (Validator::USPhone($_getvars['q']))
										{
											$list->SetFilterCellPhone($_getvars['q']);
											sm_title($currentcompany->LabelForCustomers().' - find by cellphone number');
										}
									else
										{
											$list->SetFilterName($_getvars['q']);
											sm_title($currentcompany->LabelForCustomers().' - search');
										}
								}

							if (!empty($_getvars['list']))
								{
									$listcustomer = new TContactsList($_getvars['list']);
									if ( $listcustomer->Exists() )
										$list->SetFilterIDs($listcustomer->GetCustomerIDsArray());

									$extendedfilters=true;
								}

							if (!empty($_getvars['registeredfrom']))
								{
									$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredfrom']);
									$list->SetFilterRegisteredFrom($tmp);
									$extendedfilters=true;
								}

							if (!empty($_getvars['registeredto']))
								{
									$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredto']);
									$list->SetFilterRegisteredTo($tmp);
									$extendedfilters=true;
								}

							if (!empty($_getvars['tag']))
								{
									$_getvars['tags_selected'] = $_getvars['tag'];
									$_getvars['tag']='';
								}

							if(!empty($_getvars['tags_selected']))
								{
									if (count($tagsfilter)>0)
										{
											$tagsfilter = array_values(array_unique($tagsfilter));
											$list->SetFilterIDs($tagsfilter);
											$extendedfilters=true;
										}
								}

							$list->Limit($limit);
							$list->Load();

							if($list->TotalCount() > $limit)
								$data[$m['statusarray'][$j]]['showlostleadsbutton']='Show All '.$currentcompany->LabelForCustomers();

							for ($i = 0; $i < $list->Count(); $i++)
								{
									$customer=$list->items[$i];
									$data['customer'][$m['statusarray'][$j]][$i]['id'] = $customer->ID();
									$data['customer'][$m['statusarray'][$j]][$i]['name'] = $customer->Name();
									$data['customer'][$m['statusarray'][$j]][$i]['status'] = $customer->StatusTag();
									if ($customer->HasUnreadConversation())
										$data['customer'][$m['statusarray'][$j]][$i]['unreaded'] = 'Unreaded';

									if ($customer->HasCellphone())
										$data['customer'][$m['statusarray'][$j]][$i]['cellphone'] = Formatter::USPhone($customer->Cellphone());

									if ($customer->HasEmail())
										{
											$data['customer'][$m['statusarray'][$j]][$i]['email'] = $customer->Email();
//											$data['customer'][$m['statusarray'][$j]][$i]['emailstatus'] = $customer->GetEmailStatus();
										}


									$data['customer'][$m['statusarray'][$j]][$i]['note'] = strlen($customer->Note(false)) > 150 ? substr($customer->Note(false),0,150)."..." : $customer->Note(false);
									$data['customer'][$m['statusarray'][$j]][$i]['view_url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();
									$data['customer'][$m['statusarray'][$j]][$i]['editurl'] = 'index.php?m='.sm_current_module().'&d=edit&id='.$customer->ID().'&returnto='.urlencode('index.php?m='.sm_current_module());
									$data['customer'][$m['statusarray'][$j]][$i]['deleteurl'] = 'index.php?m='.sm_current_module().'&d=postdelete&id='.$customer->ID().'&returnto='.urlencode('index.php?m='.sm_current_module());

									if ($customer->isSendingMessagesRejected())
										$data['customer'][$m['statusarray'][$j]][$i]['tags'][] = Array(
											'title' => 'Do Not Text',
											'class' => 'label-danger',
											'url' => 'javascript:;'
										);
									if ($customer->isSendingMessagesPending())
										$data['customer'][$m['statusarray'][$j]][$i]['tags'][] = Array(
											'title' => 'Pending Accept Texts',
											'class' => 'label-default',
											'url' => 'javascript:;'
										);
									if ($customer->isSendingMessagesNoResponse())
										$data['customer'][$m['statusarray'][$j]][$i]['tags'][] = Array(
											'title' => 'No Response',
											'class' => 'label-warning',
											'url' => 'javascript:;'
										);
									for ($tag_index = 0; $tag_index < $tags->Count(); $tag_index++)
										{
											if ($customer->HasTagID($tags->items[$tag_index]->ID()))
												{
													$data['customer'][$m['statusarray'][$j]][$i]['tags'][] = Array(
														'title' => $tags->items[$tag_index]->Name(),
														'url' => 'index.php?m=customers&d=listview&tag='.$tags->items[$tag_index]->ID()
													);
												}
										}
									$data['customer'][$m['statusarray'][$j]][$i]['tags_count'] = count($data['customer'][$m['statusarray'][$j]][$i]['tags']);
								}
							unset($list);
							unset($customer);
						}

					$ui->AddTPL('leadslist.tpl', '', $data);
					$ui->Output(true);
				}

			if (sm_action('listview'))
				{
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					use_api('smdatetime');
					use_api('ttaglist');
					sm_add_jsfile('call.js');
					sm_add_jsfile('customers.js');
					sm_add_jsfile('ext/datepicker/js/bootstrap-datepicker.js', true);
					sm_add_cssfile('ext/datepicker/css/datepicker.css', true);
					add_path_home();
					if ($_getvars['status']=='noresponse')
						{
							add_path('Conversations', 'index.php?m=messages');
							add_path('Messages', 'index.php?m=customers&d=listview&status=noresponse');
							sm_title('Messages - No Response');
						}
					else
						{
							add_path($currentcompany->LabelForCustomers(), 'index.php?m=customers&d=list');
							sm_title($currentcompany->LabelForCustomers());
						}

					$extendedfilters=false;
					$offset=abs(intval($_getvars['from']));
					$limit=intval($_getvars['ipp']);
					if ($limit<30)
						$limit=30;
					$tags=new TTagList();
					$tags->OrderByName();
					$tags->Load();

					$contactlist=new TContactsLists();
					$contactlist->SetFilterCompany(TCompany::CurrentCompany());
					$contactlist->OrderByTitle(false);
					$contactlist->Load();

					$ui = new TInterface();
					$b=new TButtons();

					$customers = new TCustomerList();
					$customers->SetFilterEnabled();
					$customers->SetFilterCompany($currentcompany);
					if (!empty($_getvars['q']))
						{
							if (Validator::USPhone($_getvars['q']))
								{
									$customers->SetFilterCellPhone($_getvars['q']);
									sm_title($currentcompany->LabelForCustomers().' - find by cellphone number');
								}
							else
								{
									$customers->SetFilterName($_getvars['q']);
									sm_title($currentcompany->LabelForCustomers().' - search');
								}
						}

					if (!empty($_getvars['id']))
						{
							$customers->SetFilterIDs(array(intval($_getvars['id'])));
							$extendedfilters=true;
						}
					if (!empty($_getvars['list']))
						{
							$listcustomer = new TContactsList($_getvars['list']);
							if ( $listcustomer->Exists() )
								$customers->SetFilterIDs($listcustomer->GetCustomerIDsArray());

							$extendedfilters=true;
						}
					if (!empty($_getvars['make']))
						{
							$customers->SetFilterVehicleMake($_getvars['make']);
							$extendedfilters=true;
						}
					if (!empty($_getvars['model']))
						{
							$customers->SetFilterVehicleModel($_getvars['model']);
							$extendedfilters=true;
						}
					if (!empty($_getvars['condition']))
						{
							$customers->SetFilterVehicleCondition($_getvars['condition']);
							$extendedfilters=true;
						}
					if (!empty($_getvars['registeredfrom']))
						{
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredfrom']);
							$customers->SetFilterRegisteredFrom($tmp);
							$extendedfilters=true;
						}
					if (!empty($_getvars['registeredto']))
						{
							$tmp=SMDateTime::TimestampFromUSDateAndTime($_getvars['registeredto']);
							$customers->SetFilterRegisteredTo($tmp);
							$extendedfilters=true;
						}
					if (!empty($_getvars['tag']))
						{
							$_getvars['tags_selected'] = $_getvars['tag'];
							$_getvars['tag']='';
						}
					$tagsfilter=Array();
					if(!empty($_getvars['tags_selected']))
						{
							$tags_array = explode(',', $_getvars['tags_selected']);
							for( $i = 0; $i < count($tags_array); $i++ )
								{
									$tag_selected = new TTag($tags_array[$i]);
									if($tag_selected->Exists())
										{
											$tmp = $tag_selected->GetCustomerIDsArray();
											$tagsfilter=array_merge($tagsfilter, $tmp);
										}
									unset($tag_selected);
								}
							if (count($tagsfilter)>0)
								{
									$tagsfilter=array_values(array_unique($tagsfilter));
									$customers->SetFilterIDs($tagsfilter);
									$extendedfilters=true;
								}
						}

					if (intval($_getvars['unread'])==1)
						$customers->SetFilterUnread();

					$customers->OrderByUnreadOrLastUpdate(false);
					$customers->Limit($limit);
					$customers->Offset($offset);
					if ($_getvars['status']!='noresponse')
						{
							$b->AddButton('add', $lang['common']['add'], 'index.php?m=customers&d=add&returnto='.urlencode(sm_this_url()));
							if (intval($_getvars['unread'])==1 || !empty($_getvars['q']) || $extendedfilters)
								$b->AddButton('all', 'Show All', 'index.php?m=customers&d=listview');
							if (intval($_getvars['unread'])!=1)
								$b->AddButton('unread', 'Show Unread', 'index.php?m=customers&d=listview&unread=1');
							if (!$extendedfilters)
								$b->AddToggle('extsrch', 'Extended Search', 'ext-search');
							$b->AddButton('smsblast', 'Send Text');
							$b->Style('smsblast', 'display:none;');
							$b->AddClassname('smsblast', 'smsblast');
							$b->OnClick("$('#smsblastform').submit();", 'smsblast');

							$ui->div_open('ext-search', '', $extendedfilters?'':'display:none');
							$ui->h(3, 'Extended Search');
							$f=new TForm('index.php', '', 'get');
							$f->AddHidden('m', 'customers');
							$f->AddHidden('d', 'listview');
							if(!empty($_getvars['status']))
								$f->AddHidden('status', dbescape($_getvars['status']));


							for ($i = 0; $i < $tags->Count(); $i++)
								{
									$data['tags'][$i]['title'] = $tags->items[$i]->Name();
									$data['tags'][$i]['value'] = $tags->items[$i]->ID();
								}

							if ($tags_array)
								{
									for($j=0; $j<count($tags_array); $j++)
										{
											$tag_selected = new TTag($tags_array[$j]);
											if($tag_selected->Exists())
												{
													if( $j == 0 )
														$data['values_selected'].= $tag_selected->ID();
													else
														$data['values_selected'].= ','.$tag_selected->ID();
												}
										}
								}


							$f->AddLabel('tagbar', 'Tags', '');
							$f->InsertTPL('tags_filter.tpl', $data, '', 'Tags', 'tagbar');
							unset($data);

							$f->AddSelectVL('status', 'Status', Array('all', 'received', 'contact', 'appointment', 'sold', 'lost'), Array('All', 'Received', 'Contact', 'Appointment', 'Sold', 'Lost'))->WithValue($_getvars['status']);
							$f->AddSelectVL('list', 'Contact List', $contactlist->ExtractIDsArray(), $contactlist->ExtractTitlesArray());
							$f->SelectAddBeginVL('list', '', '----');
							$f->AddText('registeredfrom', 'Registered After');
							$f->Calendar();
							$f->AddText('registeredto', 'Registered Before');
							$f->Calendar();
							$f->AddSelectVL('ipp', 'Items Per Page', Array(30, 50, 100, 200, 1000), Array(30, 50, 100, 200, 1000));
							$f->LoadValuesArray($_getvars);
							$f->SaveButton('Search');
							$ui->AddForm($f);
							$ui->div_close();
							$b->AddButton('pipeline', 'Pipeline View', 'index.php?m='.sm_current_module().'&d=list', '', 'margin-top:-55px; margin-bottom:-20px;');
							$b->AddClassname('add_asset_button ab-button pull-right', 'pipeline');
							$ui->AddButtons($b);

							$status_buttons=new TButtons();
							$status_buttons->AddClassnameGlobal('status-buttons');

							$status_buttons->AddButton('all', 'Show All', 'index.php?m='.sm_current_module().'&d='.sm_current_action());
							$status_buttons->AddButton('received', 'Received', 'index.php?m='.sm_current_module().'&d='.sm_current_action().'&status=received');
							$status_buttons->AddButton('contact', 'Contact', 'index.php?m='.sm_current_module().'&d='.sm_current_action().'&status=contact');
							$status_buttons->AddButton('appointment', 'Appointment', 'index.php?m='.sm_current_module().'&d='.sm_current_action().'&status=appointment');
							$status_buttons->AddButton('sold', 'Sold', 'index.php?m='.sm_current_module().'&d='.sm_current_action().'&status=sold');
							$status_buttons->AddButton('lost', 'Lost', 'index.php?m='.sm_current_module().'&d='.sm_current_action().'&status=lost');

							if ($_getvars['status']=='contact')
								{
									$customers->SetFilterStatus('contact');
									$status = $_getvars['status'];
									$status_buttons->AddClassname('current', 'contact');

								}
							elseif ($_getvars['status']=='appointment')
								{
									$customers->SetFilterStatus('appointment');
									$status = $_getvars['status'];
									$status_buttons->AddClassname('current', 'appointment');
								}
							elseif ($_getvars['status']=='sold')
								{
									$customers->SetFilterStatus('sold');
									$status = $_getvars['status'];
									$status_buttons->AddClassname('current', 'sold');
								}
							elseif ($_getvars['status']=='lost')
								{
									$customers->SetFilterStatus('lost');
									$status = $_getvars['status'];
									$status_buttons->AddClassname('current', 'lost');
								}
							elseif ($_getvars['status']=='noresponse')
								{
									$customers->SetFilterSMSAcceptNoResponse();
								}
							elseif ($_getvars['status']=='received')
								{
									$customers->SetFilterStatus('received');
									$status = $_getvars['status'];
									$status_buttons->AddClassname('current', 'received');
								}
							else
								$status_buttons->AddClassname('current', 'all');

							$ui->AddButtons($status_buttons);
						}
					else
						{
							$data['currmode'] = sm_current_module();
							$ui->AddTPL('messages_header.tpl', '', $data);
						}

					$customers->Load();

					$ui->html('<form action="index.php?m=smsblast&d=startmulti&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
                    for ($i = 0; $i < $customers->Count(); $i++)
						{
							$customer = new TCustomer($customers->items[$i]->ID());
							$data['id'] = $customer->ID();
							$data['url'] = 'index.php?m=customerdetails&d=info&id='.$customer->ID();
							if ($customer->HasProfilePhoto())
								$data['profile_photo'] = $customer->ProfilePhotoURL();
							else
								$data['profile_photo'] = 'ext/images/default-avatar.png';
							$data['profile_change_photo_url'] = 'index.php?m=customers&d=setphoto&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							$data['initials'] = $customer->Initials();
							$data['vehicle_condition'] = $customer->VehicleCondition();
                            $data['name'] = $customer->Name();
							$data['boxes'] = Array();
//							$data['boxes'][] = Array(
//								'label' => 'Marketing Messages',
//								'value' => $customer->MarketingMessagesCount()
//							);

							if (is_array($customer->GetLastContact()))
								{
									$data['boxes'][] = Array(
										'label' => 'Last Contacted',
										'value' => $customer->GetLastContact()['time'].' - '.ucfirst($customer->GetLastContact()['action'])
									);
								}

							if (is_array($customer->GetActiveSequence()))
								{
									$data['boxes'][] = Array(
										'label' => 'Sequence',
										'value' => $customer->GetActiveSequence()['campaign'],
										'url' => $customer->GetActiveSequence()['campaign_url']
									);
								}

							if (is_array($customer->GetNextActivity()))
								{
									$data['boxes'][] = Array(
										'label' => 'Next Activity',
										'value' => $customer->GetNextActivity()['time'].' - '.ucfirst($customer->GetNextActivity()['action']),
										'url' => $customer->GetNextActivity()['url']
									);
								}

							if ($customer->HasCellphone())
								{
									$data['cellphone'] = Formatter::USPhone($customer->Cellphone());
									$sendmessage_url = "index.php?m=contactcustomer&d=call&id=".$customer->ID()."&theonepage=1&returnto=".urlencode(sm_this_url());
									$data['cellphone_url'] = '<a href="javascript:;" onclick=\'make_a_call("'.$sendmessage_url.'", '.$customer->ID().')\'  title="Call"><i class="fa fa-phone"></i> '.Formatter::USPhone($customer->Cellphone()).'</a>';
								}

							if ($customer->HasEmail())
								$data['email'] = $customer->Email();

							$data['info'] = 'Info';
							if ($customer->HasUnreadConversation())
								$data['conversation'] = 'Unread';
							else
								$data['conversation'] = 'View';
							$data['conversation_url'] = 'index.php?m=customers&d=log&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							$data['conversation_url'] = 'index.php?m=customerdetails&d=conversation&id='.$customer->ID();
							$data['edit'] = 'index.php?m=customers&d=edit&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							$data['delete'] = 'index.php?m=customers&d=postdelete&id='.$customer->ID().'&returnto='.urlencode(sm_this_url());
							if ($customer->isSendingMessagesRejected())
								$data['tags'][] = Array(
									'title' => 'Do Not Text',
									'class' => 'label-danger',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesPending())
								$data['tags'][] = Array(
									'title' => 'Pending Accept Texts',
									'class' => 'label-default',
									'url' => 'javascript:;'
								);
							if ($customer->isSendingMessagesNoResponse())
								$data['tags'][] = Array(
									'title' => 'No Response',
									'class' => 'label-warning',
									'url' => 'javascript:;'
								);
							if ($customer->isUnsubscribeStatus())
								$data['tags'][] = Array(
									'title' => 'Unsubscribed',
									'class' => 'label-warning',
									'url' => 'javascript:;'
								);

							$customers_tag = $customer->GetTagIDsArray();
							for ($j = 0; $j < count($customers_tag); $j++)
								{
									$tag = new TTag($customers_tag[$j]);
									if ($tag->Exists())
										{
											$data['tags'][] = Array(
												'title' => $tag->Name(),
												'url' => 'index.php?m=customers&d=listview&tag='.$tag->ID()
											);
										}
								}
							$ui->AddTPL('customerslist.tpl', '', $data);
							unset($tag);
							unset($customers_tag);
							unset($customer);
							unset($data);
						}
					if ($customers->Count() == 0)
						{
							$data['noinfo'] = 'Nothing Found';
							$ui->AddTPL('customerslist.tpl', '', $data);
						}
					$ui->html('</form>');
					$ui->javascript("\$('.at-bulk-checkbox').change(function(){checksmsblast()});");
					$ui->AddPagebarParams($customers->TotalCount(), $limit, $offset);
					$ui->div_open('messagemodal', 'modal fade');
					$ui->div_open('', 'modal-dialog');
					$ui->div_open('', 'modal-content');
					$ui->div_open('messagemodal_content');
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();
					$ui->div_close();
					$ui->br();
					$ui->Output(true);
				}

			if (sm_action('sendmessage'))
				{
					$customer=new TCustomer(intval($_getvars['id']));
					if ($customer->Exists())
						{
							if (!empty($_postvars['text']) || !empty($_postvars['text_template']))
								{
									$sendtime=0;
									$tmp=SMDateTime::TimestampFromUSDateAndTime($_postvars['schedule'], $_postvars['hr'].':'.$_postvars['min'].' '.$_postvars['ampm']);
									if ($tmp>SMDateTime::Now())
										$sendtime=$tmp;

									if(!empty($_postvars['text_template']))
										{
											$sms = new TMessageTemplate(intval($_postvars['text_template']));
											if ($sms->Exists() && !empty($sms->Text()))
												$_postvars['text'] = $sms->Text();
											if($sms->HasAssetID())
												$attachment = Array(sm_homepage().'index.php?m=companyassets&d=twiliomms&id='.intval($sms->AssetID()));
										}
									else
										{
											if (!empty($_postvars['attachment']))
												$attachment = Array(sm_homepage().'index.php?m=companyassets&d=twiliomms&id='.intval($_postvars['attachment']));
											else
												$attachment = Array();
										}
									$customer->SendMessage(replace_tags_campaign($_postvars['text'], $customer), System::MyAccount()->ID(), true, 'startmessage', $sendtime, $attachment, true, intval($_postvars['attachment']));
									$customer->SendSMSAction();
									sm_notify('Message sent');
								}
							else
								sm_notify('Message cannot be empty', 'Error', 'error');

							sm_redirect($_getvars['returnto']);
						}
				}

			if ($userinfo['level']==3)
				{
					if (sm_action('admin'))
						{
							add_path_home();
							$m['title'] = $currentcompany->LabelForCustomers();
							include_once('includes/admininterface.php');
							$ui = new TInterface();
							$ui->a('index.php?m=customers&d=list', $lang['common']['list']);
							$ui->Output(true);
						}
				}
		}
	else
		sm_redirect('index.php?m=account');

<?php

	sm_add_body_class('wizard');

	if ($userinfo['level']>0)
		{
			$m['campaign_duration']=10;

			if($_getvars['action'] == 'create' && empty($_getvars['list']))
				$m['wizardsteps'] = ['campaigntitle', 'sequence'];
			elseif($_getvars['action'] == 'create' && !empty($_getvars['list']))
				$m['wizardsteps'] = ['campaigntitle', 'sequence', 'listdetails', 'schedule'];
			else
				$m['wizardsteps'] = ['selectype', 'selectlist', 'add_tags', 'listdetails', 'schedule'];

			if (!empty($_getvars['id']))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->CampaignSingleUser())
						$m['wizardsteps'] = ['selectype', 'addcontact', 'listdetails', 'schedule'];

					if ($campaign->Status() == 'started' || $campaign->Status() == 'scheduled')
						$m['wizardsteps']=['campaigntitle', 'updated'];
				}

			function get_wizard_steps($current_action='', $id_campaign = 0)
				{
					global $m;
					$wizard=array();

					if (!empty($id_campaign))
						{
							$campaign = new TCampaign(intval($id_campaign));
							if ($campaign->CampaignSingleUser())
								$m['wizardsteps'] = ['selectype', 'addcontact', 'listdetails', 'schedule'];
							else
								$m['wizardsteps'] = ['selectype', 'selectlist', 'add_tags', 'listdetails', 'schedule'];

							if ($campaign->Status() == 'started' || $campaign->Status() == 'scheduled')
								$m['wizardsteps']=['campaigntitle', 'updated'];
						}

					for($i=0; $i<count($m['wizardsteps']); $i++)
						{
							if($m['wizardsteps'][$i]==$current_action)
								{
									$wizard['next_steps']=$m['wizardsteps'][$i+1];
									$wizard['previous_steps']=$m['wizardsteps'][$i-1];
									$wizard['current_step']=$i;
								}
						}
					return $wizard;
				}

			if (!function_exists('wizard_import_data'))
				{
					/**
					 * @param TCampaign $campaign
					 * @param string $filename
					 * @return string
					 */
					function wizard_import_data($campaign, $filename, $new_tags=Array(), $updatecontacts=0)
						{
							$error_message = '';
							if (!file_exists($filename))
								{
									$error_message = 'File not found';
								}
							else
								{
									$i = 1;
									$fh = fopen($filename, 'r');
									while ($data = fgetcsv($fh))
										{
											if ($data[0] == 'First Name')
												continue;
											if (!Validator::Email($data[2]))
												{
													$error_message = 'Email in line '.$i.' is not valid. Please check your data';
													break;
												}
											if (!empty($data[3]) && !Validator::USPhone($data[3]))
												{
													$error_message = 'Phone number in line '.$i.' is not valid. Please check your data';
													break;
												}
											$i++;
										}
									fclose($fh);
									if (empty($error_message) && $i > 501)
										$error_message = 'The file is too large. 500 records per file allowed';
								}
							if (empty($error_message))
								{
									$fh = fopen($filename, 'r');
									$tags=new TTagList();
									$tags->Load();
									while ($data=fgetcsv($fh))
										{
											if($data[0]=='First Name')
												continue;
											if(!empty($data[3]))
												{
													$data[3] = Cleaner::USPhone($data[3]);
													$customer=TCustomer::initWithCustomerPhone($data[3], TCompany::CurrentCompany()->ID());
												}
											else
												$customer=TCustomer::initWithCustomerEmail($data[2], TCompany::CurrentCompany()->ID());

											if ($customer->isDeleted())
												continue;

											if (!$customer->Exists() && !$customer->isDeleted())
												{
													$customer = TCustomer::CreateByStaff(
														TCompany::CurrentCompany(),
														$data[0],
														$data[1],
														$data[3],
														trim($data[2])
													);
												}
											$customer->AddToCampaign($campaign);
											for ($i = 4; $i < count($data); $i++)
												{
													if (empty($data[$i]))
														continue;
													if (!$tags->HasTagName($data[$i]))
														{
															TTag::Create(TCompany::CurrentCompany(), $data[$i]);
															$tags=new TTagList();
															$tags->Load();
														}
													$customer->SetTagID($tags->GetTagByName($data[$i])->ID());
												}

											for($j=0; $j<count($new_tags); $j++)
												{
													if($tags->GetTagByName($new_tags[$j]))
														$customer->SetTagID($tags->GetTagByName($new_tags[$j])->ID());
													else
														{
															$customer->SetTagID(TTag::Create(TCompany::CurrentCompany(), $new_tags[$j])->ID());
															$tags=new TTagList();
															$tags->Load();
														}
												}

											if($customer->Exists() && !$customer->isDeleted())
												{
													if ($updatecontacts==1)
														{
															if(!empty($data[3]))
																$customer->SetCellPhone(Cleaner::USPhone($data[3]));
															if(!empty($data[2]))
																$customer->SetEmail($data[2]);
															if(!empty($data[0]))
																$customer->SetFirstName($data[0]);
															if(!empty($data[1]))
																$customer->SetLastName($data[1]);
														}
													$campaignitems = new TCampaignItemList();
													$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
													$campaignitems->SetFilterCampaign($campaign);
													$campaignitems->Load();
													$exist=0;
													for($n=0; $n<$campaignitems->Count(); $n++)
														{
															if((!empty($customer->Cellphone()) && $customer->Cellphone() == $campaignitems->items[$n]->Phone()) || (!empty($customer->Email()) && $customer->Email() == $campaignitems->items[$n]->Email()))
																{
																	$exist=1;
																	continue;
																}

														}
													if($exist!=1)
														{
															$campaignitem = TCampaignItem::Create(TCompany::CurrentCompany(), $campaign);
															$campaignitem->SetFirstName($customer->FirstName());
															$campaignitem->SetLastName($customer->LastName());
															$campaignitem->SetCompany($customer->GetBusinessName());
															if($customer->HasEmail())
																$campaignitem->SetEmail($customer->Email());
															if($customer->HasCellphone())
																$campaignitem->SetPhone($customer->Cellphone());
															$campaignitem->SetPartner($customer->ID());
														}
												}
										}
									fclose($fh);
									unlink($filename);
								}
							return $error_message;
						}
				}
			if($_getvars['action'] == 'create')
				sm_default_action('campaigntitle');
			else
				sm_default_action('selectype');


			if(sm_action('postaddtitle'))
				{
					if (empty($_postvars['campaign_title']))
						{
							$error_message = 'Wrong title';
						}
					else
						{
							if (!empty($_getvars['id']))
								{
									$campaign = new TCampaign(intval($_getvars['id']));
									$newid = intval($_getvars['id']);
								}
							else
								{
									/** @var TCampaign $campaign */
									$campaign = TCampaign::Create(TCompany::CurrentCompany());
									$newid = $campaign->ID();
								}
							$campaign->SetTitle($_postvars['campaign_title']);
						}
					if (!empty($error_message))
						{
							sm_set_action('campaigntitle');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$newid.'&action=create'.(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):''));
							else
								sm_redirect('index.php?m=campaigns');
						}
				}

			if(sm_action('postselectype'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');

					if (empty($_postvars['selectype']))
						$error_message = 'Choose type';
					else
						{
							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign->ID());
							$contacts->Load();
							for($i=0; $i<$contacts->Count(); $i++)
								{
									$contacts->items[$i]->Remove();
								}
							$campaign->SetCampaignType($_postvars['selectype']);
							$campaign = new TCampaign(intval($_getvars['id']));
							$m['wizardsprogress']=get_wizard_steps('selectype', $campaign->ID());
						}
					if (!empty($error_message))
						sm_set_action('selectype');
					else
						sm_redirect('index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['next_steps'].'&id='.$_getvars['id']);
				}

			if (sm_action('selectype'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');
					$m["module"] = sm_current_module();
					sm_title('Sequence Type');
					add_path('Drip', 'index.php?m=campaigns');
					add_path_current('Sequence Type');
					if(!empty($error_message))
						{
							$m['error_message']=$error_message;
						}

					$m['selected_type'] = $campaign->CampaignType();
					$m['page_title']='Sequence Type';
					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['action_url']='index.php?m='.sm_current_module().'&d=postselectype&id='.$_getvars['id'];
				}

			if(sm_action('campaigntitle'))
				{
					$m['module']='campaignwizard';
					sm_title('Title');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					add_path_current('Title');
					if(!empty($error_message))
						$m['error_message']=$error_message;

					$m['page_title']='Drip Wizard';

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['action_url']='index.php?m='.sm_current_module().'&d=postaddtitle&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'].(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):'');
					if(!empty($_getvars['id']))
						{
							$campaign=new TCampaign(intval($_getvars['id']));
							if (!$campaign->Exists() || $campaign->CompanyID()!=TCompany::CurrentCompany()->ID())
								exit('Access Denied');

							$m['campaign_title']=$campaign->Title();
						}
				}


			if (sm_action('postaddcontacts'))
				{

					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied');

					if ($campaign->TotalCustomersCount()==0)
						$error_message='Contact list is empty!';

					if (!empty($error_message))
						{
							sm_set_action('contacts');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id']);
							else
								sm_redirect('index.php?m=campaigns');
						}
				}

			if (sm_action('contacts'))
				{
					$m["module"] = 'campaignwizard';
					sm_title('Add Contacts');
					sm_use('ui.modal');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					if (!empty($error_message))
						$m['error_message'] = $error_message;

					$m['page_title']='Add Contacts';

					$m['choose_customers_label'] = 'Choose '.$currentcompany->LabelForCustomers();
					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['addtags_url'] = 'index.php?m='.sm_current_module().'&d=add_tags&id='.$_getvars['id'];
					$m['addcontacts_url'] = 'index.php?m='.sm_current_module().'&d=import&id='.$_getvars['id'];
					$m['selectontactlist_url'] = 'index.php?m='.sm_current_module().'&d=selectlist&id='.$_getvars['id'];

					$m['next_url'] = 'index.php?m='.sm_current_module().'&d=postaddcontacts&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];
				}


			if (sm_action('postaddcontact'))
				{
					$error_message='';
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied');

					if (!Validator::USPhone($_postvars['cellphone']) || empty($_postvars['cellphone']))
						{
							$error_message='Wrong cellphone number';
						}
					if (!empty($_postvars['email']) && !Validator::Email($_postvars['email']))
						{
							$error_message='Wrong email';
						}

					if(empty($error_message))
						{
							$customer_exist=0;
							if ( !empty($_postvars['cellphone']))
								{
									$customer = TCustomer::initWithCustomerPhoneNotDeleted($_postvars['cellphone'], TCompany::CurrentCompany());
									if($customer->Exists())
										{
											$customer_exist = 1;
										}
								}

							if ( !empty($_postvars['email']) && $customer_exist==0)
								{
									$customer = TCustomer::initWithCustomerEmailNotDeleted($_postvars['email'], TCompany::CurrentCompany());
									if($customer->Exists())
										{
											$customer_exist = 1;
										}
								}


							if($customer_exist==0)
								{
									$customer = TCustomer::CreateByStaff($currentcompany, $_postvars['first_name'], $_postvars['last_name'], Cleaner::USPhone($_postvars['cellphone']));
									$customer->SendInitialSMS();
									$customer->SetFirstName($_postvars['first_name']);
									$customer->SetLastName($_postvars['last_name']);
									$customer->SetCellPhone(Cleaner::USPhone($_postvars['cellphone']));
									$customer->SetEmail($_postvars['email']);
									$customer->SetLastUpdateTime();
								}

							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign->ID());
							$contacts->Load();

							for($i=0; $i<$contacts->Count(); $i++)
								{
									if ($contacts->items[$i]->Exists())
										$contacts->items[$i]->Remove();
								}

							$campaignitem = TCampaignItem::Create(TCompany::CurrentCompany(), $campaign);
							$campaignitem->SetFirstName($customer->FirstName());
							$campaignitem->SetLastName($customer->LastName());
							if($customer->HasEmail())
								$campaignitem->SetEmail($customer->Email());
							if($customer->HasCellphone())
								$campaignitem->SetPhone($customer->Cellphone());
							$campaignitem->SetPartner($customer->ID());

						}
					if (!empty($error_message))
						{
							sm_set_action('addcontact');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id']);
							else
								sm_redirect('index.php?m=campaigns');
						}
				}

			if (sm_action('addcontact'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied');

					$m["module"] = sm_current_module();
					sm_title('Add Contact');
					add_path_home();
					add_path('Run Sequence', 'index.php?m=runsequence');
					$m['page_title']='Add Contact';

					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/adminbuttons.php');

					$ui = new TInterface();

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());
					$m['action_url']='index.php?m='.sm_current_module().'&d=postaddcontact&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];

					$ui->html('<div class="stepssection">');
					$ui->html('<h2>Add Contact</h2>');
					$ui->AddTPL('pageswizard.tpl');
					$ui->html('</div>');

					$ui->html('<div class="addcustomer">');
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$f=new TForm($m['action_url'], '');
					$f->AddClassnameGlobal('hidesubmitbutton');

					$f->AddSeparator('contacts', 'Contacts');
					$f->AddText('first_name', 'First Name');
					$f->AddText('last_name', 'Last Name');
					$f->AddText('cellphone', 'Cellphone', true);
					$f->AddText('email', 'Email');
					if (sm_action('edit'))
						$f->LoadValuesArray($customer->info);
					if (is_array($_postvars))
						$f->LoadValuesArray($_postvars);

					sm_setfocus('first_name');

					$f->InsertHTML('
					        <div class="schedule_buttons bottom_buttons">
					        	<a href="'.$m['back_url'].'" class="pull-left backarrow">Back</a>
            					<input type="submit" value="Next" class="pull-right">
        					</div>
					');

					$ui->AddForm($f);
					$ui->html('</div>');
					$ui->Output(true);
				}

			if (sm_action('postadd_tags'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							$tags = new TTagList();
							$tags->SetFilterIDs($campaign->GetTagIDsArray());
							$tags->Load();
							for($i=0; $i<$tags->Count(); $i++)
								{
									$tags->items[$i]->UnsetCampaignID($campaign);
								}

							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign->ID());
							if(!empty($campaign->ContactListID()))
								{
									$contactlist = new TContactsList($campaign->ContactListID());
									if ( $contactlist->Exists() )
										{
											$contacts->SetFilterExcludeCustomerIDs($contactlist->GetCustomerIDsArray());
										}
								}
							$contacts->Load();

							for($i=0; $i<$contacts->Count(); $i++)
								{
									$contacts->items[$i]->Remove();
								}

							if (!empty($_postvars['tags_selected']))
								{
									$tags=$_postvars['tags_selected'];
									$tagsfilter = Array();
									for ($i = 0; $i < count($tags); $i++)
										{
											if (!empty($tags[$i]))
												{
													$tmpid=str_replace('tag_','', $tags[$i]);
													$tag = new TTag($tmpid);
													if($tag->Exists())
														{
															$tag->AddToCampaign($campaign);
															$tmp = $tag->GetCustomerIDsArray();
															$tagsfilter = array_merge($tagsfilter, $tmp);
														}
													$tagsfilter = array_merge($tagsfilter, $tmp);
												}
										}
									if (count($tagsfilter) > 0)
										{
											$tagsfilter = array_values(array_unique($tagsfilter));
											for($i=0; $i<count($tagsfilter); $i++)
												{
													$customer=new TCustomer($tagsfilter[$i]);
													if($customer->Exists() && !$customer->isDeleted())
														{
															$customer->AddToCampaign($campaign);
															$campaignitems = new TCampaignItemList();
															$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
															$campaignitems->SetFilterCampaign($campaign);
															$campaignitems->Load();
															$exist=0;
															for($n=0; $n<$campaignitems->Count(); $n++)
																{
																	if((!empty($customer->Cellphone()) && $customer->Cellphone() == $campaignitems->items[$n]->Phone()) || (!empty($customer->Email()) && $customer->Email() == $campaignitems->items[$n]->Email()))
																		{
																			$exist=1;
																			continue;
																		}

																}
															if($exist!=1)
																{
																	$campaignitem = TCampaignItem::Create(TCompany::CurrentCompany(), $campaign);
																	$campaignitem->SetFirstName($customer->FirstName());
																	$campaignitem->SetLastName($customer->LastName());
																	$campaignitem->SetCompany($customer->GetBusinessName());
																	if($customer->HasEmail())
																		$campaignitem->SetEmail($customer->Email());
																	if($customer->HasCellphone())
																		$campaignitem->SetPhone($customer->Cellphone());
																	$campaignitem->SetPartner($customer->ID());
																}
														}
												}
										}
								}

						}
					if (!empty($error_message))
						{
							sm_set_action('add_tags');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id']);
							else
								sm_redirect('index.php?m=campaigns');
						}
				}


			if (sm_action('add_tags'))
				{
					$m["module"] = 'campaignwizard';
					sm_title('Choose contacts by tag');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					$m['page_title']='Choose contacts by tag';
					if (is_array($_postvars))
						$data = $_postvars;
					$tags = new TTagList();
					$tags->Load();
					$tags_selected = $campaign->GetTagIDsArray();
					for ($i = 0; $i < $tags->Count(); $i++)
						{
							$m['tags'][$i]['title'] = $tags->items[$i]->Name();
							$m['tags'][$i]['value'] = 'tag_'.$tags->items[$i]->ID();

							if(count($tags_selected)>0)
								{
									for ( $j=0; $j < count( $tags_selected ); $j++ )
										{
											if($tags_selected[$j]==$tags->items[$i]->ID())
												$m['tags'][$i]['checked'] = '1';
										}
								}
						}

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['action_url']='index.php?m='.sm_current_module().'&d=postadd_tags&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];
				}

			if (sm_action('postselectlist'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							$tagsfilter=Array();
							$tags = new TTagList();
							$tags->SetFilterIDs($campaign->GetTagIDsArray());
							$tags->Load();
							for($i=0; $i<$tags->Count(); $i++)
								{
									$tmp=$tags->items[$i]->GetCustomerIDsArray();
									$tagsfilter=array_merge($tagsfilter, $tmp);
								}

							$contacts = new TCampaignItemList();
							$contacts->SetFilterCompany(TCompany::CurrentCompany());
							$contacts->SetFilterCampaign($campaign->ID());
							if (count($tagsfilter)>0)
								{
									$tagsfilter=array_values(array_unique($tagsfilter));
									$contacts->SetFilterExcludeCustomerIDs($tagsfilter);
								}
							$contacts->Load();

							for($i=0; $i<$contacts->Count(); $i++)
								{
									$contacts->items[$i]->Remove();
								}

							if (empty($_postvars['list']))
								$campaign->SetContactListID(0);
							else
								{
									$customers = new TCustomerList();
									$customers->SetFilterCompany($currentcompany);
									$customers->SetFilterEnabled();
									$listcustomer = new TContactsList($_postvars['list']);
									if ( $listcustomer->Exists() )
										{
											$customers->SetFilterIDs($listcustomer->GetCustomerIDsArray());
											$campaign->SetContactListID($listcustomer->ID());
										}
									$customers->Load();

									for($i=0; $i<$customers->Count(); $i++)
										{
											$customer = $customers->items[$i];
											if($customer->Exists() && !$customer->isDeleted())
												{
													$customer->AddToCampaign($campaign);
													$campaignitems = new TCampaignItemList();
													$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
													$campaignitems->SetFilterCampaign($campaign);
													$campaignitems->Load();
													$exist=0;
													for($n=0; $n<$campaignitems->Count(); $n++)
														{
															if((!empty($customer->Cellphone()) && $customer->Cellphone() == $campaignitems->items[$n]->Phone()) || (!empty($customer->Email()) && $customer->Email() == $campaignitems->items[$n]->Email()))
																{
																	$exist=1;
																	continue;
																}

														}
													if($exist!=1)
														{
															$campaignitem = TCampaignItem::Create(TCompany::CurrentCompany(), $campaign);
															$campaignitem->SetFirstName($customer->FirstName());
															$campaignitem->SetLastName($customer->LastName());
															$campaignitem->SetCompany($customer->GetBusinessName());
															if($customer->HasEmail())
																$campaignitem->SetEmail($customer->Email());
															if($customer->HasCellphone())
																$campaignitem->SetPhone($customer->Cellphone());
															$campaignitem->SetPartner($customer->ID());
														}
												}
										}

								}
						}
					if (!empty($error_message))
						{
							sm_set_action('selectlist');
						}
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id']);
							else
								sm_redirect('index.php?m=campaigns');
						}
				}

			if (sm_action('selectlist'))
				{
					$m["module"] = sm_current_module();
					sm_title('Select contacts list');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					$m['page_title']='Select contacts list';

					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/adminbuttons.php');

					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());
					$m['action_url']='index.php?m='.sm_current_module().'&d=postselectlist&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];
					$ui->html('<div class="stepssection">');
					$ui->html('<h2>Select contacts list</h2>');
					$ui->AddTPL('pageswizard.tpl');
					$ui->html('</div>');

					$ui->html('<div class="addcustomer">');

					$f=new TForm($m['action_url'], '');
					$f->AddClassnameGlobal('hidesubmitbutton');
					$contactlist=new TContactsLists();
					$contactlist->SetFilterCompany(TCompany::CurrentCompany());
					$contactlist->OrderByTitle(false);
					$contactlist->Load();

					$f->AddSelectVL('list', 'Contact List', $contactlist->ExtractIDsArray(), $contactlist->ExtractTitlesArray());
					$f->SetValue('list', $campaign->ContactListID());
					$f->SelectAddBeginVL('list', '', '----');

					$f->InsertHTML('
					        <div class="schedule_buttons bottom_buttons">
					        	<a href="'.$m['back_url'].'" class="pull-left backarrow">Back</a>
            					<input type="submit" value="Next" class="pull-right">
        					</div>
					');

					$ui->AddForm($f);
					$ui->html('</div>');
					$ui->Output(true);
				}


			if (sm_action('postimport'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							$filename=sm_upload_file();
							if (!file_exists($filename))
								{
									$error_message='Error uploading file';
								}
							else
								{
									$error_message=wizard_import_data($campaign, $filename, $_postvars['new_tags'], $_postvars['updatecontacts']);
								}
						}
					if (!empty($error_message))
						{
							if (!empty($filename) && file_exists($filename))
								unlink($filename);
							sm_set_action('import');
						}
					else
						sm_redirect('index.php?m='.sm_current_module().'&d=contacts&id='.intval($_getvars['id']));
				}

			if (sm_action('import'))
				{
					$m["module"] = 'campaignwizard';
					sm_title('Import Contacts');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					$m['page_title']='Choose contacts by tag';

					if (!empty($error_message))
						$m['error_message'] = $error_message;
					if (!empty($_postvars['updatecontacts']))
						$m['updatecontacts']=$_postvars['updatecontacts'];

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());
					$m['wizardsprogress']['current_step']=1;

					$m['action_url']='index.php?m='.sm_current_module().'&d=postimport&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];
				}

			if (sm_action('postaddsequence'))
				{
					$m["module"] = 'campaignwizard';
					if (empty($_postvars['email1_template']))
						{
							$error_message = 'Select email templates';
						}
					if (empty($error_message))
						{
							$timearray= Array();
							$i=0;
							foreach ($_postvars as $key => $val)
								{
									if (strpos($key, 'message_time_hrs_') !== false)
										{
											$id[$i]['hrs'] = intval(str_replace('message_time_hrs_','', $key));
											$hr[$i] = intval($val);
											if (empty($_postvars['message_time_hrs_'.$id[$i]['hrs']]) && empty($_postvars['message_time_min_'.$id[$i]['hrs']]) && empty($_postvars['message_time_'.$id[$i]['hrs']]))
												{
													$error_message = 'Select correct sequence time';
													break;
												}
										}

									if (strpos($key, 'message_template_') !== false)
										{
											if (empty($val))
												{
													$error_message = 'Select message sequence2';
													break;
												}
										}
									if (strpos($key, 'template_ctgs_') !== false)
										{
											if (empty($val) && $val != 0)
												{
													$error_message = 'Select message sequence1';
													break;
												}
										}
								}

						}

					if (empty($error_message))
						{
							$campaign = new TCampaign(intval($_getvars['id']));
							if ($campaign->Exists())
								{
									$campaign->SetEmailTemplateID(1, $_postvars['email1_template']);
									$tpl=new TEmailTemplate($campaign->GetEmailTemplateID(1));
									$campaign->SetEmailSubject(1, $tpl->Subject());
									$campaign->SetEmailMessage(1, $tpl->Message());
									unset($tpl);
									$sequencelist = new TCampaignSequenceList();
									$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
									$sequencelist->SetFilterCampaign($campaign->ID());
									$sequencelist->Load();
									for ($i = 0; $i < $sequencelist->Count(); $i++) {
										if (!empty($_postvars['message_template_'.$sequencelist->items[$i]->ID()]))
											{
												$sequenceitem= new TCampaignSequence($sequencelist->items[$i]->ID());
												if($sequencelist->items[$i]->GetMode()=='sms')
													{
														$smsmessage = new TMessageTemplate(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()]));
														if($smsmessage->Exists())
															{
																$sequencelist->items[$i]->SetText($smsmessage->Text());
																$sequencelist->items[$i]->SetIdAsset($smsmessage->AssetID());
															}
													}
												elseif($sequencelist->items[$i]->GetMode()=='voice')
													{
														$asset=new TAsset(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()]));
														if ($asset->Exists() || $asset->CompanyID()==TCompany::CurrentCompany()->ID() && $asset->isEligibleForVoiceMessages())
															{
																$sequencelist->items[$i]->SetIdAsset($asset->ID());
																$_postvars['voice_template'] = '1';
															}
													}
												elseif($sequencelist->items[$i]->GetMode()=='email')
													{
														$sequencelist->items[$i]->SetEmailTemplate(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()]));
														$tpl=new TEmailTemplate(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()]));
														if($tpl->Exists())
															{
																$sequencelist->items[$i]->SetEmailSubject($tpl->Subject());
																$sequencelist->items[$i]->SetEmailMessage($tpl->Message());
															}
														unset($tpl);
													}

												$sequence_time = 60 * ( (intval($_postvars['message_time_'.$sequencelist->items[$i]->ID()]) * 24 * 60 ) + (intval($_postvars['message_time_hrs_'.$sequencelist->items[$i]->ID()]) * 60 ) + intval( $_postvars['message_time_min_'.$sequencelist->items[$i]->ID()] ));
												if($i==0)
													$sequencelist->items[$i]->SetScheduledTimestamp(intval($sequence_time));
												else
													$sequencelist->items[$i]->SetScheduledTimestamp(intval($sequence_time) + $sequencelist->items[$i-1]->ScheduledTimestamp());
											}
									}
									if (!empty($_postvars['voice_template']) && is_object($asset))
										$campaign->SetVoiceMessageAsset($asset->ID());
									else
										$campaign->UnsetVoiceMessageAsset();
									unset($tpl);
								}

							if (!empty($_getvars['list']))
								{
									$listcustomer = new TContactsList($_getvars['list']);
									if ( $listcustomer->Exists() )
										{
											$customers = new TCustomerList();
											$customers->SetFilterCompany(TCompany::CurrentCompany());
											$customers->SetFilterIDs($listcustomer->GetCustomerIDsArray());
											$customers->SetFilterEnabled();
											$customers->Load();

											for($i=0; $i<$customers->Count(); $i++)
												{
													$customer = $customers->items[$i];

													if( $customer->Exists() && !$customer->isDeleted() )
														{
															$customer->AddToCampaign($campaign);
															$campaignitems = new TCampaignItemList();
															$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
															$campaignitems->SetFilterCampaign($campaign);
															$campaignitems->Load();
															$exist=0;
															for($n=0; $n<$campaignitems->Count(); $n++)
																{
																	if((!empty($customer->Cellphone()) && $customer->Cellphone() == $campaignitems->items[$n]->Phone()) || (!empty($customer->Email()) && $customer->Email() == $campaignitems->items[$n]->Email()))
																		{
																			$exist=1;
																			continue;
																		}
																}
															if($exist!=1)
																{
																	$campaignitem = TCampaignItem::Create(TCompany::CurrentCompany(), $campaign);
																	$campaignitem->SetFirstName($customer->FirstName());
																	$campaignitem->SetLastName($customer->LastName());
																	$campaignitem->SetCompany($customer->GetBusinessName());
																	if($customer->HasEmail())
																		$campaignitem->SetEmail($customer->Email());
																	if($customer->HasCellphone())
																		$campaignitem->SetPhone($customer->Cellphone());
																	$campaignitem->SetPartner($customer->ID());
																}
														}
												}
										}
								}
						}

					if (!empty($error_message))
						sm_set_action('sequence');
					else
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id'].'&action=create'.(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):''));
							else
								sm_redirect('index.php?m=campaigns&d=runsequence'.(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):''));
						}
				}


			if (sm_action('removesequence'))
				{
					/**@param TCampaignSequence $sequenceitem */

					$m["module"] = 'campaignwizard';
					$special['main_tpl'] = 'theonepage';
					$special['no_blocks'] = true;
					$special['no_borders_main_block'] = true;
					$sequenceitem = new TCampaignSequence(intval($_getvars['id']));
					if($sequenceitem->Exists() && TCompany::CurrentCompany()->ID()==$sequenceitem->CompanyID())
						{
							$sequenceitem->Remove();
						}
				}

			if (sm_action('addsequence'))
				{
					/**@param TCampaignSequence $sequenceitem */
					$m["module"] = 'campaignwizard';
					$special['main_tpl'] = 'theonepage';
					$special['no_blocks'] = true;
					$special['no_borders_main_block'] = true;
					if($_getvars['mode']=='sms')
						$m['sequence_mode']='sms';
					elseif($_getvars['mode']=='voice')
						$m['sequence_mode']='voice';
					else
						$m['sequence_mode']='email';

					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							$sequencelist= new TCampaignSequenceList();
							$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
							$sequencelist->SetFilterCampaign($campaign->ID());
							$m['sequence_count']=$sequencelist->TotalCount()+2;
							if($sequencelist->TotalCount() < $m['campaign_duration']-1)
								{
									$templatectgs = new TTemplateCategoriesList();
									$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
									$templatectgs->Load();

									$sequenceitem = TCampaignSequence::Create(TCompany::CurrentCompany(), $campaign);
									$sequenceitem->SetMode($m['sequence_mode']);
									$m['message_template_id']=$sequenceitem->ID();
									for($i=0; $i<=$m['campaign_duration']; $i++)
										$m['sequenceitems_days'][$i]=$i;

									$m['hr'] = Array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

									$tmp=range(0, 59);
									for ($l = 0; $l < count($tmp); $l++)
										if ($tmp[$l]<10)
											$tmp[$l]='0'.$tmp[$l];

									$m['min']['val'] = range(0, 59);
									$m['min']['titles'] = $tmp;

									if($sequenceitem->GetMode()=='sms')
										{
											$templates_ctg = array();
											$r = 0;
											for ($j=0; $j<$templatectgs->Count(); $j++)
												{
													$templatectg = $templatectgs->items[$j];

													$messages = new TMessageTemplateList();
													$messages->SetFilterCompany(TCompany::CurrentCompany());
													$messages->SetFilterCategory($templatectg->ID());
													$messages->Load();

													if ( $templatectg->TextTemplatesCount()>0 )
														{
															$templates_ctg[$r]['id'] = $templatectg->ID();
															$templates_ctg[$r]['title'] = $templatectg->Title();

															$templates[$r]['ctg_id'] = $templatectg->ID();
															$templates[$r]['message_ids'] = $messages->ExtractIDsArray();
															$titles = $messages->ExtractTitlesArray();
															for($k=0; $k<count($titles); $k++)
																{
																	$clean_titles[] = str_replace('%', '%25', $titles[$k]);
																}
															$templates[$r]['message_titles'] = $clean_titles;

															$r++;
															unset($clean_titles);
															unset($messages);
														}
												}
											$m['sequenceitems']['templates'] = htmlescape(json_encode($templates));
											$m['sequenceitems']['categories'] = $templates_ctg;
										}
									elseif($sequenceitem->GetMode()=='voice')
										{
											$assets=new TAssetList();
											$assets->SetFilterCompany(TCompany::CurrentCompany());
											$assets->Load();
											$m['text_template']=Array();
											for ($i = 0; $i < $assets->Count(); $i++)
												{
													if (!$assets->items[$i]->isEligibleForVoiceMessages())
														continue;
													$m['message_template'][]=Array(
														'title'=>$assets->items[$i]->FileNameWithComment(),
														'id'=>$assets->items[$i]->ID(),
													);
												}
										}
									else
										{
											$templates_ctg = array();
											$r = 0;
											for ($j=0; $j<$templatectgs->Count(); $j++)
												{
													$templatectg = $templatectgs->items[$j];

													$messages = new TEmailTemplateList();
													$messages->SetFilterCompany(TCompany::CurrentCompany());
													$messages->SetFilterCategory($templatectg->ID());
													$messages->Load();

													if ( $templatectg->EmailTemplatesCount()>0 )
														{
															$templates_ctg[$r]['id'] = $templatectg->ID();
															$templates_ctg[$r]['title'] = $templatectg->Title();

															$templates[$r]['ctg_id'] = $templatectg->ID();
															$templates[$r]['message_ids'] = $messages->ExtractIDsArray();
															$titles = $messages->ExtractTitlesArray();
															for($k=0; $k<count($titles); $k++)
																{
																	$clean_titles[] = str_replace('%', '%25', $titles[$k]);
																}
															$templates[$r]['message_titles'] = $clean_titles;

															$r++;
															unset($clean_titles);
															unset($messages);
														}
												}
											$m['sequenceitems']['templates'] = htmlescape(json_encode($templates));
											$m['sequenceitems']['categories'] = $templates_ctg;

										}
									$m['load_more_code_email']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=email&id=".$campaign->ID()."')";
									$m['load_more_code_sms']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=sms&id=".$campaign->ID()."')";
									$assets_available=new TAssetList();
									$assets_available->SetFilterCompany(TCompany::CurrentCompany());
									$assets_available->SetFilterAudio();
									if ($assets_available->TotalCount() > 0)
										$m['load_more_code_voice']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=voice&id=".$_getvars['id']."')";
									$m['remove_sequence_step']="remove_sequence('index.php?m=".sm_current_module()."&d=removesequence&id=".$sequenceitem->ID()."', 'message_row_".$sequenceitem->ID()."')";
								}
						}
				}

			if (sm_action('sequence'))
				{
					$m["module"] = 'campaignwizard';
					sm_title('Sequence');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					$m['page_title']='Sequence';
					sm_add_jsfile('sequence.js');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['action_url']='index.php?m='.sm_current_module().'&d=postaddsequence&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'].'&action=create'.(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'].'&action=create'.(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):'');

					$m['load_more_code_email']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=email&id=".$_getvars['id']."')";
					$m['load_more_code_sms']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=sms&id=".$_getvars['id']."')";

					$assets_available=new TAssetList();
					$assets_available->SetFilterCompany(TCompany::CurrentCompany());
					$assets_available->SetFilterAudio();
					if ($assets_available->TotalCount() > 0)
						$m['load_more_code_voice']="loader_sequence('index.php?m=".sm_current_module()."&d=addsequence&mode=voice&id=".$_getvars['id']."')";

					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							$m['data']['email1_template'] = $campaign->GetEmailTemplateID(1);
							$sequencelist = new TCampaignSequenceList();
							$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
							$sequencelist->SetFilterCampaign($campaign->ID());
							$sequencelist->Load();

							$m['sequencelist_count']=$sequencelist->TotalCount();

							$texts=new TMessageTemplateList();
							$texts->SetFilterCompany(TCompany::CurrentCompany());
							$texts->OrderByTitle();
							$texts->Load();

							$assets=new TAssetList();
							$assets->SetFilterCompany(TCompany::CurrentCompany());
							$assets->Load();
							$assets->Load();

							$emails=new TEmailTemplateList();
							$emails->SetFilterCompany(TCompany::CurrentCompany());
							$emails->OrderByTitle();
							$emails->Load();

							$templatectgs = new TTemplateCategoriesList();
							$templatectgs->SetFilterCompanyAndDefault(TCompany::CurrentCompany());
							$templatectgs->Load();

							for ($i = 0; $i < $sequencelist->Count(); $i++)
								{
									$m['sequenceitems'][$i]['mode']=$sequencelist->items[$i]->GetMode();
									$m['sequenceitems'][$i]['id']=$sequencelist->items[$i]->ID();
									$m['sequenceitems'][$i]['remove_sequence_step']="remove_sequence('index.php?m=".sm_current_module()."&d=removesequence&id=".$sequencelist->items[$i]->ID()."', 'message_row_".$sequencelist->items[$i]->ID()."')";


									$m['sequenceitems'][$i]['hr'] = Array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

									$tmp=range(0, 59);
									for ($l = 0; $l < count($tmp); $l++)
										if ($tmp[$l]<10)
											$tmp[$l]='0'.$tmp[$l];

									if ($i==0)
										$sequence_time = $sequencelist->items[$i]->ScheduledTimestamp();
									else
										$sequence_time = $sequencelist->items[$i]->ScheduledTimestamp() - $sequencelist->items[$i-1]->ScheduledTimestamp();


									if(!empty($_postvars['message_time_'.$sequencelist->items[$i]->ID()]))
										$m['sequenceitems'][$i]['day_selected'] = $_postvars['message_time_'.$sequencelist->items[$i]->ID()];
									else
										$m['sequenceitems'][$i]['day_selected'] = ( floor($sequence_time/(24*60*60) ) );

									if(!empty($_postvars['message_time_hrs_'.$sequencelist->items[$i]->ID()]))
										$m['sequenceitems'][$i]['hrs_selected'] = $_postvars['message_time_hrs_'.$sequencelist->items[$i]->ID()];
									else
										$m['sequenceitems'][$i]['hrs_selected'] = floor(($sequence_time - $m['sequenceitems'][$i]['day_selected']*24*3600)/3600);

									if(!empty($_postvars['message_time_min_'.$sequencelist->items[$i]->ID()]))
										$m['sequenceitems'][$i]['min_selected'] = $_postvars['message_time_min_'.$sequencelist->items[$i]->ID()];
									else
										$m['sequenceitems'][$i]['min_selected'] = ( ($sequence_time/60) - ( ($m['sequenceitems'][$i]['day_selected']*24 + $m['sequenceitems'][$i]['hrs_selected'])*60));


									$m['sequenceitems'][$i]['min']['val'] = range(0, 59);
									$m['sequenceitems'][$i]['min']['titles'] = $tmp;


									for($k=0; $k<=$m['campaign_duration']; $k++)
										{
											$m['sequenceitems'][$i]['days'][$k]=Array(
												'id'=>$k,
												'selected'=>!empty(intval($_postvars['message_time_'.$sequencelist->items[$i]->ID()]))?intval($_postvars['message_time_'.$sequencelist->items[$i]->ID()]):round($sequencelist->items[$i]->ScheduledTimestamp() / (60 * 60 * 24))
											);
										}
									if($m['sequenceitems'][$i]['mode']=='sms')
										{
											$templates_ctg = array();
											$r = 0;
											for ($j=0; $j<$templatectgs->Count(); $j++)
												{
													$templatectg = $templatectgs->items[$j];

													$messages = new TMessageTemplateList();
													$messages->SetFilterCompany(TCompany::CurrentCompany());
													$messages->SetFilterCategory($templatectg->ID());
													$messages->Load();

													if ( $templatectg->TextTemplatesCount()>0 )
														{
															$templates_ctg[$r]['id'] = $templatectg->ID();
															$templates_ctg[$r]['title'] = $templatectg->Title();

															$templates[$r]['ctg_id'] = $templatectg->ID();
															$templates[$r]['message_ids'] = $messages->ExtractIDsArray();
															$titles = $messages->ExtractTitlesArray();
															for($k=0; $k<count($titles); $k++)
																{
																	$clean_titles[] = str_replace('%', '%25', $titles[$k]);
																}
															$templates[$r]['message_titles'] = $clean_titles;

															$r++;
															unset($clean_titles);
															unset($messages);
														}
												}
											$m['sequenceitems'][$i]['templates'] = htmlescape(json_encode($templates));
											$m['sequenceitems'][$i]['categories'] = $templates_ctg;

											$has_selected_ctg=0;
											for ($j = 0; $j < $texts->Count(); $j++)
												{
													if(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0 && intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])==$texts->items[$j]->ID())
														{
															$m['sequenceitems'][$i]['categories_selected'] = $texts->items[$j]->CategoryID();
															$has_selected_ctg=$texts->items[$j]->CategoryID();
														}
													elseif($sequencelist->items[$i]->GetText()==$texts->items[$j]->Text())
														{
															$m['sequenceitems'][$i]['categories_selected'] = $texts->items[$j]->CategoryID();
															$has_selected_ctg=$texts->items[$j]->CategoryID();
														}

												}

											$categorytexts=new TMessageTemplateList();
											$categorytexts->SetFilterCompany(TCompany::CurrentCompany());
											$categorytexts->OrderByTitle();
											if(!(empty($has_selected_ctg)))
												{
													$categorytexts->SetFilterCategory($has_selected_ctg);
												}
											$categorytexts->Load();
											for ($j = 0; $j < $categorytexts->Count(); $j++)
												{

													$selected_text='';
													if(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0)
														{
															$selected_text_message = new TMessageTemplate($_postvars['message_template_'.$sequencelist->items[$i]->ID()]);
															if ( $selected_text_message->Exists() )
																$selected_text = $selected_text_message->Text();
														}

													$m['sequenceitems'][$i]['messagetemplates'][$j]=Array(
														'title'=>$categorytexts->items[$j]->Title(),
														'id'=>$categorytexts->items[$j]->ID(),
														'selected'=>
															$categorytexts->items[$j]->ID()==(
																intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0?strcmp($selected_text, $categorytexts->items[$j]->Text())==0:strcmp($sequencelist->items[$i]->GetText(), $categorytexts->items[$j]->Text())==0
															),
													);
												}

										}
									elseif($m['sequenceitems'][$i]['mode']=='voice')
										{
											$m['sequenceitems'][$i]['messagetemplates']=Array();
											for ($j = 0; $j < $assets->Count(); $j++)
												{
													if (!$assets->items[$j]->isEligibleForVoiceMessages())
														continue;
													$m['sequenceitems'][$i]['messagetemplates'][]=Array(
														'title'=>$assets->items[$j]->FileNameWithComment(),
														'id'=>$assets->items[$j]->ID(),
														'selected'=>$assets->items[$j]->ID()==(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0?intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])==$assets->items[$j]->ID():$sequencelist->items[$i]->IdAsset()==$assets->items[$j]->ID()),
													);
												}
										}
									else
										{
											$templates_ctg = array();
											$r = 0;
											for ($j=0; $j<$templatectgs->Count(); $j++)
												{
													$templatectg = $templatectgs->items[$j];

													$messages = new TEmailTemplateList();
													$messages->SetFilterCompany(TCompany::CurrentCompany());
													$messages->SetFilterCategory($templatectg->ID());
													$messages->Load();

													if ( $templatectg->EmailTemplatesCount()>0 )
														{
															$templates_ctg[$r]['id'] = $templatectg->ID();
															$templates_ctg[$r]['title'] = $templatectg->Title();

															$templates[$r]['ctg_id'] = $templatectg->ID();
															$templates[$r]['message_ids'] = $messages->ExtractIDsArray();
															$titles = $messages->ExtractTitlesArray();
															for($k=0; $k<count($titles); $k++)
																{
																	$clean_titles[] = str_replace('%', '%25', $titles[$k]);
																}
															$templates[$r]['message_titles'] = $clean_titles;

															$r++;
															unset($clean_titles);
															unset($messages);
														}
												}
											$m['sequenceitems'][$i]['templates'] = htmlescape(json_encode($templates));
											$m['sequenceitems'][$i]['categories'] = $templates_ctg;
											$has_selected_ctg = 0;
											for ($j = 0; $j < $emails->Count(); $j++)
												{
													if(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0 && intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])==$emails->items[$j]->ID())
														{
															$has_selected_ctg=$emails->items[$j]->CategoryID();
															$m['sequenceitems'][$i]['categories_selected'] = $emails->items[$j]->CategoryID();
														}
													elseif($sequencelist->items[$i]->EmailTemplate()==$emails->items[$j]->ID())
														{
															$has_selected_ctg=$emails->items[$j]->CategoryID();
															$m['sequenceitems'][$i]['categories_selected'] = $emails->items[$j]->CategoryID();
														}



												}

											$categoryemails=new TEmailTemplateList();
											$categoryemails->SetFilterCompany(TCompany::CurrentCompany());
											if(!(empty($has_selected_ctg)))
												{
													$categoryemails->SetFilterCategory($has_selected_ctg);
												}
											$categoryemails->OrderByTitle();
											$categoryemails->Load();

											for ($j = 0; $j < $categoryemails->Count(); $j++)
												{

													$m['sequenceitems'][$i]['messagetemplates'][$j]=Array(
														'title'=>$categoryemails->items[$j]->Title(),
														'id'=>$categoryemails->items[$j]->ID(),
														'selected'=>$categoryemails->items[$j]->ID()==(intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])>0?intval($_postvars['message_template_'.$sequencelist->items[$i]->ID()])==$categoryemails->items[$j]->ID():$sequencelist->items[$i]->EmailTemplate()==$categoryemails->items[$j]->ID()),
													);
												}
											unset($categoryemails);
										}

								}
						}


					$templates_ctg = array();
					$r = 0;
					for ($j=0; $j<$templatectgs->Count(); $j++)
						{
							$templatectg = $templatectgs->items[$j];

							$messages = new TEmailTemplateList();
							$messages->SetFilterCompany(TCompany::CurrentCompany());
							$messages->SetFilterCategory($templatectg->ID());
							$messages->Load();

							if ( $templatectg->EmailTemplatesCount()>0 )
								{
									$templates_ctg[$r]['id'] = $templatectg->ID();
									$templates_ctg[$r]['title'] = $templatectg->Title();

									$templates[$r]['ctg_id'] = $templatectg->ID();
									$templates[$r]['message_ids'] = $messages->ExtractIDsArray();

									$titles = $messages->ExtractTitlesArray();
									for($k=0; $k<count($titles); $k++)
										{
											$clean_titles[] = str_replace('%', '%25', $titles[$k]);
										}
									$templates[$r]['message_titles'] = $clean_titles;

									$r++;
									unset($clean_titles);
									unset($messages);
								}
						}
					$m['emailtemplates']['templates'] = htmlescape(json_encode($templates));
					$m['emailtemplates']['categories'] = $templates_ctg;
					$has_selected_ctg = 0;

					for ($j = 0; $j < $emails->Count(); $j++)
						{
							if(intval($_postvars['email1_template'])>0 && intval($_postvars['email1_template'])==$emails->items[$j]->ID())
								{
									$has_selected_ctg=$emails->items[$j]->CategoryID();
									$m['emailtemplates']['categories_selected'] = $emails->items[$j]->CategoryID();
								}
							elseif($campaign->GetEmailTemplateID(1)==$emails->items[$j]->ID())
								{
									$has_selected_ctg=$emails->items[$j]->CategoryID();
									$m['emailtemplates']['categories_selected'] = $emails->items[$j]->CategoryID();
								}

						}
					$categoryemails = new TEmailTemplateList();
					$categoryemails->SetFilterCompany(TCompany::CurrentCompany());
					if(!(empty($has_selected_ctg)))
						{
							$categoryemails->SetFilterCategory($has_selected_ctg);
						}
					$categoryemails->OrderByTitle();
					$categoryemails->Load();
					for ($j = 0; $j < $categoryemails->Count(); $j++)
						{

							$m['emailtemplates']['messagetemplates'][$j]=Array(
								'title'=>$categoryemails->items[$j]->Title(),
								'id'=>$categoryemails->items[$j]->ID(),
								'selected'=>$categoryemails->items[$j]->ID()==(intval($_postvars['email1_template'])>0?intval($_postvars['email1_template'])==$categoryemails->items[$j]->ID():$campaign->GetEmailTemplateID(1)==$categoryemails->items[$j]->ID()),
							);
						}
					unset($categoryemails);
				}

			if (sm_action('postschedule'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if ($campaign->Exists())
						{
							if ($_getvars['startnow']=='yes')
								{
									$campaign->SetStarttime(time());
									$starttime = time();
								}
							elseif($_getvars['startnow']=='no')
								{
									$time=$_postvars['scheduledate'].' '.$_postvars['hrs'].':'.$_postvars['min'];
									if(empty($_postvars['scheduledate']))
										{
											$error_message='Wrong Date';
											$redirectto='scheduletime';
										}
									$campaign->SetStarttime(strtotime($time));
									$starttime = strtotime($time);
								}
							$campaign->SetStatus('scheduled');
							if (empty($error_message))
								{
									$campaignitems = new TCampaignItemList();
									$campaignitems->SetFilterCompany(TCompany::CurrentCompany());
									$campaignitems->SetFilterCampaign($campaign);
									$campaignitems->Load();
									for( $i = 0; $i < $campaignitems->Count(); $i++ )
										{
											$customer = new TCustomer($campaignitems->items[$i]->PartnerID());
											if ( $customer->Exists() && $customer->CompanyID() == TCompany::CurrentCompany()->ID() )
												{
													$customer->StartCampaignAction($campaign->ID(), $starttime);
												}

											$sequencelist = new TCampaignSequenceList();
											$sequencelist->SetFilterCompany(TCompany::CurrentCompany());
											$sequencelist->SetFilterCampaign($campaign->ID());
											$sequencelist->Load();
											for ( $j = 0; $j < $sequencelist->Count(); $j++ )
												{
													$sequenceitem = TCampaignSequence::UsingCache($sequencelist->items[$j]->ID());
													$tmpschedule = new TCampaignScheduleList();
													$tmpschedule->SetFilterCampaign($campaign->ID());
													$tmpschedule->SetFilterCustomer($campaignitems->items[$i]->ID());
													$tmpschedule->SetFilterSequence($sequenceitem->ID());
													$tmpschedule->Limit(1);
													$tmpschedule->Load();

													if($tmpschedule->Count()==0)
														{
															$schedule = TCampaignSchedule::Create($campaign->CompanyID(), $campaign->ID());
															$schedule->SetCustomerID($campaignitems->items[$i]->ID());
															$schedule->SetSequenceID($sequenceitem->ID());
															$schedule->SetScheduledTimestamp($starttime + $sequenceitem->ScheduledTimestamp() );
															$schedule ->SetStatus('scheduled');
														}
												}
										}
								}
						}
					if (!empty($error_message))
						{
							if($redirectto=='scheduletime')
								sm_set_action('scheduletime');
							else
								sm_set_action('schedule');
						}
					else
						{
							sm_redirect('index.php?m='.sm_current_module().'&d=finish&id='.intval($_getvars['id']));
						}
				}

			if (sm_action('deletecontacts'))
				{
					$m['module'] = sm_current_module();
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');

					if(is_array($_postvars['ids']) && count($_postvars['ids'])>0)
						{
							for ($i=0; $i<count($_postvars['ids']); $i++)
								{
									$contact = new TCampaignItem($_postvars['ids'][$i]);
									if ($contact->Exists())
										$contact->Remove();
								}

						}
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('confirmlist'))
				{
					$m['module'] = sm_current_module();
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');

					$contactlist=new TCampaignItemList();
					$contactlist->SetFilterCompany(TCompany::CurrentCompany());
					$contactlist->SetFilterCampaign($campaign);
					if ( $contactlist->TotalCount()>0 )
						{
							if(!empty($_getvars['nextstep']))
								sm_redirect('index.php?m='.sm_current_module().'&d='.$_getvars['nextstep'].'&id='.$_getvars['id']);
							else
								sm_redirect('index.php?m=campaigns');
						}
					else
						{
							$error_message='Contact list is empty!';
						}
					if (!empty($error_message))
						{
							sm_set_action('listdetails');
						}
				}

			if (sm_action('postdeletecontact'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');
					$contact = new TCampaignItem($_getvars['contactid']);
					if (!$contact->Exists())
						exit('Access Denied');
					$contact->Remove();
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('listdetails'))
				{
					$campaign = new TCampaign(intval($_getvars['id']));
					if (!$campaign->Exists())
						exit('Access Denied!');

					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/admintable.php');
					include_once('includes/adminbuttons.php');
					sm_add_jsfile('customers.js');
					$listcustomer = new TContactsList($_getvars['id']);

					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					sm_title('Contacts List');

					$offset=abs(intval($_getvars['from']));
					$limit=intval($_getvars['ipp']);
					if ($limit < 30)
						$limit = 30;

					$contactlist = new TCampaignItemList();
					$contactlist->SetFilterCompany(TCompany::CurrentCompany());
					$contactlist->SetFilterCampaign($campaign);
					$contactlist->Load();
					$ui = new TInterface();

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['next_url']='index.php?m='.sm_current_module().'&d=confirmlist&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'].(!empty($_getvars['action'])?'&action='.$_getvars['action']:'').(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):'');
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'].(!empty($_getvars['list'])?'&list='.intval($_getvars['list']):'').(!empty($_getvars['action'])?'&action='.$_getvars['action']:'');

					$ui->html('<div class="stepssection"><h2>Contacts List</h2>');
					$ui->AddTPL('pageswizard.tpl');
					$ui->html('</div>');

					$b=new TButtons();
					$ui->div_open( '', 'contactslist');

					$b->AddButton('smsblast', 'Bulk Delete');
					$b->Style('smsblast', 'float:right; display:none;');
					$b->AddClassname('smsblast', 'smsblast');
					$b->OnClick("$('#smsblastform').submit();", 'smsblast');
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$ui->AddButtons($b);

					$ui->html('<form action="index.php?m='.sm_current_module().'&d=deletecontacts&id='.$campaign->ID().'&returnto='.urlencode(sm_this_url()).'" id="smsblastform" method="post">');
					for ($i = 0; $i < $contactlist->Count(); $i++)
						{
							$data['currenmode'] = 'contactlist';
							$contact = $contactlist->items[$i];
							$data['id'] = $contact->ID();

							$data['initials'] = $contact->Initials();
							$data['name'] = $contact->Name();
							$data['boxes'] = Array();

							if ($contact->HasPhone())
								$data['cellphone'] = Formatter::USPhone($contact->Phone());
							if ($contact->HasEmail())
								$data['email'] = $contact->Email();

							$data['delete'] = 'index.php?m='.sm_current_module().'&d=postdeletecontact&contactid='.$contactlist->items[$i]->ID().'&id='.$campaign->ID().'&returnto='.urlencode(sm_this_url());

							$ui->AddTPL('customerslist.tpl', '', $data);
							unset($contact);
							unset($data);
						}
					if ($contactlist->Count() == 0)
						{
							$data['noinfo'] = 'Nothing Found';
							$ui->AddTPL('customerslist.tpl', '', $data);
						}

					$ui->html('</form>');
					$ui->javascript("\$('.at-bulk-checkbox').change(function(){checksmsblast()});");
					$ui->AddPagebarParams($contactlist->TotalCount(), $limit, $offset);
					$ui->html('<div class="schedule_buttons">
            							<a href="'.$m['back_url'].'" class="pull-left backarrow">Back</a>
            							<a href="'.$m['next_url'].'" class="startnow pull-right">Next</a>
        							</div>');
					$ui->div_close();
					$ui->Output(true);
				}

			if (sm_action('schedule'))
				{
					$m["module"] = 'campaignwizard';
					sm_title('Schedule Your Campaign');
					sm_use('ui.modal');
					add_path_home();
					add_path('Drip', 'index.php?m=campaigns');
					$m['page_title']='Schedule Your Campaign';

					if (!empty($error_message))
						$m['error_message'] = $error_message;

					$m['wizardsprogress']=get_wizard_steps(sm_current_action());

					$m['next_url'] = 'index.php?m='.sm_current_module().'&d=postschedule&id='.$_getvars['id'].'&startnow=yes&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['schedule_url'] = 'index.php?m='.sm_current_module().'&d=scheduletime&id='.$_getvars['id'].'&nextstep='.$m['wizardsprogress']['next_steps'];
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d='.$m['wizardsprogress']['previous_steps'].'&id='.$_getvars['id'];
				}

			if (sm_action('scheduletime'))
				{
					include_once('includes/admininterface.php');
					include_once('includes/adminform.php');
					include_once('includes/adminbuttons.php');
					include_once('includes/admintable.php');
					$m["module"] = 'campaignwizard';
					use_api('smdatetime');
					sm_use('ui.modal');
					sm_add_jsfile('ext/datepicker/js/bootstrap-datepicker.js', true);
					sm_add_cssfile('ext/datepicker/css/datepicker.css', true);
					add_path_home();
					add_path('Campaign', 'index.php?m=customerwizard&d=fourthstep&id='.intval($_getvars['id']));
					sm_title('Schedule Campaign');
					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$f=new TForm('index.php?m='.sm_current_module().'&d=postschedule&startnow=no&id='.$_getvars['id'], '');
					$f->AddClassnameGlobal('scheduleform');
					$f->AddText('scheduledate', 'Date');
					$f->SetValue('scheduledate', date('m/d/Y', time()));
					$f->Calendar();
					$f->MergeColumns();
					for($i=0; $i<24; $i++){
						if($i<12){
							if($i==0){
								$hrs[]='12 am';
								$hrs_val[]=$i;
							}
							else{
								$hrs[]=$i.' am';
								$hrs_val[]=$i;
							}
						}
						else{
							if($i==12){
								$hrs[]='12 pm';
								$hrs_val[]=$i;
							}
							else{
								$hrs[]=($i-12).' pm';
								$hrs_val[]=$i;
							}
						}
					}

					$f->AddSelectVL('hrs', 'Hours:', $hrs_val, $hrs)
						->WithValue(intval(date('H')));
					$f->MergeColumns();
					for($i=0; $i<60; $i+=5){
						if($i<10)
							$mins[]='0'.$i;
						else
							$mins[]=$i;
					}
					$f->AddSelectVL('min', 'Minutes:', $mins, $mins);
					$f->MergeColumns();
					$f->SaveButton('Schedule');
					$ui->style('#hrs, #min {width:100px;}');
					$ui->AddForm($f);
					$ui->Output(true);
				}
			if (sm_action('finish'))
				{
					sm_use('ui.interface');
					sm_title('Drip Wizard');
					$ui = new TInterface();
					$ui->p('The campaign is started');
					$ui->Output(true);
				}

			if (sm_action('updated'))
				{
					sm_use('ui.interface');
					sm_title('Campaign Updated');
					$ui = new TInterface();
					$ui->p('The campaign is updated');
					$ui->Output(true);
				}
			if (sm_action('downloadcsv'))
				{
					$m["module"] = 'customerimport';
					$special['main_tpl'] = '';
					$special['no_blocks'] = true;
					header('Content-Type: text/csv');
					header('Content-Disposition: attachment; filename=example.csv');
					$fp = fopen('ext/csv/examplewizard.csv', 'rb');
					fpassthru($fp);
					fclose($fp);
					exit;
				}
		}
	else
		sm_redirect('index.php?m=account');
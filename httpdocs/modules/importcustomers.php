<?php

	sm_default_action('list');

	if (!function_exists('import_customers'))
		{
			/**
			 * @param TCampaign $campaign
			 * @param string $filename
			 * @return string
			 */
			function set_import_customers($filename, $fields_order, $contactlist, $new_tags=Array(), $status='')
				{
					$error_message = '';
					if (!file_exists($filename))
						{
							$error_message = 'File not found';
						}
					else
						{
							$i = 0;
							$fh = fopen($filename, 'r');

							$fields_order_array = unserialize($fields_order);

							while ($data = fgetcsv($fh))
								{
									if ($i > 0)
										{
											if (!empty($data[$fields_order_array['email']]) && !Validator::Email($data[$fields_order_array['email']]))
												{
													$error_message = 'Email in line '.$i.' is not valid. Please check your data';
													break;
												}
										}
									$i++;
								}
							fclose($fh);
							if (empty($error_message) && $i > 5000)
								$error_message = 'The file is too large. 5000 records per file allowed';
						}

					if (empty($error_message))
						{
							$import_id = TImportCustomers::Create($filename, $fields_order, $new_tags, $contactlist, TCompany::CurrentCompany()->ID());
							$m['statusarray'] = ['received', 'contact', 'appointment', 'sold', 'lost'];

							if(in_array($status, $m['statusarray']))
								$import_id->SetStaus($status);
							else
								$import_id->SetStaus('received');

							return $import_id->ID();
						}
					else
						return $error_message;
				}

			function import_customers($filename, $fields_order, $contactlist, $new_tags=Array())
				{
					$error_message = '';
					if (!file_exists($filename))
						{
							$error_message = 'File not found';
						}
					else
						{
							$i = 0;
							$fh = fopen($filename, 'r');

							while ($data = fgetcsv($fh))
								{
									if ($i > 0)
										{
											if (!empty($data[$fields_order['email']]) && !Validator::Email($data[$fields_order['email']]))
												{
													$error_message = 'Email in line '.$i.' is not valid. Please check your data';
													break;
												}
										}
									$i++;
								}
							fclose($fh);
							if (empty($error_message) && $i > 5000)
								$error_message = 'The file is too large. 5000 records per file allowed';
						}

					if (empty($error_message))
						{
							$i = 0;
							$fh = fopen($filename, 'r');
							$tags=new TTagList();
							$tags->Load();
							$list = new TContactsList($contactlist);
							if( !$list->Exists() )
								exit('Access Denied!');

							while ($data=fgetcsv($fh))
								{
									if ($i > 0)
										{
											if(!empty($data[$fields_order['phone']]))
												{
													$data[$fields_order['phone']] = Cleaner::USPhone($data[$fields_order['phone']]);
													$customer = TCustomer::initWithCustomerPhone($data[$fields_order['phone']], TCompany::CurrentCompany()->ID());
												}
											else
												$customer = TCustomer::initWithCustomerEmail(trim($data[$fields_order['email']]), TCompany::CurrentCompany()->ID());

											if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
												{
													$category_check = TFieldsCategory::IntWithName(TCompany::CurrentCompany()->ID(), 'Other', TCompany::CurrentCompany()->CustomerFormTemplate());
													if(!$category_check->Exists())
														$new_category = TFieldsCategory::Create(TCompany::CurrentCompany()->ID(), 'Other', TCompany::CurrentCompany()->CustomerFormTemplate());
													else
														$new_category = $category_check;

													$custom_fields = $fields_order['custom_fields'];
												}

											if ($customer->Exists() && !$customer->isDeleted())
												{
													if (!$list->HasContactID($customer->ID()))
														$list->SetContactID($customer->ID());

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

													if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
														{
															for ( $j=0; $j<count($custom_fields); $j++)
																{
																	$field_check = TFields::IntWithName(TCompany::CurrentCompany()->ID(), $custom_fields[$j]['label'], TCompany::CurrentCompany()->CustomerFormTemplate());
																	if(!$field_check->Exists())
																		$new_field = TFields::Create(TCompany::CurrentCompany()->ID(), $new_category->ID(), $custom_fields[$j]['label'], TCompany::CurrentCompany()->CustomerFormTemplate());
																	else
																		$new_field = $field_check;
																	$customer->SetMetaData('customfield_'.$new_field->ID(), $data[$custom_fields[$j]['selected']]);
																}
														}
													continue;
												}

											if( !empty($data[$fields_order['phone']]) && !Validator::USPhone($data[$fields_order['phone']]) )
												continue;

											if (!$customer->Exists() && !$customer->isDeleted())
												{
													$newcustomer = TCustomer::CreateByImport(
														TCompany::CurrentCompany(),
														$data[$fields_order['first_name']],
														$data[$fields_order['last_name']],
														$data[$fields_order['phone']],
														trim($data[$fields_order['email']]),
														$data[$fields_order['address']],
														$data[$fields_order['city']],
														$data[$fields_order['state']],
														$data[$fields_order['zip']]
													);

													if (!$list->HasContactID($newcustomer->ID()))
														$list->SetContactID($newcustomer->ID());

													for($j=0; $j<count($new_tags); $j++)
														{
															if($tags->GetTagByName($new_tags[$j]))
																$newcustomer->SetTagID($tags->GetTagByName($new_tags[$j])->ID());
															else
																{
																	$newcustomer->SetTagID(TTag::Create(TCompany::CurrentCompany(), $new_tags[$j])->ID());
																	$tags=new TTagList();
																	$tags->Load();
																}
														}

													if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
														{
															for ( $j=0; $j<count($custom_fields); $j++)
																{
																	$field_check = TFields::IntWithName(TCompany::CurrentCompany()->ID(), $custom_fields[$j]['label'], TCompany::CurrentCompany()->CustomerFormTemplate());
																	if(!$field_check->Exists())
																		$new_field = TFields::Create(TCompany::CurrentCompany()->ID(), $new_category->ID(), $custom_fields[$j]['label'], TCompany::CurrentCompany()->CustomerFormTemplate());
																	else
																		$new_field = $field_check;
																	$newcustomer->SetMetaData('customfield_'.$new_field->ID(), $data[$custom_fields[$j]['selected']]);
																}
														}
												}

										}
									$i++;
								}
							fclose($fh);
						}
					return $error_message;
				}

		}

	if ($userinfo['level'] > 0)
		{

			if (sm_action('postimport'))
				{
					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					if(empty($_postvars['first_name']))
						$error_message = 'Field First Name is required';
					elseif(empty($_postvars['last_name']))
						$error_message = 'Field Last Name is required';
					elseif( empty($_postvars['phone']) && empty($_postvars['email']) )
						$error_message = 'Field Email or Phone is required';

					if(empty($error_message))
						{
							$filename = 'files/download/cus_mod_list_'.$currentcompany->ID().'.csv';
							if (!file_exists($filename))
								{
									$error_message="File is not exist";
								}
							else
								{
									$fields = [];
									foreach($_postvars as $key => $val )
										{
											if (strpos($key, 'custom_field') === false)
												$fields[$key] = $val-1;
										}
									$j=0;
									for($i=0; $i<3; $i++)
										{
											if (!empty($_postvars['tag_'.$i]))
												{
													$m['tags'][$j] = dbescape($_postvars['tag_'.$i]);
													$j++;
												}
										}
									$j=0;
									for($i=0; $i<5; $i++)
										{
											if (!empty($_postvars['custom_field_lable_'.($i+1)]) && !empty($_postvars['custom_field_'.$i]))
												{
													$m['custom_fields'][$j]['label'] = $_postvars['custom_field_lable_'.($i+1)];
													$m['custom_fields'][$j]['selected'] = $_postvars['custom_field_'.$i]-1;
													$j++;
												}
										}
									$fields['custom_fields'] = $m['custom_fields'];
									if(!empty($fields))
										$error_message=set_import_customers($filename, serialize($fields), $contactlist->ID(), serialize($m['tags']), $_postvars['statusarray']);
//										$error_message=import_customers($filename, $fields, $contactlist->ID(), $m['tags']);

								}

						}

					if (!empty($error_message))
						{
							if (is_numeric($error_message))
								{
									sm_redirect('index.php?m='.sm_current_module().'&d=compliancemessage&id='.$contactlist->ID().'&import_id='.intval($error_message));
								}
							else
								{
									sm_set_action('mapping');
									$m['current_action'] = 'postimport_error';
								}
						}

				}

			if (sm_action('finish'))
				{
					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					if(!empty($_getvars['import_id']))
						{
							$import = new TImportCustomers($_getvars['import_id']);
							if (!$import->Exists())
								exit('Access Denied');

							if ($_postvars['compliancemessage']==1)
								$import->SetComplianceMessage(1);

							$import->SetReadyToImport(1);
						}

/*
					$customers = new TCustomerList();
					$customers->SetFilterCompany($currentcompany);
					$customers->SetFilterIDs($contactlist->GetCustomerIDsArray());
					$customers->SetFilterSMSAcceptedTag('undefined');
					$customers->Load();
					for ($i=0; $i<$customers->Count(); $i++)
						{
							if ($_postvars['compliancemessage']!=1)
								{
									$customers->items[$i]->SetSMSAcceptedTimestamp();
									$customers->items[$i]->SetSMSAcceptedTag('yes');
								}
						}
*/
					sm_redirect('index.php?m=contactlist&d=listdetails&id='.$contactlist->ID());
				}

			if (sm_action('compliancemessage'))
				{
					$m['module'] = sm_current_module();
					sm_extcore();

					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					if(!empty($error_message))
						$m['error_message'] = $error_message;

					$m['compliancemessage'] = 1;
					if(!empty($_postvars['compliancemessage']))
						$m['compliancemessage'] = $_postvars['compliancemessage'];

					if(!empty($_getvars['import_id']))
						{
							$import = new TImportCustomers($_getvars['import_id']);
							if(!$import->Exists())
								exit('Access Denied');

							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=finish&id='.$contactlist->ID().'&import_id='.$import->ID();
						}
					else
						{
							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=finish&id='.$contactlist->ID();
						}
					$m['back_url'] = 'index.php?m='.sm_current_module().'&d=import&id='.$contactlist->ID();

				}

			if (sm_action('mapping'))
				{
					$m['module'] = sm_current_module();
					sm_extcore();

					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					if(!empty($error_message))
						$m['error_message'] = $error_message;

					$file = 'files/download/cus_mod_list_'.$currentcompany->ID().'.csv';

					if(file_exists($file))
						{
							$fh = fopen($file, 'r');
							$m['fields'] = fgetcsv($fh);
							fclose($fh);
							$m['customer_fields'] = [
								[
									'id' => 'first_name',
									'required' => '1',
									'value' => 'First Name',
									'selected' => $_postvars['first_name']
								],
								[
									'id' => 'last_name',
									'required' => '1',
									'value' => 'Last Name',
									'selected' => $_postvars['last_name']
								],
								[
									'id' => 'email',
									'value' => 'Email',
									'selected' => $_postvars['email']
								],
								[
									'id' => 'phone',
									'value' => 'Phone',
									'selected' => $_postvars['phone']
								],
								[
									'id' => 'business_name',
									'value' => 'Business Name',
									'selected' => $_postvars['phone']
								],
								[
									'id' => 'address',
									'value' => 'Address',
									'selected' => $_postvars['address']
								],
								[
									'id' => 'city',
									'value' => 'City',
									'selected' => $_postvars['city']
								],
								[
									'id' => 'state',
									'value' => 'State',
									'selected' => $_postvars['state']
								],
								[
									'id' => 'zip',
									'value' => 'Zip',
									'selected' => $_postvars['zip']
								],
							];
							$m['custom_fields'] = [];
							$m['tag_0'] = $_postvars['tag_0'];
							$m['tag_1'] = $_postvars['tag_1'];
							$m['tag_2'] = $_postvars['tag_2'];
							for($i=0; $i<5; $i++)
								{
									$m['custom_fields'][$i]['id'] = $i+1;
									$m['custom_fields'][$i]['label'] = $_postvars['custom_field_lable_'.($i+1)];
									$m['custom_fields'][$i]['selected'] = $_postvars['custom_field_'.$i];
								}

							$m['statusarray'] = ['received', 'contact', 'appointment', 'sold', 'lost'];
							$m['statusarray_selected'] = $_postvars['statusarray'];
							$m['back_url'] = 'index.php?m='.sm_current_module().'&d=import&id='.$contactlist->ID();
							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=postimport&id='.$contactlist->ID();
						}
				}

			if (sm_action('postupload'))
				{
					sm_extcore();

					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					if ( $_postvars['checkedval']=='')
						$error_message = 'Select at least 1 customer';

					if (empty($error_message))
						{
							$values_array = explode(',', $_postvars['checkedval']);

							$file = 'files/download/cus_list_'.$currentcompany->ID().'.csv';
							$new_file = 'files/download/cus_mod_list_'.$currentcompany->ID().'.csv';
							if(file_exists($new_file))
								{
									unlink($new_file);
								}
							if(file_exists($file))
								{
									$fh = fopen($file, 'r');
									$fp = fopen($new_file, 'w');
									$headr_row = fgetcsv($fh);
									fputcsv($fp, $headr_row);
									$row_count = count(file($file))-1;
									$i = 0;
									while ($data = fgetcsv($fh))
										{
											$row = [];
											for ($j=0; $j < count($data); $j++)
												{
													$row[] = rtrim(trim($data[$j], '"'));
												}
											if (in_array($i, $values_array))
												fputcsv($fp, $row);
											$i++;
										}
									fclose($fp);
									fclose($fh);
									sm_redirect('index.php?m='.sm_current_module().'&d=mapping&id='.$contactlist->ID());
								}
						}

					if (!empty($error_message))
						{
							$m['current_action']='postimport_error';
							sm_set_action('upload');
						}
				}

			if (sm_action('upload'))
				{
					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					$m['module'] = sm_current_module();
					$new_file = 'files/download/cus_list_'.$currentcompany->ID().'.csv';

					if (!empty($error_message))
						$m['error_message'] = $error_message;

					if( $m['current_action']!='postimport_error')
						{
							if(file_exists($new_file))
								{
									unlink($new_file);
								}

							if(!file_exists($new_file))
								{
									$filename=sm_upload_file();
									if (!file_exists($filename))
										{
											$error_message='Error uploading file';
										}

									if (!empty($filename) && file_exists($filename))
										{
											rename($filename, $new_file);
										}

									if (!empty($error_message))
										{
											if (!empty($filename) && file_exists($filename))
												unlink($filename);
											sm_set_action('import');
										}
								}
						}

					if(file_exists($new_file))
						{
							$fh = fopen($new_file, 'r');
							$i = 0;
							$headr_row = fgetcsv($fh);
							$m['td_count'] = count($headr_row);

							for ($j=0; $j < count($headr_row); $j++)
								{
									$m['customer_csv']['title'][$j] = $headr_row[$j];
								}

							while ($data = fgetcsv($fh))
								{
									for ($j=0; $j < $m['td_count']; $j++)
										{
											$m['customer_csv']['rows'][$i]['title'][$j] = $data[$j];
										}
									$i++;
								}
							fclose($fh);
							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=postupload&id='.$contactlist->ID();
						}

				}

			if (sm_action('import'))
				{
					$contactlist = new TContactsList(intval($_getvars['id']));
					if (!$contactlist->Exists())
						exit('Access Denied!');

					$m['module'] = sm_current_module();
					sm_title('Import '.$currentcompany->LabelForCustomers());
					add_path_home();
					add_path('Import '.$currentcompany->LabelForCustomers(), 'index.php?m='.sm_current_module().'&d=import');
					if (!empty($error_message))
						$m['error_message'] = $error_message;

					$m['action_url'] = 'index.php?m='.sm_current_module().'&d=upload&id='.$contactlist->ID();
				}

			if (sm_action('postaddlist'))
				{
					if (empty($_postvars['list_title']))
						{
							$error_message = 'Wrong title';
						}
					else
						{
							if (!empty($_getvars['id']))
								{
									$contactlist = new TContactsList(intval($_getvars['id']));
									if (!$contactlist->Exists())
										exit('Access Denied!');
									$newid = intval($_getvars['id']);
								}
							else
								{
									/** @var TContactsList $contactlist */
									$contactlist = TContactsList::Create(TCompany::CurrentCompany()->ID());
									$newid = $contactlist->ID();
								}
							$contactlist->SetTitle($_postvars['list_title']);
						}
					if (!empty($error_message))
						{
							sm_set_action('list');
						}
					else
						{
								sm_redirect('index.php?m='.sm_current_module().'&d=import&id='.$newid);
						}
				}

			if (sm_action('list'))
				{
					$m['module'] = sm_current_module();
					sm_title('Import '.$currentcompany->LabelForCustomers());
					add_path_home();
					add_path('Import '.$currentcompany->LabelForCustomers(), 'index.php?m='.sm_current_module().'&d=import');
					if (!empty($error_message))
						$m['error_message'] = $error_message;
					if(!empty($_getvars['id']))
						{
							$contactlist = new TContactsList(intval($_getvars['id']));
							if (!$contactlist->Exists())
								exit('Access Denied!');
							$m['list_title']=$contactlist->Title();
							$m['action_url'] = 'index.php?m='.sm_current_module().'&d=postaddlist&id='.$contactlist->ID();
						}
					else
						$m['action_url'] = 'index.php?m='.sm_current_module().'&d=postaddlist';

				}
		}


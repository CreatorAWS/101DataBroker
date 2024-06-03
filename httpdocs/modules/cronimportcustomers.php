<?php

	$timeend=time()+58;

	function import_customers($filename, $fields_order, $contactlist, $new_tags=Array(), $company, $compliancemessage, $status = 'received')
		{
			if (!file_exists($filename))
				{
					print('File not found');
					return false;
				}

			/** @var $company TCompany */
			$i = 0;
			$fh = fopen($filename, 'r');
			$tags=new TTagList($company->ID());
			$tags->Load();

			$list = new TContactsList($contactlist);
			if( !$list->Exists() )
				{
					print('Access Denied!');
					return false;
				}

			while ($data=fgetcsv($fh))
				{
					if ($i > 0)
						{
							if(!empty($data[$fields_order['phone']]))
								{
									$data[$fields_order['phone']] = Cleaner::USPhone($data[$fields_order['phone']]);
									$customer = TCustomer::initWithCustomerPhone($data[$fields_order['phone']], $company->ID());
								}
							else
								$customer = TCustomer::initWithCustomerEmail(trim($data[$fields_order['email']]), $company->ID());

							if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
								{
									$category_check = TFieldsCategory::IntWithName($company->ID(), 'Other', $company->CustomerFormTemplate());
									if(!$category_check->Exists())
										$new_category = TFieldsCategory::Create($company->ID(), 'Other', $company->CustomerFormTemplate());
									else
										$new_category = $category_check;

									$custom_fields = $fields_order['custom_fields'];
								}

							if ($customer->Exists() && !$customer->isDeleted())
								{
									if (!$list->HasContactID($customer->ID()))
										$list->SetContactID($customer->ID());

									$customer->SetStatusTag($status);
									if($data[$fields_order['first_name']] != $customer->FirstName())
										$customer->SetFirstName($data[$fields_order['first_name']]);
									if($data[$fields_order['last_name']] != $customer->LastName())
										$customer->SetLastName($data[$fields_order['last_name']]);
									if($data[$fields_order['address']] != $customer->Address1())
										$customer->SetAddress($data[$fields_order['address']]);
									if($data[$fields_order['business_name']] != $customer->GetBusinessName())
										$customer->SetBusinessName($data[$fields_order['business_name']]);
									if($data[$fields_order['city']] != $customer->City())
										$customer->SetCity($data[$fields_order['city']]);
									if($data[$fields_order['state']] != $customer->State())
										$customer->SetState($data[$fields_order['state']]);
									if($data[$fields_order['zip']] != $customer->ZIP())
										$customer->SetZip($data[$fields_order['zip']]);

									for($j=0; $j<count($new_tags); $j++)
										{
											if($tags->GetTagByName($new_tags[$j]))
												$customer->SetTagID($tags->GetTagByName($new_tags[$j])->ID());
											else
												{
													$customer->SetTagID(TTag::Create($company, $new_tags[$j])->ID());
													$tags=new TTagList();
													$tags->Load();
												}
										}

									if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
										{
											for ( $j=0; $j<count($custom_fields); $j++)
												{
													$field_check = TFields::IntWithName($company->ID(), $custom_fields[$j]['label'], $company->CustomerFormTemplate());
													if(!$field_check->Exists())
														$new_field = TFields::Create($company->ID(), $new_category->ID(), $custom_fields[$j]['label'], $company->CustomerFormTemplate());
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
										$company,
										$data[$fields_order['first_name']],
										$data[$fields_order['last_name']],
										$data[$fields_order['phone']],
										trim($data[$fields_order['email']]),
										$data[$fields_order['address']],
										$data[$fields_order['city']],
										$data[$fields_order['state']],
										$data[$fields_order['zip']]
									);
									$newcustomer->SetStatusTag($status);
									if ($compliancemessage!=1)
										{
											$newcustomer->SetSMSAcceptedTimestamp();
											$newcustomer->SetSMSAcceptedTag('yes');
										}

									if (!$list->HasContactID($newcustomer->ID()))
										$list->SetContactID($newcustomer->ID());

									for($j=0; $j<count($new_tags); $j++)
										{
											if($tags->GetTagByName($new_tags[$j]))
												$newcustomer->SetTagID($tags->GetTagByName($new_tags[$j])->ID());
											else
												{
													$newcustomer->SetTagID(TTag::Create($company, $new_tags[$j])->ID());
													$tags=new TTagList($company->ID());
													$tags->Load();
												}
										}

									if(is_array($fields_order['custom_fields']) && count($fields_order['custom_fields'])>0)
										{
											for ( $j=0; $j<count($custom_fields); $j++)
												{
													$field_check = TFields::IntWithName($company->ID(), $custom_fields[$j]['label'], $company->CustomerFormTemplate());
													if(!$field_check->Exists())
														$new_field = TFields::Create($company->ID(), $new_category->ID(), $custom_fields[$j]['label'], $company->CustomerFormTemplate());
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
			return true;
		}

	function import_customers_from_list()
		{
			$items=new TImportCustomersList();
			$items->SetFilterReadyToImport();
			$items->Limit(1);
			$items->OrderByID();
			$items->Load();
			if ($items->Count()==0)
				return false;
			$item = $items->items[0];
			$item->SetReadyToImport(0);
			$company = new TCompany($item->CompanyID());
			import_customers($item->Filename(), unserialize($item->Fields()), $item->ContactListID(), unserialize($item->Tags()), $company, $item->ComplianceMessage(), $item->Staus());
			$item->Remove();
			return true;
		}

	while (time()<=$timeend)
		if (!import_customers_from_list())
			sleep(5);
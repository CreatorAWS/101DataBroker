<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("products_FUNCTIONS_DEFINED"))
		{
			define("products_FUNCTIONS_DEFINED", 1);
		}

	if (System::LoggedIn())
		{
			sm_default_action('list');
			sm_add_body_class('products_list');

			if (sm_action('postdelete'))
				{
					sm_use('tproduct');
					$product=new TProduct(intval($_getvars['id']));
					if ($product->Exists())
						{
							$product->Remove();
							sm_extcore();
							sm_saferemove('index.php?m=products&d=view&id='.intval($product->ID()));
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_action('download'))
				{
					sm_use('tproduct');
					sm_use('tpurchase');
					$product = new TProduct(intval($_getvars['id']));
					if ($product->Exists())
						{
							$file = $product->FilePathToDownload();
							if (file_exists($file))
								{
									sm_session_close();
									header("Content-type: application/octet-stream");
									header("Content-Disposition: attachment; filename=".$product->FileToDownloadBasename());
									$fp = fopen($file, 'rb');
									fpassthru($fp);
									fclose($fp);
									exit;
								}
						}
				}

			if (sm_actionpost('postupload'))
				{
					sm_use('tproduct');
					$product=new TProduct(intval($_getvars['id']));
					if ($product->Exists())
						{
							$tmpfile = sm_upload_file();
							if (!file_exists($tmpfile))
								$error='Failed to upload file';
							else
								{
									$dst = $product->DefaultDownloadPath();
									if (file_exists($dst))
										unlink($dst);
									rename($tmpfile, $dst);
									$product->SetOriginalFileNameToDownload($_uplfilevars['userfile']['name']);
									sm_notify('File uploaded');
									if (!empty($_getvars['returnto']))
										sm_redirect($_getvars['returnto']);
									else
										sm_redirect('index.php?m='.sm_current_module().'&d=list');
								}
						}
					if (!empty($error))
						sm_set_action('upload');
				}

			if (sm_action('upload'))
				{
					add_path_home();
					add_path('Products', 'index.php?m=products&d=list');
					sm_use('tproduct');
					$product=new TProduct(intval($_getvars['id']));
					if ($product->Exists())
						{
							sm_use('ui.interface');
							sm_use('ui.form');
							$ui = new TInterface();
							sm_title('Upload File');
							if (!empty($error))
								$ui->NotificationError($error);
							$ui->p('Product: '.$product->Title());
							$f = new TForm('index.php?m='.sm_current_module().'&d=postupload&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
							$f->AddFile('userfile', 'File')->SetFocus();
							$f->LoadValuesArray($_postvars);
							$ui->AddForm($f);
							$ui->Output(true);
						}
				}

			if (sm_actionpost('postadd', 'postedit'))
				{
					if (!empty($_postvars['webhook_url']) && !Validator::URL($_postvars['webhook_url']))
						$error_message='Invalid webhook URL';
					$price=round(floatval($_postvars['price']), 2);
					if ($price<=0)
						$error='Wrong price';
					if (empty($error))
						{
							$q=new TQuery('products');
							if (sm_action('postadd'))
								{
									if ($_getvars['type']=='package')
										$q->Add('id_u', TCompany::SystemCompany()->ID());
									else
										$q->Add('id_u', dbescape(TCompany::CurrentCompany()->ID()));
								}
							$q->Add('title', dbescape($_postvars['title']));
							$q->Add('price', $price);
							$q->Add('redirect_after_checkout', dbescape($_postvars['redirect_after_checkout']));
							$q->Add('text', dbescape($_postvars['text']));
							$q->Add('sidebartext', dbescape($_postvars['sidebartext']));
							$q->Add('css', dbescape($_postvars['css']));
							$q->Add('multiple_qty_allowed', intval($_postvars['multiple_qty_allowed']));
							$q->Add('shippable', intval($_postvars['shippable']));
							$q->Add('downloadable', intval($_postvars['downloadable']));
							if ($userinfo['level']==3)
								$q->Add('download_path', dbescape($_postvars['download_path']));
							if (sm_action('postadd'))
								{
									$id=$q->Insert();
									sm_notify('Product added');
								}
							else
								{
									$id=intval($_getvars['id']);
									$q->Update('id', intval($id));
									sm_notify('Product updated');
								}
							sm_use('tproduct');
							$product=new TProduct($id);
							if ($_getvars['type']=='package')
								{
									$product->SetAskForPassword(1);
									$product->SetSicSearch($_postvars['sic_search_enabled']);
									$product->SetGoogleSearch($_postvars['google_search_enabled']);
									$product->SetBuiltWithSearch($_postvars['builtwith_search_enabled']);
									$product->SetStateSearch($_postvars['state_search_enabled']);
									$product->SetInterval($_postvars['interval']);
									$product->SetWebhookURL('https://'.main_domain().'/index.php?m=companyautocreate');
								}
							if (sm_action('postadd') && $_getvars['type']=='package')
								{
									$product->SetPLanType('package');
								}

							if (!empty($_postvars['webhook_url']))
								$product->SetWebhookURL($_postvars['webhook_url']);


							if (sm_action('postadd') && $product->isDownloadable())
								sm_redirect('index.php?m=products&d=upload&id='.$product->ID().'&returnto='.urlencode($_getvars['returnto']));
							else
								sm_redirect($_getvars['returnto']);
						}
					else
						{
							if (sm_action('postadd'))
								sm_set_action('add');
							else
								sm_set_action('edit');
						}
				}

			if (sm_action('add', 'edit'))
				{
					add_path_home();

					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (sm_action('edit'))
						{
							$product = new TProduct(intval($_getvars['id']));
							if (!$product->Exists() || ($product->CompanyID() != TCompany::CurrentCompany()->ID()))
								exit('Access Denied');
						}

					if ( sm_action('add') && $_getvars['type']=='package')
						$plan_type = 'package';
					elseif ( sm_action('edit') && $product->isPLanTypePackage() )
						$plan_type = 'package';

					if ( $plan_type!='package' )
						{
							$m['active_tab'] = 'products';
							$ui->AddTPL('sales_tabs.tpl');
						}

					if (!empty($error))
						$ui->NotificationError($error);

					if (sm_action('edit'))
						{
							if ( $plan_type=='package' )
								{
									sm_title('Edit Package');
									add_path('Packages', 'index.php?m=globalsettings&d=packagesmgmt');
								}
							else
								{
									add_path('Products', 'index.php?m='.sm_current_module().'&d=list');
									sm_title('Edit Product');
								}

							$f=new TForm('index.php?m=products&d=postedit&id='.$product->ID().($plan_type == 'package'?'&type=package':'').'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							if ( $plan_type=='package' )
								{
									sm_title('Add Package');
									add_path('Packages', 'index.php?m=globalsettings&d=packagesmgmt');
								}
							else
								{
									sm_title('Add Product');
									add_path('Plans', 'index.php?m='.sm_current_module().'&d=list');
								}
							$f=new TForm('index.php?m=products&d=postadd'.($plan_type == 'package'?'&type=package':'').'&returnto='.urlencode($_getvars['returnto']));
						}
					add_path_current();

					$f->AddText('title', 'Title');
					$f->AddText('price', 'Price');
					if ( $plan_type=='package' )
						{
							$f->AddSelectVL('interval', 'Interval Duration', Array(StripeIntervalType::Month(), StripeIntervalType::Year(), StripeIntervalType::Week(), StripeIntervalType::Day()), Array(StripeIntervalType::Month(), StripeIntervalType::Year(), StripeIntervalType::Week(), StripeIntervalType::Day()), true)->WithValue(StripeIntervalType::Month());
							$f->Separator('Services Included in Package');
							$f->AddCheckbox('google_search_enabled', 'Google Search');
							$f->AddCheckbox('builtwith_search_enabled', 'Tech Search');
							$f->AddCheckbox('sic_search_enabled', 'Sic Codes Search');
							$f->AddCheckbox('state_search_enabled', 'State Search');

						}
					else
						{
							$f->AddCheckbox('multiple_qty_allowed', 'Multiple Quantity Allowed');
							$f->AddText('redirect_after_checkout', 'Redirect After Checkout');
						}
					$f->AddEditor('text', 'Text');
					$f->AddEditor('sidebartext', 'Sidebar Text');
					if ( $plan_type!='package' )
						{
							$f->AddCheckbox('shippable', 'Shippable');
							$f->AddCheckbox('downloadable', 'Downloadable');
							$f->ToggleFor('download_path');
							if ($userinfo['level']==3)
								$f->AddText('download_path', 'Custom download file path (absolute path on the server). Use for large files only');

							$f->Separator('Customization');
							$f->AddTextarea('css', 'Custom CSS');
							$f->AddText('webhook_url', 'Webhook URL');
						}


					$data['selected'] = 0;

					if (sm_action('edit'))
						{
							$product = new TProduct(intval($_getvars['id']));
							if (!$product->Exists())
								exit('Access Denied');
						}

					$f->InsertTPL('payment_template.tpl', $data);

					if (sm_action('edit'))
						$f->LoadValuesArray($product->GetRawData());

					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title');
				}

			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_use('formatter');
					sm_use('tproduct');

					sm_add_body_class('transform_table');
					sm_add_jsfile('transform_table.js');
					sm_add_cssfile('transform_table.css');

					add_path_home();
					add_path_current();
					sm_title('Products');
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$m['active_tab'] = 'products';
					$ui->AddTPL('sales_tabs.tpl');
					$ui->html('<div class="additional-buttons-section">');
					$b = new TButtons();
					$b->AddButton('add', '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Add Product', 'index.php?m=productwizard');

					$b->AddClassname('action-buttons pull-right');
					$ui->html('<div class="buttons flex">');
					$ui->AddTPL('productbuttons.tpl', $sm['products_buttons']);
					$ui->AddButtons($b);
					$ui->html('</div>');
					$ui->html('</div>');


					$t=new TGrid();
					$t->AddCol('id', 'ID', '2%');
					$t->AddCol('title', 'Title', '30%');
					$t->AddCol('price', 'Price', '3%');
					$t->AddCol('url', 'URL', '55%');
					$t->AddCol('file', 'File', '5%');
					$t->AddCol('purchases_count_total', 'Purchases', '5%');
					$t->AddEdit();
					$t->AddDelete();

					$products = new TProductList();
					$products->SetFilterUserID(TCompany::CurrentCompany()->ID());
					$products->SetFilterProduct();
					$products->OrderByTitle();
					if (!empty($_getvars['id']))
						$products->SetFilterIDs([$_getvars['id']]);
					$products->Offset($offset);
					$products->Limit($limit);
					$products->Load();

					for ($i = 0; $i< $products->Count(); $i++)
						{
							/** @var $product TProduct */
							$product = $products->items[$i];
							$t->Label('id', $product->ID());
							$t->Label('title', $product->Title());
							if ($product->isShippable())
								$t->InlineImage('title', 'shipping.png');
							if ($product->isMultipleQuantityAllowed())
								$t->InlineImage('title', 'multiple.png');
							$t->Label('price', Formatter::Money($product->Price()));
							$t->Label('purchases_count_total', $product->PurchasesCountTotal());
							$t->Label('url', $product->FrontendURL());
							$t->URL('url', $product->FrontendURL(), true);
							if ($product->isDownloadable())
								{
									$t->DropDownItem('file', 'Upload File', 'index.php?m=products&d=upload&id='.$product->ID().'&returnto='.urlencode(sm_this_url()));
									if ($product->HasFileToDownload())
										{
											$t->Label('file', 'Actions');
											$t->Hint('file', $product->FileToDownloadBasename());
											$t->Hint('title', $product->FileToDownloadBasename());
											$t->InlineImage('title', 'attachment');
											$t->DropDownItem('file', 'Download', 'index.php?m=products&d=download&id='.$product->ID());
										}
									else
										{
											$t->Label('file', 'Not Uploaded');
											$t->CellHighlightError('file');
										}
								}
							else
								$t->Label('file', '-');

							$t->URL('edit', 'index.php?m=productwizard&id='.$product->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$product->ID().'&returnto='.urlencode(sm_this_url()));

							$t->NewRow();
							unset($product);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->AddGrid($t);
					$ui->AddPagebarParams($products->TotalCount(), $limit, $offset);
					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=account');

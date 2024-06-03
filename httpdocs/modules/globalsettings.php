<?php

	if ($userinfo['level']==3)
		{
			sm_default_action('view');

			if (sm_action('savesettings'))
				{
					sm_extcore();

					if ((!empty($_postvars['stripe_secret_key']) && (empty($_postvars['stripe_public_key']) || empty($_postvars['stripe_endpoint_secret']))) || (!empty($_postvars['stripe_public_key']) && (empty($_postvars['stripe_secret_key']) || empty($_postvars['stripe_endpoint_secret']))) || (!empty($_postvars['stripe_endpoint_secret']) && (empty($_postvars['stripe_secret_key']) || empty($_postvars['stripe_public_key']))))
						$error_message='Fill in The Stripe Key Fields';

					if(empty($error_message))
						{
							if (!sm_has_settings('mailjet_api_key'))
								sm_add_settings('mailjet_api_key', '');

							sm_update_settings('mailjet_api_key', dbescape($_postvars['mailjet_api_key']));

							if (!sm_has_settings('mailjet_api_secret'))
								sm_add_settings('mailjet_api_secret', '');

							sm_update_settings('mailjet_api_secret', dbescape($_postvars['mailjet_api_secret']));

							if (!sm_has_settings('google_places_api_key'))
								sm_add_settings('google_places_api_key', '');

							sm_update_settings('google_places_api_key', dbescape($_postvars['google_places_api_key']));

							if (!sm_has_settings('search_section_for_companies'))
								sm_add_settings('search_section_for_companies', '');

							sm_update_settings('search_section_for_companies', dbescape($_postvars['search_section_for_companies']));

							if (!sm_has_settings('builtwith_api_key'))
								sm_add_settings('builtwith_api_key', '');

							sm_update_settings('builtwith_api_key', $_postvars['builtwith_api_key']);

							TCompany::SystemCompany()->SetStripeWebhooksEndpointSecret($_postvars['stripe_endpoint_secret']);
							TCompany::SystemCompany()->SetStripeSecretKey($_postvars['stripe_secret_key']);
							TCompany::SystemCompany()->SetStripePublicKey($_postvars['stripe_public_key']);

							sm_notify('Settings updated');
							sm_redirect('index.php?m=globalsettings&d=view');
						}
					else
						sm_set_action(Array('savesettings' => 'view'));
				}

			if (sm_action('view'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.buttons');
					sm_title('System Settings');
					$ui=new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					$f=new TForm('index.php?m='.sm_current_module().'&d=savesettings');

					$f->Separator('MailJet');
					$f->AddText('mailjet_api_key', 'MailJet API Key')->WithValue(sm_settings('mailjet_api_key'));
					$f->AddText('mailjet_api_secret', 'MailJet API Secret')->WithValue(sm_settings('mailjet_api_secret'));

					$f->Separator('Search Section');
					$f->AddText('google_places_api_key', 'Google Places API Key')->WithValue(sm_settings('google_places_api_key'));
					$f->AddText('builtwith_api_key', 'BuiltWith API Key')->WithValue(sm_settings('builtwith_api_key'));
					$f->AddCheckbox('search_section_for_companies', 'Search Available For Each Company')->WithValue(sm_settings('search_section_for_companies'));

					$f->Separator('Stripe');
					$f->AddText('stripe_secret_key', 'Secret key')->WithValue(TCompany::SystemCompany()->StripeSecretKey());
					$f->AddText('stripe_public_key', 'Public key')->WithValue(TCompany::SystemCompany()->StripePublicKey());
					$f->AddText('stripe_endpoint_secret', 'Webhooks endpoint secret')->WithValue(TCompany::SystemCompany()->StripeWebhooksEndpointSecret());

					$ui->Add($f);

					if (TCompany::SystemCompany()->HasStripeSettings() && TCompany::SystemCompany()->isStripeTestMode())
						{
							$ui->AddBlock('Save to Purchases in Stripe Test Mode - '.(TCompany::SystemCompany()->isEnabledSaveToPurchasesTestMode()?'Enabled':'Disabled'));
							$b = new TButtons();
							if (TCompany::SystemCompany()->isEnabledSaveToPurchasesTestMode())
								$b->Button('Disable save to purchases on test mode', 'index.php?m='.sm_current_module().'&d=disablesavetopurchasestest&returnto='.urlencode(sm_this_url()));
							else
								$b->Button('Enable save to purchases on test mode', 'index.php?m='.sm_current_module().'&d=enablesavetopurchasestest&returnto='.urlencode(sm_this_url()));
							$ui->Add($b);
						}

					$ui->AddBlock('Stripe Hint');
					$f=new TForm(false);
					$f->AddText('url', 'Please use this URL to register Stripe webhooks')->WithValue(sm_homepage().'stripewebhook-'.TCompany::SystemCompany()->ID());
					$ui->Add($f);

					$ui->Output(true);
				}

			if (sm_action('enablesavetopurchasestest'))
				{
					if (!TCompany::SystemCompany()->isEnabledSaveToPurchasesTestMode())
						{
							TCompany::SystemCompany()->EnableSaveToPurchasesTestMode();
						}
					sm_notify('Enabled');
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('disablesavetopurchasestest'))
				{
					if (TCompany::SystemCompany()->isEnabledSaveToPurchasesTestMode())
						{
							TCompany::SystemCompany()->DisableSaveToPurchasesTestMode();
						}
					sm_notify('Disabled');
					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postdelete'))
				{
					$category = new TTemplateCategories(intval($_getvars['id']));
					if(!$category->Exists())
						exit('Access Denied!');
					$category->Remove();

					$templates=new TMessageTemplateList();
					$templates->SetFilterCompany(TCompany::CurrentCompany());
					$templates->SetFilterCategory($category->ID());
					$templates->Load();

					for ( $i=0; $i<$templates->Count(); $i++ )
						{
							$templates->items[$i]->SetCategoryID(1);
						}

					unset($templates);

					$templates=new TEmailTemplateList();
					$templates->SetFilterCompany(TCompany::CurrentCompany());
					$templates->SetFilterCategory($category->ID());
					$templates->Load();

					for ( $i=0; $i<$templates->Count(); $i++ )
						{
							$templates->items[$i]->SetCategoryID(1);
						}

					sm_redirect($_getvars['returnto']);
				}

			if (sm_action('postadd', 'postedit'))
				{
					$error='';
					if (empty($_postvars['title']))
						$error='Fill required fields';

					if (empty($error))
						{
							if (sm_action('postadd'))
								{
									$template = TTemplateCategories::Create($_postvars['title']);
									sm_notify('Category added');
								}
							else
								{
									$template = new TTemplateCategories(intval($_getvars['id']));
									$template->SetTitle($_postvars['title']);
									sm_notify('Category updated');
								}
							sm_redirect($_getvars['returnto']);
						}
					else
						sm_set_action(Array('postadd'=>'add', 'postedit'=>'edit'));
				}

			if (sm_action('add', 'edit'))
				{
					add_path_home();
					add_path('Template Categories', 'index.php?m='.sm_current_module().'&d=templatectg');
					sm_use('ui.interface');
					sm_use('ui.form');
					$ui = new TInterface();
					if (!empty($error))
						$ui->NotificationError($error);
					if (sm_action('edit'))
						{
							sm_title($lang['common']['edit']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.intval($_getvars['id']).'&returnto='.urlencode($_getvars['returnto']));
						}
					else
						{
							sm_title($lang['common']['add']);
							$f=new TForm('index.php?m='.sm_current_module().'&d=postadd&returnto='.urlencode($_getvars['returnto']));
						}

					$f->AddText('title', 'Title Name', true);
					if (sm_action('edit'))
						{
							$category = new TTemplateCategories(intval($_getvars['id']));
							if (!$category->Exists())
								exit('Access Denied!');
							$f->LoadValuesArray($category->GetRawData());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title');
				}

			if (sm_action('templatectg'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_title('Template Categories');
					$ui=new TInterface();
					$limit=30;
					$offset=intval($_getvars['from']);

					$templates = new TTemplateCategoriesList();
					$templates->SetFilterDefaultCategories();
					$templates->Offset($offset);
					$templates->Limit($limit);
					$templates->Load();

					$b=new TButtons();
					$b->Button('Add Category', 'index.php?m='.sm_current_module().'&d=add&returnto='.urlencode(sm_this_url()));
					$ui->Add($b);

					$t = new TGrid();
					$t->AddCol('title','Title');
					$t->AddEdit();
					$t->AddDelete();

					for ($i = 0; $i < $templates->Count(); $i++)
						{
							$t->Label('title', $templates->items[$i]->Title());
							$t->URL('edit', 'index.php?m='.sm_current_module().'&d=edit&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							if($templates->items[$i]->ID()!=1)
								$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$templates->items[$i]->ID().'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($templates->TotalCount(), $limit, $offset);

					$ui->Output(true);
				}

			if (sm_action('purchases'))
				{
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					add_path_home();
					add_path('Packages', 'index.php?m='.sm_current_module().'&d=packagesmgmt');
					add_path_current();
					sm_title('Purchases');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();

					$t=new TGrid();
					$t->AddCol('id', 'OrderID', '5%');
					$t->AddCol('time', 'Time', '5%');
					$t->AddCol('customer', 'Customer', '15%');
					$t->AddCol('phone', 'Phone', '10%');
					$t->AddCol('email', 'Email', '20%');
					$t->AddCol('title', 'Product', '30%');
					$t->AddCol('actions', 'Actions', '5%');
					$t->AddCol('price', 'Price', '5%');
					$t->AddCol('discount', 'Discount', '5%');
					$t->AddCol('total', 'Total', '5%');
					$t->AddCol('expand', '', '16');

					$product = new TProductList();
					$product->SetFilterTypePackage();
					$product->Load();

					$q=new TQuery('purchases');
					$q->Add('id_u', intval(TCompany::SystemCompany()->ID()));
					if (!empty(intval($_getvars['id_product'])))
						$q->Add('id_p', intval($_getvars['id_product']));
					else
						$q->AddWhere('id_p IN('.implode(",", Cleaner::ArrayQuotedAndDBEscaped($product->ExtractIDsArray())).')');
					$q->OrderBy('id DESC');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();

					for ($i = 0; $i<count($q->items); $i++)
						{
							$purchase=new TPurchase($q->items[$i]);
							$t->Label('id', $purchase->ID());
							$title=$purchase->Title();
							$t->Label('title', $title);
							$t->Label('price', Formatter::Money($q->items[$i]['price']));
							if (floatval($q->items[$i]['discount'])>0)
								$t->Label('discount', Formatter::Money($q->items[$i]['discount']));

							$t->Label('total', Formatter::Money(floatval($q->items[$i]['total'])));
							$t->Label('id_p', $q->items[$i]['id_p']);
							if (!empty($q->items[$i]['phone']))
								$t->Label('phone', Formatter::USPhone($q->items[$i]['phone']));
							$t->Label('email', $q->items[$i]['email']);
							$t->Label('time', Formatter::Date($q->items[$i]['timebought']));
							$t->Label('customer', $q->items[$i]['name']);
							$t->Image('expand', 'info');
							$t->Expand('expand');
							$t->Hint('customer', 'Click to expand');
							$t->Expand('customer');
							$html='
								Name: '.$q->items[$i]['name'].'<br /> 
								Email: '.$q->items[$i]['email'].'<br /> 
								Phone: '.$q->items[$i]['phone'].'<br /> 
								Address Line 1: '.$q->items[$i]['address_line1'].'<br /> 
								Address Line 2: '.$q->items[$i]['address_line2'].'<br /> 
								City: '.$q->items[$i]['city'].'<br /> 
								State: '.$q->items[$i]['state'].'<br /> 
								ZIP: '.$q->items[$i]['zip'].'<br />
								<br /> 
								Product: '.$q->items[$i]['title'].' (ID '.$q->items[$i]['id_p'].')<br /> 
								Purchase Time: '.Formatter::DateTime($q->items[$i]['timebought']).'<br /> 
								Stripe Token: '.$q->items[$i]['stripetoken'];
							$html.='<br />';
							if (!empty($q->items[$i]['coupon_code']))
								{
									$html.='<br /> 
											Coupon: '.$q->items[$i]['coupon_code'].'<br /> 
											Discount: $'.Formatter::Money($q->items[$i]['discount']).'<br /> 
										';
								}
							$t->ExpanderHTML($html);
							if ($t->DropDownItemsCount('actions')>0)
								$t->Label('actions', 'Actions');
							else
								$t->Label('actions', '-');
							$t->NewRow();
							unset($purchase);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('No purchases yet');
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->TotalCount(), $limit, $offset);
					$ui->Output(true);
				}

			if (sm_action('packagesmgmt'))
				{
					sm_add_body_class('transform_table packages_mgmt');
					sm_add_jsfile('transform_table.js');
					sm_add_cssfile('transform_table.css');

					sm_add_body_class('large-submenu');
					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_title('Packages');
					$ui=new TInterface();
					$limit=30;
					$offset=intval($_getvars['from']);

					add_path_home();

					$packages=new TProductList();
//					$packages->SetFilterTypePackage();
					$packages->ShowAllItemsIfNoFilters();
					$packages->OrderByOrder();
					$packages->Limit($limit);
					$packages->Offset($offset);
					$packages->Load();

					add_path_current();
					$b=new TButtons();


					$ui->html('<div class="additional-buttons-section">');
					$b = new TButtons();
					$b->AddButton('add', '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Add Package', 'index.php?m=products&d=add&type=package&returnto='.urlencode('index.php?m=globalsettings&d=packagesmgmt'));
					$b->AddButton('purchases', '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="CurrentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Purchases', 'index.php?m='.sm_current_module().'&d=purchases');
					$b->AddClassname('action-buttons pull-right');
					$ui->html('<div class="pipeline-dropdown"></div>');
					$ui->html('<div class="buttons">');
					$ui->AddButtons($b);
					$ui->html('</div>');
					$ui->html('</div>');

					$t = new TGrid();
					$t->AddCol('title','Title');
					$t->AddCol('price','Price');
					$t->AddCol('limits','Services Available');
					$t->AddCol('qty','Purchases');
					$t->AddCol('product_link','');
					$t->AddEdit();
					$t->AddDelete();
//					$t->AddCol('action_links', '', '110px');

					for ($i = 0; $i < $packages->Count(); $i++)
						{
							/** @var TProduct $package */
							$package = $packages->Item($i);
							$t->Label('title', $package->Title());
							$t->Label('price', $package->Price());
							$t->Label('points', $package->Points());
							$limits = '';
							$limits .= $package->HasGoogleSearch()? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="16px" height="16px" style="margin-right: 5px;"><path d="M 15.003906 3 C 8.3749062 3 3 8.373 3 15 C 3 21.627 8.3749062 27 15.003906 27 C 25.013906 27 27.269078 17.707 26.330078 13 L 25 13 L 22.732422 13 L 15 13 L 15 17 L 22.738281 17 C 21.848702 20.448251 18.725955 23 15 23 C 10.582 23 7 19.418 7 15 C 7 10.582 10.582 7 15 7 C 17.009 7 18.839141 7.74575 20.244141 8.96875 L 23.085938 6.1289062 C 20.951937 4.1849063 18.116906 3 15.003906 3 z"/></svg>' : '';
							$limits .= $package->HasBuiltWithSearch()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16px" height="16px" viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /></svg>' : '';
							$limits .= $package->HasSicSearch()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none"  width="16px" height="16px" viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>' : '';
							$limits .= $package->HasStateSearch()? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="16px" height="16px"  viewBox="0 0 24 24" style="margin-right: 5px;" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>' : '';

							$t->Label('limits', $limits);
							$t->Label('qty', $package->PurchasesCountTotal());
							$t->URL('qty', 'index.php?m='.sm_current_module().'&d=purchases&id_product='.$package->ID());
							$t->Label('product_link', 'Open Package');
							$t->URL('product_link', 'https://'.main_domain().'/product-'.$package->ID(), true);

							$t->URL('edit', 'index.php?m=products&d=edit&type=package&id='.$package->ID().'&returnto='.urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=products&d=postdelete&id='.$package->ID().'&returnto='.urlencode(sm_this_url()));

							$t->NewRow();
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->Add($t);
					$ui->AddPagebarParams($packages->TotalCount(), $limit, $offset);

					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=account');

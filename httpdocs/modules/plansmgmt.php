<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("plans_FUNCTIONS_DEFINED"))
		{
			define("plans_FUNCTIONS_DEFINED", 1);
		}

	sm_add_body_class('large-submenu');

	if ($userinfo['level']>0)
		{
			if (!System::MyAccount()->isSuperAdmin())
				exit('Access Denied!');
			
			sm_default_action('list');

			if (sm_action('postdelete'))
				{
					sm_use('tplan');
					$plan=new TPlan(intval($_getvars['id']));
					if ($plan->Exists())
						{
							$plan->Remove();
							sm_extcore();
							sm_saferemove('index.php?m='.sm_current_module().'&d=view&id='.intval($plan->ID()));
							sm_redirect($_getvars['returnto']);
						}
				}

			if (sm_actionpost('postadd', 'postedit'))
				{
					if (empty($_postvars['title']))
						$error_message='Fill reqired fields';
					elseif (intval($_postvars['trial_period_days'])<0)
						$error_message='Trial period should be equal of greater than 0';
					elseif (intval($_postvars['qty_min_available'])<1)
						$error_message='Min item quantity should be equal of greater than 1';
					elseif (intval($_postvars['qty_min_available'])==intval($_postvars['qty_max_available']) && intval($_postvars['qty_min_available'])!=1)
						$error_message='Wrong min/max quantity';
					elseif (intval($_postvars['qty_step'])<1)
						$error_message='Quantity step should be equal of greater than 1';
					elseif (intval($_postvars['qty_max_available'])<intval($_postvars['qty_min_available']))
						$error_message='Max item quantity should be equal of greater than min';
					elseif (!empty($_postvars['webhook_url']) && !Validator::URL($_postvars['webhook_url']))
						$error_message='Invalid webhook URL';
					if (empty($error_message))
						{
							if (sm_action('postadd'))
								$plan=TPlan::Create(TCompany::CurrentCompany());
							else
								{
									$plan = new TPlan(intval($_getvars['id']));
									if (!$plan->Exists() || ($plan->CompanyID()!=TCompany::CurrentCompany()->ID() && !$plan->isPLanTypePackage()) )
										exit('Error E-PLN-324786-3245');
								}
							if (sm_action('postadd') || sm_action('postedit') && $plan->CanStripePlanBeEdited())
								{
									if (empty($_postvars['interval']) || empty($_postvars['interval_count']))
										$error_message='Fill reqired fields';
									elseif (intval($_postvars['interval_count'])<1)
										$error_message='Interval should be equal of greater than 1';
									if (Cleaner::FloatMoney($_postvars['price'])<=0)
										$error_message='Wrong price';
								}
							if (empty($error_message))
								{
									$plan->SetTitle($_postvars['title']);
									if ($plan->CanStripePlanBeEdited())
										{
											$plan->SetPrice(Cleaner::FloatMoney($_postvars['price']));
											$plan->SetIntervalType($_postvars['interval']);
											$plan->SetIntervalCount(intval($_postvars['interval_count']));
										}
									$plan->SetAskForPasswordOnCheckout(intval($_postvars['ask_for_passwd'])==1);
									$plan->SetSetupFee(Cleaner::FloatMoney($_postvars['setup_fee']));
									$plan->SetSetupFeeCustomTitle($_postvars['setup_fee_title']);
									$plan->SetSetupFeeChargedBeforeTrialPeriod(intval($_postvars['setup_fee_trial_start'])==1);
									$plan->SetText($_postvars['text']);
									$plan->SetSidebarText($_postvars['sidebartext']);
									$plan->SetThankYouText($_postvars['thank_you_text']);
									$plan->SetRedirectAfterCheckoutURLValue($_postvars['redirect_after_checkout']);
									$plan->SetMinAvailableQuantity($_postvars['qty_min_available']);
									$plan->SetMaxAvailableQuantity($_postvars['qty_max_available']);
									$plan->SetStepForAvailableQuantity($_postvars['qty_step']);
									$plan->SetTitleForItemQuantity($_postvars['qty_title']);
									$plan->SetTrialPeriodDays($_postvars['trial_period_days']);
									$plan->SetCSS($_postvars['css']);
									$plan->SetWebhookURL($_postvars['webhook_url']);
									if ($_getvars['type']=='package')
										{
											$plan->SetPLanType('package');
											if (empty($_postvars['webhook_url']))
												$plan->SetWebhookURL('https://'.main_domain().'/index.php?m=companyautocreate');

											$plan->SetCompanyID(0);
										}

									if (sm_action('postadd'))
										{
											$plan->CreateStripePlan();
										}
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
					sm_add_body_class('wizard_page');

					if (sm_action('edit'))
						{
							$plan = new TPlan(intval($_getvars['id']));
							if (!$plan->Exists() || ($plan->CompanyID()!=TCompany::CurrentCompany()->ID() && !$plan->isPLanTypePackage()) )
								exit('Error E-PLN-324786-3945');
						}

					if ( sm_action('add') && $_getvars['type']=='package')
						$plan_type = 'package';
					elseif ( sm_action('edit') && $plan->isPLanTypePackage() )
						$plan_type = 'package';

					sm_use('ui.interface');
					sm_use('ui.form');

					$ui = new TInterface();
					if (!empty($error_message))
						$ui->NotificationError($error_message);
					if (sm_action('edit'))
						{
							if ( $plan_type=='package' )
								{
									sm_title('Edit Package');
									add_path('Packages', 'index.php?m=globalsettings&d=packagesmgmt');
								}
							else
								{
									add_path('Plans', 'index.php?m='.sm_current_module().'&d=list');
									sm_title('Edit Plan');
								}
							$f=new TForm('index.php?m='.sm_current_module().'&d=postedit&id='.$plan->ID().($plan_type == 'package'?'&type=package':'').'&returnto='.urlencode($_getvars['returnto']));
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
									sm_title('Add Plan');
									add_path('Plans', 'index.php?m='.sm_current_module().'&d=list');
								}
							$f=new TForm('index.php?m='.sm_current_module().'&d=postadd'.($plan_type == 'package'?'&type=package':'').'&returnto='.urlencode($_getvars['returnto']));
						}
					add_path_current();
					$f->AddText('title', 'Title', true);
					if (sm_action('add') || is_object($plan) && $plan->CanStripePlanBeEdited())
						{
							$f->AddText('price', 'Price', true);
							$f->AddSelectVL('interval', 'Interval Duration', Array(StripeIntervalType::Month(), StripeIntervalType::Year(), StripeIntervalType::Week(), StripeIntervalType::Day()), Array(StripeIntervalType::Month(), StripeIntervalType::Year(), StripeIntervalType::Week(), StripeIntervalType::Day()), true)->WithValue(StripeIntervalType::Month());
							$f->AddText('interval_count', 'Interval Count', true)->WithValue(1);
						}
					else
						{
							$f->Label('Price', $plan->Price());
							$f->Label('Interval Duration', $plan->IntervalType());
							$f->Label('Interval Count', $plan->IntervalCount());
						}

					if ( sm_action('add') && $plan_type == 'package' )
						{
							$f->Separator('Account');
							$f->AddCheckbox('ask_for_passwd', 'Ask customer for password for his new account on checkout page')->WithValue(1);
						}
					elseif ( sm_action('edit') && $plan_type == 'package' )
						{
							$f->AddCheckbox('ask_for_passwd', 'Ask customer for password for his new account on checkout page')->WithValue(1);
						}
					else
						$f->AddCheckbox('ask_for_passwd', 'Ask customer for password for his new account on checkout page');

					$f->AddCheckbox('ask_for_passwd', 'Ask customer for password for his new account on checkout page');
					$f->Separator('Setup Fee');
					$f->AddText('setup_fee', 'Setup Fee')
						->WithFieldEndText('Leave empty or zero if no setup fee')
						->WithFieldAttribute('style', 'width:150px;')
						->WithValue(0);
					$f->AddText('setup_fee_title', 'Setup Fee Custom Title');
					$f->AddSelect('setup_fee_trial_start', 'Setup Fee on Trial Period', [0, 1], [
						'Charge Setup Fee after Trial Period end',
						'Charge Setup Fee before Trial Period start'
					])
						->WithValue(0);
					$f->AddText('trial_period_days', 'Trial Period in Days')
						->WithFieldEndText('Leave empty or zero if no trial period')
						->WithFieldAttribute('style', 'width:150px;')
						->WithValue(0);

					$f->Separator('Available Quantities');
					$f->AddText('qty_min_available', 'Min Quantity Available', true)->WithValue(1);
					$f->AddText('qty_max_available', 'Max Quantity Available', true)->WithValue(1);
					$f->AddText('qty_step', 'Step (for dropdown with quantity on checkout)', true)->WithValue(1);
					$f->AddText('qty_title', 'Items name (users, items...)')->WithValue('');
					$f->Separator('Appearance');
					$f->AddEditor('text', 'Product Text');
					$f->AddEditor('sidebartext', 'Sidebar Text');
					$f->Separator('Thank You Page');
					$f->AddEditor('thank_you_text', 'Thank You Text');
					$f->AddText('redirect_after_checkout', 'Redirect After Checkout (URL)');

					$f->Separator('Customization');
					$f->AddTextarea('css', 'Custom CSS');
					$f->Separator('Other');
					$f->AddText('webhook_url', 'Webhook URL')
						->WithValue(sm_settings('checkout_default_webhook_url'));
					if (sm_action('edit'))
						{
							$plan = new TPlan(intval($_getvars['id']));
							if (!$plan->Exists() || ($plan->CompanyID()!=TCompany::CurrentCompany()->ID() && !$plan->isPLanTypePackage()) )
								exit('Error E-PLN-324786-3244');
							$f->SetValue('title', $plan->Title());
							$f->SetValue('price', $plan->Price());
							$f->SetValue('ask_for_passwd', $plan->isAskForPasswordOnCheckout()?1:0);
							$f->SetValue('setup_fee', $plan->SetupFee());
							$f->SetValue('setup_fee_title', $plan->SetupFeeCustomTitle());
							$f->SetValue('setup_fee_trial_start', $plan->isSetupFeeChargedBeforeTrialPeriod()?1:0);
							$f->SetValue('interval', $plan->IntervalType());
							$f->SetValue('interval_count', $plan->IntervalCount());
							$f->SetValue('text', $plan->Text());
							$f->SetValue('sidebartext', $plan->SidebarText());
							$f->SetValue('thank_you_text', $plan->ThankYouText());
							$f->SetValue('redirect_after_checkout', $plan->RedirectAfterCheckoutURLValue());
							$f->SetValue('qty_min_available', $plan->MinAvailableQuantity());
							$f->SetValue('qty_max_available', $plan->MaxAvailableQuantity());
							$f->SetValue('qty_step', $plan->StepForAvailableQuantity());
							$f->SetValue('qty_title', $plan->TitleForItemQuantity());
							$f->SetValue('trial_period_days', $plan->TrialPeriodDays());
							$f->SetValue('css', $plan->CSS());
							$f->SetValue('webhook_url', $plan->WebhookURL());
						}
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->Output(true);
					sm_setfocus('title');
				}

			if ( sm_action('searchtitle') )
				{
					sm_use('ui.interface');

					$ui = new TInterface();

					if (!empty($_postvars['title']))
						{
							$list = new TPlanList();
							$list->SetFilterTitleBeginnig($_postvars['title']);
							$list->SetFilterCompany(TCompany::CurrentCompany()->ID());
							$list->Limit(10);
							$list->Load();

							if ( $list->Count() > 0 )
								{
									$ui->html('<ul id="country-list">');

									for ( $i = 0; $i < $list->Count(); $i++ )
										{
											/** @var  $plan TPlan */
											$plan = $list->Item($i);
											$ui->html('<li><a href="index.php?m='.sm_current_module().'&id='.$plan->ID().'&search='.$_postvars['title'].'">'.$plan->Title().'</a></li>');
										}

									$ui->html('</ul>');
								}
							else
								{
									$ui->html('<ul id="country-list"><li class="nothing-found">Nothing Found</li></ul>');
								}
						}

					$ui->Output(true);
				}

			if (sm_action('list'))
				{
					sm_add_body_class('products_list');
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_use('formatter');
					sm_use('tplan');
					sm_add_cssfile('ajaxsearch.css');
					add_path_home();
					add_path('Plans', 'index.php?m='.sm_current_module().'&d=list');
					sm_title('Plans');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();

					$data2['templates'][] = [
						'url' => 'index.php?m=socialcontent',
						'title' => 'Social Media',
						'selected' => $sm['g']['m']=='socialcontent'
					];

					if ((System::MyAccount()->isSuperAdmin() && TCompany::CurrentCompany()->ID()== TCompany::SystemCompany()->ID() && (TCompany::SystemCompany()->HasStripeSettings())))
						{
							$data2['templates'][] = [
								'url' => 'index.php?m=products',
								'title' => 'Products',
								'selected' => $sm['g']['m']=='products'
							];

							$data2['templates'][] = [
								'url' => 'index.php?m=plansmgmt',
								'title' => 'Recurring',
								'selected' => $sm['g']['m']=='plansmgmt'
							];
						}

					$data2['templates'][] = [
						'url' => 'index.php?m=companyassets&mode=image&type=view',
						'title' => 'Image Library',
						'selected' => $sm['g']['m']=='companyassets' && $sm['g']['mode'] == 'image' && $sm['g']['type'] == 'view'
					];

					$data2['templates'][] = [
						'url' => 'index.php?m=companyassets&mode=voice&type=view',
						'title' => 'Voice Library',
						'selected' => $sm['g']['m']=='companyassets' && $sm['g']['mode'] == 'voice' && $sm['g']['type'] == 'view'
					];

					$data2['templates'][] = [
						'url' => 'index.php?m=ffmpeg',
						'title' => 'Video Library',
						'selected' => $sm['g']['m']=='ffmpeg'
					];

					$ui->html('<div class="additional-buttons-section">');

					if (!empty($_getvars['search']))
						$data['search'] = $_getvars['search'];

					$data['searchurl'] = sm_this_url(['d' => 'searchtitle', 'theonepage' => '1']);
					$data['clearfilterurl'] = sm_this_url(['d' => '', 'theonepage' => '', 'id' => '', 'search' => '']);


					sm_add_jsfile('templatesfilters.js');
					$ui->AddTPL('templatesfilters.tpl', '', $data2);
					$ui->AddTPL('ajaxsearch.tpl', '', $data);

					$b = new TButtons();

					$b->AddButton('add', '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Add Plan', 'index.php?m=planswizard');
					$b->AddButton('share', '<svg width="15px" height="17px" viewBox="0 0 15 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Symbols" stroke="none" stroke-width="1" fill="CurrentColor" fill-rule="evenodd"><path d="M12.0058594,16.4882812 C12.8613281,16.4648438 13.5703125,16.171875 14.1328125,15.609375 C14.6953125,15.046875 14.9882812,14.34375 15.0117188,13.5 C14.9882812,12.6445312 14.6953125,11.9355469 14.1328125,11.3730469 C13.5703125,10.8105469 12.8613281,10.5175781 12.0058594,10.4941406 C11.4667969,10.4941406 10.96875,10.6259766 10.5117188,10.8896484 C10.3158482,11.0026507 10.136659,11.1350247 9.97415099,11.2867706 L9.87985938,11.3811406 L5.89285938,9.06614062 L5.93239928,8.91138301 C5.96339101,8.77037061 5.98547262,8.62451575 5.99864411,8.47381844 L6.01171875,8.24414062 C6.0053267,8.01083097 5.97888688,7.78841684 5.93239928,7.57689824 L5.89285938,7.42314063 L9.87985938,5.10814062 L9.97415099,5.20151068 C10.0824896,5.30267459 10.1982422,5.39522879 10.3214086,5.47917331 L10.5117188,5.59863281 C10.96875,5.86230469 11.4667969,5.99414062 12.0058594,5.99414062 C12.8613281,5.97070312 13.5703125,5.67773438 14.1328125,5.11523438 C14.6953125,4.55273438 14.9882812,3.84375 15.0117188,2.98828125 C14.9882812,2.14453125 14.6953125,1.44140625 14.1328125,0.87890625 C13.5703125,0.31640625 12.8613281,0.0234375 12.0058594,0 C11.4667969,0 10.96875,0.131835938 10.5117188,0.395507812 C10.0546875,0.659179688 9.68847656,1.02539062 9.41308594,1.49414062 C9.13769531,1.96289062 9,2.4609375 9,2.98828125 C9,3.21428571 9.02529098,3.43544723 9.07587293,3.65176578 L9.11985938,3.81114063 L5.14585938,6.13314062 L5.1328125,6.1171875 C4.62144886,5.60582386 3.98902376,5.31721333 3.23553719,5.25135589 L3.00585938,5.23828125 C2.46679688,5.25 1.96875,5.38769531 1.51171875,5.65136719 C1.0546875,5.91503906 0.688476562,6.28125 0.413085938,6.75 C0.137695312,7.21875 0,7.71679688 0,8.24414062 C0,8.77148438 0.137695312,9.26953125 0.413085938,9.73828125 C0.688476562,10.2070312 1.0546875,10.5732422 1.51171875,10.8369141 C1.96875,11.1005859 2.46679688,11.2382812 3.00585938,11.25 C3.86132812,11.2265625 4.5703125,10.9335938 5.1328125,10.3710938 L5.1328125,10.3710938 L5.14585938,10.3561406 L9.11985938,12.6781406 L9.07587293,12.8365155 C9.04215163,12.9807278 9.01967076,13.1270926 9.00843033,13.2756099 L9,13.5 C9,14.0273438 9.13769531,14.5253906 9.41308594,14.9941406 C9.68847656,15.4628906 10.0546875,15.8291016 10.5117188,16.0927734 C10.96875,16.3564453 11.4667969,16.4882812 12.0058594,16.4882812 Z M12.0058594,4.5 C11.5839844,4.48828125 11.2324219,4.34179688 10.9511719,4.06054688 C10.6699219,3.77929688 10.5292969,3.42480469 10.5292969,2.99707031 C10.5292969,2.56933594 10.6699219,2.21484375 10.9511719,1.93359375 C11.2324219,1.65234375 11.5839844,1.51171875 12.0058594,1.51171875 C12.4277344,1.51171875 12.7792969,1.65234375 13.0605469,1.93359375 C13.3417969,2.21484375 13.4824219,2.56933594 13.4824219,2.99707031 C13.4824219,3.42480469 13.3417969,3.77929688 13.0605469,4.06054688 C12.7792969,4.34179688 12.4277344,4.48828125 12.0058594,4.5 Z M3.00585938,9.73828125 C2.58398438,9.7265625 2.23242188,9.58007812 1.95117188,9.29882812 C1.66992188,9.01757812 1.52929688,8.66601562 1.52929688,8.24414062 C1.52929688,7.82226562 1.66992188,7.47070312 1.95117188,7.18945312 C2.23242188,6.90820312 2.58398438,6.76757812 3.00585938,6.76757812 C3.42773438,6.76757812 3.77929688,6.90820312 4.06054688,7.18945312 C4.34179688,7.47070312 4.48242188,7.82226562 4.48242188,8.24414062 C4.48242188,8.5078125 4.42749023,8.74401855 4.31762695,8.95275879 L4.31285938,8.95914062 L4.2890625,8.99121094 C4.27148438,9.01904297 4.25610352,9.04779053 4.24291992,9.07745361 L4.15942383,9.19006348 L4.15942383,9.19006348 L4.06054688,9.29882812 C3.77929688,9.58007812 3.42773438,9.7265625 3.00585938,9.73828125 Z M12.0058594,14.9941406 C11.5839844,14.9824219 11.2324219,14.8359375 10.9511719,14.5546875 C10.6699219,14.2734375 10.5292969,13.9189453 10.5292969,13.4912109 C10.5292969,13.2773438 10.5644531,13.0817871 10.6347656,12.904541 L10.6608594,12.8461406 L10.6776123,12.8276367 L10.6776123,12.8276367 L10.7314453,12.7441406 C10.7475586,12.7148438 10.7616577,12.6849976 10.7737427,12.6546021 L10.7798594,12.6341406 L10.8522949,12.5366364 L10.8522949,12.5366364 L10.9511719,12.4277344 C11.2324219,12.1464844 11.5839844,12.0058594 12.0058594,12.0058594 C12.4277344,12.0058594 12.7792969,12.1464844 13.0605469,12.4277344 C13.3417969,12.7089844 13.4824219,13.0634766 13.4824219,13.4912109 C13.4824219,13.9189453 13.3417969,14.2734375 13.0605469,14.5546875 C12.7792969,14.8359375 12.4277344,14.9824219 12.0058594,14.9941406 Z" id="path-1"></path></g></svg> Share Content', 'index.php?m=socialproductslib&d=plans');

					$b->AddClassname('action-buttons pull-right');
					$ui->html('<div class="pipeline-dropdown"></div>');
					$ui->html('<div class="buttons flex">');
					$ui->AddTPL('palnbuttons.tpl', $sm['plans_buttons']);
					$ui->AddButtons($b);
					$ui->html('</div>');
					$ui->html('</div>');

					$t=new TGrid();
					$t->AddCol('id', 'ID', '2%');
					$t->AddCol('title', 'Title', '30%');
					$t->AddCol('interval', 'Interval', '10%');
					$t->AddCol('price', 'Price', '3%');
					$t->AddCol('setup', 'Fee', '3%');
					$t->AddCol('url', 'URL', '40%');
					$t->AddCol('subscribers', 'Subscribers', '10%');
					$t->AddCol('action_links', '', '110px');
					$plans=new TPlanList();
					$plans->SetFilterCompany(TCompany::CurrentCompany());
					$plans->SetFilterActive();
					$plans->SetFilterPlan();
					$plans->OrderByTitle();
					$plans->Limit($limit);
					$plans->Offset($offset);
					$plans->Load();
					for ($i = 0; $i<$plans->Count(); $i++)
						{
							$plan=$plans->Item($i);
							$t->Label('id', $plan->ID());
							$t->Label('title', $plan->Title());
//							if ($plan->HasTrialPeriod())
//								$t->AppendCellFooterHTML('title', ' <div class="label label-info">Trial period</div>');
//							if ($plan->HasMultipleItems())
//								$t->AppendCellFooterHTML('title', ' <div class="label label-info">'.$plan->MinAvailableQuantity().'-'.$plan->MaxAvailableQuantity().' '.$plan->TitleForItemQuantity().'</div>');
							$t->Label('price', Formatter::Money($plan->Price()));
							if ($plan->SetupFee()>0)
								$t->Label('setup', Formatter::Money($plan->SetupFee()));
							$t->Label('interval', $plan->IntervalCount().' '.$plan->IntervalType());
							$t->Label('url', $plan->FrontendURL());
							$t->URL('url', $plan->FrontendURL(), true);

							$action_links = '<div class="action_links">';
							$action_links .= '<a href="index.php?m=planswizard&id='.$plan->ID().'" data-toggle="tooltip" data-placement="top" title="Edit"><svg width="24px" height="24px" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="PencilOutlineIcon"><path d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" fill="CurrentColor"></path></svg></a>';
							if ($plan->CanBeDeleted())
								$action_links .= '<a href="javascript:;" onclick="button_msgbox(\'index.php?m='.sm_current_module().'&d=postdelete&id='.$plan->ID().'&returnto='.urlencode(sm_this_url()).'\', \'Are you sure?\');" data-toggle="tooltip" data-placement="top" title="Delete"><svg width="16" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 16C1 17.1 1.9 18 3 18H11C12.1 18 13 17.1 13 16V4H1V16ZM3 6H11V16H3V6ZM10.5 1L9.5 0H4.5L3.5 1H0V3H14V1H10.5Z" fill="CurrentColor"/></svg></a>';
							$action_links .= '</div>';
							$t->Label('action_links', $action_links);



							$t->Label('subscribers', 'View List');
							$t->URL('subscribers', 'index.php?m=subscriptionsmgmt&d=list&plan='.$plan->ID());
							$t->NewRow();
							unset($plan);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->AddGrid($t);
					$ui->AddPagebarParams($plans->TotalCount(), $limit, $offset);
					$ui->Output(true);
				}

		}
	else
		sm_redirect('index.php?m=account');

<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("products_FUNCTIONS_DEFINED"))
		{

			define("products_FUNCTIONS_DEFINED", 1);
		}

	if ($userinfo['level']>0)
		{
			if (!System::MyAccount()->isSuperAdmin())
				exit('Access Denied!');


			if (sm_action('updatecardurl'))
				{
					add_path_home();
					if ($_getvars['type'] == 'package')
						add_path('Packages', 'index.php?m=packagesmgmt');
					else
						add_path('Plans', 'index.php?m=plans&d=list');
					add_path_current();
					$subscription = new TSubscription(intval($_getvars['id']));

					if ($subscription->Exists() && ($subscription->CompanyID()==TCompany::CurrentCompany()->ID()) && !$subscription->isCancelled())
						{
							if ($subscription->Plan()->isPLanTypePackage())
								{
									sm_add_body_class('large-submenu');
								}
							sm_use('ui.interface');
							sm_use('ui.form');
							sm_use('ui.buttons');
							sm_use('ui.fa');
							sm_title('Update Card');
							$ui = new TInterface();
							$f = new TForm(false);
							$f->AddText('url', 'URL for Customer')
								->WithValue(sm_homepage().'index.php?m=subscriptioncard&id='.$subscription->ID().'&hash='.$subscription->PublicHash());
							$ui->Add($f);
							$b=new TButtons();
							$b->Button(FA::EmbedCodeFor('backward').' Back', $_getvars['returnto']);
							$ui->Output(true);
						}
				}
			if (sm_action('cancel'))
				{
					add_path_home();
					if ($_getvars['type'] == 'package')
						add_path('Packages', 'index.php?m=packagesmgmt');
					else
						add_path('Plans', 'index.php?m=plans&d=list');
					add_path_current();
					$subscription=new TSubscription(intval($_getvars['id']));
					if ($subscription->Exists() && $subscription->CompanyID()==TCompany::CurrentCompany()->ID() && !$subscription->isCancelled())
						{
							if ($subscription->Plan()->isPLanTypePackage())
								{
									sm_add_body_class('large-submenu');
								}
							$subscription->Cancel();
							sm_redirect($_getvars['returnto']);
						}
				}
			if (sm_actionpost('postsetdiscount'))
				{
					$subscription=new TSubscription(intval($_getvars['id']));
					if ($subscription->Exists() && $subscription->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							$amount=Cleaner::FloatMoney($_postvars['discount']);
							if ($amount<0)
								$error_message='Wrong discount';
							elseif (Cleaner::FloatMoney($_postvars['discount'])>$subscription->Plan()->Price())
								$error_message='Discount is greater then price';
							if (empty($error_message))
								{
									$subscription->SetDiscount($amount);
									sm_redirect($_getvars['returnto']);
								}
							if (!empty($error_message))
								sm_set_action('setdiscount');
						}
				}
			if (sm_action('setdiscount'))
				{
					add_path_home();
					if ($_getvars['type'] == 'package')
						add_path('Packages', 'index.php?m=packagesmgmt');
					else
						add_path('Plans', 'index.php?m=plans&d=list');
					add_path_current();
					$subscription=new TSubscription(intval($_getvars['id']));
					if ($subscription->Exists() && $subscription->CompanyID()==TCompany::CurrentCompany()->ID())
						{
							if ($subscription->Plan()->isPLanTypePackage())
								{
									sm_add_body_class('large-submenu');
								}
							sm_use('ui.interface');
							sm_use('ui.form');
							sm_title('Set Subscription Discount');
							if (!empty($error_message))
								$ui->NotificationError($error_message);
							$ui = new TInterface();
							$f = new TForm('index.php?m='.sm_current_module().'&d=postsetdiscount&id='.$subscription->ID().'&returnto='.urlencode($_getvars['returnto']));
							$f->AddText('discount', 'Discount')
								->WithValue($subscription->Discount())
								->SetFocus();
							$ui->Add($f);
							$ui->Output(true);
						}
				}
			if (sm_actionpost('postsetquantity'))
				{
					add_path_home();
					if ($_getvars['type'] == 'package')
						add_path('Packages', 'index.php?m=packagesmgmt');
					else
						add_path('Plans', 'index.php?m=plans&d=list');
					add_path_current();
					$subscription=new TSubscription(intval($_getvars['id']));
					if ($subscription->Exists() && $subscription->CompanyID()==TCompany::CurrentCompany()->ID() && $subscription->Plan()->HasMultipleItems())
						{
							$quantity=intval($_postvars['quantity']);
							if ($quantity<$subscription->Plan()->MinAvailableQuantity() || $quantity>$subscription->Plan()->MaxAvailableQuantity())
								$error_message='Wrong quantity';
							if (empty($error_message))
								{
									$subscription->SetPlanQuantity($quantity);
									sm_redirect($_getvars['returnto']);
								}
							if (!empty($error_message))
								sm_set_action('setdiscount');
						}
				}
			if (sm_action('setquantity'))
				{
					$subscription=new TSubscription(intval($_getvars['id']));
					if ($subscription->Exists() && $subscription->CompanyID()==TCompany::CurrentCompany()->ID() && $subscription->Plan()->HasMultipleItems())
						{
							if ($subscription->Plan()->isPLanTypePackage())
								{
									sm_add_body_class('large-submenu');
								}
							sm_use('ui.interface');
							sm_use('ui.form');
							sm_title('Set Subscription Quantity');
							if (!empty($error_message))
								$ui->NotificationError($error_message);
							$ui = new TInterface();
							$f = new TForm('index.php?m='.sm_current_module().'&d=postsetquantity&id='.$subscription->ID().'&returnto='.urlencode($_getvars['returnto']));
							$qty_values=array();
							$qty_labels=array();
							for ($i = $subscription->Plan()->MinAvailableQuantity(); $i <= $subscription->Plan()->MaxAvailableQuantity(); $i+=$subscription->Plan()->StepForAvailableQuantity())
								{
									$qty_values[]=$i;
									$qty_labels[]=$i.' '.$subscription->Plan()->TitleForItemQuantity().' - $'.Formatter::Money($subscription->Plan()->Price()*$i);
								}
							$f->AddSelectVL('quantity', 'Quantity', $qty_values, $qty_labels)
								->WithValue($subscription->PlanQuantity())
								->SetFocus();
							$ui->Add($f);
							$ui->Output(true);
						}
				}
			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					sm_use('ui.buttons');
					sm_use('ui.fa');
					add_path_home();
					if ($_getvars['type'] == 'package')
						add_path('Packages', 'index.php?m=globalsettings&d=packagesmgmt');
					else
						add_path('Plans', 'index.php?m=plans&d=list');

					add_path('Subscriptions', 'index.php?m=subscriptionsmgmt&d=list');
					sm_title('Subscriptions');
					$offset=abs(intval($_getvars['from']));
					$limit=30;
					$ui = new TInterface();

					$ui->html('<div class="additional-buttons-section">');

					$b = new TButtons();

					$t=new TGrid();
					$t->AddCol('id', 'ID', '2%');
					$t->HeaderDropDownItem('id', 'Sort 1-10', sm_this_url(['orderby'=>'idasc', 'from'=>'']));
					$t->HeaderDropDownItem('id', 'Sort 10-1', sm_this_url(['orderby'=>'iddesc', 'from'=>'']));
					$t->AddCol('name', 'Name');
					$t->HeaderDropDownItem('name', 'Sort A-Z', sm_this_url(['orderby'=>'nameasc', 'from'=>'']));
					$t->HeaderDropDownItem('name', 'Sort Z-A', sm_this_url(['orderby'=>'namedesc', 'from'=>'']));
					$t->AddCol('contacts', 'Contacts');
					$t->AddCol('address', 'Address');
					$t->AddCol('plan', 'Plan');
					$t->AddCol('price', 'Price');
					$t->AddCol('actions', 'Actions');
					$t->AddCol('status', 'Status');
					$t->AddCol('info', '', '10');

					$subscriptions=new TSubscriptionList();
					$subscriptions->SetFilterCompany(TCompany::CurrentCompany());
					if(!empty($_getvars['plan']))
						$subscriptions->SetFilterPlan(intval($_getvars['plan']));
					if ($_getvars['orderby']=='idasc')
						$subscriptions->OrderByID();
					elseif ($_getvars['orderby']=='iddesc')
						$subscriptions->OrderByID(false);
					elseif ($_getvars['orderby']=='namedesc')
						$subscriptions->OrderByName(false);
					else
						$subscriptions->OrderByName();

					if ($_getvars['type'] == 'package')
						{
							sm_add_body_class('large-submenu');
							$plans = new TPlanList();
							$plans->SetFilterTypePackage();
							$plans->Load();
							$subscriptions->SetFilterPackageIDs($plans->ExtractIDsArray());
						}
					else
						{
							$plans = new TPlanList();
							$plans->SetFilterTypePackage();
							$plans->Load();
							$subscriptions->SetFilterExcludePackageIDs($plans->ExtractIDsArray());
						}

					$subscriptions->Load();
					for ($i = 0; $i<$subscriptions->Count(); $i++)
						{
							$subscription=$subscriptions->Item($i);
							$t->Label('id', $subscription->ID());
							$t->Label('name', $subscription->ContactName());
							$t->LabelAppend('contacts', '<div>'.$subscription->Email().'</div>');
							if ($subscription->HasCellphone())
								$t->LabelAppend('contacts', '<div>'.Formatter::USPhone($subscription->Cellphone()).'</div>');
							$t->Label('address', $subscription->AddressFormatted());
							$t->Label('plan', $subscription->Plan()->Title());
							if ($subscription->Plan()->HasMultipleItems())
								$t->AppendCellFooterHTML('plan', '<br /><div class="label label-info">'.$subscription->PlanQuantity().' '.$subscription->Plan()->TitleForItemQuantity().'</div>');
							if ($subscription->HasDiscount())
								{
									$t->LabelAppend('plan', ' <div class="label label-info">Discount</div>');
									$t->LabelAppend('price', '<div>'.Formatter::Money(round($subscription->Plan()->Price()*$subscription->PlanQuantity(), 2)-$subscription->Discount()).'</div>');
									$t->LabelAppend('price', '<div style="text-decoration:line-through;">'.Formatter::Money(round($subscription->Plan()->Price()*$subscription->PlanQuantity(), 2)).'</div>');
								}
							else
								$t->Label('price', Formatter::Money(round($subscription->Plan()->Price()*$subscription->PlanQuantity(), 2)));
							if ($subscription->isCancelled())
								{
									$t->Label('status', 'Cancelled');
									$t->CellHighlightError('status');
								}
							else
								{
									if ($subscription->UnsuccessfulPaymentsCount()>0)
										{
											$t->Label('status', 'Past Due');
											$t->CellHighlightAttention('status');
										}
									else
										{
											$t->Label('status', 'Active');
											$t->CellHighlightSuccess('status');
										}
									$t->DropDownItem('actions', 'Set Discount', 'index.php?m='.sm_current_module().'&d=setdiscount&id='.$subscription->ID().($subscription->Plan()->isPLanTypePackage()?'&type=package':'').'&returnto='.urlencode(sm_this_url()));
									if ($subscription->Plan()->HasMultipleItems())
										$t->DropDownItem('actions', 'Set Quantity', 'index.php?m='.sm_current_module().'&d=setquantity&id='.$subscription->ID().($subscription->Plan()->isPLanTypePackage()?'&type=package':'').'&returnto='.urlencode(sm_this_url()));
									$t->DropDownItem('actions', 'Update Card', 'index.php?m='.sm_current_module().'&d=updatecardurl&id='.$subscription->ID().($subscription->Plan()->isPLanTypePackage()?'&type=package':'').'&returnto='.urlencode(sm_this_url()));
									$t->DropDownItem('actions', 'Cancel', 'index.php?m='.sm_current_module().'&d=cancel&id='.$subscription->ID().($subscription->Plan()->isPLanTypePackage()?'&type=package':'').'&returnto='.urlencode(sm_this_url()), 'Are you sure?');
								}
							$t->Label('actions', 'Actions');
							$t->Label('info', FA::EmbedCodeFor('info-circle'));
							$t->Expand('info');
							$data=Array();
							$data[]='Subscribed: '.Formatter::DateTime($subscription->AddedTimestamp());
							if ($subscription->LastSuccessfulPaymentTimestamp()>0)
								$data[]='Last successful payment: '.Formatter::DateTime($subscription->LastSuccessfulPaymentTimestamp()).' ('.Formatter::DurationDH(time()-$subscription->LastSuccessfulPaymentTimestamp()).')';
							if ($subscription->ExpirationTimestamp()>0)
								$data[]='Expiration: '.Formatter::DateTime($subscription->ExpirationTimestamp());
							$data[]='Billing cycles paid: '.$subscription->BillingCyclesPaid();
							if ($subscription->UnsuccessfulPaymentsCount()>0)
								$data[]='Unsuccessful payments count: '.$subscription->UnsuccessfulPaymentsCount();
							$data[]='Stripe Customer ID: '.$subscription->StripeCustomerID();
							$data[]='Stripe Subscription ID: '.$subscription->StripeSubscriptionID();
							if ($subscription->HasCouponStripeID())
								$data[]='Stripe Coupon ID: '.$subscription->CouponStripeID();
							if ($subscription->HasCouponCode())
								$data[]='Coupon Code: '.$subscription->CouponCode();
							$t->ExpanderHTML(implode('<br />', $data));
							$t->NewRow();
							unset($subscription);
						}
					if ($t->RowCount()==0)
						$t->SingleLineLabel('Nothing found');
					$ui->AddGrid($t);
					$ui->AddPagebarParams($subscriptions->TotalCount(), $limit, $offset);
					$ui->Output(true);
				}

		}
	else
		sm_redirect(sm_homepage());

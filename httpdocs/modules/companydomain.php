<?php

	include_once 'includes/mailjet.php';
	use ParseRoute\Route as MailJet;

	if ($userinfo['level'] == 3)
		{
			sm_default_action('view');

			if (sm_action('getroutes'))
				{
					$apikey = sm_settings('mailjet_api_key');
					$apisecret = sm_settings('mailjet_api_secret');

					$mail = new MailJet($apikey, $apisecret);
					$routeInfo = $mail->getRoutes();
					print_r($routeInfo);
				}

			if (sm_action('postaddemail'))
				{
					if (empty(trim($_postvars['sender_email'])))
						$error_message = 'Fill required fields';
					elseif (empty(TCompany::CurrentCompany()->CompanyEmailDomain()))
						$error_message = 'Domain is empty';
					elseif (!TCompany::CurrentCompany()->isCompanyEmailActive())
						$error_message = 'Domain name wasn\'t activated yet';

					$company_domain = trim($_postvars['sender_email']) . '@' . TCompany::CurrentCompany()->CompanyEmailDomain();

					if ( empty($error_message) && ( !TCompany::CurrentCompany()->HasCompanyEmailParseRouteURL() || !TCompany::CurrentCompany()->HasCompanyEmailParseRouteID()) )
						{

							$apikey = sm_settings('mailjet_api_key');
							$apisecret = sm_settings('mailjet_api_secret');

							$mail = new MailJet($apikey, $apisecret);
							$webhookURL = sm_homepage() . 'index.php?m=receivecompanymailjetwebhook&id=' . TCompany::CurrentCompany()->ID();
							$routeInfo = $mail->addRoute($company_domain, $webhookURL);
							if ($routeInfo && is_array($routeInfo) && isset($routeInfo['Data'][0]['ID']))
								{
									// route added successfully
									$route_id = $routeInfo['Data'][0]['ID'];
									TCompany::CurrentCompany()->SetCompanyEmailParseRouteID($route_id);
									TCompany::CurrentCompany()->SetCompanyEmailParseRouteURL($routeInfo['Data'][0]['Url']);

									// if route added succesfully then change the status of verification in db
									$senderDetails = $mail->getSender(TCompany::CurrentCompany()->CompanyEmailDomainID());
									if (isset($senderDetails['Data'][0]['Status']))
										TCompany::CurrentCompany()->SetCompanyEmailStatus($senderDetails['Data'][0]['Status']);

									sm_notify('Domain has been verified.');
								}
							elseif ($routeInfo && !is_array($routeInfo))
								{
									// set error msg recived from mail jet
									$error_message = $routeInfo;
								}
						}
					elseif ( empty($error_message) && ( TCompany::CurrentCompany()->HasCompanyEmailParseRouteURL() || TCompany::CurrentCompany()->HasCompanyEmailParseRouteID()) && $company_domain != TCompany::CurrentCompany()->EmailFrom())
						{
							$apikey = sm_settings('mailjet_api_key');
							$apisecret = sm_settings('mailjet_api_secret');

							$mail = new MailJet($apikey, $apisecret);
							$webhookURL = sm_homepage() . 'index.php?m=receivecompanymailjetwebhook&id=' . TCompany::CurrentCompany()->ID();
							$routeInfo = $mail->updateRoute(TCompany::CurrentCompany()->CompanyEmailParseRouteID(), $webhookURL, $company_domain);
						}

					if ( empty($error_message) )
						{
							TCompany::CurrentCompany()->SetEmailFrom($company_domain);
							sm_notify('Company email has been added.');
							sm_redirect('index.php?m=' . sm_current_module());
						}
					else
						sm_set_action('editemail');
				}

			if (sm_action('postdelete'))
				{
					$error_message = '';

					$apikey = sm_settings('mailjet_api_key');
					$apisecret = sm_settings('mailjet_api_secret');

					if ( $apikey && $apisecret )
						{
							if (TCompany::CurrentCompany()->HasCompanyEmailDomainID())
								{
									$mail = new MailJet($apikey, $apisecret);
									$deleteSender = $mail->deleteSender(TCompany::CurrentCompany()->CompanyEmailDomainID());
									if (TCompany::CurrentCompany()->HasCompanyEmailParseRouteID())
										$mail->deleteRoute(TCompany::CurrentCompany()->CompanyEmailParseRouteID());

									TCompany::CurrentCompany()->SetCompanyEmailDnsID('');
									TCompany::CurrentCompany()->SetCompanyEmailDomainID('');
									TCompany::CurrentCompany()->SetCompanyEmailStatus('');
									TCompany::CurrentCompany()->SetCompanyEmailDomain('');
									TCompany::CurrentCompany()->SetCompanyEmailOwnerShipToken('');
									TCompany::CurrentCompany()->SetCompanyEmailOwnerShipTokenRecordName('');
									TCompany::CurrentCompany()->SetCompanyEmailParseRouteID('');
									TCompany::CurrentCompany()->SetCompanyEmailParseRouteURL('');
									TCompany::CurrentCompany()->SetCompanyEmailSPFRecordValue('');
									TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordName('');
									TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordValue('');

									sm_notify('Domain has been deleted.');
									sm_redirect('index.php?m=' . sm_current_module());
								}
							else
								$error_message = "Domain does not exist";

							sm_redirect($_getvars['returnto']);

						}
					else
						$error_message = "API key not found";

					sm_set_action('view');
				}

			if (sm_action('verifydns'))
				{
					$error_message = '';

					if (TCompany::CurrentCompany()->HasCompanyEmailDomainID())
						{
							$apikey = sm_settings('mailjet_api_key');
							$apisecret = sm_settings('mailjet_api_secret');

							$mail = new MailJet($apikey, $apisecret);
							$result = $mail->validateSender(TCompany::CurrentCompany()->CompanyEmailDomainID());

							if ( $mail->HasError() && $mail->ErrorMessage() != 'The sender is already active!')
								$error_message = $mail->ErrorMessage();
							else
								{
									$routeInfo = dns_get_record(TCompany::CurrentCompany()->CompanyEmailDomain(), DNS_MX);

									if (empty($routeInfo) || $routeInfo[0]['host'] != TCompany::CurrentCompany()->CompanyEmailDomain() || $routeInfo[0]['target'] != 'parse.mailjet.com' )
										$error_message = 'MX Record Does not exist.';
								}

							if (empty($error_message))
								{
									$dnsInfo = $mail->dns_check(TCompany::CurrentCompany()->CompanyEmailDnsID());
									if ( is_array($dnsInfo) )
										{
											if ( strtolower($dnsInfo['Data'][0]['DKIMStatus']) != 'ok' )
												$error_message = 'DKIM validation failed. Check your CNAME record';
											else
												TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordStatus($dnsInfo['Data'][0]['DKIMStatus']);

											if ( strtolower($dnsInfo['Data'][0]['SPFStatus']) != 'ok' )
												$error_message = 'SPF validation failed. Check your TXT record';
											else
												TCompany::CurrentCompany()->SetCompanyEmailSPFRecordStatus($dnsInfo['Data'][0]['SPFStatus']);
										}
									else
										$error_message = $dnsInfo;

								}
						}
					else
						$error_message = 'Domain Record Does not exist.';

					if ( empty($error_message) )
						{
							sm_notify('Domain was added');
							TCompany::CurrentCompany()->SetCompanyEmailStatus('Active');
							sm_redirect('index.php?m='.sm_current_module().'&d=editemail');
						}
					else
						sm_set_action('view');
				}


			if (sm_action('postadd'))
				{
					$error_message = '';

					$_postvars['domain'] = trim($_postvars['domain']);

					if (TCompany::CurrentCompany()->HasCompanyEmailDomain())
						$error_message = 'Already One Domain Exist!';

					if (empty($_postvars['domain']))
						$error_message = 'Fill required fields';

					if (empty($error_message))
						$error_message = url_validator($_postvars['domain']);

					if (empty($error_message))
						{
							$apikey = sm_settings('mailjet_api_key');
							$apisecret = sm_settings('mailjet_api_secret');

							$mail = new MailJet($apikey, $apisecret);
							$domainEmail = '*@' . trim($_postvars['domain']);
							$result = $mail->create('', $domainEmail);

							if ( is_array($result) )
								{
									TCompany::CurrentCompany()->SetCompanyEmailDnsID($result['Data'][0]['DNSID']);
									TCompany::CurrentCompany()->SetCompanyEmailDomainID($result['Data'][0]['ID']);
									TCompany::CurrentCompany()->SetCompanyEmailStatus($result['Data'][0]['Status']);
									$dnsInfo = $mail->dns(TCompany::CurrentCompany()->CompanyEmailDnsID());

									if ( is_array($dnsInfo) )
										{
											TCompany::CurrentCompany()->SetCompanyEmailDomain($dnsInfo['Data'][0]['Domain']);
											TCompany::CurrentCompany()->SetCompanyEmailSPFRecordValue($dnsInfo['Data'][0]['SPFRecordValue']);
											TCompany::CurrentCompany()->SetCompanyEmailSPFRecordStatus($dnsInfo['Data'][0]['SPFStatus']);
											TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordName($dnsInfo['Data'][0]['DKIMRecordName']);
											TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordValue($dnsInfo['Data'][0]['DKIMRecordValue']);
											TCompany::CurrentCompany()->SetCompanyEmailDKIMRecordStatus($dnsInfo['Data'][0]['DKIMStatus']);
											TCompany::CurrentCompany()->SetCompanyEmailOwnerShipToken($dnsInfo['Data'][0]['OwnerShipToken']);
											TCompany::CurrentCompany()->SetCompanyEmailOwnerShipTokenRecordName($dnsInfo['Data'][0]['OwnerShipTokenRecordName']);

											sm_notify('Domain has been added.');
											sm_redirect($_getvars['returnto']);
										}
									else
										$error_message = $dnsInfo;
								}
							else
								$error_message = $result;
						}
					sm_set_action('view');
				}

			if (sm_action('editemail'))
				{
					sm_title('Edit Company Email');

					add_path_home();

					add_path_current();

					sm_use('ui.interface');
					sm_use('ui.grid');

					$ui = new TInterface();

					if (!empty($error_message))
						$ui->NotificationError($error_message);

					$ui->h('4', 'Add Sender Email');
					sm_use('ui.form');
					$f = new TForm('index.php?m=' . sm_current_module() . '&d=postaddemail&returnto=' . urlencode($_getvars['returnto']));
					$f->AddText('sender_email', 'Sender Email', true);

					if (strpos(TCompany::CurrentCompany()->EmailFrom(), '@'.TCompany::CurrentCompany()->CompanyEmailDomain()) !== false)
						$f->SetValue('sender_email', str_replace('@'.TCompany::CurrentCompany()->CompanyEmailDomain(), '', TCompany::CurrentCompany()->EmailFrom()));

					$f->SetFieldEndText('sender_email', '@' . TCompany::CurrentCompany()->CompanyEmailDomain());
					$f->LoadValuesArray($_postvars);
					$ui->AddForm($f);
					$ui->style('#sender_email {width:60%;}');

					$ui->Output(true);
				}

			if (sm_action('view'))
				{
					sm_title('Domain');

					add_path_home();
					add_path_current();

					sm_use('ui.interface');
					sm_use('ui.grid');
					sm_use('ui.buttons');

					$ui = new TInterface();

					if (!empty($error_message))
						$ui->NotificationError($error_message);

					if (!TCompany::CurrentCompany()->HasCompanyEmailDomain())
						{
							$ui->h('4', 'Add Domain');
							sm_use('ui.form');
							$f = new TForm('index.php?m=' . sm_current_module() . '&d=postadd&returnto=' . urlencode('index.php?m='.sm_current_module().'&d=view'));
							$f->AddText('domain', 'Domain', true)->SetFieldBottomText('domain', 'Enter only domain name without http or www For example: google.com')->SetFocus();

							if (is_array($_postvars) && count($_postvars) > 0 )
								$f->LoadValuesArray($_postvars);

							$ui->AddForm($f);
						}
					elseif (TCompany::CurrentCompany()->CompanyEmailStatus() != 'Active')
						{
							// if email status is not active then show verify page with dns details

							$ui->h('4', 'Create TXT DNS Record');
							$ui->p('If you manage ' . TCompany::CurrentCompany()->CompanyEmailDomain() . ' and you have access to your DNS records, you can create a  new TXT record with the following values.');
							$t = new TGrid();
							$t->AddCol('key', 'Key');
							$t->AddCol('value', 'Value');

							$t->Label('key', 'Host name');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailOwnerShipTokenRecordName());
							$t->NewRow();
							$t->Label('key', 'Value');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailOwnerShipToken());
							$t->NewRow();
							$ui->AddGrid($t);

							$ui->h('4', 'Create TXT DNS Record');

							$t = new TGrid();
							$t->AddCol('key', 'Key');
							$t->AddCol('value', 'Value');

							$t->Label('key', 'Host name');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailDomain().'.');
							$t->NewRow();
							$t->Label('key', 'Value');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailSPFRecordValue());
							$t->NewRow();
							$ui->AddGrid($t);

							$ui->h('4', 'Create TXT DNS Record');

							$t = new TGrid();
							$t->AddCol('key', 'Key');
							$t->AddCol('value', 'Value');

							$t->Label('key', 'Host name');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailDKIMRecordName());
							$t->NewRow();
							$t->Label('key', 'Value');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailDKIMRecordValue());
							$t->CellAddStyle('value', 'word-break: break-all;');
							$t->NewRow();
							$ui->AddGrid($t);

							$ui->h('4', 'Create MX DNS Record');
							$ui->p('Add an MX entry on the domain or subdomain DNS to receive emails on your own domain name');
							$t = new TGrid();
							$t->AddCol('key', 'Key');
							$t->AddCol('value', 'Value');

							$t->Label('key', 'Host name');
							$t->Label('value', TCompany::CurrentCompany()->CompanyEmailDomain());
							$t->NewRow();
							$t->Label('key', 'Value');
							$t->Label('value', '10 parse.mailjet.com. (final dot is important)');
							$t->NewRow();

							$t->SingleLineLabel('<img src="themes/default/images/gdomainexample.jpg" style="max-width:800px;"/>');
							$t->NewRow();

							$ui->AddGrid($t);

							$ui->a('index.php?m=' . sm_current_module() . '&d=verifydns&returnto=' . urlencode(sm_this_url()), 'Verify', '', 'ab-button');
							$ui->a('index.php?m=' . sm_current_module() . '&d=postdelete&returnto=' . urlencode(sm_this_url()), 'Delete', '', 'ab-button', '', " return confirm('Are you sure?')");
						}
					else
						{
							$ui->h('4', 'Domain Details');
							$t = new TGrid();
							$t->AddCol('company_email', 'Company Email');
							$t->AddCol('company_domain', 'Sender Domain');
							$t->AddEdit();
							$t->AddDelete();

							$t->Label('company_email', TCompany::CurrentCompany()->EmailFrom());
							$t->Label('company_domain', TCompany::CurrentCompany()->CompanyEmailDomain());
							$t->URL('edit', 'index.php?m=' . sm_current_module() . '&d=editemail&id=&returnto=' . urlencode(sm_this_url()));
							$t->URL('delete', 'index.php?m=' . sm_current_module() . '&d=postdelete&id=&returnto=' . urlencode(sm_this_url()));

							$ui->AddGrid($t);
						}
					$ui->Output(true);
				}
		}
	else
		sm_redirect('index.php?m=dashboard');
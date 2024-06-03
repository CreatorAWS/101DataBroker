<?php

	sm_default_action('create');

	if ( sm_action('create') )
		{
			header("HTTP/1.1 200 OK");
			$entityBody = $_POST;
			if ( !empty($entityBody) )
				{
					$data = $entityBody;
					sm_extcore();
					/** @var TCompany $company */

					sm_log('package_purchase', 1, print_r($data, true));

					if ( $data['webhook'] == 'purchase-create')
						{
							if (!empty($data['contacts']['email']))
								{
									$usr1=sm_userinfo($data['contacts']['email'], 'email');
								}

							if (empty($usr1['id']))
								{
									$company = TCompany::Create();
									if (!$company->Exists())
										exit('Error - Company is not created');
								}
							else
								{
									$company = new TCompany(intval($usr1['info']['id_company']));
									if (!$company->Exists() )
										exit('Error - Company is not exists');
								}

							if ( is_object($company) && $company->Exists() && !empty($data['purchase']['id']) )
								{
									$purchase = new TPurchase($data['purchase']['id']);
									if ($purchase->Exists())
										{
											$product = new TProduct($purchase->ProductID());
											if ($product->Exists())
												{
													if ($product->HasStateSearch())
														$company->EnableStatesSearch();
													else
														$company->DisableStatesSearch();

													if ($product->HasSicSearch())
														$company->EnableSicCodesSearch();
													else
														$company->DisableSicCodesSearch();

													if ($product->HasGoogleSearch())
														$company->EnableGoogleSearch();
													else
														$company->DisableGoogleSearch();

													if ($product->HasBuiltWithSearch())
														$company->EnableBuiltWithSearch();
													else
														$company->DisableBuiltWithSearch();
												}

											if (!empty($data['contacts']['first_name']) && !empty($data['contacts']['last_name']))
												$company->SetName($data['contacts']['first_name'].' '.$data['contacts']['last_name']);
											else
												$company->SetName($data['contacts']['email']);

											if(!empty($product->Interval()))
												{
													if ($product->Interval() == 'day')
														$company->SetExpirationTimestamp(time() + 86400);
													elseif ($product->Interval() == 'week')
														$company->SetExpirationTimestamp(time() + 604800);
													elseif ($product->Interval() == 'month')
														$company->SetExpirationTimestamp(time() + 2592000);
													elseif ($product->Interval() == 'year')
														$company->SetExpirationTimestamp(time() + 31536000);
												}

											if(!empty($data['purchase']['password']))
												$m['password'] = $data['purchase']['password'];
											else
												$m['password'] = substr(md5(microtime()), 0, 8);
											if(!empty($data['address']['line1']))
												$company->SetAddress($data['address']['line1']);
											if(!empty($data['address']['line2']))
												$company->SetAddress2($data['address']['line2']);
											if(!empty($data['address']['city']))
												$company->SetCity($data['address']['city']);
											if(!empty($data['address']['state']))
												$company->SetState($data['address']['state']);
											if(!empty($data['address']['zip']))
												$company->SetZip($data['address']['zip']);

											if (empty($usr1['id']))
												$user_id = sm_add_user( $data['contacts']['email'], $m['password'], $data['contacts']['email']);

											$employee = new TEmployee($user_id);
											$employee->SetCompanyID($company->ID());
											$employee->SetFirstName($data['contacts']['first_name']);
											$employee->SetLastName($data['contacts']['last_name']);
											$employee->SetCellphone($data['contacts']['cellphone']);
											//--------------------------------------------------
											$subject="Login details";
											$message="Hello ".$data['contacts']['first_name'].",
															<br><br>This is your login details for ".main_domain()."
															<br><a href='https://".main_domain()."'>Link to your dashboard</a>
															<br>Login: ".$employee->Email()."<br>Password: ".$m['password']
											;
											//--------------------------------------------------
											$employee->SendEmail($subject, $message);
										}
								}
						}
				}
			exit;

		}
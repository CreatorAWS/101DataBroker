<?php

	if (System::LoggedIn() && !empty($_getvars['id']))
		{
			$customer = new TCustomer(intval($_getvars['id']));
			if (!$customer->Exists() || $customer->isDeleted())
				exit('Access Denied!');

			$m['module'] = sm_current_module();

			sm_use('ui.interface');
			sm_use('ui.grid');
			sm_use('ui.form');
			sm_use('ui.buttons');
			use_api('smdatetime');

			$ui = new TInterface();

			if ($customer->Exists() && TCompany::CurrentCompany()->ID()==$customer->CompanyID())
				{
					if (sm_action('notes'))
						{
							$m['show_conversation_send_message'] = true;
							$m['initjs']="var dash_url='';dash_show_note(".$customer->ID().");";
							$ui->html('<link rel="stylesheet" href="themes/current/conversation.css" type="text/css" />');
							$ui->html('<script type="text/javascript" src="themes/default/notes.js"></script>');

							$ui->html('<style>.notes-date-info{display:none;} .rd-dash-conversation-view > .px-4, .row > .px-4{padding-left: 0!important;}</style>');
							$ui->html("<script type='text/javascript'>dash_show_note(".$customer->ID().");</script>");
							if ($m['show_conversation_send_message'])
								{
									$ui->html('<div class="col-md-12">');
									$ui->html('<p class="text-base text-gray-500 mt-3 ">Notes</p>');
									$ui->html('<script type="text/javascript">'.$m['initjs'].'</script>');
									$ui->html('<section class="customer-notes" aria-labelledby="notes-title"><div class="bg-white sm:rounded-lg sm:overflow-hidden"><div class="divide-y divide-gray-200"><div><div class="row"><div class="col-md-12 rd-dash-conversation-view"></div></div>');
									$ui->html('<div class="row">
											<div class="col-md-12 rd-dash-conversation-answer-loading" style="display:none;">Loading...</div>
											<div class="flex space-x-3">
												<div class="col-md-12 rd-dash-conversation-answer popup-notes-form" style="display:none;">
													<div>
														<textarea id="dashboard-conversation-text" name="comment" rows="3" class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md" placeholder="Add a note"></textarea>
													</div>
													<div class="mt-3 flex items-center justify-between">
														<span></span>
														<button onclick="dash_send_note()" type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
															Comment
														</button>
													</div>
												</div>
											</div>');
									$ui->html('</div></div></section></div>');
								}
							$m['uipanel']=$ui->Output();
						}

					if (sm_action('call'))
						{
							$b = new TButtons();
							if ($customer->HasCellphone())
								{
                                    $ui->html('<div class="col-md-8 phone_call_section">');
                                    $ui->html('<div class="call_icon"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3h5m0 0v5m0-5l-6 6M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" /></svg></div>');

                                    if (!empty($customer->FirstName()) || !empty($customer->LastName()))
                                    	$ui->html('<h3 class="name">'.$customer->Name().'</h3>');
                                    else
										$ui->html('<h3 class="name">'.$customer->GetBusinessName().'</h3>');

									if ($customer->HasCellphone())
                                    	$ui->html('<p class="phone_icon"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 float-left mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>'.Formatter::USPhone($customer->Cellphone()).'</p>');
									if ($customer->HasEmail())
										$ui->html('<p class="phone_icon"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 float-left mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'.$customer->Email().'</p>');


									$ui->html('<div id="controls"><div id="call-info" style="display: block;">Connecting...</div><div id="call-controls" style="width: 100%;"><button id="button-hangup" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm" style="display: none;">Hangup</button></div><div id="log" style="display: none"></div></div>');
									$ui->html('</div>');

									$m['show_conversation_send_message'] = true;
									$m['initjs']="var dash_url='';dash_show_note(".$customer->ID().");";
									$ui->html('<link rel="stylesheet" href="themes/current/conversation.css" type="text/css" />');
									$ui->html('<script type="text/javascript" src="themes/default/notes.js"></script>');

									$ui->html('<style>.notes-date-info{display:none;} .rd-dash-conversation-view > .px-4, .row > .px-4{padding-left: 0!important;}</style>');
									$ui->html("<script type='text/javascript'>dash_show_note(".$customer->ID().");</script>");
									if ($m['show_conversation_send_message'])
										{
											$ui->html('<div class="col-md-12">');
											$ui->html('<p class="text-base text-gray-500 mt-3 ">Notes</p>');
											$ui->html('<script type="text/javascript">'.$m['initjs'].'</script>');
											$ui->html('<section class="customer-notes" aria-labelledby="notes-title"><div class="bg-white sm:rounded-lg sm:overflow-hidden"><div class="divide-y divide-gray-200"><div><div class="row"><div class="col-md-12 rd-dash-conversation-view"></div></div>');
											$ui->html('<div class="row">
											<div class="col-md-12 rd-dash-conversation-answer-loading" style="display:none;">Loading...</div>
											<div class="flex space-x-3">
												<div class="col-md-12 rd-dash-conversation-answer popup-notes-form" style="display:none;">
													<div>
														<textarea id="dashboard-conversation-text" name="comment" rows="3" class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md" placeholder="Add a note"></textarea>
													</div>
													<div class="flex items-center justify-between">
														<span></span>
														<button onclick="dash_send_note()" type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
															Comment
														</button>
													</div>
												</div>
											</div>');
											$ui->html('</div></div></section></div>');
										}

                                    $ui->html('<script>
									$(\'#messagemodal\').on(\'hidden.bs.modal\', function () {
										device.disconnectAll();
									});
									
									$( "#button-hangup" ).click(function() {
										log(\'Hanging up...\');
										$(\'#button-hangup\').css(\'display\', \'none\');
										device.disconnectAll();
										$(\'#messagemodal\').modal(\'hide\');
									});
									</script>');
								}
							else
								$ui->NotificationWarning('This '.TCompany::CurrentCompany()->LabelForCustomer().' doesn\'t have phone number');
							$m['uipanel']=$ui->Output();
						}
				}

		}
	elseif ($userinfo['level'] == 0)
		sm_redirect('index.php?m=account');
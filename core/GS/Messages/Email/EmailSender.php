<?php /** @noinspection PhpUnhandledExceptionInspection */

	namespace GS\Messages\Email;

	use Exception;
	use Mailjet\Resources;
	use NylasConnector;
	use TAttachment;
	use TCompany;
	use TEmail;
	use TEmployee;
	use TSupportMessage;

	class EmailSender
		{

			//TODO: rewrite
			public static function SendEmail($from, $to, $subject, $message, $attachment='', $company_name='', $sendnylas = true, $id_email = '', $support_message = '', $id_employee = 0, $cc = '', $bcc = '')
				{
					$sent = 0;
					sm_settings('resource_title');
					if (strpos($from, '@'.mail_domain())!==false || true)
						{
							if($from == 'noreply@'.main_domain())
								{
									if (!sm_empty_settings('sendgrid_api_key'))
										$apikey_sendgrid = sm_settings('sendgrid_api_key');
									else
										{
											$apikey = sm_settings('mailjet_api_key');
											$apisecret = sm_settings('mailjet_api_secret');
										}
								}
							else
								{
									if ($sendnylas)
										{
											$employee = TEmployee::initWithEmailNotDeleted( $from );
											if ($employee->Exists() && $employee->HasEmailAccount())
												{
													sm_use('nylasfunctions');
													$email = new NylasConnector($employee->ID(), $employee->CompanyID());
													$message_response = $email->SendMessage($to, $subject, $message, $id_email, $cc, $bcc);
													if (!empty($message_response[0]['error']))
														{
															$sendnylas = false;
															$sent = 0;
														}
													else
														$sent = 1;
												}
										}

									if ( !$sendnylas && empty($sent) )
										{
											$company_email = str_replace('support.', '', $from);
											$company = TCompany::initWithEmail($company_email);
											if (is_object($company) && $company->Exists() && $company->HasEmailFrom() && $company->HasSendGridApiKey())
												{
													$apikey_sendgrid = $company->SendGridApiKey();
												}
											elseif (!sm_empty_settings('sendgrid_api_key'))
												{
													$apikey_sendgrid = sm_settings('sendgrid_api_key');
												}
											else
												{
													if (is_object($company) && $company->Exists() && $company->HasEmailFrom() && $company->HasMailjetApiSecret())
														{
															$apikey = $company->MailjetApiKey();
															$apisecret = $company->MailjetApiSecret();
															$company_name = $company->Name();
														}
													else
														{
															$apikey = sm_settings('mailjet_api_key');
															$apisecret = sm_settings('mailjet_api_secret');
														}
												}
										}
								}

							if(empty($company_name))
								$company_name = sm_settings('resource_title');

							if ( empty($sent) )
								{
									if (!empty($apikey_sendgrid))
										{
											$email = new \SendGrid\Mail\Mail();
											$email->setFrom($from, $company_name);
											$email->setSubject($subject);
											$email->addTo($to);
											$email->addContent(
												"text/html", $message
											);
											$sendgrid = new \SendGrid($apikey_sendgrid);
											try
												{
													$response = $sendgrid->send($email);
												}
											catch (Exception $e)
												{
													echo 'Caught exception: ',  $e->getMessage(), "\n";
												}
											return $response;
										}
									elseif( !empty($apikey) && !empty($apisecret) )
										{
											$mj = new \Mailjet\Client($apikey, $apisecret,true,['version' => 'v3.1']);
//											$body = [
//												'Messages' => [
//													[
//														'From' => [
//															'Email' => $from,
//															'Name' => $company_name
//														],
//														'To' => [
//															[
//																'Email' => $to
//															]
//														],
//														'Subject' => $subject,
//														'HTMLPart' => $message,
//														'Headers' => [
//															'Reply-To' => '<'.$id_email.'@'.mail_domain().'>'
//														],
//														'Attachments' => [
//															[
//																'ContentType' => '',
//																'Filename' => '',
//																'Base64Content' => '',
//															]
//														]
//													]
//												]
//											];

											$data['From'] = [
												'Email' => $from,
												'Name' => $company_name
											];
											$data['To'] = [
												[
													'Email' => $to
												]
											];

											if (!empty($cc))
												{
													$cc_list = [];
													foreach (unserialize($cc) as $email)
														{
															$cc_list[] = [
																'Email' => $email,
																'Name' => ''
															];
														}

													$data['Cc'] = $cc_list;
												}

											if (!empty($bcc))
												{
													$bcc_list = [];
													foreach (unserialize($bcc) as $email)
														{
															$bcc_list[] = [
																'Email' => $email,
																'Name' => ''
															];
														}

													$data['Bcc'] = $bcc_list;
												}

											$data['Subject'] = $subject;
											$data['HTMLPart'] = $message;

											if ( !empty($support_message) )
												{
													$sp_message = new TSupportMessage($support_message);
													if ($sp_message->Exists())
														{
															$data['Attachments'] = $sp_message->GetAttachmentsBase64Array();
														}
												}
											elseif (!empty($id_email))
												{
													$email_details = TEmail::initWithEmailID($id_email);

													$company = new TCompany($email_details->CompanyID());

													if (is_object($company) && $company->Exists() && $company->isCompanyEmailActive() && strpos($company->EmailFrom(), '@'.$company->CompanyEmailDomain()) !== false)
														{
															$data['CustomID'] = $email_details->EmailID();
														}
													else
														{
															$data['Headers'] = [
																'Reply-To' => '<'.$email_details->EmailID().'@'.mail_domain().'>'
															];
														}

													if ($email_details->Exists() && $email_details->HasOutgoingAttachments())
														{
															foreach ($email_details->LoadOutgoingAttachmentsArray() as $attachments_arr)
																{
																	$attachment = new TAttachment($attachments_arr);
																	if ($attachment->Exists())
																		{
																			$data['Attachments'][] = $attachment->GetAttachmentsBase64Array();
																		}
																	unset($attachment);
																}
															$email_details->UnsetOutgoingAttachments();
														}
												}
											elseif (!empty($id_employee))
												{
													$employee = new TEmployee($id_employee);
													if (is_object($employee) && $employee->Exists() && $employee->HasAttachments())
														{
															foreach ($employee->GetAttachmentsList() as $attachments_arr)
																{
																	$attachment = new TAttachment($attachments_arr['id']);
																	if ($attachment->Exists())
																		{
																			$data['Attachments'][] = $attachment->GetAttachmentsBase64Array();
																		}
																	unset($attachment);
																}
															$employee->ClearAttachments();
														}
												}

											$body = ['Messages' => [$data]];
											$response = $mj->post(Resources::$Email, ['body' => $body]);
											return $response->getBody();
										}
								}
						}
				}
		}
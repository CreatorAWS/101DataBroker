<?php

	use PhpMimeMailParser\Parser;

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	function check_emails()
		{
			require_once(sm_cms_rootdir().'ext/vendor/autoload.php');

			$dir_path = dirname(dirname(dirname(__FILE__))).'/Maildir/new';

			$fi = new FilesystemIterator($dir_path, FilesystemIterator::SKIP_DOTS);

			if (iterator_count($fi) == 0)
				return false;

			$dir = new DirectoryIterator($dir_path);

			foreach ($dir as $fileinfo)
				{
					if (!$fileinfo->isDot())
						{
							$path = $dir_path.'/'.$fileinfo->getFilename();
							$parser = new Parser();
							$parser->setPath($path);

							$original_message_id = 0;
							$arrayHeaderTo = $parser->getAddresses('to');

							if (empty($arrayHeaderTo[0]['address']))
								{
									unlink($path);
									continue;
								}

							$arrayHeaderFrom = $parser->getAddresses('from');

							if (empty($arrayHeaderFrom[0]['address']))
								{
									unlink($path);
									continue;
								}

							$company = TCompany::checkCompanyWithEmail($arrayHeaderTo[0]['address']);
							if (!is_object($company) || !$company->Exists())
								{
									$id_email = str_replace('@'.mail_domain(), '', $arrayHeaderTo[0]['address']);

									$email = TEmail::initWithEmailID($id_email);
									if (!is_object($email) || !$email->Exists())
										{
											unlink($path);
											continue;
										}
									else
										{
											$company = new TCompany($email->CompanyID());
											if (!$company->Exists())
												{
													unlink($path);
													continue;
												}
											$customer = new TCustomer($email->CustomerID());
											if (!$customer->Exists())
												{
													unlink($path);
													continue;
												}

											$reply_message = TMessagesLog::initWithMessageIDAndType($email->ID(), 'email');
											if ( is_object($reply_message) && $reply_message->Exists() )
												$original_message_id = $reply_message->ID();

										}
								}
							else
								{
									$customer = TCustomer::initWithCustomerEmailNotDeleted($arrayHeaderFrom[0]['address'], $company->ID());
									if (!$customer->Exists())
										{
											unlink($path);
											continue;
										}
								}

							$subject = $parser->getHeader('subject');
							$htmlEmbedded = $parser->getMessageBody('htmlEmbedded');

							$id_object = 0;

							$id = TEmail::CreateIncoming(
								$subject,
								$htmlEmbedded,
								$company->ID(),
								$customer->ID()
							);
							if (!empty($id))
								{
									TMessagesLog::Create('email', $id, $customer->CompanyID(), $customer, time(), 1, 0, $original_message_id);
									$id_object = $id;
								}

							$customer->IncomingEmailAction( $id_object );
							$attachments = $parser->getAttachments();

							foreach ($attachments as $attachment)
								{
									if ($attachment->getContentType()!='application/php')
										$attachment->save(sm_cms_rootdir().'files/img/email_attachments_'.$id.'/', Parser::ATTACHMENT_DUPLICATE_SUFFIX);
								}

							unlink($attachments);
							unlink($path);
						}
				}

			return false;
		}

	$timeend = time()+58;
	while ( time() <= $timeend )
		{
			if (!check_emails())
				sleep(1);
		}

	exit();
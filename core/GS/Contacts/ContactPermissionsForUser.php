<?php

	namespace GS\Contacts;

	use TCompany;
	use TCustomer;
	use TEmployee;

	class ContactPermissionsForUser
		{

			private $contact;
			private $user;
			private $company;

			//markers with default values
			public $send_sms = false;
			public $send_email = false;
			public $unsubscribe_from_email = false;
			public $subscribe_to_email = false;
			public $unsubscribe_from_sms = false;
			public $subscribe_to_sms = false;
			public $approve = false;
			public $start_campaign = false;
			public $create_task = false;
			public $send_video_upload_link = false;
			public $edit = false;
			public $make_call = false;

			function __construct(TCustomer $contact, TEmployee $user)
				{
					$this->contact = $contact;
					$this->user = $user;

					$this->company = new TCompany($this->contact->CompanyID());

					if ($this->contact->HasEmail())
						{
							$this->send_email = true;

							if (!$this->contact->isUnsubscribeStatus())
								$this->unsubscribe_from_email = true;
							else
								{
									$this->subscribe_to_email = true;
									$this->send_email = false;
								}
						}

					if ($this->contact->HasCellphone())
						{
							$this->send_sms = true;

							if (!$this->contact->isUnsubscribeFromTextStatus())
								$this->unsubscribe_from_sms = true;
							else
								{
									$this->subscribe_to_sms = true;
									$this->send_sms = false;
								}
						}

					if ( $this->contact->isApproved())
						$this->create_task = true;

					if ( !$this->user->isBusinessUser())
						$this->edit = true;

					if ( !$this->user->isBusinessUser() && !$this->contact->isApproved())
						$this->approve = true;
					else
						{
							if ( !$this->user->isBusinessUser())
								$this->start_campaign = true;

							if ($this->contact->HasCellphone() && $this->contact->isSendingMessagesFeatureAvaialble() && $this->company->CanSendTextMessages() && !$this->contact->isUnsubscribeFromTextStatus())
								$this->send_video_upload_link = true;
						}

					if (!$this->company->CanSendEmailMessages())
						$this->send_email = false;

					if (!$this->company->CanSendTextMessages())
						$this->send_sms = false;

					if ($this->company->isInAppCallsEnabled())
						$this->make_call = true;
				}

		}

<?php

	namespace GS\User;

	use TCompany;
	use TCustomer;
	use TEmployee;

	class UserPermissionsForProfile
		{
			private $user;
			private $company;

			//markers with default values
			public $send_sms = false;
			public $connect_email = false;
			public $disconnect_email = false;
			public $connect_callendar = false;
			public $disconnect_callendar = false;
			public $nylas_available = false;
			public $send_video_upload_link = false;
			public $connect_facebook = false;
			public $disconnect_facebook = false;
			public $connect_twitter = false;
			public $disconnect_twitter = false;
			public $connect_linkedin = false;
			public $disconnect_linkedin = false;

			function __construct(TEmployee $user)
				{
					$this->user = $user;
					$this->company = new TCompany($this->user->CompanyID());

					if ( ($this->company->HasNylasAPI() || ( sm_get_settings('nylas_client_id') && sm_get_settings('nylas_client_secret') )) && $this->user->HasEmail() )
						$this->nylas_available = true;

					if ($this->nylas_available)
						{
							if (!$this->user->HasEmailAccount())
								$this->connect_email = true;
							else
								$this->disconnect_email = true;

							if ($this->user->HasEmailAccount())
								{
									if (!$this->user->HasNylasCalendarID())
										$this->connect_callendar = true;
									else
										$this->disconnect_callendar = true;
								}

							if ($this->user->HasEmail())
								$this->connect_email = true;
						}

					if ($this->user->HasCellphone())
						$this->send_sms = true;

					if ($this->user->HasCellphone() && $this->company->CanSendTextMessages() )
						$this->send_video_upload_link = true;

					if ($this->user->isFacebookConnected())
						$this->disconnect_facebook = true;
					else
						$this->connect_facebook = true;

					if ($this->user->isTwitterConnected())
						$this->disconnect_twitter = true;
					else
						$this->connect_twitter = true;

					if ($this->user->isLinkedInConnected())
						$this->disconnect_linkedin = true;
					else
						$this->connect_linkedin = true;
				}

		}

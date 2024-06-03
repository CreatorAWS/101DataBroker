<?php

	if (!defined("TAppointment_DEFINED"))
		{
			Class TAppointment
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('appointments')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company' => intval($val)));
						}

					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer' => intval($val)));
						}

					function AddedTimestamp()
						{
							return intval($this->info['timeadded']);
						}

					function SetAddedTimestamp($val)
						{
							$this->UpdateValues(Array('timeadded' => intval($val)));
						}

					function ScheduledTimestamp()
						{
							return intval($this->info['timescheduled']);
						}

					function SetScheduledTimestamp($val)
						{
							$this->UpdateValues(Array('timescheduled' => intval($val)));
						}

					function Note()
						{
							return $this->info['note'];
						}

					function SetNote($val)
						{
							$this->UpdateValues(Array('note' => $val));
						}

					function HasNote()
						{
							return !empty($this->info['note']);
						}

					function NotificationSentTimestamp()
						{
							return intval($this->info['notification_sent']);
						}

					function SetNotificationSentTimestamp($val)
						{
							$this->UpdateValues(Array('notification_sent' => intval($val)));
						}

					function HasNotificationSentTimestamp()
						{
							return !empty($this->info['notification_sent']);
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('appointments');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($customer)
						{
							/** @var TCustomer $customer */
							$q = new TQuery('appointments');
							$q->Add('id_customer', intval($customer->ID()));
							$q->Add('id_company', intval($customer->CompanyID()));
							$q->Add('timeadded', time());
							$object=new TAppointment($q->Insert());
							$customer->MarkAsHasAppointments();
							return $object;
						}

					function Remove()
						{
							$q = new TQuery('appointments');
							$q->AddWhere('id', intval($this->ID()));
							$q->Remove();
						}

				}

			define("TAppointment_DEFINED", 1);
		}
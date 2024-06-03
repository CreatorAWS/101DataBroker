<?php

	if (!defined("TCustomerLog_DEFINED"))
		{
			Class TCustomerLog
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('customer_log')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCustomerLog'][$id]))
								{
									$object=new TCustomerLog($id);
									if ($object->Exists())
										$sm['cache']['TCustomerLog'][$id]=$object->GetRawData();
								}
							else
								$object=new TCustomerLog($sm['cache']['TCustomerLog'][$id]);
							return $object;
						}

					public static function initWithObjectTypeAndID($object_id, $type, $id_company)
						{
							use_api('cleaner');

							$info = TQuery::ForTable('customer_log')->Add('id_company', Cleaner::IntObjectID($id_company))->Add('id_object', intval($object_id))->Add('action', dbescape($type))->OrderBy('id')->Get();
							$log = new TCustomerLog($info);
							return $log;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}

					function isCampaign()
						{
							return $this->Action() == 'start_campaign';
						}

					function CompanyID()
						{
							return intval($this->info['id_company']);
						}

					function SetCompanyID($val)
						{
							$this->UpdateValues(Array('id_company'=>intval($val)));
						}

					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function HasCustomerID()
						{
							return !empty($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer'=>intval($val)));
						}

					function EmployeeID()
						{
							return intval($this->info['id_employee']);
						}

					function SetEmployeeID($val)
						{
							$this->UpdateValues(Array('id_employee'=>intval($val)));
						}

					function Addedtime()
						{
							return intval($this->info['addedtime']);
						}

					function SetAddedtime($val)
						{
							$this->UpdateValues(Array('addedtime'=>intval($val)));
						}

					function ActionTitle()
						{
							if ($this->Action() == 'bulk_email')
								return 'Broadcast Email';
							if ($this->Action() == 'bulk_sms')
								return 'Broadcast SMS';
							if ($this->Action() == 'incoming_sms')
								return 'Incoming SMS';
							if ($this->Action() == 'sms')
								return 'Outgoing SMS';
							if ($this->Action() == 'incoming_email')
								return 'Incoming Email';
							if ($this->Action() == 'email')
								return 'Outgoing Email';
							if ($this->Action() == 'incoming_call')
								return 'Incoming Call';
							if ($this->Action() == 'call')
								return 'Outgoing Call';
							if ($this->Action() == 'note')
								return 'Note';
							if ($this->Action() == 'task')
								return 'Task';
							if ($this->Action() == 'booked_call')
								return 'Calendly Meeting';

						}

					function Direction()
						{
							if ($this->Action() == 'bulk_email')
								return 'outgoing';
							if ($this->Action() == 'bulk_sms')
								return 'outgoing';
							if ($this->Action() == 'incoming_sms')
								return 'incoming';
							if ($this->Action() == 'sms')
								return 'outgoing';
							if ($this->Action() == 'incoming_email')
								return 'incoming';
							if ($this->Action() == 'email')
								return 'outgoing';
							if ($this->Action() == 'incoming_call')
								return 'incoming';
							if ($this->Action() == 'call')
								return 'outgoing';
							if ($this->Action() == 'booked_call')
								return 'Calendly Meeting';
							return '';
						}

					function ActionType()
						{
							if ($this->Action() == 'bulk_email')
								return 'email';
							if ($this->Action() == 'bulk_sms')
								return 'sms';
							if ($this->Action() == 'incoming_sms')
								return 'sms';
							if ($this->Action() == 'sms')
								return 'sms';
							if ($this->Action() == 'incoming_email')
								return 'email';
							if ($this->Action() == 'email')
								return 'email';
							if ($this->Action() == 'incoming_call')
								return 'call';
							if ($this->Action() == 'call')
								return 'call';
							if ($this->Action() == 'note')
								return 'note';
							if ($this->Action() == 'task')
								return 'task';
							if ($this->Action() == 'booked_call')
								return 'Calendly Meeting';
						}
					function isBookedCall()
						{
							return $this->Action() == 'booked_call';
						}
					function isNote()
						{
							return $this->Action() == 'note';
						}

					function isTask()
						{
							return $this->Action() == 'task';
						}

					function isCallAction()
						{
							return $this->Action() == 'incoming_call' || $this->Action() == 'call';
						}

					function isIncomingCall()
						{
							return $this->Action() == 'incoming_call';
						}

					function isMessageAction()
						{
							return $this->Action() == 'email' || $this->Action() == 'bulk_email' || $this->Action() == 'incoming_email' || $this->Action() == 'bulk_sms' || $this->Action() == 'sms' || $this->Action() == 'incoming_sms';
						}

					function GetEmailStatus()
						{
							if ($this->isEmailAction())
								{
									$log_item = new TEmail($this->ObjectID());
									if ( $log_item->Exists() )
										{
											if($log_item->isIncoming())
												{
													return 'Received';
												}
											else
												{
													if ($log_item->HasStatus())
														return $log_item->Status();
													else
														return 'Sent';
												}
										}

								}
						}

					function isEmailAction()
						{
							return $this->ActionType() == 'email';
						}

					function Action()
						{
							return $this->info['action'];
						}

					function SetAction($val)
						{
							$this->UpdateValues(Array('action'=>$val));
						}

					function Description()
						{
							return $this->info['description'];
						}

					function SetDescription($val)
						{
							$this->UpdateValues(Array('description'=>$val));
						}

					function HasObjectID()
						{
							return !empty(intval($this->info['id_object']));
						}

					function ObjectID()
						{
							return intval($this->info['id_object']);
						}

					function SetObjectID($val)
						{
							$this->UpdateValues(Array('id_object'=>intval($val)));
						}

					function Scheduledtime()
						{
							return intval($this->info['scheduledtime']);
						}

					function SetScheduledtime($val)
						{
							$this->UpdateValues(Array('scheduledtime'=>intval($val)));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('customer_log');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($id_customer, $action, $description, $id_employee = 0, $id_company = 0, $id_object = 0, $scheduledtime = 0)
						{
							$sql=new TQuery('customer_log');
							$sql->Add('id_company', intval($id_company));
							$sql->Add('id_customer', intval($id_customer));
							$sql->Add('id_employee', intval($id_employee));
							$sql->Add('addedtime', time());
							$sql->Add('action', dbescape($action));
							$sql->Add('description', dbescape($description));
							$sql->Add('id_object', intval($id_object));
							$sql->Add('scheduledtime', intval($scheduledtime));
							$object = new TCustomerLog($sql->Insert());

							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('customer_log')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TCustomerLog_DEFINED", 1);
		}


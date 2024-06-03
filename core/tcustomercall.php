<?php

	if (!defined("TCustomerCall_DEFINED"))
		{
			Class TCustomerCall
				{
					protected $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info = $id_or_cachedinfo;
							else
								$this->info = TQuery::ForTable('customer_calls')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TCustomerCall'][$id]))
								{
									$object = new TCustomerCall($id);
									if ($object->Exists())
										$sm['cache']['TCustomerCall'][$id] = $object->GetRawData();
								}
							else
								$object = new TCustomerCall($sm['cache']['TCustomerCall'][$id]);
							return $object;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}


					function CustomerID()
						{
							return intval($this->info['id_customer']);
						}

					function SetCustomerID($val)
						{
							$this->UpdateValues(Array('id_customer' => intval($val)));
						}

					function EmloyeeID()
						{
							return intval($this->info['id_emloyee']);
						}

					function SetEmloyeeID($val)
						{
							$this->UpdateValues(Array('id_emloyee' => intval($val)));
						}

					function Timemade()
						{
							return intval($this->info['timemade']);
						}

					function SetTimemade($val)
						{
							$this->UpdateValues(Array('timemade' => intval($val)));
						}

					function Phone()
						{
							return $this->info['phone'];
						}

					function SetPhone($val)
						{
							$this->UpdateValues(Array('phone' => $val));
						}

					function Status()
						{
							return $this->info['status'];
						}

					function SetStatus($val)
						{
							$this->UpdateValues(Array('status' => $val));
						}

					function DurationSec()
						{
							return intval($this->info['duration_sec']);
						}

					function SetDurationSec($val)
						{
							$this->UpdateValues(Array('duration_sec' => intval($val)));
						}

					function RecordingUrl()
						{
							return $this->info['recording_url'];
						}

					function SetRecordingUrl($val)
						{
							$this->UpdateValues(Array('recording_url' => $val));
						}

					function HasRecordingUrl()
						{
							return !empty($this->info['recording_url']);
						}


					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q = new TQuery('customer_calls');
							foreach ($params as $key => $val)
								{
									$this->info[$key] = $val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					function Remove()
						{
							TQuery::ForTable('customer_calls')->AddWhere('id', intval($this->ID()))->Remove();
						}

					public static function Create($customer, $employee, $phone, $type = 'Outgoing')
						{
							$q=new TQuery('customer_calls');
							$q->Add('id_customer', Cleaner::IntObjectID($customer));
							$q->Add('id_employee', Cleaner::IntObjectID($employee));
							$q->Add('phone', dbescape($phone));
							$q->Add('status', dbescape('unknown'));
							$q->Add('recording_url', dbescape(''));
							$q->Add('timemade', time());
							$q->Add('type', dbescape($type));
							$object = new TCustomerCall($q->Insert());
							return $object;
						}

				}

			define("TCustomerCall_DEFINED", 1);
		}

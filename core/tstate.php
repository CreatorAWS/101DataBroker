<?php

	if (!defined("TState_DEFINED"))
		{
			Class TState
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('states')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TState'][$id]))
								{
									$object=new TState($id);
									if ($object->Exists())
										$sm['cache']['TState'][$id]=$object->GetRawData();
								}
							else
								$object=new TState($sm['cache']['TState'][$id]);
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


					function State()
						{
							return $this->info['state'];
						}

					function SetState($val)
						{
							$this->UpdateValues(Array('state'=>$val));
						}

					function StateAbbr()
						{
							return $this->info['state_abbr'];
						}

					function SetStateAbbr($val)
						{
							$this->UpdateValues(Array('state_abbr'=>$val));
						}

					function TotalCount()
						{
							return intval($this->info['total_count']);
						}

					function SetTotalCount($val)
						{
							$this->UpdateValues(Array('total_count'=>intval($val)));
						}

					function Processed()
						{
							return intval($this->info['processed']);
						}

					function SetProcessed($val)
						{
							$this->UpdateValues(Array('processed'=>intval($val)));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('states');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($state, $state_abbr, $total_count, $processed)
						{
							$sql=new TQuery('states');
							$sql->Add('state', dbescape($state));
							$sql->Add('state_abbr', dbescape($state_abbr));
							$sql->Add('total_count', intval($total_count));
							$sql->Add('processed', intval($processed));
							$object = new TState($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('states')->AddWhere('id', intval($this->ID()))->Remove();
						}

					function DownloadFileExists()
						{
							return file_exists($this->DownloadPathRelative());
						}

					function DownloadPathRelative()
						{
							return 'files/csv_states/'.$this->StateAbbr().'.zip';
						}


				}
			define("TState_DEFINED", 1);
		}

<?php

	if (!defined("TSicCodes_DEFINED"))
		{
			Class TSicCodes
				{
					protected $info;
					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('sic_codes')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TSicCodes'][$id]))
								{
									$object=new TSicCodes($id);
									if ($object->Exists())
										$sm['cache']['TSicCodes'][$id]=$object->GetRawData();
								}
							else
								$object=new TSicCodes($sm['cache']['TSicCodes'][$id]);
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


					function Sic()
						{
							return $this->info['sic'];
						}

					function SetSic($val)
						{
							$this->UpdateValues(Array('sic'=>$val));
						}

					function SicName()
						{
							return $this->info['sic_name'];
						}

					function SetSicName($val)
						{
							$this->UpdateValues(Array('sic_name'=>$val));
						}

					function DownloadFileExists()
						{
							return file_exists($this->DownloadPathRelative());
						}

					function DownloadPathRelative()
						{
							return 'files/csv/'.$this->info['sic'].'.zip';
						}

					function Disabled()
						{
							return intval($this->info['disabled']);
						}

					function SetDisabled($val)
						{
							$this->UpdateValues(Array('disabled'=>intval($val)));
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
							$q=new TQuery('sic_codes');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($sic, $sic_name, $disabled, $total_count, $processed)
						{
							$sql=new TQuery('sic_codes');
							$sql->Add('sic', dbescape($sic));
							$sql->Add('sic_name', dbescape($sic_name));
							$sql->Add('disabled', intval($disabled));
							$sql->Add('total_count', intval($total_count));
							$sql->Add('processed', intval($processed));
							$object = new TSicCodes($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('sic_codes')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}
			define("TSicCodes_DEFINED", 1);
		}

<?php

	namespace GS\Common;

	use TQuery;
	use GS\Common\DB\DBQuery;

	trait GenericMetadataTrait
		{
			protected $metadata;

			function HasMetaData($key): bool
				{
					$this->LoadMetaData();
					if (!is_array($this->metadata))
						return false;
					return array_key_exists($key, $this->metadata);
				}

			protected function LoadMetaData(): self
				{
					if ($this->metadata===NULL)
						{
							$this->metadata=[];
							$data=DBQuery::ForTable($this->table_name.'_metadata')
								->AddWhere('id_object', $this->ID())
								->Select()
								->items;
							for ($i=0; $i<count($data); $i++)
								$this->metadata[$data[$i]['key']]=$data[$i]['value'];
						}
					return $this;
				}

			function GetMetaData($key): string
				{
					$this->LoadMetaData();
					return $this->metadata[$key] ?? '';
				}

			function isMetaDataEmpty($key): bool
				{
					return empty($this->GetMetaData($key));
				}

			function UnsetMetaData($key): void
				{
					$this->SetMetaData($key, NULL);
				}

			protected function RemoveAllMetaData(): void
				{
					$q = new DBQuery($this->table_name.'_metadata');
					$q->AddInt('id_object', $this->ID());
					$q->Remove();
					$this->metadata=[];
				}

			/*
			 * @param $value - use NULL to delete metadata
			 */
			function SetMetaData($key, $value, $use_empty_as_null=false): self
				{
					if (empty($value) && $use_empty_as_null)
						$value=NULL;
					$this->LoadMetaData();
					$q = new TQuery($this->table_name.'_metadata');
					$q->Add('id_object', $this->ID());
					$q->Add('key', dbescape($key));
					$info=$q->Get();
					if (!empty($info['id']))
						{
							$q = new TQuery($this->table_name.'_metadata');
							if ($value===NULL)
								{
									$q->Add('id', intval($info['id']));
									$q->Remove();
									unset($this->metadata[$key]);
								}
							else
								{
									$q = new TQuery($this->table_name.'_metadata');
									$q->Add('value', dbescape($value));
									$q->Update('id', intval($info['id']));
									$this->metadata[$key]=$value;
								}
						}
					elseif ($value!==NULL)
						{
							$q = new TQuery($this->table_name.'_metadata');
							$q->Add('id_object', $this->ID());
							$q->Add('key', dbescape($key));
							$q->Add('value', dbescape($value));
							$q->Insert();
							$this->metadata[$key]=$value;
						}
					else
						unset($this->metadata[$key]);
					return $this;
				}

			protected function GetMetaDataAsInt(string $key): int
				{
					return intval($this->GetMetaData($key));
				}

			protected function GetMetaDataAsBool(string $key): bool
				{
					return intval($this->GetMetaData($key))>0;
				}

		}


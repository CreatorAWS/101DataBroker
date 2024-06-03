<?php

	if (!defined("TFieldsList_DEFINED"))
		{
			Class TFieldsList extends TGenericList
				{
				/** @var TFields[] $itemsinfo */
					public $items;
					protected $tablename='customer_fields';
					protected $idfield='id';
					protected $titlefield='field';

					function SetFilterCompany($id_company)
						{
							if (is_object($id_company))
								$id_company=$id_company->ID();
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_company=".intval($id_company);
						}
					function ExcludeCtg($ctg)
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_ctg<>".intval($ctg);
						}
					function ExtractNamesArray($addrolename=false)
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->Field();
								}
							return $r;
						}
					function SetFilterCategory($id_ctg)
						{
							if (is_object($id_ctg))
								$id_ctg = $id_ctg->ID();
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_ctg=".($id_ctg);
						}
					function SetFilterTemplate($id_template)
						{
							if (is_object($id_template))
								$id_template=$id_template->ID();
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" id_template=".intval($id_template);
						}
					function SetFilterDisabled()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" disabled = 1";
						}

					function SetFilterEnabled()
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" disabled = 0";
						}

					protected function InitItem($index)
						{
							$item = new TFields($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TFieldsList_DEFINED", 1);
		}

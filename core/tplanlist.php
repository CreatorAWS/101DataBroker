<?php

	use GS\Common\Legacy\EachItemTrait;

	if (!defined("TPlanList_DEFINED"))
		{
			/**
			  * @method TPlan Item(integer $index)
			  */

			/**
			 * @method TPlan[] EachItemOfLoaded()
			 */

			Class TPlanList extends TGenericList
				{
				/** @var TPlan[] $items */
					public $items;
					protected $tablename = 'plans';
					protected $idfield = 'id';
					protected $titlefield = 'title';
					protected $orderfield='`title`';
					use EachItemTrait;

					protected function InitItem($index)
						{
							$item = new TPlan($this->itemsinfo[$index]);
							return $item;
						}

					function SetFilterTitleBeginnig(string $beginning): void
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" (title LIKE '".dbescape($beginning)."%')";
						}

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_company=".Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterActive()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " deleted=0";
						}

					function SetFilterTypePackage()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " type='package'";
						}

					function SetFilterPlan()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " type='plan'";
						}

					function OrderByOrder($asc=true)
						{
							$this->orderby=$this->orderfield;
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					function ExtractNamesArray($add_price=false)
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->Title().($add_price?' - $'.$this->items[$i]->Price():'');
								}
							return $r;
						}
				}

			define("TPlanList_DEFINED", 1);
		}

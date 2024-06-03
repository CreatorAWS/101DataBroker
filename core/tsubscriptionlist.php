<?php

	if (!defined("TSubscriptionList_DEFINED"))
		{
			/**
			  * @method TSubscription Item(integer $index)
			  */
			Class TSubscriptionList extends TGenericList
				{
				/** @var TSubscription[] $items */
					public $items;
					protected $tablename = 'subscriptions';
					protected $idfield = 'id';
					protected $titlefield = 'last_name';

					protected function InitItem($index)
						{
							$item = new TSubscription($this->itemsinfo[$index]);
							return $item;
						}
					
					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_company=".Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterPlan($id_plan)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_plan=".Cleaner::IntObjectID($id_plan);
						}
					function SetFilterEmail($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " email LIKE '".dbescape($email)."'";
						}
					function OrderByName($asc=true)
						{
							$this->orderby='first_name';
							if (!$asc)
								$this->orderby.=' DESC';
							$this->orderby.=', last_name';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					function SetFilterExcludePackageIDs($arrayids)
						{
							if (count($arrayids)>0)
								{
									if (!empty($this->sql))
										$this->sql .= ' AND ';
									if (!is_array($arrayids))
										$this->sql .= " 1=2 ";
									else
										$this->sql .= " id_plan NOT IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
								}
						}

					function SetFilterPackageIDs($arrayids)
						{
							if (count($arrayids)>0)
								{
									if (!empty($this->sql))
										$this->sql .= ' AND ';
									if (!is_array($arrayids))
										$this->sql .= " 1=2 ";
									else
										$this->sql .= " id_plan IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
								}
						}

					function SetFilterActive()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " cancelled=0";
						}

				}

			define("TSubscriptionList_DEFINED", 1);
		}

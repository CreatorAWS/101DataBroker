<?php
	use GS\Common\Legacy\EachItemTrait;

	if (!defined("TPurchasesList_DEFINED"))
		{
			class TPurchasesList extends TGenericList
				{
					/** @var $items TPurchase[] */
					/**
					 * @method TPurchase[] EachItemOfLoaded()
					 */

					public $items;
					protected $tablename='purchases';
					protected $idfield='id';
					protected $titlefield='title';
					use EachItemTrait;

					function SetFilterCompanyID($user_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_u=".Cleaner::IntObjectID($user_id);
						}

					function HideAll()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " 1<>1";
						}

					function SetFilterEmail($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " customeremail = '".dbescape($email)."'";
						}

					protected function InitItem($index)
						{
							$item = new TPurchase($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TPurchasesList_DEFINED", 1);
		}
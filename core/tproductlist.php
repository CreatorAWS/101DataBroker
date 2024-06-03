<?php

	use GS\Common\Legacy\EachItemTrait;

	if (!defined("TProductList_DEFINED"))
		{
			sm_use('tproduct');
			class TProductList extends TGenericList
				{
					/** @var $items TProduct[] */
					/**
					 * @method TProduct[] EachItemOfLoaded()
					 */

					public $items;
					protected $tablename='products';
					protected $idfield='id';
					protected $titlefield='title';
					protected $orderfield='`order`';
					use EachItemTrait;

					function SetFilterUserID($user_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " id_u=".intval($user_id);
						}

					function SetFilterTitleBeginnig(string $beginning): void
						{
							if (!empty($this->sql))
								$this->sql.=' AND ';
							$this->sql.=" (title LIKE '".dbescape($beginning)."%')";
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

					protected function InitItem($index)
						{
							$item = new TProduct($this->itemsinfo[$index]);
							return $item;
						}

					function SetFilterTypePackage()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " type = 'package'";
						}

					function SetFilterProduct()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " type='product'";
						}

					function OrderByOrder($asc=true)
						{
							$this->orderby=$this->orderfield;
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}
				}

			define("TProductList_DEFINED", 1);
		}
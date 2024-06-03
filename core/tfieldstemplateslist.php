<?php

	if (!defined("TFieldsTemplatesList_DEFINED"))
		{
			Class TFieldsTemplatesList extends TGenericList
				{
				/** @var TFieldsTemplate[] $itemsinfo */
					public $items;
					protected $tablename='customer_fields_templates';
					protected $idfield='id';
					protected $titlefield='field';

					function ExtractNamesArray($addrolename=false)
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->Title();
								}
							return $r;
						}

					protected function InitItem($index)
						{
							$item = new TFieldsTemplate($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TFieldsTemplatesList_DEFINED", 1);
		}

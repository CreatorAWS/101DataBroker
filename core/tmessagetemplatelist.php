<?php

	if (!defined("TMessageTemplateList_DEFINED"))
		{
			Class TMessageTemplateList extends TGenericList
				{
					/** @var TMessageTemplate[] $items */
					public $items;
					protected $tablename='message_templates';
					protected $idfield='id';
					protected $titlefield='text';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCategory($id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_ctg='.Cleaner::IntObjectID($id);
						}

					protected function InitItem($index)
						{
							$item = new TMessageTemplate($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TMessageTemplateList_DEFINED", 1);
		}

<?php

	if (!defined("TEmailTemplateList_DEFINED"))
		{
			Class TEmailTemplateList extends TGenericList
				{
					/** @var TEmailTemplate[] $items */
					public $items;
					protected $tablename='email_templates';
					protected $idfield='id';
					protected $titlefield='name';

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
							$item = new TEmailTemplate($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TEmailTemplateList_DEFINED", 1);
		}

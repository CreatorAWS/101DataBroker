<?php

	if (!defined("TCampaignSequenceList_DEFINED"))
		{
			Class TCampaignSequenceList extends TGenericList
				{
					/** @var TCampaignSequence[] $items */
					public $items;
					protected $tablename = 'campaigns_sequences';
					protected $idfield = 'id';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterCampaign($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_campaign='.Cleaner::IntObjectID($object_or_id);
						}

					function SetFilterContacts($object_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_customer='.Cleaner::IntObjectID($object_or_id);
						}


					function SetFilterMode($mode)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " mode='".dbescape($mode)."'";
						}

					function OrderByActionTime($asc=true)
						{
							$this->orderby='scheduledtime';
							if (!$asc)
								$this->orderby.=' DESC';
							$this->orderby.=', id';
							if (!$asc)
								$this->orderby.=' DESC';
							return $this;
						}

					protected function InitItem($index)
						{
							$item = new TCampaignSequence($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TCampaignSequenceList_DEFINED", 1);
		}

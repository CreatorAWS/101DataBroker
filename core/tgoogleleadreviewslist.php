<?php

	if (!defined("TGoogleLeadReviewsList_DEFINED"))
		{
			Class TGoogleLeadReviewsList extends TGenericList
				{
				/** @var TGoogleLeadReviews[] $items */
					public $items;
					protected $tablename='google_reviews';
					protected $idfield='id';

					function SetFilterLead($lead_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_lead='.Cleaner::IntObjectID($lead_id);
						}

					protected function InitItem($index)
						{
							$item = new TGoogleLeadReviews($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TGoogleLeadReviewsList_DEFINED", 1);
		}

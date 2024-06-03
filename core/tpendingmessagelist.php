<?php

	if (!defined("TPendingMessageList_DEFINED"))
		{
			Class TPendingMessageList extends TMessageList
				{
					/** @var TPendingMessage[] $items */
					public $items;
					protected $tablename='smslog_pending';
					protected $idfield='id';
					protected $titlefield='text';

					protected function InitItem($index)
						{
							$item = new TPendingMessage($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TPendingMessageList_DEFINED", 1);
		}

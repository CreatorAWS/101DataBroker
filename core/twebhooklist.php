<?php

	if (!defined("TWebhookList_DEFINED"))
		{
			/**
			  * @method TWebhookItem Item(integer $index)
			  */
			Class TWebhookList extends TGenericList
				{
				/** @var TWebhookItem[] $items */
					public $items;
					protected $tablename = 'webhooks_queue';
					protected $idfield = 'id';
					protected $titlefield = 'webhook_url';

					protected function InitItem($index)
						{
							$item = new TWebhookItem($this->itemsinfo[$index]);
							return $item;
						}
					
				}

			define("TWebhookList_DEFINED", 1);
		}

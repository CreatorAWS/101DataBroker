<?php

	if (!defined("TPendingMessage_DEFINED"))
		{
			Class TPendingMessage extends TGenericMessage
				{
					protected $info;

					public static function Table()
						{
							return 'smslog_pending';
						}
				}

			define("TPendingMessage_DEFINED", 1);
		}

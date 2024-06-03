<?php

	if (!defined("TMessage_DEFINED"))
		{
			Class TMessage extends TGenericMessage
				{
					protected $info;

					public static function Table()
						{
							return 'smslog';
						}
				}

			define("TMessage_DEFINED", 1);
		}

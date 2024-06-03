<?php

	if (!defined("TEmail_DEFINED"))
		{
			Class TEmail extends TGenericEmail
				{
					protected $info;

					public static function Table()
						{
							return 'maillog';
						}
				}

			define("TEmail_DEFINED", 1);
		}

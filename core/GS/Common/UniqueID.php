<?php

	namespace GS\Common;

	class UniqueID
		{

			public static function Generate($prefix='', $parts_count=4)
				{
					mt_srand();
					$parts=[];
					if (strlen($prefix)>0)
						$parts[]=$prefix;
					$parts[]=md5(microtime(true).mt_rand(11111, 99999));
					while (count($parts)<$parts_count)
						{
							$parts[]=dechex(mt_rand(10000000, 99999999));
						}
					return implode('-', $parts);
				}

		}

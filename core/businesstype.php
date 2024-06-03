<?php

	class BusinessType
		{
			public static function Tags()
				{
					return Array(
						'generic',
						'dealership'
					);
				}
			public static function Titles()
				{
					return Array(
						'Generic',
						'Dealership'
					);
				}
			public static function TitleForTag($tag)
				{
					$tags=BusinessType::Tags();
					$titles=BusinessType::Titles();
					for ($i = 0; $i < count($titles); $i++)
						{
							if (strcmp($tags[$i], $tag)==0)
								return $titles[$i];
						}
					return '';
				}
		}

?>
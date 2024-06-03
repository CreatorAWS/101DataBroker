<?php

	class CustomerFields
		{
			public static function Fields()
				{
					return Array(
						Array('name' => 'salesperson', 'title' => 'Sales Person 1', 'section' => 'Staff'),
						Array('name' => 'salesperson2', 'title' => 'Sales Person 2', 'section' => 'Staff'),
						Array('name' => 'salesmanager', 'title' => 'Sales Manager', 'section' => 'Staff'),
					);
				}

			public static function Tags()
				{
					$r = Array();
					$f = CustomerFields::Fields();
					for ($i = 0; $i < count($f); $i++)
						{
							$r[]=$f[$i]['name'];
						}
					return $r;
				}

			public static function Titles()
				{
					$r = Array();
					$f = CustomerFields::Fields();
					for ($i = 0; $i < count($f); $i++)
						{
							$r[]=$f[$i]['title'];
						}
					return $r;
				}

			public static function TitleForTag($tag)
				{
					$tags = CustomerFields::Tags();
					$titles = CustomerFields::Titles();
					for ($i = 0; $i < count($titles); $i++)
						{
							if (strcmp($tags[$i], $tag) == 0)
								return $titles[$i];
						}
					return '';
				}

			public static function GetSections()
				{
					$r = Array();
					$f = CustomerFields::Fields();
					for ($i = 0; $i < count($f); $i++)
						{
							$r[]=$f[$i]['section'];
						}
					return array_values(array_unique($r));
				}

			public static function GetTagsForSection($section_title)
				{
					$r = Array();
					$f = CustomerFields::Fields();
					for ($i = 0; $i < count($f); $i++)
						{
							if (strcmp($f[$i]['section'], $section_title)==0)
								$r[]=$f[$i]['name'];
						}
					return$r;
				}
		}

?>
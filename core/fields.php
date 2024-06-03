<?php

	class Fields
		{
			public static function Tags()
				{
					return Array(
						'first_name',
						'last_name',
						'address',
						'city',
						'state',
						'zip',
						'cellphone',
						'email',
						'ssn',
						'birthdate',
						'salesperson',
						'salesmanager',
						'financemanager',
						'yearmade',
						'make',
						'model',
						'miles',
						'notes'
					);
				}
			public static function Titles()
				{
					return Array(
						'First Name',
						'Last Name',
						'Address',
						'City',
						'State',
						'Zip',
						'Cell Phone',
						'Email',
						'Social Security Number',
						'Birthdate',
						'Salesperson',
						'Sales Manager',
						'Finance Manager',
						'Year',
						'Make',
						'Model',
						'Miles',
						'Notes'
					);
				}
			public static function TitleForTag($tag)
				{
					$tags=Fields::Tags();
					$titles=Fields::Titles();
					for ($i = 0; $i < count($titles); $i++)
						{
							if (strcmp($tags[$i], $tag)==0)
								return $titles[$i];
						}
					return '';
				}
		}

?>
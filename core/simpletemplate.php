<?php

	class SimpleTemplate
		{
			public static function Replace($template, $array_values)
				{
					if (!is_array($array_values) || count($array_values)==0)
						return $template;
					foreach ($array_values as $key=>$val)
						$template=str_replace('{'.$key.'}', $val, $template);
					return $template;
				}
		}

?>
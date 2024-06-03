<?php

	function seconds_to_str($time)
		{
			$days = floor($time/(3600*24));
			$time -= $days*24*3600;
			$hrs = floor($time/3600);
			$time -= $hrs*3600;
			$min = floor($time/60);
			$str = '';
			if ($days>0)
				$str .= $days.' days ';
			if ($hrs>0)
				$str .= $hrs.' hrs ';
			if ($min>0)
				$str .= $min.' mins ';
			return trim($str);
		}
	
	function seconds_to_str_short($time)
		{
			$days = floor($time/(3600*24));
			$time -= $days*24*3600;
			$hrs = floor($time/3600);
			$time -= $hrs*3600;
			$min = floor($time/60);
			$str = '';
			if ($days>0)
				$str .= $days.'d ';
			if ($hrs>0)
				$str .= $hrs.'h ';
			if ($min>0)
				$str .= $min.'m ';
			return trim($str);
		}
	
?>
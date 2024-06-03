<?php

include_once(dirname(__FILE__).'/resize_class.php');

function resized_image($inputfile, $outputfile, $neededwidth, $neededheight, $skipifimageless=1, $quality=100, $needcrop=0)
	{
		if (!file_exists($inputfile)) return false;
		if (file_exists($outputfile))
			unlink($outputfile);
		$resizeObj = new resizetmp($inputfile);
		if ($skipifimageless==1 && $resizeObj->width<=$neededwidth && $resizeObj->height<=$neededheight && !empty($resizeObj->width) && !empty($resizeObj->height))
			{
				copy($inputfile, $outputfile);
			}
		else
			{
				if ($needcrop==1)
					$param='crop';
				else
					$param='auto';
				$resizeObj -> resizeImage($neededwidth, $neededheight, $param);
				$resizeObj -> saveImage($outputfile, $quality);
			}
		unset($resizeObj);
		return file_exists($outputfile);
	}

?>
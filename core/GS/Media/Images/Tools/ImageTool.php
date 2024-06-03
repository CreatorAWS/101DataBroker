<?php

	namespace GS\Media\Images\Tools;

	use resizetmp_api_class;

	class ImageTool
		{

			public static function QuickResize(string $inputfile, string $outputfile, int $neededwidth, int $neededheight, bool $skipifimageless=true, int $quality=100, bool $needcrop=false)
				{
					include_once(dirname(__FILE__).'/inc/resizeimgclass.php');
					if (!file_exists($inputfile))
						return false;
					if (file_exists($outputfile))
						unlink($outputfile);
					$resizeObj=new resizetmp_api_class($inputfile);
					if ($skipifimageless && $resizeObj->width<=$neededwidth && $resizeObj->height<=$neededheight && !empty($resizeObj->width) && !empty($resizeObj->height))
						{
							copy($inputfile, $outputfile);
						}
					else
						{
							if ($needcrop)
								$param='crop';
							else
								$param='auto';
							$resizeObj->resizeImage($neededwidth, $neededheight, $param);
							$resizeObj->saveImage($outputfile, $quality);
						}
					unset($resizeObj);
					return file_exists($outputfile);
				}

		}

<?php

	namespace GS\Common\Download;

	class DownloadHelper
		{

			public static function DownloadAsJPG(string $existent_file_name, string $file_name_to_display): void
				{
					header("Content-type: image/jpeg");
					header('Content-Disposition: inline; filename="'.$file_name_to_display.'"');
					$fp=fopen($existent_file_name, 'rb');
					fpassthru($fp);
					fclose($fp);
					exit();
				}

			public static function DownloadAsOctetStream(string $existent_file_name, string $file_name_to_display): void
				{
					header("Content-type: application/octet-stream");
					header('Content-Disposition: attachment; filename="'.$file_name_to_display.'"');
					$fp=fopen($existent_file_name, 'rb');
					fpassthru($fp);
					fclose($fp);
					exit();
				}

		}
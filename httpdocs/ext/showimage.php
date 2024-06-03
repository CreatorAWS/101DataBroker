<?php

	/*
	$cache_time - time to live for cache files in seconds
	$_GET['background'] - hexadecimal background for filling transparent areas (FFFFFF by default)
	$_GET['img'] - file name without path and extension
	$_GET['format'] - output format 
	$_GET['quality'] - output quality 
	$_GET['path'] - ID of the root directory for image (0 - files/, 1 - files/img/)
	$_GET['ext'] - directory(ies) inside path
	$_GET['png'] - open file with png extension
	$_GET['gif'] - open file with gif extension
	 */

	header('Expires: '.gmdate('D, d M Y H:i:s', time() + 86400).' GMT');
	$cache_dir = '../files/temp';
	$cache_time = 86400 * 15;

	function get_string($name, $default = '')
		{
			return array_key_exists($name, $_GET) ? strval($_GET[$name]) : $default;
		}

	function get_integer($name, $default = 0)
		{
			return array_key_exists($name, $_GET) ? intval($_GET[$name]) : $default;
		}

	function check_range($value, $min, $max)
		{
			return $value < $min ? $min : $value > $max ? $max : $value;
		}

	$path = Array();
	$path[0] = 'files/';
	$path[1] = 'files/img/';

	if (!empty($_GET['ext']))
		{
			if (strpos($_GET['ext'], ':') || strpos($_GET['ext'], '.') || strpos($_GET['ext'], '/') || strpos($_GET['ext'], '\\'))
				$extpath = '';
			else
				$extpath = $_GET['ext'].'/';
		}
	else
		$extpath = '';
	if (empty($_GET['path']) || !in_array($_GET['path'], Array(1, 2)))
		$_GET['path'] = 1;
	$filepath = $path[$_GET['path']];

	if (empty($_GET['img']) || strpos($_GET['img'], ':') || strpos($_GET['img'], '.') || strpos($_GET['img'], '/') || strpos($_GET['img'], '\\'))
		{
			$_GET['img'] = 'noimage';
			$filepath = $path[0];
			$extpath = '';
		}
	if ($_GET['png'] == 1)
		$image = '../'.$filepath.$extpath.$_GET['img'].'.png';
	elseif ($_GET['gif'] == 1)
		$image = '../'.$filepath.$extpath.$_GET['img'].'.gif';
	else
		$image = '../'.$filepath.$extpath.$_GET['img'].'.jpg';

	$new_width = check_range(get_integer('width'), 0, 2048);
	$new_height = check_range(get_integer('height'), 0, 2048);
	if (empty($_GET['background']))
		$_GET['background'] = 'FFFFFF';
	$background = get_string('background');

	$format = get_string('format');
	if (!in_array($format, array('gif', 'jpeg', 'png'))) $format = 'jpeg';
	$quality = check_range(get_integer('quality', -1), -1, 100);

	if (is_readable($image) and $file_size = filesize($image) and $file_time = filemtime($image))
		$cache_file = $cache_dir.'/'.md5($image.' '.$file_size.' '.$file_time.' '.$new_width.' '.$new_height.' '.$background.' '.$format.' '.$quality).'.'.$format;
	else
		{
			$file_size = 0;
			$file_time = 0;
			$cache_file = '';
		}

	if ($cache_file and !file_exists($cache_file) and is_writable($cache_dir) and $size = @getimagesize($image))
		{
			$width = $size[0];
			$height = $size[1];
			$type = $size[2];
			if ($width >= 1 and $height >= 1 and $type >= 1 and $type <= 3)
				{
					switch ($type)
					{
						case 1:
							$src = @imagecreatefromgif($image);
							break;
						case 2:
							$src = @imagecreatefromjpeg($image);
							break;
						case 3:
							$src = @imagecreatefrompng($image);
							break;
						default:
							$src = false;
					}
					if ($src)
						{
							if ($new_width and $new_height)
								{
									$ratio = $width / $height;
									$new_ratio = $new_width / $new_height;
									if ($ratio < $new_ratio)
										{
											$new_width = round($new_height * $ratio);
											if (!$new_width) $new_width = 1;
										}
									elseif ($ratio > $new_ratio)
										{
											$new_height = round($new_width / $ratio);
											if (!$new_height) $new_height = 1;
										}
									if ($dest = @imagecreatetruecolor($new_width, $new_height))
										{
											if ($background)
												{
													$background = hexdec($background);
													$color = imagecolorallocate($dest, ($background & 255 << 16) >> 16, ($background & 255 << 8) >> 8, $background & 255);
													imagefilledrectangle($dest, 0, 0, $new_width - 1, $new_height - 1, $color);
													imagecolordeallocate($dest, $color);
												}
											imagecopyresampled($dest, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
											imagedestroy($src);
										}
								}
							else $dest = $src;
							if ($dest)
								{
									switch ($format)
									{
										case 'gif':
											if (!imagegif($dest, $cache_file)) $cache_file = '';
											break;
										case 'jpeg':
											if (!($quality < 0 ? imagejpeg($dest, $cache_file) : imagejpeg($dest, $cache_file, $quality))) $cache_file = '';
											break;
										case 'png':
											if (!imagepng($dest, $cache_file)) $cache_file = '';
											break;
									}
									imagedestroy($dest);
									if ($cache_file) chmod($cache_file, 0666);
								}
						}
				}
		}

	if ($cache_file and is_readable($cache_file))
		{
			header('Content-type: image/'.$format);
			readfile($cache_file);
			touch($cache_file);
		}

	if (rand(1, 50) == 1 and $dir = opendir($cache_dir))
		{
			$time = time() - $cache_time;
			while ($item = readdir($dir))
				if (is_file($file = $cache_dir.'/'.$item) and filemtime($file) < $time) unlink($file);
			closedir($dir);
		}
?>
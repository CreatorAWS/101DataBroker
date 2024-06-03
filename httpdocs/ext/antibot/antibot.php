<?php
session_start();
include('../../includes/dbsettings.php');
$value=$_SESSION[$session_prefix.'protect_code'];
$length=4;
$block_size=15;
$width=$length*$block_size+5;
$height=$block_size+5;
$lines=50;
$font_size=12;
if ($image=imagecreatetruecolor($width,$height))
{
 $mx=$width-1;
 $my=$height-1;
 imagefilledrectangle($image,0,0,$mx,$my,imagecolorallocate($image,255,255,255));
 for ($i=0; $i<$lines; $i++)
  imageline($image,rand(0,$mx),rand(0,$my),rand(0,$mx),rand(0,$my),
   imagecolorallocate($image,rand(192,255),rand(192,255),rand(192,255)));
 for ($i=0; $i<4; $i++)
  imagettftext($image,$font_size,rand(0,1) ? rand(0,20) : rand(340,360),$i*$block_size+5,$block_size,
   imagecolorallocate($image,rand(0,127),rand(0,127),rand(0,127)),'./font.ttf',substr($value, $i, 1));
 header('Content-type: image/png');
 imagepng($image);
 imagedestroy($image);
}
?>
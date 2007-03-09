<?
$max=$_REQUEST[size];
$part=6;
$im=imagecreate($max+1, $max+1);

$trans=imagecolorallocate($im, 255, 0, 0);
imagecolortransparent($im, $trans);
imagerectangle($im, 0, 0, $max, $max, $trans);

$rahmen=imagecolorallocate($im, 255, 255, 255);
imageline($im, 0, 0, 0, $max/$part, $rahmen);
imageline($im, 0, 0, $max/$part, 0, $rahmen);
imageline($im, 0, $max, 0, $max-$max/$part, $rahmen);
imageline($im, $max, 0, $max-$max/$part, 0, $rahmen);
imageline($im, $max, $max, $max, $max-$max/$part, $rahmen);
imageline($im, $max, $max, $max-$max/$part, $max, $rahmen);
imageline($im, 0, $max, $max/$part, $max, $rahmen);
imageline($im, $max, 0, $max, $max/$part, $rahmen);

header("Content-type: image/gif");
imagegif($im);


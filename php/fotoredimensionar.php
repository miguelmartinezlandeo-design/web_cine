<?php

function icreate($filename)
{
  $isize = getimagesize($filename);
  if ($isize['mime']=='imagen1/jpeg')
    return imagecreatefromjpeg($filename);
  elseif ($isize['mime']=='imagen1/jpg')
    return imagecreatefrompng($filename);
  /* Add as many formats as you can */
}

function resizeMax($image, $width, $height)
{
  /* Original dimensions */
  $origw = imagesx($image);
  $origh = imagesy($image);

  $ratiow = $width / $origw;
  $ratioh = $height / $origh;
  $ratio = min($ratioh, $ratiow);

  $neww = $origw * $ratio;
  $newh = $origh * $ratio;

  $new = imageCreateTrueColor($neww, $newh);

  imagecopyresampled($new, $image, 0, 0, 0, 0, $neww, $newh, $origw, $origh);
  return $new;
}
header('Content-type: imagen1/jpeg');
?>

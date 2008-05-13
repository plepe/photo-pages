<?
function read_gps_data($ret, $page, $t) {
  global $file_path;
  global $orig_path;

  $e=exif_read_data("$file_path/{$t->path}/$orig_path/$t->img");

  if($lat=$e["GPSLatitude"]) {
    foreach($lat as $k=>$d1) {
      $d1=explode("/", $d1);
      $lat[$k]=$d1[0]/$d1[1];
    }
  }
  if($lon=$e["GPSLongitude"]) {
    foreach($lon as $k=>$d1) {
      $d1=explode("/", $d1);
      $lon[$k]=$d1[0]/$d1[1];
    }
  }
  if($lat[0]||$lat[1]||$lat[2]) {
    $ret["gps_pos"]=sprintf("%s %.0f° %.0f' %.2f\"<br>%s %.0f° %.0f' %.2f\"", $e["GPSLatitudeRef"], $lat[0], $lat[1], $lat[2], $e["GPSLongitudeRef"], $lon[0], $lon[1], $lon[2]);

    $lat=$lat[0]+$lat[1]/60+$lat[2]/3600;
    $lon=$lon[0]+$lon[1]/60+$lon[2]/3600;
    $ret["gps_pos"]=sprintf("<a href='http://maps.google.com/?ie=UTF8&ll=%F,%F&z=14'>%s</a>", $lat, $lon, $ret["gps_pos"]);

    if($alt=$e["GPSAltitude"]) {
      $d1=explode("/", $alt);
      $alt=$d1[0]/$d1[1];
      if($alt)
        $ret["gps_pos"].=sprintf("<br>Height %.0fm", $alt);
    }
  }
}

register_hook("img_details", read_gps_data);

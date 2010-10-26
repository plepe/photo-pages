<?

// Google Maps API Key: ABQIAAAAeEqsE9X0Q906P0rnPSlJeRQZ4mIvHXT2Uizv1wYK190UnoknvxQxWepPo9wxeynr65DyANRzv3ixrw

function gps_load_route($page) {
  global $file_path;

  if(file_exists("$file_path/{$page->path}/route.gpx")) {
    $dom=new DOMDocument();
    if(!$dom->load("$file_path/{$page->path}/route.gpx")) {
      echo "Error while parsing route data\n";
    }

    $tracks=$dom->getElementsByTagname("trk");
    foreach($tracks as $track) {
      $trkpts=$track->getElementsByTagname("trkpt");
      $route_part=array();
      foreach($trkpts as $trkpt) {
        $pos=array("lat"=>$trkpt->getAttribute("lat"), "lon"=>$trkpt->getAttribute("lon"));
//        if($x=$trkpt->getElementsByTagname("ele")) {
//          $pos["ele"]=$x->item(0)->nodeValue;
//        }
//        if($x=$trkpt->getElementsByTagname("time")) {
//          $pos["time"]=$x->item(0)->nodeValue;
//        }
        $route_part[]=$pos;
      }

      $route[]=$route_part;
    }
  }

  return $route;
}

function gps_show_geo_info($text, $page, $t) {
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
  if(!($lat[0])&&(!$lat[1])&&(!$lat[2])) {
    return;
  }

  add_toolbox_item("gps", 
    sprintf("Latitude: %s %.0f° %.0f' %.0f\"<br>Longitude: %s %.0f° %.0f' %.0f\"", $e["GPSLatitudeRef"], $lat[0], $lat[1], $lat[2], $e["GPSLongitudeRef"], $lon[0], $lon[1], $lon[2]));

  $lat=($e["GPSLatitudeRef"]=="S"?-1:1)*($lat[0]+$lat[1]/60+$lat[2]/3600);
  $lon=($e["GPSLatitudeRef"]=="W"?-1:1)*($lon[0]+$lon[1]/60+$lon[2]/3600);
  $ret["gps_pos"]=sprintf("<a href='http://maps.google.com/?ie=UTF8&ll=%F,%F&z=14'>%s</a>", $lat, $lon, $ret["gps_pos"]);

  if($alt=$e["GPSAltitude"]) {
    $d1=explode("/", $alt);
    $alt=$d1[0]/$d1[1];
    if($alt)
      add_toolbox_item("gps", sprintf("<br>Height: %.0fm", $alt));
  }

  $route=gps_load_route($page);
  add_toolbox_item("gps", 
//  sprintf("<iframe src='http://openlayers.org/viewer/?center=%F,%F&zoom=12' ".
//              "width='240' height='200' ".
//              "scrolling='no' ".
//              "marginwidth='0' marginheight='0' ".
//              "frameborder='0'></iframe>", $lat, $lon));
  sprintf("<div id='gps_map' pos_lat='%F' pos_lon='%F'></div>",
          $lat, $lon));

  $text.=show_toolbox("gps");
  html_export_var(array("gps_route"=>$route));
}

function gps_header($text, $page) {
  global $gps_google_api_keys;

  $api_key=$gps_google_api_keys[$_SERVER['HTTP_HOST']];
  $text.="<script src='http://openlayers.org/api/OpenLayers.js'></script>\n";
  $text.="<script src='http://maps.google.com/maps?file=api&v=2&key=$api_key' type='text/javascript'></script>\n";
  $text.="<script src='http://www.openstreetmap.org/openlayers/OpenStreetMap.js'></script>\n";
}

//register_hook("img_details", read_gps_data);
register_hook("image_toolboxes", gps_show_geo_info);
register_hook("header", gps_header);

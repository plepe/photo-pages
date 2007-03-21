<?
include "extensions/details.php";

function details_search($found, $page, $img) {
  $desc=details_load($page, $img);

  if(in_array($img->type, array("SubdirChunk", "SeriesChunk", "HeaderChunk")))
    $found=1;

  foreach($desc as $d) {
    if(stristr($d["desc"], $_REQUEST[search_query]))
      $found=1;
  }
}

function details_album_desc($desc, $page, $img) {
  global $lang_str;

  $details=details_load($page, $img);

  if(sizeof($details)) {
    $desc.="$lang_str[details_desc]: ";
    $list=array();
    foreach($details as $d)
      $list[]=$d[desc];
    $desc.=implode(", ", $list);
    $desc.="<br>\n";
  }
}

register_hook("search", details_search);
register_hook("album_desc", details_album_desc);

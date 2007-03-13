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

register_hook("search", details_search);

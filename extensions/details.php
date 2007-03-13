<?
function details_load($page, $img) {
  global $page;
  global $file_path;
  $details_desc=array();

  if(file_exists("$file_path/$img->path/details/{$img->img}")) {
    $f=fopen("$file_path/$img->path/details/{$img->img}", "r");
    while($r=fgets($f)) {
      $r=chop($r);
      eregi("^([0-9]+):([0-9]+):(.*)", $r, $m);
      $details_desc[]=array("x"=>$m[1], "y"=>$m[2], "desc"=>$m[3]);
    }
  }

  return $details_desc;
}



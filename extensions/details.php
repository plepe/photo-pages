<?
function details_load() {
  global $page;
  global $file_path;

  if(file_exists("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}")) {
    $details=array();
    $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "r");
    while($r=fgets($f)) {
      eregi("^([0-9]+):([0-9]+):(.*)", $r, $m);
      $details[]=array("x"=>$m[1], "y"=>$m[2], "desc"=>$m[3]);
    }

    html_export_var(array("details_desc"=>$details));
  }
  else {
    html_export_var(array("details_desc"=>array()));
  }
}



register_hook("album_modify_list", details_load);

$str="";
$str.="<input type='submit' value='$lang_str[details_name]' onClick='details_choosepos()' id='details_button' class='toolbox_input'>\n";

add_toolbox_item("imageview_toolbox", $str);

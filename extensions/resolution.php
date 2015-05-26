<?
global $normal_res;

function resolution_select(&$params, $page, $img) {
  global $lang_str;
  global $resolutions;
  global $normal_res;
  global $file_path;
  global $orig_path;

  $str ="$lang_str[info_resolution]: <select name='resolution_choice' id='resolution_choice' class='toolbox_input' onChange='set_normal_res()'>\n";
  $d=opendir("$file_path/$img->path/");
  $reslist=array();
  while($r=readdir($d)) {
    if(eregi("^([0-9]+)$", $r))
      $reslist[]=$r;
  }
  $reslist[]=$orig_path;
  closedir($d);

  foreach($reslist as $res) {
    $str.="<option value='$res'";
    if($res==$normal_res)
      $str.=" selected";
    $str.=">$res</option>\n";
  }
  $str.="</select><br>\n";

  add_toolbox_item("imageview_toolbox", $str);
}

# normal_res ueberschreiben
if($_SESSION[normal_res])
  $normal_res=$_SESSION[normal_res];

$_SESSION[normal_res]=$normal_res;

register_hook("imageview", resolution_select);

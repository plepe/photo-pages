<?
global $lang_str;
global $resolutions;
global $normal_res;

# normal_res ueberschreiben
if($_SESSION[normal_res])
  $normal_res=$_SESSION[normal_res];

$str ="$lang_str[info_resolution]: <select name='resolution_choice' id='resolution_choice' class='toolbox_input' onChange='set_normal_res()'>\n";
foreach($resolutions as $res) {
  $str.="<option value='$res'";
  if($res==$normal_res)
    $str.=" selected";
  $str.=">$res</option>\n";
}
$str.="</select><br>\n";

add_toolbox_item("imageview_toolbox", $str);
$_SESSION[normal_res]=$normal_res;
session_register("normal_res");

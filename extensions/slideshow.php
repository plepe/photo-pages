<?
global $lang_str;

if(!$_SESSION[slideshow_time]) {
  session_register("slideshow_active");
  session_register("slideshow_time");
  $_SESSION[slideshow_time]=5;
}

$str="";
$str.="<input type='submit' value='$lang_str[slideshow_name]' class='".($_SESSION[slideshow_active]?"toolbox_input_active":"toolbox_input")."' onClick='toggle_slideshow()' id='slideshow_button'>\n";
$str.="<select id='slideshow_time' class='toolbox_input' onChange='set_slideshow_time()'>\n";
foreach(array(5, 10, 15) as $t) {
  $str.="  <option value='$t'";
  if($_SESSION[slideshow_time]==$t)
    $str.=" selected";
  $str.=">$t $lang_str[slideshow_sec]</option>\n";
}
$str.="</select><br>\n";

add_toolbox_item("toolbox", $str);
html_export_var(array("slideshow_active"=>$_SESSION[slideshow_active], "slideshow_time"=>$_SESSION[slideshow_time]));

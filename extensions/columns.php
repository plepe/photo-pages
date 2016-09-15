<?
global $lang_str;
global $cols;

if(!$cols)
  $cols=$_SESSION["cols"];
if(!$cols) {
  $cols=4;
  $_SESSION["cols"]=4;
}

$ret="";
$ret.="$lang_str[nav_columns]:\n";
for($i=3;$i<7;$i++) {
  $ret.="<input type='submit' onClick='columns_change_cols($i)' class='";
  $ret.=($cols==$i?"toolbox_input_active":"toolbox_input");
  $ret.="' id='cols_$i' title=\"$lang_str[tooltip_change_cols]\" value=\"$i\">\n";
}
$ret.="<br>\n";

add_toolbox_item("album_toolbox", $ret);

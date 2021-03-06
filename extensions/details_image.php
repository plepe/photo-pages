<?
include "extensions/details.php";

function details_image_load($page, $img) {
  global $details_desc;

  $details_desc=details_load($page, $img);

  html_export_var(array("details_desc"=>$details_desc));
}

function details_list_details(&$text, $page, $img) { 
  global $details_desc;
  global $lang_str;

  details_image_load($page, $img);

  if(sizeof($details_desc)) {
    $text.="<span id='detail_list'>$lang_str[details_desc]: ";
    $list=array();
    foreach($details_desc as $dk=>$d) {
      $list[]="<span onMouseOver='details_show_single($dk)' onMouseOut='details_hide_single($dk)' id='detail_$dk'>$d[desc]</span>";
    }

    $text.=implode(", ", $list);
    $text.="</span>\n";
  }
  else {
    $text.="<span id='detail_list'></span>\n";
  }
} 


register_hook("image_description", details_list_details);

$str="";
$str.="<input type='submit' value='$lang_str[details_name]' onClick='details_choosepos()' id='details_button' class='toolbox_input' accesskey='d'><br>\n";

add_toolbox_item("imageview_admintools", $str);

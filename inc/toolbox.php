<?
$toolbox_data=array();

function show_toolbox($toolbox) {
  global $toolbox_data;

  $ret ="<div class='toolbox' id='toolbox_$toolbox'>\n";
  $ret.=$toolbox_data[$toolbox];
  $ret.="</div>\n";
  return $ret;
}

function add_toolbox_item($toolbox, $item) {
  global $toolbox_data;

  $toolbox_data[$toolbox].=$item;
}

<?
$toolbox_data=array();

function show_toolbox($toolbox) {
  global $toolbox_data;
  global $extensions_page;

  if($toolbox_data[$toolbox]) {
    $ret ="<div class='toolbox_$extensions_page' id='toolbox_$toolbox'>\n";
    $ret.=$toolbox_data[$toolbox];
    $ret.="</div>\n";
  }

  return $ret;
}

function add_toolbox_item($toolbox, $item) {
  global $toolbox_data;

  $toolbox_data[$toolbox].=$item;
}

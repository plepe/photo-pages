<?
$text_data=array();

function show_text($place) {
  global $text_data;

  if($text_data[$place])
    return $text_data[$place];

  return "";
}

function add_text_item($place, $item) {
  global $text_data;

  $text_data[$place].=$item;
}

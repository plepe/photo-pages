<?
global $lang_str;
global $language;

$ret ="";
$ret.="<form name='langchoose_form' method='post'>\n";
$ret.="$lang_str[nav_language]:\n";
$ret.="<select name='language' class='toolbox_input' id='langchoose_select' onChange='langchoose_change()'>\n";
foreach(lang_list() as $l=>$lname) {
  $ret.="<option value='$l'";
  if($l==$language)
    $ret.=" selected";
  $ret.=">$lname</option>\n";
}
$ret.="</select>\n";
$ret.="<input type='hidden' name='page' value='$this->path'>\n";
$ret.="<input type='hidden' name='series' value='$this->series'>\n";
$ret.="<input type='submit' value='$lang_str[nav_ok]' class='toolbox_input'>\n";
$ret.="</form>\n";

add_toolbox_item("album_toolbox", $ret);
html_export_var(array("language"=>$_SESSION[language]));

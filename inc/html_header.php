<?
function use_javascript($file) {
  global $url_javascript;
  global $page;

  print sprintf("<script src='$url_javascript' type='text/javascript'></script>\n", $page->path, $page->series, "$file.js");
}

function start_html_header($title) {
  Header("content-type: text/html; charset=iso-8859-15");
  setlocale(LC_ALL, "de_AT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Photopage :: <?=$title?></title>
<link rel=stylesheet type="text/css" href="<?=url_img("style.css");?>">
<?
use_javascript("global");
}

function end_html_header() {
  ?>
</head>
  <?
}

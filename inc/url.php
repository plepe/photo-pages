<?
function urls_write() {
  global $url_page;
  global $url_photo;
  global $url_script;
  global $url_javascript;
  global $url_img;

  use_javascript("url");
  print "<script type='text/javascript'>\n";
  print "var v_url_page=\"$url_page\";\n";
  print "var v_url_photo=\"$url_photo\";\n";
  print "var v_url_script=\"$url_script\";\n";
  print "var v_url_javascript=\"$url_javascript\";\n";
  print "var v_url_img=\"$url_img\";\n";
  print "</script>\n";
}

function url_page($path, $series, $skript) {
  global $url_page;

  return sprintf($url_page, $path, $series, $skript);
}

function url_photo($path, $series, $skript, $imgnum, $imgname, $size, $imgversion) {
  global $url_photo;

  return sprintf($url_photo, $path, $series, $skript, $imgnum, $imgname, $size, $imgversion);
}

function url_script($path, $series, $skript, $imgnum) {
  global $url_script;

  return sprintf($url_script, $path, $series, $skript, $imgnum);
}

function url_javascript($path, $series, $skript, $imgnum) {
  global $url_javascript;

  return sprintf($url_javascript, $path, $series, $skript, $imgnum);
}

function url_img($imgfile) {
  global $url_img;

  return sprintf($url_img, $imgfile);
}

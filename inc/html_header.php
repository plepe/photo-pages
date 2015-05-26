<?
/* html_header.php
 * - Writes a nice HTML Header
 *
 * Copyright (c) 1998-2006 Stephan Plepelits <skunk@xover.mud.at>
 *
 * This file is part of Skunks' Photosscripts 
 * - http://xover.mud.at/~skunk/proj/photo
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
$finished_http_header=null;
$saved_js="";
$saved_css="";

function use_javascript($file=0) {
  global $url_javascript;
  global $page;
  global $finished_http_header;
  global $saved_js;

  if($file)
    $saved_js.="<script src='".url_javascript(array("script"=>"$file.js"))."' type='text/javascript'></script>\n";

  if($finished_http_header) {
    print $saved_js;
    $saved_js="";
  }
}

function use_css($file=0) {
  global $url_javascript;
  global $page;
  global $finished_http_header;
  global $saved_css;

  if($file)
    $saved_css.="<link rel=stylesheet type='text/css' href=\"".url_javascript(array("script"=>"$file.css"))."\">\n";

  if($finished_http_header) {
    print $saved_css;
    $saved_css="";
  }
}

function start_html_header($title) {
  global $finished_http_header;

  Header("content-type: text/html; charset=utf-8");
  setlocale(LC_ALL, "de_AT");
  $finished_http_header=1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Photopage :: <?=$title?></title>
<link rel=stylesheet type="text/css" href="<?=url_img("style.css");?>">
<?
use_javascript();
use_css();
$text="";
call_hooks("header", $text, $page);
print $text;
html_export_var(array());
}

function end_html_header() {
  ?>
</head>
  <?
}

function html_footer() {
  global $lang_str;

  if(file_exists("VERSION")) {
    $f=fopen("VERSION", "r");
    $version=fgets($f);
    $version=chop($version);
    fclose($f);
  }
  else {
    $f=popen("svn info", "r");
    $x=array();
    while($r=fgets($f)) {
      if(ereg("^([^:]*): (.*)$", $r, $m)) {
        $x[$m[1]]=$m[2];
      }
    }
    if(!$x[Revision]) {
      $version="unknown";
    }
    else {
      $date=explode(" ", $x["Last Changed Date"]);
      $version="svn-r$x[Revision] ($date[0])";
    }
    pclose($f);
  }

  $footer=strtr($lang_str[footer], array("%version%"=>$version));
  print "<div class='footer'>$footer</div>\n";
  print "</body></html>\n";
}

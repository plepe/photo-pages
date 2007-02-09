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
function use_javascript($file) {
  global $url_javascript;
  global $page;

  print "<script src='".url_javascript(array("script"=>"$file.js"))."' type='text/javascript'></script>\n";
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

function html_footer() {
  global $lang_str;
  print "<div class='footer'>$lang_str[footer]</div>\n";
  print "</body></html>\n";
}

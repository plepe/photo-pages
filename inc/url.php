<?
/* url.php
 * - Correct URLs for PHP
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

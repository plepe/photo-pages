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
function build_url($template, $params) {
  $p=array();
  $erg=$template;

  foreach($params as $key=>$v) {
    if(strpos($erg, "%$key%")!==false)
      $erg=str_replace("%$key%", $v, $erg);
    elseif(isset($v))
      array_push($p, "$key=$v");
  }

  if(sizeof($p))
    return $erg."?".implode("&", $p);
  else
    return $erg;
}

function urls_write() {
  global $url_page;
  global $url_photo;
  global $url_script;
  global $url_javascript;
  global $url_img;
  global $page;

  use_javascript("url");
  print "<script type='text/javascript'>\n";
  print "<!--\n";
  print "var series=\"{$page->series}\";\n";
  print "var page=\"{$page->path}\";\n";
  print "var v_url_page=\"$url_page\";\n";
  print "var v_url_photo=\"$url_photo\";\n";
  print "var v_url_script=\"$url_script\";\n";
  print "var v_url_javascript=\"$url_javascript\";\n";
  print "var v_url_img=\"$url_img\";\n";
  print "//-->\n</script>\n";
  print "</script>\n";
}

function url_page($path, $series=0, $script=0) {
  global $url_page;

  if(is_array($path)) {
    if($path["page"]->series)
      $path["series"]=$path["page"]->series;
    $path["page"]=$path["page"]->path;

    return build_url($url_page, $path);
  }

  return build_url($url_page, array("page"=>$path, "series"=>$series));
}

function url_photo($path, $series, $skript, $imgnum, $imgname, $size, $imgversion) {
  global $url_photo;

  if(is_array($path)) {
    if($path["page"]->series)
      $path["series"]=$path["page"]->series;
    $path["page"]=$path["page"]->path;

    return build_url($url_photo, $path);
  }

  return build_url($url_photo, array("page"=>$path, "series"=>$series, "img"=>$imgnum, "imgname"=>$imgname, "size"=>$size, "version"=>$imgversion));
}

function url_script($path, $series=0, $script=0, $imgnum=0) {
  global $url_script;

  if(is_array($path)) {
    if($path["page"]->series)
      $path["series"]=$path["page"]->series;
    $path["page"]=$path["page"]->path;

    return build_url($url_script, $path);
  }

  return build_url($url_script, array("page"=>$path, "series"=>$series, "script"=>$script, "img"=>$imgnum));
}

function url_javascript($path, $series=0, $script=0, $imgnum=0) {
  global $url_javascript;

  if(is_array($path)) {
    if($path["page"]->series)
      $path["series"]=$path["page"]->series;
    $path["page"]=$path["page"]->path;

    return build_url($url_javascript, $path);
  }

  return build_url($url_javascript, array("page"=>$path, "series"=>$series, "script"=>$script, "img"=>$imgnum));
}

function url_img($imgfile) {
  global $url_img;

  if(is_array($imgfile)) {
    return build_url($url_img, $imgfile);
  }

  return build_url($url_img, array("imgname"=>$imgfile));
}

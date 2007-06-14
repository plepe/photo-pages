<?
/* main.php
 * - The right page in a frameview with no picture shown
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
$extensions_page="get_image";
include "data.php";

if($_REQUEST[tmp_id]) {
  $tmp_id=$_REQUEST[tmp_id];
  $_REQUEST["img"]=$_SESSION["tmp_$tmp_id"]["img"];
  $_REQUEST["page"]=$_SESSION["tmp_$tmp_id"]["page"];
  $_REQUEST["series"]=$_SESSION["tmp_$tmp_id"]["series"];
  $_REQUEST["size"]=$_SESSION["tmp_$tmp_id"]["size"];
  $page=get_page($_REQUEST[page], $_REQUEST[series]);
  $page->get_viewlist();
}

if($_REQUEST[img]==="main") {
  eregi("^(.*/)?([^/]*)$", $page->cfg[MAIN_PICTURE], $m);
  $filename="$file_path/{$page->path}/$m[1]/$_REQUEST[size]/$m[2]";
}
else {
  if($_REQUEST["img"]>=sizeof($page->cfg["LIST"])) {
    if($page->get_right($_SESSION[current_user], "edit")) {
      $unused=$page->page_edit_load_unused_images(1);
      $img=$unused[$_REQUEST[img]-sizeof($page->cfg["LIST"])];
    }
  }
  else {
    $img=$page->cfg["LIST"][$_REQUEST[img]];
  }
  if($_REQUEST[size]=="flv") {
    eregi("^(.*)\.([^.]+)$", $img->mov, $m);
    $flv_file="$m[1].flv";
    $largest_path=$img->get_largest_path($flv_file);
    $filename="$file_path/$img->path/$largest_path/$flv_file";
  }
  elseif($_REQUEST[size]=="movie") {
    $largest_path=$img->get_largest_path($img->mov);
    $filename="$file_path/$img->path/$largest_path/$img->mov";
  }
  else
    $filename="$file_path/$img->path/$_REQUEST[size]/$img->img";
}
//print $filename;

call_hooks("image_modify", &$filename, $img);

$type=mime_content_type($filename);
Header("content-type: $type");
Header("Last-Modified: ".http_date($page->last_modified()));
Header("Expires: ".http_date($page->last_modified()+8640000));
Header("Cache-Control:");
Header("Pragma:");

$f=fopen("$filename", "r");
while($r=fread($f, 1024)) {
  print $r;
}
fclose($f);

call_hooks("image_done", &$filename, $img);

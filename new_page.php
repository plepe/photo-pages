<? 
/* upload_image.php
 * - You can use this file to upload images.
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
require "data.php";
setlocale(LC_ALL, "de_AT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel=stylesheet type="text/css" href="<?=url_img("style.css");?>">
<title><?=$cfg[TITLE]?></title>
</head>
<BODY>
<?
print $page->header();

if($data=$_REQUEST[data]) {
  $error=array();
  if($data[page_name]=="")
    $error[]=$lang_str[new_page_no_page_name];
  if(substr($data[page_name], 0, 1)==".")
    $error[]=$lang_str[new_page_no_dot];

  if(sizeof($error)) {
    foreach($error as $e)
      print "$e<br>\n";
  }
  else {
    $v="$file_path/$page->path/$data[page_name]";
    mkdir($v);
    $f=fopen("$v/fotocfg.txt", "w");
    fwrite($f, "TITLE $data[title]\n\n");

    print "Verzeichnis angelegt.<br>\n";
    print "<a href='".url_page("$page->path/$data[page_name]", "", "index.php")."'>&Ouml;ffne Seite</a>\n";
  }
}

if(!$_REQUEST[data]) {
  print "<p>\n";
  print "<a href='".url_page($page->path, $page->series, "index.php")."'>Back</a> /\n";
  print "<a href='".url_script($page->path, $page->series, "page_edit.php", null)."'>Edit Page</a>\n";
  print "<p>\n";
  print "<form action='".url_script($page->path, $page->series, "new_page.php", null)."' method='post' ".
	"enctype='multipart/form-data'>\n";
  print "<table>\n";
  print "<tr><td>Titel der Unterseite:</td><td><input name='data[title]'></td></tr>";
  print "<tr><td>Kurzbezeichnung:</td><td><input name='data[page_name]'></td></tr>";
  print "<tr><td colspan='2'><input type='submit' value='Ok'></td></tr>\n";
  print "</table>\n";
  print "</form>\n";
}

html_footer();

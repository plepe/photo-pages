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
start_html_header($page->cfg[TITLE]);
urls_write();
?>
<BODY>
<?
print $page->header();

$data=$_REQUEST[data];
if($_REQUEST[submit_ok]) {
  $data[title]=stripslashes($data[title]);
  $data[page_name]=stripslashes($data[page_name]);
  // Replace spaces in filename through _
  $data[page_name]=replace_invalid_chars($data[page_name]);

  $error=array();
  if(is_dir("$file_path/$page->path/$data[page_name]")) {
    $error[]="$lang_str[new_page_dir_exists]";
  }

  if($data[page_name]=="")
    $error[]=$lang_str[new_page_no_page_name];
  if(substr($data[page_name], 0, 1)==".")
    $error[]=$lang_str[new_page_no_dot];
  if(!preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\-\.:]*$/", $data[page_name]))
    $error[]="\"$data[page_name]\" $lang_str[error_invalid_chars]";

  if(sizeof($error)) {
    foreach($error as $e)
      print "$e<br>\n";
    unset($_REQUEST[submit_ok]);
  }
  else {
    switch($data[subpage]) {
      case "main":
        $v="$file_path/$data[page_name]";
        break;
      case "sub":
        $v="$file_path/$page->path/$data[page_name]";
        break;
    }

    mkdir($v);
    $f=fopen("$v/fotocfg.txt", "w");
    fwrite($f, "TITLE $data[title]\n\n");

    print "$lang_str[new_page_done].<br>\n";
    print "<a href='".url_page("$page->path/$data[page_name]", "", "index.php")."'>$lang_str[new_page_go_there]</a>\n";
  }
}
else {
  $data=array("subpage"=>"main");
}

if(!$_REQUEST[submit_ok]) {
  print "<p>\n";
  print "<a href='".url_page($page->path, $page->series, "index.php")."'>Back</a> /\n";
  print "<a href='".url_script($page->path, $page->series, "page_edit.php", null)."'>Edit Page</a>\n";
  print "<p>\n";
  print "<form action='".url_script($page->path, $page->series, "new_page.php", null)."' method='post' ".
	"enctype='multipart/form-data'>\n";
  print "<table>\n";
  print "<tr><td>$lang_str[new_page_dir]:</td><td><input name='data[page_name]' value=\"$data[page_name]\"></td></tr>";
  print "<tr><td>$lang_str[new_page_title]:</td><td><input name='data[title]' value=\"$data[title]\"></td></tr>";
  print "<tr><td>";
  foreach(array("main", "sub") as $a) {
    print "<input type='radio' name='data[subpage]' value='$a'";
    if($a==$data[subpage])
      print " checked";
    print "> {$lang_str["new_page_$a"]}<br>\n";
  }

  print "<tr><td colspan='2'><input type='submit' name='submit_ok' value='$lang_str[new_page_ok]'></td></tr>\n";
  print "</table>\n";
  print "</form>\n";
}

html_footer();

<? 
/* index.php
 * - The entry page to a photopage
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
$extensions_page="album";
require "data.php";

start_html_header($page->cfg[TITLE]);
use_javascript("index");
use_javascript("user");
urls_write();
end_html_header();

if ($_REQUEST['page'] && !preg_match("/^[a-zA-Z\-0-9_\/]+$/", $_REQUEST['page'])) {
  print "Illegal page!";
  exit(1);
}

?>
<BODY>
<?
if($_REQUEST[cols]) {
  $_SESSION[album_cols]=$_REQUEST[cols];
}

if($_REQUEST[rows]) {
  $_SESSION[album_rows]=$_REQUEST[rows];
}

$img=0;
if($_REQUEST["img"])
  $img=$_REQUEST["img"];

if(eregi("img_(.*)$", $img, $m)) {
//  foreach($list as $el)
//    if($el->file_name()==$m[1])
//      $img=$el->get_index();
}

if(!($cols=$_SESSION[cols])) # absichtliche Zuweisung
  $cols=4;

print "<script type='text/javascript'>\n<!--\ncols=$cols;\nrows=$rows;\n//-->\n</script>\n";

print "<table class='heading'>\n";
print "<tr><td class='heading_info' colspan='2'>\n";
//print $page->show_path();
print $page->header();
if($page->get_right($_SESSION[current_user], "view")) {
  print $page->welcome();
}
$text="";
call_hooks("album_heading", $text, $page);
print $text;


print "</td>\n";
print "<td class='heading_tools' rowspan='2'>\n";
print $_SESSION[current_user]->toolbox();
print $page->toolbox();
print show_toolbox("album_toolbox");
print show_toolbox("album_admin");

  print "</td></tr>\n";

print "<tr><td class='heading_spacer'></td><td class='heading_nav'>\n";
//print $page->album_nav($img);
print show_text("album_subheading");
print "</td>\n";

  print "</tr></table>\n";

//print "<pre>\n";
//print_r($page->get_rights($_SESSION[current_user]));
//print_r($_SESSION);
//print "</pre>\n";

if($page->get_right($_SESSION[current_user], "view")) {
  $page->read_list();

  print show_text("album_start");

  $page->show_album($img);

  print show_text("album_end");

  small_login_form();
}

if((!$page->get_right($_SESSION[current_user], "view"))||
   ($page->hidden_files)) {
  login_form();
  small_login_form();
}

print $page->show_path();

html_footer();

?>

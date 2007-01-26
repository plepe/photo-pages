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
require "data.php";

start_html_header($page->cfg[TITLE]);
use_javascript("index");
use_javascript("user");
end_html_header();

?>
<BODY>
<?
if($cols) {
  $album_cols=$cols;
  session_register("album_cols");
}

if(!($cols=$album_cols)) # absichtliche Zuweisung
  $cols=4;

print "<script type='text/javascript'>\n<!--\ncols=$cols;\n//-->\n</script>\n";

print "<table class='heading'>\n";
print "<tr><td class='heading_info'>\n";
//print $page->show_path();
print $page->header();
if($page->get_right($_SESSION[current_user], "view")) {
  print $page->welcome();
}

print "</td>\n";
print "<td class='heading_tools'>\n";
print $_SESSION[current_user]->toolbox();
print $page->toolbox();

  print "</td>\n";
  print "</tr></table>\n";

//print_r($page->get_rights($_SESSION[current_user]));
if($page->get_right($_SESSION[current_user], "view")) {
  $page->read_list();

  $page->show_album();

  small_login_form();
}

if((!$page->get_right($_SESSION[current_user], "view"))||
   ($page->hidden_files)) {
  login_form();
}

print $page->show_path();

?>

<? 
/* page_edit.php
 * - This page lets you edit a particular photopage
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
$extensions_page="page_edit";
require "data.php";
if($_REQUEST[submit][ok]) {
  $result=$page->set_page_edit_data($_REQUEST[data]);
}
start_html_header($page->cfg[TITLE]);
use_javascript("page_edit");
urls_write();
end_html_header();

?>
<BODY onLoad='global_initfun()' onUnLoad='call_events(event)'>
<?
print "<div>".$page->get_path()."</div>\n";
print "<div class='wait_screen' id='wait_screen'><table width='100%' height='100%'><tr><td align='center' valign='middle'>Please wait</td></tr></table></div>\n";
print "<p>\n";

if($_REQUEST[submit][ok]) {
  if($result)
    print $lang_str[page_edit_saved];
}

if($_REQUEST[submit][ok]) {
  print "<br><br><a href='".url_page($page->path, $page->series, "index.php")."'>Zur&uuml;ck</a>.";
}
else {
  $page->show_page_edit_form();
}

html_footer();

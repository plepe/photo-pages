<?
/* list.php
 * - The left list for the frameview
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
include "data.php";
start_html_header($page->cfg[TITLE]);
use_javascript("list");
end_html_header();
?>
<BODY onScroll='notify_scroll()'>
<center>
<?

if(!$img)
  $img=0;

$list=$page->cfg["LIST"];
unset($cur_index);

print "    <a name='img_0'></a>\n";
foreach($list as $el) {
  if($el->get_index()!==$cur_index) {
    $cur_index=$el->get_index();
    print "    <a name='img_".($cur_index+1)."'></a>\n";
  }

  print "<div class='list_".$el->html_class()."' onClick='notify_click()'>\n";
  print $el->list_show();
  print "</div>\n";
}


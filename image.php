<?
/* image.php 
 * - Shows a single image
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
$extensions_page="imageview";
include "data.php";
$img=$_REQUEST[img];
if(!$img)
  $img="0";

start_html_header("{$page->cfg[TITLE]} :: Bild ".($img+1));
use_javascript("image");
use_javascript("magnify");
urls_write();
end_html_header();


?>
<BODY onLoad='global_initfun()' onUnLoad='call_events(event)'>
<?
$width=$_REQUEST[width];

if(!ereg("^[0-9]*$", $width)) {
  print "Ung&uuml;ltige Bildgr&ouml;&szlig;e<br>\n";
  unset($width);
}

$nav=$page->get_chunk_nav($img);
print $nav;
print "<div>".$page->get_path()."</div>\n";

//print $page->short_header();

$list=$page->cfg["LIST"];
foreach($page->cfg["LIST"] as $el) {
  if($el->get_index()==$img) {
    print "<div index='$img' class='imageview_".$el->html_class()."'>\n";
    print $el->imageview_show();
    print "</div>\n";
  }
}

    print "<div id='debug'>\n";
    print "&nbsp;</div>\n";

?>
<div class='message' id='message'>
no message
</div>
<?
html_footer();

<? 
/* frame.php
 * - The parent of the frame-view
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
end_html_header();

if (preg_match("/^[0-9]+$/", $_REQUEST['img'])) {
  $img = $_REQUEST['img'];
}

?>
<FRAMESET cols="250,*" border=0 frameborder=0 framespacing=0 bordercolor="#0080FF">
<?
if($img) {
  print "<FRAME SRC=\"".url_script($page->path, $page->series, "list.php", "")."#img_$img\" NAME=\"list\" noresize>\n";
  print "<FRAME SRC=\"".url_script($page->path, $page->series, "image.php", $img)."\" NAME=\"main\">\n";
}
else {
  print "<FRAME SRC=\"".url_script($page->path, $page->series, "list.php", "")."\" NAME=\"list\" noresize>\n";
  print "<FRAME SRC=\"".url_skript($page->path, $page->series, "image.php", "")."\" NAME=\"main\">\n";
}
?>
</FRAMESET>

<NOFRAMES><BODY>

<CENTER><P><FONT COLOR="#FFFFFF"><FONT SIZE=+2><? echo $cfg[TITLE]; ?></FONT></FONT></P></CENTER>

<P><FONT COLOR="#FFFFFF"><FONT SIZE=-1>Your Browser doesn't support Frames.
</p>
<p>
<font size=+2><? echo $cfg[DATE]; ?></font><br>
Photos by <? echo $cfg[PHOTOS_BY]; ?></p>
<p>
<?
print "Here you have <a href=\"".url_script($page->path, $page->series, "list.php", "")."\">the list of Pictures</a></P>\n";
html_footer();

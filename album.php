<?
/* album.php
 * - Album View of a photopage
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
Header("Last-Modified: ".http_date($page->last_modified()));
Header("Expires: ".http_date($page->last_modified()+8640000));
Header("Cache-Control:");
Header("Pragma:");

start_html_header($page->cfg[TITLE]);
use_javascript("album");
end_html_header();

?>
</head>
<BODY onLoad='init_album()'>
<table border=0><tr><td colspan=2>
<?

//$zoom=$_REQUEST[zoom];
$start=$_REQUEST[start];
$cols=$_REQUEST[cols];
//$zoom=$_REQUEST[zoom];

//if(!ereg("^[0-9]*$", $rows)) {
//  unset($rows);
//}
if(!ereg("^[0-9]*$", $start)) {
  unset($start);
}
if(!ereg("^[0-9]*$", $cols)) {
  unset($cols);
}
//if(!ereg("^[0-9]*$", $zoom)) {
//  print "Ung&uuml;ltige Bildgr&ouml;&szlig;e<br>\n";
//  unset($zoom);
//}

//$album_width=$_SESSION[album_width];
$album_rows=$_SESSION[album_rows];
$album_cols=$_SESSION[album_cols];

//if($zoom) {
//  $album_width=$zoom;
//  session_register("album_width");
//}
if($rows) {
  $album_rows=$rows;
  session_register("album_rows");
}
if($cols) {
  $album_cols=$cols;
  session_register("album_cols");
}

//$album_rows=9999;
//if(!$album_width)
//  $album_width=200;
//$zoom=$album_width;

if(!($cols=$album_cols))
  $cols=4;

print "<script type='text/javascript'>\n<!--\ncols=$cols;\n//-->\n</script>\n";
//if(!($rows=$album_rows))
//  $rows=5;

print "<div class='choose_cols'>\n";
for($i=3;$i<7;$i++) {
  if($cols==$i)
    print "[$i]\n";
  else
    print "<a onClick='change_cols($i)'>$i</a>\n";
    //print "<a href='album.php?series=$series&cols=$i&rows=$rows&start=$start'>$i</a>\n";
}
print $lang_str["nav_columns"];
print "</div>\n";
?>

<?
print $page->get_album_nav();
print $page->short_header();

// Das Album darstellen
//echo "<p id='debug'>&nbsp;</p>";
print "$nav\n";

$page->show_album();

print $lang_str["hint_colour_age"];


?>
</body>
</html>
<?
html_footer();

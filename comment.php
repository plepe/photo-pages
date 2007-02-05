<?
/* comment.php
 * - Adds a comment to an image
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
?>
<html>
<head>
</head>
<BODY <? echo $cfg[BODY]; ?>>

<?
$id=$_REQUEST[id];
foreach($page->cfg["LIST"] as $el) {
  if($el->id==$id) {
    $img=$el->img;
  }
}

//if(check_img($img)) {
  if($_REQUEST[submit_ok]) {
//    if(!file_exists("$orig_path/$img")) {
//      echo "Ein solches Bild gibt es nicht, also wird auch kein Kommentar erstellt.<br>\n";
//    }
//    else {
      $datum=time();
      @mkdir("$page->path/comments");
      $comm=fopen("$page->path/comments/$img", "a");
      $text=htmlentities(stripcslashes($_REQUEST[text]));
      $name=htmlentities(stripcslashes($_REQUEST[name]));
      fputs($comm, "$$$$$\n$name\n$datum\n$text\n");
      fclose($comm);

      echo "$lang_str[comments_done] <a href='image.php?series=$series&img=$id'>$lang_str[nav_back]</a>.";
    }
  //}
  if(!$datum) {
    echo "$lang_str[comments_write]:<br>\n";
    echo "<img src='$index_res/$img'>\n<hr>";
    echo "<form method=post action='comment.php'>\n";
    echo "<input type=hidden name=id value='$id'>\n";
    echo "<input type=hidden name=series value='$series'>\n";
    echo "$lang_str[field_comment_name]: <input name=name><br>\n";
    echo "$lang_str[field_comment_comment]:<br><textarea cols=40 rows=8 name=text></textarea><br>\n";
    echo "<input type=submit name='submit_ok' value='$lang_str[nav_save]'>\n";
    echo "</form>\n";
  
    echo "<hr><p><font size=-2>\n";
    echo "$lang_str[hint_comment]\n";
  }
//}
html_footer();

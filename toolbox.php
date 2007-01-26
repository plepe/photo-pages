<?
/* toolbox.php
 * - This script is called via XMLHttpRequest and changes configuration or pictures
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

Header("content-type: text/xml");

require "data.php";

function scale() {
  global $img;
  global $resolutions;
  global $orig_path;
  global $page;

  foreach($resolutions as $res) {
    system("convert -resize {$res}x{$res} -filter Hamming -quality 85 -interlace PLANE $page->path/$orig_path/$img $page->path/$res/$img");
  }
}

print "<xml version=\"1.0\" encoding=\"ISO-8859-15\">\n";

$img=$_REQUEST[img];
$todo=$_REQUEST[todo];
print "<todo>$todo</todo>\n";

switch($todo) {
  case "rot_right":
    system("exiftran -9 -o $page->path/$orig_path/new.$img $page->path/$orig_path/$img");
    system("mv $page->path/$orig_path/new.$img $page->path/$orig_path/$img");
    scale();
    $_SESSION[img_version][$img]++;
    print "<status>success</status>\n";
    print "<changed_image>yes</changed_image>\n";
    break;
  case "rot_left":
    system("exiftran -2 -o $page->path/$orig_path/new.$img $page->path/$orig_path/$img");
    system("mv $page->path/$orig_path/new.$img $page->path/$orig_path/$img");
    scale();
    $_SESSION[img_version][$img]++;
    print "<status>success</status>\n";
    print "<changed_image>yes</changed_image>\n";
    break;
  case "edit_desc":
    $data=htmlentities(stripcslashes($_REQUEST[data]));

    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    //print "<status>$page->filename</status>\n";
    $error=0;
    if($_REQUEST[series]) {
      if(eregi("^\.", $_REQUEST[series])) {
        print "<status>Invalid Seriesname</status>\n";
        $error=1;
        print "</xml>\n";
        exit;
      }
      else
        $filename="$_REQUEST[series].lst";
    }
    else {
      $filename="fotocfg.txt";
    }

    $str="";
    if(!($f=fopen($page->filename, "r"))) {
      print "<status>Can't open file for reading</status>\n";
      print "</xml>\n";
      exit;
    }

    $success=0;
    while($r=fgets($f, 8192)) {
      if(strpos($r, $_REQUEST[img])===0) {
        $str.="$_REQUEST[img] $data\n";
        $success=1;
      }
      else
        $str.=$r;
    }
    fclose($f);

    if(!($f=fopen($page->filename, "w"))) {
      print "<status>Can't open file for writing</status>\n";
      print "</xml>\n";
      exit;
    }

    fwrite($f, $str);
    fclose($f);

    if($success)
      print "<status>success</status>\n";
    else
      print "<status>An error occured $page->filename</status>\n";
    print "<newdata>".html_entity_decode($data, ENT_QUOTES, "utf-8")."</newdata>\n";
    break;
  case "add_comment":
    if(!file_exists("$page->path/$orig_path/$img")) {
      print "<status>Ein solches Bild gibt es nicht, also wird auch kein Kommentar erstellt</status>\n";
    }
    else {
      $datum=time();
      if(!file_exists("$page->path/comments")) {
        mkdir("$page->path/comments");
      }

      if(!($comm=fopen("$page->path/comments/$img", "a"))) {
        print "<status>Couldn't open file for writing</status>\n";
      }
      $text=htmlentities(stripcslashes($_REQUEST[comment]));
      $name=htmlentities(stripcslashes($_REQUEST[comment_name]));
      fputs($comm, "$$$$$\n$name\n$datum\n$text\n");
      fclose($comm);
      print "<status>success</status>\n";
      print "<newname>".html_entity_decode($name)."</newname>\n";
      print "<newcomm>".html_entity_decode($text)."</newcomm>\n";
    }
    break;
  case "set_cols":
    print "<status>success</status>\n";

    $_SESSION[album_cols]=$_REQUEST[cols];
    session_register("album_cols");
    break;
  case "set_normal_res":
    print "<status>success</status>\n";

    $_SESSION[normal_res]=$_REQUEST[res];
    session_register("normal_res");
    break;
  case "login":
    if($_REQUEST[username]) {
      $test=new User($_REQUEST[username]);
      if($test->authenticate($_REQUEST[password])) {
        $_SESSION[current_user]=$test;
        print "<status>success</status>\n";
      }
      else {
        print "<status>$lang_str[error_invalid_auth]</status>\n";
      }
    }
    break;
  case "set_window_size":
    session_register("window_width");
    $_SESSION[window_width]=$_REQUEST[window_width];
    session_register("window_height");
    $_SESSION[window_height]=$_REQUEST[window_height];

    print "<status>success</status>\n";
    break;
  case "set_fullscreen_mode":
    session_register("fullscreen_mode");
    $_SESSION[fullscreen_mode]=$_REQUEST[fullscreen];

    print "<status>success</status>\n";
    break;
  default:
}

print "</xml>\n";

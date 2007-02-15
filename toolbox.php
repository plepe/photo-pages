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
$request_type="xml";

require "data.php";

function scale() {
  global $img;
  global $resolutions;
  global $orig_path;
  global $page;
  global $file_path;

  foreach($resolutions as $res) {
    system("convert -resize {$res}x{$res} -filter Hamming -quality 85 -interlace PLANE $file_path/$page->path/$orig_path/$img->img $file_path/$page->path/$res/$img->img");
  }
}

print "<xml version=\"1.0\" encoding=\"ISO-8859-15\">\n";

$img=$_REQUEST[img];
$todo=$_REQUEST[todo];
print "<todo>$todo</todo>\n";

switch($todo) {
  case "rot_right":
    $img=$page->cfg["LIST"][$img];
    system("exiftran -9 -o $file_path/$page->path/$orig_path/new.$img->img $file_path/$page->path/$orig_path/$img->img");
    system("mv $file_path/$page->path/$orig_path/new.$img->img $file_path/$page->path/$orig_path/$img->img");
    scale();
    $_SESSION[img_version][$img->img->img]++;
    print "<status>success</status>\n";
    print "<changed_image>yes</changed_image>\n";
    break;
  case "rot_left":
    $img=$page->cfg["LIST"][$img];
    system("exiftran -2 -o $file_path/$page->path/$orig_path/new.$img->img $file_path/$page->path/$orig_path/$img->img");
    system("mv $file_path/$page->path/$orig_path/new.$img->img $file_path/$page->path/$orig_path/$img->img");
    scale();
    $_SESSION[img_version][$img->img]++;
    print "<status>success</status>\n";
    print "<changed_image>yes</changed_image>\n";
    break;
  case "edit_desc":
    $data=htmlentities(stripcslashes($_REQUEST[data]));

    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    //print "<status>$page->filename</status>\n";
    $error=0;
//    if($_REQUEST[series]) {
//      if(eregi("^\.", $_REQUEST[series])) {
//        print "<status>Invalid Seriesname</status>\n";
//        $error=1;
//        print "</xml>\n";
//        exit;
//      }
//      else
//        $filename="$_REQUEST[series].lst";
//    }
//    else {
//      $filename="fotocfg.txt";
//    }

//    $str="";
//    if(!($f=fopen($page->filename, "r"))) {
//      print "<status>Can't open file for reading</status>\n";
//      print "</xml>\n";
//      exit;
//    }

    $success=0;
    $page_data=$page->load_data();
    foreach($page_data["LIST"] as $k=>$v) {
      if($v->index_id==$_REQUEST[index_id]) {
        $page_data["LIST"][$k]->text=$_REQUEST[data];
        $success=1;
      }
    }

    if($error=$page->save_data($page_data)) {
      print "<status>$error</status>\n";
      print "</xml>\n";
      exit;
    }

    if($success)
      print "<status>success</status>\n";
    else
      print "<status>Richtiges Feld nicht gefunden</status>\n";
    print "<newdata>".html_entity_decode($data, ENT_QUOTES, "utf-8")."</newdata>\n";
    break;
  case "add_comment":
    $success=0;

    $page_data=$page->load_data();
    foreach($page_data["LIST"] as $k=>$v) {
      if($v->index_id==$_REQUEST[index_id]) {
        if(!($img=$v->mov))
          $img=$v->img;
        $success=1;
      }
    }

    if(!file_exists("$file_path/$page->path/$orig_path/$img")) {
      print "<status>Ein solches Bild gibt es nicht, also wird auch kein Kommentar erstellt</status>\n";
    }
    else {
      $datum=time();
      if(!file_exists("$file_path/$page->path/comments")) {
        mkdir("$file_path/$page->path/comments");
      }

      if(!($comm=fopen("$file_path/$page->path/comments/$img", "a"))) {
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
  case "set_rows":
    print "<status>success</status>\n";

    $_SESSION[album_rows]=$_REQUEST[rows];
    session_register("album_rows");
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
  case "logout":
    $_SESSION[current_user]=get_user("anonymous");
    print "<status>success</status>\n";
    break;
  case "read_upload_dir":
    $x=list_dir($_REQUEST["dir"]);
    html_export_var(array("dir"=>$x));
    break;
  case "set_session_vars":
    foreach($_REQUEST["var"] as $varname=>$value) {
      if($value=="null")
        $value=null;
      $_SESSION[$varname]=$value;
      print "<debug>set $varname to '$value'</debug>\n";
    }
    break;
  default:
}

print "</xml>\n";

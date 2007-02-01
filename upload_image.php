<? 
/* upload_image.php
 * - You can use this file to upload images.
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
setlocale(LC_ALL, "de_AT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel=stylesheet type="text/css" href="<?=url_img("style.css");?>">
<?
use_javascript("global");
use_javascript("upload_image");
?>
<title><?=$cfg[TITLE]?></title>
</head>
<BODY>
<?
print $page->header();

echo $page->path;
echo("$file_path/$page->path/$orig_path");
if(!file_exists("$file_path/$page->path/$orig_path"))
  mkdir("$file_path/$page->path/$orig_path");

foreach($resolutions as $r) {
  if(!file_exists("$file_path/$page->path/$r"))
    mkdir("$file_path/$page->path/$r");
}


function autoconvert() {
  global $page;
  global $resolutions;
  global $convert_options;
  global $orig_path;

  $list=opendir("$file_path/$page->path/$orig_path");
  while($file=readdir($list)) {
    foreach($resolutions as $r) {
      if(!file_exists("$file_path/$page->path/$r/$file")) {
        print "Scaling $file to {$r}x{$r}<br>\n";
        flush(); ob_flush();
        system("nice convert -resize {$r}x{$r} $convert_options $file_path/$page->path/orig/$file $file_path/$page->path/$r/$file");
      }
    }
  }
  closedir($list);
}

function upload_file($file, $tmpname, $desc) {
  global $index_res;
  global $resolutions;
  global $page;
  global $convert_options;

  if(!eregi("\.(png|jpg|gif)$", $file)) {
    return;
  }

  print "<br>Uploading $file ...<br>";
  if(file_exists("$file_path/$page->path/orig/$file")) {
    print "Datei existiert bereits.<br>\n";
  }
  else {
    rename($tmpname, "$file_path/$page->path/orig/$file");
    print "Scaling to {$index_res}x{$index_res}<br>\n";
    flush(); ob_flush();

    system("nice convert -resize {$index_res}x{$index_res} $convert_options $file_path/$page->path/orig/$file $file_path/$page->path/$index_res/$file");
    $f=fopen("$file_path/$page->path/fotocfg.txt", "a");

    if($desc)
      fputs($f, "$file $desc\n");
    else
      fputs($f, "$file\n");

    fclose($f);

    foreach($resolutions as $r) {
      if($index_res!=$r) {
        print "Scaling to {$r}x{$r}<br>\n";
        flush(); ob_flush();

        system("nice convert -resize {$r}x{$r} $convert_options $file_path/$page->path/orig/$file $file_path/$page->path/$r/$file");
      }
    }
  }
}

function process_upload_file($p, $f) {
  global $page;
  global $file_path;
  global $orig_path;

  print_r($f);

  if(eregi("^(.*)\.(jpg|jpeg|png|gif)$", $f)) {
    if($keep)
      copy("$p/$f", "$file_path/$page->path/$orig_path/$f");
    else
      system("convert -resize 1280x1280 $p/$f $file_path/$page->path/$orig_path/$f");
    print "Imported $f.<br>\n";
  }

  if(eregi("^(.*)\.(mov|avi|mpeg|mpg)", $f, $m)) {
    if($keep)
      copy("$p/$f", "$file_path/$page->path/$orig_path/$f");
    else
      system("mencoder -oac copy -ovc lavc -o $file_path/$page->path/$orig_path/$f $p/$f");
      
    system("cd /tmp ; mplayer -vo png -ao none -ss 1 -frames 1 -ss 1 $p/$f");
    system("convert -resize 410x450 /tmp/00000001.png /tmp/00000001.png");
    system("composite -compose atop -gravity center /tmp/00000001.png /home/skunk/public_html/photos/images/filmstrip.png $file_path/$page->path/$orig_path/$m[1].jpg");
    print "Imported $f.<br>\n";
  }

  flush(); ob_flush();
}

if($_REQUEST["dir"]) {
  if(!file_exists("$file_path/$page->path/$orig_path"))
    mkdir("$file_path/$page->path/$orig_path");

  $d=opendir("$upload_path/$_REQUEST[dir]");
  while($f=readdir($d)) {
    //if(eregi("\.(jpg|jpeg)$", $f)) {
      //print "Copying $f<br>\n";
      process_upload_file("$upload_path/$_REQUEST[dir]", "$f");
    //}
  }

  autoconvert();
}

if($_FILES[image]) {
  print "<p>";

  $n=$_FILES[image][name];
  if(eregi("\.(png|jpg|gif)$", $n)) {
    upload_file($n, $_FILES[image][tmp_name], $_REQUEST[desc]);
  }
  elseif(eregi("\.(zip)$", $n)) {
    $tmpname=tempnam("/tmp", "UPLOAD");
    unlink($tmpname);
    mkdir($tmpname);
    print "<pre>\n";
    print("cd $tmpname ; echo -n 'Path: ' ; pwd ; unzip -j -o {$_FILES[image][tmp_name]}");
    system("cd $tmpname ; echo -n 'Path: ' ; pwd ; unzip -j -o {$_FILES[image][tmp_name]}");
    print "</pre>\n";
    unlink($_FILES[image][tmp_name]);
    $tmpdir=opendir($tmpname);
    while($f=readdir($tmpdir)) {
      upload_file($f, "$tmpname/$f", 0);
      @unlink("$tmpname/$f");
    }
    closedir($tmpdir);
    rmdir($tmpname);
  }
  else {
  }

  print "<br>Finished.<br>\n";
}

print "<p>\n";
print "<a href='".url_page($page, $series, "index.php")."'>Back</a> /\n";
print "<a href='".url_script($page, $series, "page_edit.php", null)."'>Edit Page</a>\n";
print "<p>\n";
print "<form action='upload_image.php' method='post' ".
      "enctype='multipart/form-data'>\n";
print "<input type='hidden' name='page' value='$page->path'>\n";
//print "<table>\n";
//print "<tr><td>Bild oder ZIP-Datei angeben:</td><td><input type='file' name='image'></td></tr>";
//print "<tr><td>Beschreibung:</td><td><input name='desc'></td></tr>";
//print "</table>\n";

$dir="";
print "<div id='dir_list' class='upload_image_dir_list'>\n";
print list_dir($_REQUEST[dir]);
print "</div>\n";

print "<tr><td colspan='2'><input type='submit' value='Import all images'></td></tr>\n";
print "</form>\n";

?>
</body>
</html>

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
<link rel=stylesheet type="text/css" href="<?=$img_path?>/style.css">
<title><?=$cfg[TITLE]?></title>
</head>
<BODY>
<?
print $page->header();

function autoconvert() {
  global $page;
  global $resolutions;
  global $convert_options;
  global $orig_path;

  if(!file_exists("$page->path/$orig_path"))
    mkdir("$page->path/$orig_path");

  foreach($resolutions as $r) {
    if(!file_exists("$page->path/$r"))
      mkdir("$page->path/$r");
  }

  $list=opendir("$page->path/$orig_path");
  while($file=readdir($list)) {
    foreach($resolutions as $r) {
      if(!file_exists("$page->path/$r/$file")) {
        print "Scaling $file to {$r}x{$r}<br>\n";
        flush(); ob_flush();
        system("nice convert -resize {$r}x{$r} $convert_options $page->path/orig/$file $page->path/$r/$file");
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
  if(file_exists("$page->path/orig/$file")) {
    print "Datei existiert bereits.<br>\n";
  }
  else {
    rename($tmpname, "$page->path/orig/$file");
    print "Scaling to {$index_res}x{$index_res}<br>\n";
    flush(); ob_flush();

    system("nice convert -resize {$index_res}x{$index_res} $convert_options $page->path/orig/$file $page->path/$index_res/$file");
    $f=fopen("$page->path/fotocfg.txt", "a");

    if($desc)
      fputs($f, "$file $desc\n");
    else
      fputs($f, "$file\n");

    fclose($f);

    foreach($resolutions as $r) {
      if($index_res!=$r) {
        print "Scaling to {$r}x{$r}<br>\n";
        flush(); ob_flush();

        system("nice convert -resize {$r}x{$r} $convert_options $page->path/orig/$file $page->path/$r/$file");
      }
    }
  }
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

autoconvert();

print "<p>\n";
print "<a href='index.php'>Back</a> /\n";
print "<a href='page_edit.php'>Edit Page</a>\n";
print "<p>\n";
print "<form action='upload_image.php' method='post' ".
      "enctype='multipart/form-data'>\n";
print "<table>\n";
print "<tr><td>Bild angeben:</td><td><input type='file' name='image'></td></tr>";
print "<tr><td>Beschreibung:</td><td><input name='desc'></td></tr>";
print "<tr><td colspan='2'><input type='submit' value='Ok'></td></tr>\n";
print "</table>\n";
print "</form>\n";

?>
</body>
</html>

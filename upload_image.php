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
start_html_header($page->cfg[TITLE]);

use_javascript("global");
use_javascript("upload_image");
urls_write();
end_html_header();
?>
<BODY>
<?
print $page->header();

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

/*
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
*/

function process_upload_file($file, $orig_file, $desc=0) {
  global $page;
  global $file_path;
  global $orig_path;
  global $script_path;
  global $resolutions;
  global $convert_options;
  global $max_res;
  global $extensions_images;
  global $extensions_movies;

  // Replace spaces in filename through _
  $file=replace_invalid_chars($file);

  print "Importing $file ...";
  flush(); ob_flush();

  // If this is an image ...
  if(eregi("^(.*)\.(".implode("|", $extensions_images).")$", $file)) {
    $maxr=getimagesize($orig_file);
    if($maxr[0]>$maxr[1])
      $maxr=$maxr[0];
    else
      $maxr=$maxr[1];

    if($maxr<$max_res)
      copy("$orig_file", "$file_path/$page->path/$orig_path/$file");
    else
      system("nice convert -resize {$max_res}x{$max_res} $convert_options \"$orig_file\" $file_path/$page->path/$orig_path/$file");

    $orig="$file_path/$page->path/$orig_path/$file";
    $name="$file";
  }

  // If this is a movie ...
  if(eregi("^(.*)\.(".implode("|", $extensions_movies).")", $file, $m)) {
    $orig="$file_path/$page->path/$orig_path/$m[1].jpg";
#    $orig_tmp="$file_path/$page->path/$orig_path/$m[1].%REPLACE%.jpg";
#    $orig_mov=
#    $i=0;
#    while(file_exists($orig)) {
#      $i++;
#      $orig=strtr($orig_tmp, "%REPLACE%", $i);
#    }

    if($keep)
      copy("$orig_file", "$file_path/$page->path/$orig_path/$file");

    // In FLV konvertieren
    system("nice ffmpeg -y -i \"$orig_file\" -vcodec flv -acodec pcm_s16le -ar 22050 /tmp/tmp.avi");
    system("nice ffmpeg -y -i /tmp/tmp.avi -vcodec copy -acodec copy $file_path/$page->path/$orig_path/$m[1].flv");
    system("rm /tmp/tmp.avi");
    // TODO: flvtool2 verwenden, um metadaten zum video hinzuzufuegen

    // Filmstrip generieren
    system("cd /tmp ; ffmpeg -y -i \"$orig_file\" -vframes 1 -f image2 /tmp/tmp.jpg");
    system("nice convert -resize 410x450 /tmp/tmp.jpg /tmp/tmp.jpg");
    system("nice composite -compose atop -gravity center /tmp/tmp.jpg images/filmstrip.png $orig");
    system("rm /tmp/tmp.jpg");

    $file="$m[1].jpg";
    $name="$m[1].flv";
  }

  // Add this image to the fotocfg.txt resp. a series
  $f=fopen("$file_path/$page->filename", "a");
  if($desc)
    fputs($f, "$name $desc\n");
  else
    fputs($f, "$name\n");
  fclose($f);

  $maxr=getimagesize($orig);
  if($maxr[0]>$maxr[1])
    $maxr=$maxr[0];
  else
    $maxr=$maxr[1];

  $lastr=$orig_path;

  // Convert Image to thumbnails
  rsort($resolutions);
  foreach($resolutions as $r) {
    if($r<$maxr) {
      system("nice convert -resize {$r}x{$r} $convert_options $file_path/$page->path/$lastr/$file $file_path/$page->path/$r/$file");
      $lastr=$r;
    }
    elseif($r==$maxr) {
      copy("$orig", "$file_path/$page->path/$r/$file");
      $lastr=$r;
    }
  }

  print " done<br>\n";
  flush(); ob_flush();
}

if(!file_exists("$file_path/$page->path/$orig_path"))
  mkdir("$file_path/$page->path/$orig_path");

foreach($resolutions as $r) {
  if(!file_exists("$file_path/$page->path/$r"))
    mkdir("$file_path/$page->path/$r");
}

if($_REQUEST["dir"]) {
  foreach($_REQUEST[upload_file] as $f) {
    process_upload_file($f, "$upload_path/$_REQUEST[dir]/$f");
  }
}

if($_FILES[image]) {
  print "<p>";

  $n=$_FILES[image][name];
  if(eregi("\.(".implode("|", array_merge($extensions_images, $extensions_movies)).")$", $n)) {
    process_upload_file($n, $_FILES[image][tmp_name], $_REQUEST[desc]);
  }
  elseif(eregi("\.(zip|rar)$", $n)) {
    $tmpname=tempnam("/tmp", "UPLOAD");
    unlink($tmpname);
    mkdir($tmpname);
    print "<pre>\n";
    if(eregi("\.(zip)$", $n)) {
      print("cd $tmpname ; echo -n 'Path: ' ; pwd ; unzip -j -o {$_FILES[image][tmp_name]}");
      system("cd $tmpname ; echo -n 'Path: ' ; pwd ; unzip -j -o {$_FILES[image][tmp_name]}");
    }
    elseif(eregi("\.(rar)$", $n)) {
      print("cd $tmpname ; echo -n 'Path: ' ; pwd ; unrar e {$_FILES[image][tmp_name]}");
      system("cd $tmpname ; echo -n 'Path: ' ; pwd ; unrar e {$_FILES[image][tmp_name]}");
    }
    print "</pre>\n";
    unlink($_FILES[image][tmp_name]);
    $tmpdir=opendir($tmpname);
    while($f=readdir($tmpdir)) {
      if(substr($f, 0, 1)!=".") {
        process_upload_file($f, "$tmpname/$f");
        @unlink("$tmpname/$f");
      }
    }
    closedir($tmpdir);
    rmdir($tmpname);
  }
  else {
  }
}

if((!$_REQUEST["dir"])&&(!$_FILES[image])) {
  print "<p>\n";
  print "<form action='upload_image.php' method='post' ".
        "enctype='multipart/form-data'>\n";
  print "<input type='hidden' name='page' value='$page->path'>\n";
  print "<input type='hidden' name='series' value='$page->series'>\n";

  print "<h4>$lang_str[upload_image_upload]</h4>\n";
  print "<table>\n";
  print "<tr><td>$lang_str[upload_image_upload_file]</td><td><input type='file' name='image'></td></tr>";
  print "<tr><td>$lang_str[upload_image_upload_desc]</td><td><input name='desc'></td></tr>";
  print "</table>\n";
  print "$lang_str[upload_image_max_size]: ".ini_get("upload_max_filesize")."<br>\n";
  print "<input type='submit' value='$lang_str[upload_image_submit]'>\n";

  if($upload_path) {
    print "<h4>$lang_str[upload_image_readdir]</h4>\n";
    $dir="";
    print "<div id='dir_list' class='upload_image_dir_list'>\n";
    print list_dir($_REQUEST["dir"]);
    print "</div>\n";

    print "<input type='button' value='$lang_str[upload_image_mark]' onClick='upload_image_mark_all()'>\n";
    print "<input type='submit' value='$lang_str[upload_image_submit]'>\n";
  }
  print "</form>\n";
}
else {
  print "<br>Finished.<br>\n";
}
print "<p>\n";
print "<a href='".url_page($page->path, $page->series, "index.php")."'>Back</a> /\n";
print "<a href='".url_script($page->path, $page->series, "page_edit.php", null)."'>Edit Page</a>\n";
html_footer();

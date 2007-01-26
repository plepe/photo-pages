<?
/* conf.php
 * - Configuration
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

$img_path="/~skunk/photo_scripts_devel/images";
$no_main_picture="$img_path/black.png";
$resolutions=array(64, 200, 600);
$orig_path="orig";

$index_res=200;
$normal_res=600;

$file_path="/home/skunk/photos";
$web_path="/~skunk/photo_scripts_devel/";
$passwd_file="$file_path/.htpasswd";
$group_file ="$file_path/.htgroup";

$convert_options="-filter Hamming -quality 85 -interlace PLANE";

$fields=array("TITLE"=>"Titel", "PHOTOS_BY"=>"Photographiert von", 
              "DATE"=>"Datum", "WELCOME_TEXT"=>"Begrüßungstext",
              "MAIN_PICTURE"=>"Hauptbild");
$rights=array("announce", "view", "addcomment", "editdesc", "new", "edit", "rights");
$global_rights=array("newusers", "useradmin");

$default_group="users";

umask(0000);
//<li> useradmin - BenutzerIn darf Gruppenzugehoerigkeiten editieren, weitere Benutzer anlegen und auch wieder loeschen

$language="de";

$default_rights=array("view", "announce");

// URLs
// es wird auf jeden fall $web_path davor gesetzt.
// in der url werden folgende elemente ersetzt:
//   %1$s page-path
//   %2$s series
//   %3$s skript
//   %4$s imgnumber (nur bei images + skript)
//   %5$s imgname (nur bei images)
//   %6$s size (nur bei images)
//   %7$s imgversion (nur bei images)
$url_page=$web_path.'/?page=%1$s&series=%2$s';
$url_img=$web_path.'/get_image.php/%5$s?page=%1$s&series=%2$s&img=%4$s&size=%6$s&%7$s';
$url_skript=$web_path.'/%3$s?page=%1$s&series=%2$s&img=%4$s';
$url_javascript=$web_path.'/%3$s';

//$url_page=$web_path.'%1$s/?series=%2$s';
//$url_img='/photo_scripts/get_image.php/%5$s?page=%1$s&series=%2$s&img=%4$s&size=%6$s&%7$s';
//$url_skript='/photo_scripts/%3$s?page=%1$s&series=%2$s&img=%4$s';





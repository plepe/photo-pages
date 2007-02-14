<? 
/* export.php
 * - Exports a photopage
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
use_javascript("index");
use_javascript("user");
urls_write();
end_html_header();

?>
<BODY>
<?
$url_page="%page%/index.html";
$url_script="image_%img%.html";
$url_photo="%size%/%imgname%";
$url_img="%root%/images/%imgname%";
$url_save_export=1;
$url_root=$page->path;
$page->export_html("/tmp/export");

symlink("$script_path/images", "/tmp/export/images");

print "FINISHED";
    //system("cd $export_path ; tar cf /tmp/export/export.tar -h .");

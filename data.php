<?
/* data.php
 * - Contains most classes used in the photopages
 * - Parses the fotocfg.txt or a series.lst and stores data in $cfg
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
 // http://xover.mud.at/~florian/testbed/p1/svg-map.js

// Default picture, if no main_picture is set
$no_main_picture="black.png";

// The header-fields
// TODO: Should be moved to the language files
$fields=array("TITLE"=>"Titel", "PHOTOS_BY"=>"Photographiert von", 
              "DATE"=>"Datum", "WELCOME_TEXT"=>"Begr&uuml;&szlig;ungstext",
              "MAIN_PICTURE"=>"Hauptbild");

$rights=array("announce", "view", "addcomment", "editdesc", "new", "edit", "rights");
$global_rights=array("newusers", "useradmin");

// Accepted Filename-Extensions
$extensions_images=array("jpg", "jpeg", "gif", "png");
$extensions_movies=array("avi", "mov", "flv", "mpeg", "mpg");

$export_page=array();
$exported_pages=array();

require "conf.php";

if(!$extensions)
  $extensions=array();

if(!defined("DATA_PHP")) {
  define("DATA_PHP", TRUE);

class Chunk {
  var $index;
  var $id;
  var $page;
  var $type;
  var $index_id;

  function colspan() { return 0; }
  function is_shown() { return 0; }
  function html_class() { return ""; }
  function album_show() { return ""; }
  function file_name() { return false; }
  function get_index() { return $this->index; }
  function get_id() { return $this->id; }
  function get_subpage() { return null; }
  function count_as_picture() { return 0; }
  function count_as_subdir() { return 0; }
  function imageview_show() { return ""; }
  function list_show() { return ""; }
  function edit_show() { return ""; }
  function export_album() { return ""; }
  function export_imageview() { return ""; }
};

class TextChunk extends Chunk {
  var $text;

  function TextChunk($page, $text, $i, $j) {
    $this->type="TextChunk";
    if(ereg("^\"(.*)\"$", $text, $m))
      $this->text=$m[1];
    else
      $this->text=$text;
    $this->index=$i;
    $this->id=$j++;
    $this->page=$page;
  }

  function colspan() { return 9999; }
  function is_shown() { return 1; }
  function html_class() { return "text"; }
  function album_show() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function count_as_picture() { return 0; }
  function imageview_show() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function edit_show($text=0) {
    global $index_res;

    if(!$text)
      $text=$this->text;

    $ret.="<textarea class='edit_input_textchunk' onKeyUp='resize_textarea(this)' name='data[LIST][$this->id][text]' onFocus='input_get_focus(this)' onLoad='resize_textarea(this)' onMouseOut='page_edit_input_leave(this)'>$text</textarea>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='TextChunk'>\n";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }

  function export_album() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function export_imageview() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }
};

class SubdirChunk extends Chunk {
  var $dir;
  var $subpage;
  var $page;
  var $path;

  function SubdirChunk(&$page, $text, &$i, &$j) {
    $this->type="SubdirChunk";
    $this->index=$i++;
    $this->id=$j++;
    $this->page=$page;

    if(is_object($text)) {
      $this->subpage=$text;
    }
    //if(is_dir("$page->path/$text")) {
    $this->dir=$text;
    //}

    if(eregi("^/([^/]*)/$", $this->dir, $m)) {
      $this->path="";
      $this->dir=$m[1];
    }
    elseif(eregi("^(/.*)/([^/]*)/$", $this->dir, $m)) {
      $this->path=$m[1];
      $this->dir=$m[2];
    }
    elseif(eregi("^(.*)/([^/]*)/$", $this->dir, $m)) {
      $this->path=$this->page->path."/".$m[1];
      $this->dir=$m[2];
    }
    elseif(eregi("^(.*)/$", $this->dir, $m)) {
      $this->path=$this->page->path;
      $this->dir=$m[1];
    }
    else {
      $this->path=$this->page->path;
    }
  }

  function colspan() { 
    return 2; 
  }

  function is_shown() {
    $this->get_subpage();

    if($this->subpage->cfg[HIDE]=="yes")
      return 0;
    if(!$this->subpage->get_right($_SESSION[current_user], "announce"))
      return 0;
    return 1;
  }

  function html_class() { return "subdir"; }
  function file_name() {
    return $this->dir;
  }

  function get_subpage() {
    global $file_path;

    if($this->subpage)
      return $this->subpage;

    return $this->subpage=get_page("{$this->path}/$this->dir");
  }

  function album_show() {
    global $pfad;
    global $web_path;
    global $no_main_picture;
    global $lang_str;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".url_photo($this->subpage->path, "", "index.php", "main", "main.jpg", 200, $_SESSION[img_version][$this->img])."' align='left'>";
    else
      $ret.="<img class='album_series' src='".url_img($no_main_picture)."' align='left'>";
    $ret.="</a>\n";

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    $ret.="$subdata[TITLE]</a><br>";
    $ret.="$subdata[DATE]<br>";
/*
    $ret.="<span class='album_subdir_count'>";

    $c=$this->subpage->count_pictures();
    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_pict]":"$lang_str[nav_picts]");
    $ret.=", ";

    $c=$this->subpage->count_subdirs();
    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_subdir]":"$lang_str[nav_subdirs]");
    $ret.="</span>\n";
    */
    return $ret;
  }

  function count_as_picture() { return 0; }
  function count_as_subdir() { 
    $this->get_subpage();
    return $this->subpage->cfg["HIDE"]!="yes";
  }

  function imageview_show() {
    global $pfad;
    global $web_path;
    global $no_main_picture;
    global $lang_str;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page($this->subpage->path, "", "index.php")."'>";
    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".url_photo($this->subpage->path, "", "index.php", "main", "main.jpg", 200, $_SESSION[img_version][$this->img])."' align='left'>";
    else
      $ret.="<img class='album_series' src='".url_img($no_main_picture)."' align='left'>";
    $ret.="</a>\n";

    $ret.="<a href='".url_page($this->subpage->path, "", "index.php")."'>";
    $ret.="$subdata[TITLE]</a><br>";
    $ret.="$subdata[DATE]<br>";
/*
    $ret.="<span class='album_subdir_count'>";

    $c=$this->subpage->count_pictures();
    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_pict]":"$lang_str[nav_picts]");
    $ret.=", ";

    $c=$this->subpage->count_subdirs();
    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_subdir]":"$lang_str[nav_subdirs]");
    $ret.="</span>\n";
    */
    return $ret;
  }

  function edit_show($text=0) {
    global $index_res;
    global $lang_str;

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<div class='edit_img'><img width='64' height='64' src='$subdata[MAIN_PICTURE]'></div>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][dir]' value='$this->dir'>\n";
    $ret.="$lang_str[nav_subdir]: $subdata[TITLE]";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='SubdirChunk'>\n";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }


  function export_album() {
    global $pfad;
    global $lang_str;
    global $no_main_picture;
    global $export_page;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".substr($this->subpage->path, strrpos($this->subpage->path, "/")+1)."/main.jpg' align='left'>";
    else
      $ret.="<img class='album_series' src='".url_img(array("page"=>$this->page, "imgname"=>$no_main_picture))."' align='left'>";
    $ret.="</a>\n";

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    $ret.="$subdata[TITLE]</a><br>";
    $ret.="$subdata[DATE]<br>";

    $export_page[]=$this;

    return $ret;
  }

  function export_imageview() {
    global $pfad;
    global $lang_str;
    global $no_main_picture;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".substr($this->subpage->path, strrpos($this->subpage->path, "/")+1)."/main.jpg' align='left'>";
    else
    $ret.="<img class='album_series' src='".url_img(array("page"=>$this->page, "imgname"=>$no_main_picture))."' align='left'>";
    $ret.="</a>\n";

    $ret.="<a href='".url_page(array("page"=>$this->subpage))."'>";
    $ret.="$subdata[TITLE]</a><br>";
    $ret.="$subdata[DATE]<br>";

    $export_page[]=$this;

    return $ret;
  }

};

class SeriesChunk extends Chunk {
  var $dir;
  var $subpage;
  var $path;

  function SeriesChunk($page, $text, $i, $j) {
    $this->type="SeriesChunk";
    if(is_object($text)) {
      $this->subpage=$text;
      $this->dir="";
    }
    else if(ereg("^(.*)@", $text, $m)) {
      $this->dir=$m[1];
      $this->subpage=null;
    }
    $this->index=$i++;
    $this->id=$j++;
    $this->page=$page;

    if(eregi("^(/.*)/([^/]*)$", $this->dir, $m)) {
      $this->path=$m[1];
      $this->dir=$m[2];
    }
    elseif(eregi("^(.*)/([^/]*)$", $this->dir, $m)) {
      $this->path=$this->page->path."/".$m[1];
      $this->dir=$m[2];
    }
    else {
      $this->path=$this->page->path;
    }
  }

  function file_name() {
    return $this->dir;
  }

  function get_subpage() {
    if(!$this->subpage)
      $this->subpage=get_page($this->path, $this->dir);

    return $this->subpage;
  }

  function colspan() { return 2; }
  function is_shown() { return 1; }
  function html_class() { return "series"; }
  function album_show() {
    global $pfad;
    global $web_path;
    global $no_main_picture;
    global $lang_str;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page($this->subpage->path, $this->subpage->series, "index.php")."'>";

    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".url_photo($this->subpage->path, $this->subpage->series, "index.php", "main", "main.jpg", 200, $_SESSION[img_version][$this->img])."' align='left'>";
    else
      $ret.="<img class='album_series' src='".url_img($no_main_picture)."' align='left'>";
    $ret.="</a>\n";

    $ret.="$lang_str[nav_view]: ";
    $ret.="<a href='".url_page($this->subpage->path, $this->subpage->series, "index.php")."'>";
    $ret.="$subdata[TITLE]</a><br>\n";
    /*
    $ret.="<a href='album.php?series=$this->dir'><img src='$img_path/view_album.png' class='viewmode' alt='*'> Albumansicht</a><br>";
    $ret.="<a href='image.php?series=$this->dir'><img src='$img_path/view_slide.png' class='viewmode' alt='*'> Diaansicht</a><br>";
    $ret.="<a href='frame.php?series=$this->dir'><img src='$img_path/view_frame.png' class='viewmode' alt='*'> Spaltenansicht</a><br>";
    */

//    $ret.="<span class='album_subdir_count'>";
//
//    $c=$this->subpage->count_pictures();
//    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_pict]":"$lang_str[nav_picts]");
//    $ret.="</span>\n";
 
    return $ret;
  }

  function count_as_picture() { return 0; }

  function imageview_show() {
    global $pfad;
    global $web_path;
    global $no_main_picture;
    global $lang_str;
    $ret="";

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<a href='".url_page($this->subpage->path, $this->subpage->series, "index.php")."'>";

    if($subdata[MAIN_PICTURE])
      $ret.="<img class='album_series' src='".url_photo($this->subpage->path, $this->subpage->series, "index.php", "main", "main.jpg", 200, $_SESSION[img_version][$this->img])."' align='left'>";
    else
      $ret.="<img class='album_series' src='".url_img($no_main_picture)."' align='left'>";
    $ret.="</a>\n";

    $ret.="$lang_str[nav_view]: ";
    $ret.="<a href='".url_page($this->subpage->path, $this->subpage->series, "index.php")."'>";
    $ret.="$subdata[TITLE]</a><br>\n";
    /*
    $ret.="<a href='album.php?series=$this->dir'><img src='$img_path/view_album.png' class='viewmode' alt='*'> Albumansicht</a><br>";
    $ret.="<a href='image.php?series=$this->dir'><img src='$img_path/view_slide.png' class='viewmode' alt='*'> Diaansicht</a><br>";
    $ret.="<a href='frame.php?series=$this->dir'><img src='$img_path/view_frame.png' class='viewmode' alt='*'> Spaltenansicht</a><br>";
    */

//    $ret.="<span class='album_subdir_count'>";
//
//    $c=$this->subpage->count_pictures();
//    $ret.="$c&nbsp;".($c==1?"$lang_str[nav_pict]":"$lang_str[nav_picts]");
//    $ret.="</span>\n";
 
    return $ret;
  }

  function edit_show($text=0) {
    global $index_res;
    global $lang_str;

    $this->get_subpage();
    $subdata=$this->subpage->cfg;

    $ret.="<div class='edit_img'><img width='64' height='64' src='$subdata[MAIN_PICTURE]'></div>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][dir]' value='$this->dir'>\n";
    $ret.="$lang_str[nav_view]: $subdata[TITLE]<br />{$this->subpage->filename}";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='SeriesChunk'>\n";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }
};

class HeaderChunk extends Chunk {
  var $level;
  var $text;

  function HeaderChunk($page, $text, &$i, $j) {
    $this->type="HeaderChunk";
    if(eregi("^index_[^ ]* (.*)$", $text, $m)) {
      $this->level=1;
      $this->text=$m[1];
    }
    elseif(eregi("^(=+)([^=].*[^=])(=+)$", $text, $m)) {
      $this->text=$m[2];
      $this->level=strlen($m[1]);
    }
    $this->index=$i;
    $this->id=$j++;
    $this->page=$page;
  }

  function colspan() {
    return 9999;
  }

  function is_shown() { return 1; }

  function html_class() {
    return "h{$this->level}";
  }

  function album_show() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function count_as_picture() { return 0; }

  function imageview_show() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function list_show() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function edit_show($text=0) {
    global $index_res;

    if(!$text)
      $text=$this->text;

    $ret.="<input name='data[LIST][$this->id][text]' class='edit_input_headerchunk' value='$text' onFocus='input_get_focus(this)' onMouseOver='page_edit_input_enter(this)' onMouseOut='page_edit_input_leave(this)'>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='HeaderChunk'>\n";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }

  function export_album() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function export_imageview() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }
};

class ImgChunk extends Chunk {
  var $text;
  var $img;
  var $comments;
  var $path;

  function ImgChunk($page, $text, &$i, $j) {
    $this->type="ImgChunk";
    if(eregi("^([^ ]*) (.*)$", $text, $m)) {
      $this->img=$m[1];
      $this->text=$m[2];
    }
    else {
      $this->img=$text;
    }
    $this->index=$i++;
    $this->id=$j++;
    $this->page=$page;
    if(eregi("^(/.*)/([^/]*)$", $this->img, $m)) {
      $this->path=$m[1];
      $this->img=$m[2];
    }
    elseif(eregi("^(.*)/([^/]*)$", $this->img, $m)) {
      $this->path=$this->page->path."/".$m[1];
      $this->img=$m[2];
    }
    else {
      $this->path=$this->page->path;
    }
//print $this->path." ".$this->img."<br>\n";

    $this->index_id="$this->id-$this->img";
  }

  function count_as_picture() { return 1; }

  function colspan() {
    return 1;
  }

  function is_shown() { return 1; }

  function html_class() {
    return "image";
  }

  function read_comments() {
    global $lang_str;

    if((!isset($this->comments))&&file_exists("{$this->page->path}/comments/$this->img")) {
      $fp=fopen("{$this->page->path}/comments/$this->img", "r");
      $mode=0;
      while($str=fgets($fp, 8192)) {
        if($str=="$$$$$\n") {
          $mode=1;
          $this->comments[]=array();
        }
        else if($mode==1) {
          $age_color="comment_name";

          if($str=="\n")
            $str="$lang_str[comments_name_anon]";
          $name=trim($str);
          $mode=2;
        }
        else if($mode==2) {
          if($str>time()-604800)
            $age_color="comment_name_week";
          if($str>time()-259200)
            $age_color="comment_name_3days";
          if($str>time()-86400)
            $age_color="comment_name_day";
          $mode=3;
          $this->comments[sizeof($this->comments)-1]+=
            array("name"=>$name, "age_color"=>$age_color);
        }
        else if($mode==3) {
          $this->comments[sizeof($this->comments)-1]+=
            array("text"=>$this->comments[sizeof($this->comments)-1][text].$str."<br>");

        }
      }
    }
  }

  function album_show() {
    global $series;
    global $index_res;
    global $file_path;

    $r=getimagesize("$file_path/{$this->path}/$index_res/$this->img");

    $ret ="<a href='".url_script($this->page->path, $this->page->series, "image.php", $this->index)."'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."'";
    
    #$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    $ret.="class='album_image' width='$r[0]' height='$r[1]'></a><br>";
    #$ret="<a href='image.php?img=$this->index&series=".$this->page->series.
    #     "'><img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    #     "class='album_image'></a><br>";

    if($this->text)
      $ret.=strtr($this->text, array("\n"=>"<br>\n"))."<br>\n";

    $this->read_comments();
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }

    return $ret;
  }

  function get_image_details() {
    global $orig_path;
    global $file_path;

    $r=getimagesize("$file_path/{$this->path}/$orig_path/$this->img");
    $e=exif_read_data("$file_path/{$this->path}/$orig_path/$this->img");

    $ret["filename"]="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $orig_path, $_SESSION[img_version][$this->img])."'>".implode(" ", explode("_", $this->img))."</a>";

    //$ret["filename"]="<a href='orig/$this->img'>$this->img</a>";
    $ret["filesize"]=sprintf("%.1f kB", filesize("$file_path/{$this->path}/$orig_path/$this->img")/1024.0);
    if($e[DateTime])
      $ret["taketime"]=$e[DateTime];

    $ret["resolution"]="$r[0]x$r[1]";

    //print_r($r);
    if($e[ExifVersion]) {
      if($e[Make]!=substr($e[Model], 0, strlen($e[Make])))
        $ret["camera"]="$e[Make] $e[Model]";
      else
        $ret["camera"]="$e[Model]";

      $ret["exptime"]=$e[ExposureTime];
    }

    return $ret;
  }

  function toolbox() {
    global $resolutions;
    global $normal_res;
    global $lang_str;

    //return "";
    /*
    $ret ="<div class='toolbox' id='toolbox'>\n";

    foreach($resolutions as $res) {
      $ret.="<input type='submit' class='";
      $ret.=($normal_res==$res?"toolbox_input_active":"toolbox_input");
      $ret.="' onClick='set_normal_res($res)' id='res_$res' value='$res'/>\n";
    }

    $res="orig";
    $ret.="<input type='submit' class='";
    $ret.=($normal_res==$res?"toolbox_input_active":"toolbox_input");
    $ret.="' onClick='set_normal_res(\"$res\")' id='res_$res' value='$res' title=\"$lang_str[tooltip_set_res]\"\"/>\n";

    //$ret.="<input type='submit' class='toolbox_input' value='orig'/>\n";
    $ret.="<br>\n";
    //$ret.="<a accesskey='m' class='toolbox_input' id='toolbox_input_mag' href='javascript:start_mag()'>$lang_str[tool_magnify_name]</a><br>\n";
    $ret.="</div>\n";
    */
    $ret.=show_toolbox("imageview_toolbox");

    $ret1="";
    if($this->page->get_right($_SESSION[current_user], "edit")) {
      $ret1.="<input class='toolbox_input' type='submit' name='rot_left' value='$lang_str[tool_rotate_left]' onClick='start_rotate(\"".url_script($this->page->path, $this->page->series, "toolbox.php", $this->id)."&todo=rot_left\", this)' title=\"$lang_str[tooltip_rotate]\"><br>\n";
      $ret1.="<input class='toolbox_input' type='submit' name='rot_right' value='$lang_str[tool_rotate_right]' onClick='start_rotate(\"".url_script($this->page->path, $this->page->series, "toolbox.php", $this->id)."&todo=rot_right\", this)' title=\"$lang_str[tooltip_rotate]\"><br>\n";
    }
    if($this->page->get_right($_SESSION[current_user], "editdesc")) {
      $ret1.="<input accesskey='e' class='toolbox_input' type='submit' id='toolbox_input_desc' value='$lang_str[tool_editdesc_name]' onClick='start_desc_edit()' title=\"$lang_str[tooltip_editdesc]\"><br>\n";
    }
    if($this->page->get_right($_SESSION[current_user], "addcomment")) {
      $ret1.="<form accesskey='c' action='comment.php?id=$this->id' method='post'><input class='toolbox_input' type='submit' id='toolbox_input_comment' value='$lang_str[tool_comments_name]' onClick='start_add_comment()' title=\"$lang_str[tooltip_addcomment]\"></form>\n";
    }
    if(strlen($ret1)>0) {
      $ret.="<div class='toolbox' id='toolbox'>\n";
      $ret.=$ret1;
      $ret.="</div>\n";
    }

    return $ret;
  }


  function imageview_show($res=0) {
    global $series;
    global $_SESSION;
    global $normal_res;
    global $orig_path;
    global $file_path;
    global $lang_str;
    $ret="";

    if(!$res)
      $res=$normal_res;

    $imgres=getimagesize("$file_path/{$this->path}/$normal_res/$this->img");

    if(!$_SESSION[img_version][$this->img])
      $_SESSION[img_version][$this->img]=0;

    $ret.="<script type='text/javascript'>\n".
          "<!--\n".
          "var img_version={$_SESSION[img_version][$this->img]};\n".
          "var img_orig=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, $orig_path, $_SESSION[img_version][$this->img])."\";\n".
          "var img_size_url=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, "%SIZE%", $_SESSION[img_version][$this->img])."\";\n".
          "var img_url=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, $normal_res, $_SESSION[img_version][$this->img])."\";\n".
          "var series=\"$series\";\n".
          "var page=\"{$this->page->path}\";\n".
          "var index_id=\"{$this->index_id}\";\n".
          "var imgchunk=\"$this->index\";\n";

    $ret.="img_width={$imgres[0]};\n".
          "img_height={$imgres[1]};\n";

    $ret.="//-->\n</script>\n";

    $img_params[width]=$imgres[0];
    $img_params[height]=$imgres[1];

    call_hooks("imageview", &$img_params);

    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $orig_path, $_SESSION[img_version][$this->img])."' target='_top'>";

//    $ret.="<a href='$orig_path/$this->img?{$_SESSION[img_version][$this->img]}' target='_top'>".
    //$ret.="<img src='$normal_res/$this->img?{$_SESSION[img_version][$this->img]}' ";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $normal_res, $_SESSION[img_version][$this->img])."' target='_top' ";
    $ret.="id='img' class='imageview_image' ".implode_vars($img_params)." onLoad='notify_img_load()'></a>\n";
// 

    $ret.="<div class='toolbox'>\n";

    $det=$this->get_image_details();
    $ret.="<table class='imageview_image_details' width='100%'>\n";
    foreach($det as $name=>$value) {
      $ret.="<tr><td>".$lang_str["info_$name"].":</td><td>$value</td></tr>\n";
    }
    $ret.="</table></div>\n";

    $ret.=$this->toolbox();

    $ret.="<br style='clear: left;'>\n";

    if($this->text) {
      $ret.="<div class='imageview_image_desc' id='desc'>".
            strtr($this->text, array("\n"=>"<br>\n"))."</div>\n";
    }
    else {
      if($this->page->get_right($_SESSION[current_user], "editdesc"))
        $ret.="<div class='imageview_image_desc' id='desc'><i onClick='start_desc_edit()'>$lang_str[tool_editdesc_desc]</i></div>\n";
      else
        $ret.="<div class='imageview_image_desc' id='desc'> </div>\n";
    }
    $ret.="<div class='imageview_image_desc_edit' id='desc_edit'>";
    $ret.="<form action='javascript: save_desc_edit(\"{$this->page->series}\")'>\n";
    $ret.="<textarea id='input_img_desc' class='input_desc_edit'>$this->text</textarea>\n";
    $ret.="<input type='submit' value='$lang_str[nav_save]'>\n";
    $ret.="</form>\n";
    $ret.="</div>";

    $this->read_comments();
    $ret.="<div class='image_view_comments' id='image_view_comments'>\n";
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }
    $ret.=" </div>\n";

    $ret.="<div class='imageview_image_add_comment' id='add_comment'>";
    $ret.="<form action='javascript: save_comment(\"{$this->page->series}\")'>\n";
    $ret.="<table>\n";
    $ret.="<tr><td>Name:</td><td><input id='input_comment_name' value='' class='input_desc_edit'></td></tr>\n";
    $ret.="<tr><td>Kommentar:</td><td><input id='input_comment' value='' class='input_desc_edit'></td></tr>\n";
    $ret.="</table>\n";
    $ret.="<input type='submit' value='$lang_str[nav_save]'>\n";
    $ret.="</form></div>";

    $text="";
    call_hooks("image_description", &$text);
    $ret.=$text;

//    if(file_exists("comment.php")) {
//      $ret.="<a href='comment.php?img=$this->img'>Kommentar hinzuf&uuml;gen</a>";
//    }

    return $ret;
  }

  function list_show() {
    global $series;
    global $_SESSION;
    global $index_res;

    $ret ="<a href='".url_script($this->page->path, $this->page->series, "image.php", $this->index)."' target='main'>";
//    $ret ="<a href='image.php?img=$this->index&series=".
//          $this->page->series.
//          "' target='main'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."' class='list_image' id='$this->img'>";
    //$ret.="<img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' class='list_image' id='$this->img'></a>";

    return $ret;
  }

  function edit_show($text=0) {
    global $index_res;
    global $orig_path;

    if(!$text)
      $text=$this->text;

    $ret.="<div class='edit_img' ".
          "onMouseOver='page_edit_show_pic(this, \"$this->img\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, 600, 
                  $_SESSION[img_version][$this->img])."\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, $orig_path, 
                  $_SESSION[img_version][$this->img])."\")' ".
          "onMouseOut='page_edit_leave_image(this)' ".
          "onMouseMove='page_edit_move_pic(event)' ".
	  "onClick='page_edit_photo_click(event)' ".
	  ">\n";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, 64, $_SESSION[img_version][$this->img])."'>";
    $ret.="</div>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][img]' value='$this->img'>\n";
    $ret.="<textarea name='data[LIST][$this->id][text]' class='edit_input_imgchunk' onFocus='input_get_focus(this)' rows='1' onKeyUp='resize_textarea(this)' onMouseOut='page_edit_input_leave(this)'>$text</textarea>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='ImgChunk'>\n";
    $ret.="<div class='edit_img_details'>$this->img</div>";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }

  function file_name() { return $this->img; }

  function export_album() {
    global $index_res;
    global $file_path;

    $r=getimagesize("$file_path/{$this->path}/$index_res/$this->img");

    $ret ="<a href='".url_script(array("page"=>$this->page, "script"=>"image.php", "img"=>$this->index))."'>";
    $ret.="<img src='".url_photo(array("page"=>$this->page, "script"=>"image.php", "img"=>$this->id, "imgname"=>$this->img, "size"=>$index_res))."'";
    
    #$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    $ret.="class='album_image' width='$r[0]' height='$r[1]'></a><br>";
    #$ret="<a href='image.php?img=$this->index&series=".$this->page->series.
    #     "'><img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    #     "class='album_image'></a><br>";

    if($this->text)
      $ret.=strtr($this->text, array("\n"=>"<br>\n"))."<br>\n";

    $this->read_comments();
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }

    return $ret;
  }

  function export_imageview() {
    global $series;
    global $_SESSION;
    global $normal_res;
    global $orig_path;
    global $file_path;
    global $lang_str;
    $ret="";

    if(!$res)
      $res=$normal_res;

    $imgres=getimagesize("$file_path/{$this->path}/$normal_res/$this->img");

    $iw=$imgres[0];
    $ih=$imgres[1];

    $ret.="<a href='".url_photo(array("page"=>$this->page, "img"=>$this->id, "imgname"=>$this->img, "size"=>$orig_path))."' target='_top'>";

//    $ret.="<a href='$orig_path/$this->img?{$_SESSION[img_version][$this->img]}' target='_top'>".
    //$ret.="<img src='$normal_res/$this->img?{$_SESSION[img_version][$this->img]}' ";
    $ret.="<img src='".url_photo(array("page"=>$this->page, "img"=>$this->id, "imgname"=>$this->img, "size"=>$normal_res))."' target='_top' ";
    $ret.="id='img' class='imageview_image' width='$iw' height='$ih'></a>";

    $ret.="<div class='toolbox'>\n";

    $det=$this->get_image_details();
    $ret.="<table class='imageview_image_details' width='100%'>\n";
    foreach($det as $name=>$value) {
      $ret.="<tr><td>".$lang_str["info_$name"].":</td><td>$value</td></tr>\n";
    }
    $ret.="</table></div>\n";

    $ret.="<br style='clear: left;'>\n";

    if($this->text) {
      $ret.="<div class='imageview_image_desc' id='desc'>".
            strtr($this->text, array("\n"=>"<br>\n"))."</div>\n";
    }
    else {
      $ret.="<div class='imageview_image_desc' id='desc'> </div>\n";
    }

    $this->read_comments();
    $ret.="<div class='image_view_comments' id='image_view_comments'>\n";
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }
    $ret.=" </div>\n";

    return $ret;
  }


};

class MovieChunk extends ImgChunk {
  var $text;
  var $img;
  var $comments;

  function MovieChunk($page, $text, &$i, $j) {
    $this->type="MovieChunk";
    if(eregi("^([^ ]*)\.(avi|mov|flv|mpg|mpeg) (.*)$", $text, $m)) {
      $this->mov="$m[1].$m[2]";
      $this->img="$m[1].jpg"; // TODO: Suchen ob jpg/gif/png/...
      $this->text=$m[3];
    }
    elseif(eregi("^([^ ]*)\.(avi|mov|flv|mpg|mpeg)$", $text, $m)) {
      $this->mov="$m[1].$m[2]";
      $this->img="$m[1].jpg"; // TODO: Suchen ob jpg/gif/png/...
    }
    else {
      $this->mov=$text;
    }
    $this->index=$i++;
    $this->id=$j++;
    $this->page=$page;

    if(eregi("^(/.*)/([^/]*).(avi|mov|flv|mpg|mpeg)$", $this->img, $m)) {
      $this->path=$m[1];
      $this->mov=$m[2].$m[3];
      $this->img=$m[2]."jpg";
    }
    elseif(eregi("^(.*)/([^/]*)$", $this->img, $m)) {
      $this->path=$this->page->path."/".$m[1];
      $this->mov=$m[2].$m[3];
      $this->img=$m[2]."jpg";
    }
    else {
      $this->path=$this->page->path;
    }

    $this->index_id="$this->id-$this->mov";
  }

  function html_class() {
    return "image";
  }

  function album_show() {
    global $series;
    global $index_res;
    global $file_path;

    $r=getimagesize("$file_path/{$this->path}/$index_res/$this->img");

    $ret ="<a href='".url_script($this->page->path, $this->page->series, "image.php", $this->index)."'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."'";
    
    #$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    $ret.="class='album_image' width='$r[0]' height='$r[1]'></a><br>";
    #$ret="<a href='image.php?img=$this->index&series=".$this->page->series.
    #     "'><img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    #     "class='album_image'></a><br>";

    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->mov, $orig_path, $_SESSION[img_version][$this->img])."'>Movie downloaden</a><br>\n";

    if($this->text)
      $ret.=strtr($this->text, array("\n"=>"<br>\n"))."<br>\n";

    $this->read_comments();
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }

    return $ret;
  }

  function get_image_details() {
    global $orig_path;
    global $file_path;

//    $r=getimagesize("{$this->page->path}/$orig_path/$this->img");
//    $e=exif_read_data("{$this->page->path}/$orig_path/$this->img");

    $ret["filename"]="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->mov, "movie", $_SESSION[img_version][$this->img])."'>$this->mov</a>";

    //$ret["filename"]="<a href='orig/$this->img'>$this->img</a>";
    $ret["filesize"]=sprintf("%.1f kB", filesize("$file_path/{$this->path}/$orig_path/$this->mov")/1024.0);
//    if($e[DateTime])
//      $ret["taketime"]=$e[DateTime];
//
//    $ret["resolution"]="$r[0]x$r[1]";
//
//    //print_r($r);
//    if($e[ExifVersion]) {
//      if($e[Make]!=substr($e[Model], 0, strlen($e[Make])))
//        $ret["camera"]="$e[Make] $e[Model]";
//      else
//        $ret["camera"]="$e[Model]";
//
//      $ret["exptime"]=$e[ExposureTime];
//    }

    return $ret;
  }

  function toolbox() {
    global $resolutions;
    global $normal_res;
    global $lang_str;

    //return "";
    $ret ="<div class='toolbox' id='toolbox'>\n";

    $ret.="</div>\n";
    
    $ret1="";
    if($this->page->get_right($_SESSION[current_user], "edit")) {
      $ret1.="<input class='toolbox_input' type='submit' name='rot_left' value='$lang_str[tool_rotate_left]' onClick='start_rotate(\"toolbox.php?todo=rot_left&img=$this->img\", this)'><br>\n";
      $ret1.="<input class='toolbox_input' type='submit' name='rot_right' value='$lang_str[tool_rotate_right]' onClick='start_rotate(\"toolbox.php?todo=rot_right&img=$this->img\", this)'><br>\n";
    }
    if($this->page->get_right($_SESSION[current_user], "editdesc")) {
      $ret1.="<input accesskey='e' class='toolbox_input' type='submit' id='toolbox_input_desc' value='$lang_str[tool_editdesc_name]' onClick='start_desc_edit()'><br>\n";
    }
    if($this->page->get_right($_SESSION[current_user], "addcomment")) {
      $ret1.="<form accesskey='c' action='comment.php?id=$this->id' method='post'><input class='toolbox_input' type='submit' id='toolbox_input_comment' value='$lang_str[tool_comments_name]' onClick='start_add_comment()'></form>\n";
    }
    if(strlen($ret1)>0) {
      $ret.="<div class='toolbox' id='toolbox'>\n";
      $ret.=$ret1;
      $ret.="</div>\n";
    }

    return $ret;
  }

  function imageview_show($res=0) {
    global $series;
    global $normal_res;
    global $orig_path;
    global $file_path;
    global $lang_str;
    global $web_path;
    $ret="";

    if(!$res)
      $res=$normal_res;

    $imgres=getimagesize("$file_path/{$this->page->path}/$normal_res/$this->img");

    $ret.="<script type='text/javascript'>\n".
          "<!--\n".
          "var img_version=\"{$_SESSION[img_version][$this->img]}\";\n".
          "var img_orig=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, $orig_path, $_SESSION[img_version][$this->img])."\";\n".
          "var img_size_url=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, "%SIZE%", $_SESSION[img_version][$this->img])."\";\n".
          "var imgurl=\"$this->img\";\n".
          "var cur_res=\"$normal_res\";\n".
          "var series=\"$series\";\n".
          "var page=\"{$this->page->path}\";\n".
          "var index_id=\"{$this->index_id}\";\n".
          "var imgchunk=\"$this->index\";\n";

    $ret.="img_width={$imgres[0]};\n".
          "img_height={$imgres[1]};\n";

    if($_SESSION[fullscreen_mode])
      $ret.="var fullscreen={$_SESSION[fullscreen_mode]};\n";
    else
      $ret.="var fullscreen=0;\n";

    $ret.="//-->\n</script>\n";

    //$ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $orig_path, $_SESSION[img_version][$this->img])."' target='_top'>";

//    $ret.="<a href='$orig_path/$this->img?{$_SESSION[img_version][$this->img]}' target='_top'>".
    //$ret.="<img src='$normal_res/$this->img?{$_SESSION[img_version][$this->img]}' ";
//    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $normal_res, $_SESSION[img_version][$this->img])."' target='_top' ";
//    $ret.="id='img' class='imageview_image' width='$iw' height='$ih' onLoad='notify_img_load()'>";
// 
    $tmp_id=rand(0, 4000);
    $_SESSION["tmp_$tmp_id"]["page"]=$this->page->path;
    $_SESSION["tmp_$tmp_id"]["series"]=$this->page->series;
    $_SESSION["tmp_$tmp_id"]["img"]=$this->id;
    $_SESSION["tmp_$tmp_id"]["size"]="movie";
    session_register("tmp_$tmp_id");

    $url="$web_path/get_image.php?tmp_id=$tmp_id";
    //$url=htmlentities($url);

?>
    <object type="application/x-shockwave-flash" data="FlowPlayer.swf" 
            width="600" height="480" id="FlowPlayer">
      <param name="allowScriptAccess" value="sameDomain" />
      <param name="movie" value="FlowPlayer.swf" />
      <param name="quality" value="high" />
      <param name="scale" value="noScale" />
      <param name="wmode" value="transparent" />
      <param name="flashvars" value="config={videoFile: '<?=$url?>', initialScale: 'fit'}" />
    </object>
<?
      //<param name="flashvars" value="config={videoFile='<?=? >'}" />
    //$ret.="</a>\n";


    $ret.="<div class='toolbox'>\n";

    $det=$this->get_image_details();
    $ret.="<table class='imageview_image_details' width='100%'>\n";
    foreach($det as $name=>$value) {
      $ret.="<tr><td>".$lang_str["info_$name"].":</td><td>$value</td></tr>\n";
    }
    $ret.="</table></div>\n";

    $ret.=$this->toolbox();

    $ret.="<br style='clear: left;'>\n";

    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->mov, "movie", $_SESSION[img_version][$this->img])."'>Movie downloaden</a><br>\n";

    if($this->text) {
      $ret.="<div class='imageview_image_desc' id='desc'>".
            strtr($this->text, array("\n"=>"<br>\n"))."</div>\n";
    }
    else {
      if($this->page->get_right($_SESSION[current_user], "editdesc"))
        $ret.="<div class='imageview_image_desc' id='desc'><i onClick='start_desc_edit()'>$lang_str[tool_editdesc_desc]</i></div>\n";
      else
        $ret.="<div class='imageview_image_desc' id='desc'> </div>\n";
    }
    $ret.="<div class='imageview_image_desc_edit' id='desc_edit'>";
    $ret.="<form action='javascript: save_desc_edit(\"{$this->page->series}\")'>\n";
    $ret.="<input id='input_img_desc' value='$this->text' class='input_desc_edit'>\n";
    $ret.="<input type='submit' value='$lang_str[nav_save]'>\n";
    $ret.="</form>\n";
    $ret.="</div>";

    $this->read_comments();
    $ret.="<div class='image_view_comments' id='image_view_comments'>\n";
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }
    $ret.=" </div>\n";

    $ret.="<div class='imageview_image_add_comment' id='add_comment'>";
    $ret.="<form action='javascript: save_comment(\"{$this->page->series}\")'>\n";
    $ret.="<table>\n";
    $ret.="<tr><td>Name:</td><td><input id='input_comment_name' value='' class='input_desc_edit'></td></tr>\n";
    $ret.="<tr><td>Kommentar:</td><td><input id='input_comment' value='' class='input_desc_edit'></td></tr>\n";
    $ret.="</table>\n";
    $ret.="<input type='submit' value='$lang_str[nav_save]'>\n";
    $ret.="</form></div>";

//    if(file_exists("comment.php")) {
//      $ret.="<a href='comment.php?img=$this->img'>Kommentar hinzuf&uuml;gen</a>";
//    }

    return $ret;
  }

  function list_show() {
    global $series;
    global $_SESSION;
    global $index_res;

    $ret ="<a href='".url_script($this->page->path, $this->page->series, "image.php", $this->index)."' target='main'>";
//    $ret ="<a href='image.php?img=$this->index&series=".
//          $this->page->series.
//          "' target='main'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."' class='list_image' id='$this->img'>";
    //$ret.="<img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' class='list_image' id='$this->img'></a>";

    return $ret;
  }

  function edit_show($text=0) {
    global $index_res;
    global $orig_path;

    if(!$text)
      $text=$this->text;

    $ret.="<div class='edit_img' ".
          "onMouseOver='page_edit_show_pic(this, \"$this->img\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, 600, 
                  $_SESSION[img_version][$this->img])."\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, $orig_path, 
                  $_SESSION[img_version][$this->img])."\")' ".
          "onMouseOut='page_edit_leave_image(this)' ".
          "onMouseMove='page_edit_move_pic(event)' ".
	  "onClick='page_edit_photo_click(event)' ".
	  ">\n";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, 64, $_SESSION[img_version][$this->img])."'>";
    $ret.="</div>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][mov]' value='$this->mov'>\n";
    $ret.="<textarea name='data[LIST][$this->id][text]' class='edit_input_imgchunk' onFocus='input_get_focus(this)' rows='1' onKeyUp='resize_textarea(this)' onMouseOut='page_edit_input_leave(this)'>$text</textarea>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='MovieChunk'>\n";
    $ret.="<div class='edit_img_details'>$this->mov</div>";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }

  function file_name() { return $this->mov; }
};

function show_path() {
  global $text_pfad;
  global $lang_str;
  global $file_path;

  print "<div class='pathinfo'>$lang_str[nav_path]: ";
  if(!$text_pfad) {
    $path_to_me=array();
    $tp=null;

    while(is_file($file_path.$tp."fotocfg.txt")) {
      $subpage=get_page($tp, "");
      $subdata=$subpage->cfg; //get_values($tp."fotocfg.txt");
      if(!$subdata[TITLE])
        $subdata[TITLE]="(unknown)";
      if($tp=="")
        array_unshift($path_to_me, "<a href='".url_page($this->path, "", "index.php")."$subdata[TITLE]</a>")."' target='_top'>";
      else
        array_unshift($path_to_me, "<a href='".url_page($this->path, "", "index.php")."$subdata[TITLE]</a>")."' target='_top'>";
      $tp.="../";
    }

    if(sizeof($path_to_me)>0) {
      $text_pfad.=implode(" /\n", $path_to_me);
    }
  }

  print $text_pfad;
  print "</div>\n";
}

function check_img($img) {
  global $lang_str;

  if(strstr($img, "/")) {
    print "$lang_str[error_invalid_pictname]";
    return 0;
  }
  if(ereg("^\.", $img)) {
    print "$lang_str[error_invalid_pictname]";
    return 0;
  }
  if(!file_exists("$orig_path/$img")) {
    print "$lang_str[error_notexist_pict]";
    return 0;
  }
  return 1;
}

function check_series($img) {
  global $lang_str;

  if(!$img)
    return 1;
  if(eregi("^(.*)\.lst", $img, $m)) {
    $img=$m[1];
  }
  if(strstr($img, "/")) {
    print "$lang_str[error_invalid_series].";
    return 0;
  }
  if(ereg("^\.", $img)) {
    print "$lang_str[error_invalid_series].";
    return 0;
  }
  if(ereg("\.php$", $img)) {
    print "$lang_str[error_invalid_series].";
    return 0;
  }
//  if(!file_exists("$img.lst")) {
//    print "$lang_str[error_invalid_series].";
//    return 0;
//  }
  return $img;
}

$allpages=array();
function get_page($path="", $series="") {
  global $allpages;

  if($x=$allpages["$path || $series"])
    return $x;

  $x=new Page($path, $series);
  $allpages["$path || $series"]=$x;
  return $x;
}

class Page {
  var $cfg;
  var $path;
  var $series;
  var $cache;
  var $filename;
  var $parent_path;
  var $parent;
  var $inh_rights;
  var $rights;
  var $rights_checked;
  var $hidden_files;
  var $show_list;

  function last_modified() {
    global $file_path;
    $x=stat("$file_path/$this->filename");
    return $x[mtime];
  }

  function get_inherited_rights($user) {
    if(is_string($user))
      $username=$user;
    else
      $username=$user->username;

    if($this->inh_rights[$username])
      return $this->inh_rights[$username];

    $this->inh_rights[$username]=array();

    if($this->get_parent()) {
      //print "bla $username";
      $this->inh_rights[$username]=$this->parent->get_rights($username);
    }

    if($this->cfg["GROUP_LIST"]) foreach($this->cfg["GROUP_LIST"] as $g=>$group) {
      if($group->is_member($username))
	$this->inh_rights[$username]=rights_merge($this->inh_rights[$username], 
                                              $this->cfg["GROUP_RIGHTS"][$g]);
    }

//print_r($this->inh_rights[$username]);
    return $this->inh_rights[$username];
  }

  function get_rights($user) {
    if(is_string($user))
      $username=$user;
    else
      $username=$user->username;

    if($this->rights[$username])
      return $this->rights[$username];

    $this->rights[$username]=$this->get_inherited_rights($username);

    if($x=$this->cfg["RIGHTS"][$username]) {
      $this->rights[$username]=rights_merge($this->rights[$username], $x);
    }

    return $this->rights[$username];
  }

  function get_right($user, $right) {
    if(in_array($right, $this->get_rights($user)))
      return 1;
//    if(in_array($right, $user->default_rights))
//      return 1;
    return 0;
  }

  function check_rights() {
    if(!$this->cfg["LIST"])
      $this->cfg["LIST"]=array();

    $newlist=array();
    $list=$this->cfg["LIST"];
    $this->hidden_files=0;

    foreach($list as $l) {
      if($l->is_shown())
        $newlist[]=$l;
      else
        $this->hidden_files=1;
    }

    $this->cfg["LIST"]=$newlist;
    $this->rights_checked=1;
    //print "xxx5\n";
  }

  function get_viewlist() {
    global $file_path;

    $this->read_list();

    if(!$this->rights_checked) {
      $this->check_rights();
    }

    $list=array();
    foreach($this->cfg["LIST"] as $el) {
      $list[]=$el->file_name();
    }

//    if($this->count_pictures()&&($this->cfg["VIEWHIDE"]!="yes"))
//      $this->cfg["LIST"][]=new SeriesChunk($this, $this, &$subindex, &$id);

    if(!$this->series) {
      // Die weiteren Ansichten einlesen (nur in der Hauptansicht)
      $id=$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->id+1;
      $subindex=$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->index+1;
      if($dir=@opendir("$file_path/$this->path")) while($file=readdir($dir)) {
        if(ereg("^(.*)\.lst$", $file, $m)) {
          if(!in_array($m[1], $list)) {
            $n=&new SeriesChunk(&$this, "$m[1]@", &$subindex, &$id);
            $m=$n->get_subpage();
            if(($m->cfg["VIEWHIDE"]!="yes")&&($m->get_right($_SESSION[current_user], "announce")))
              $this->cfg["LIST"][]=$n;
          }
        }
      }
    }

    $this->get_sublist();

    $this->show_list=$this->cfg["LIST"];
    call_hooks("album_modify_list", &$this->show_list);

    return $this->cfg["LIST"];
  }

  function get_sublist() {
    global $file_path;

    $this->read_list();
    $this->rights_checked=0;
    $list=array();
    foreach($this->cfg["LIST"] as $el) {
      $list[]=$el->file_name();
    }

    if($dir=@opendir("$file_path/$this->path")) while($file=readdir($dir)) {
      if(!ereg("^\.", $file)&&
         is_dir("$file_path/$this->path/$file")&&
         file_exists("$file_path/$this->path/{$file}/fotocfg.txt")) {
        $id=$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->id+1;
        $subindex=$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->index+1;
        if(!in_array("$file", $list)) {
          $n=&new SubdirChunk(&$this, "$file/", &$subindex, &$id);
          $m=$n->get_subpage();
          //if(($m->cfg["HIDE"]!="yes")&&($m->get_right($_SESSION[current_user], "announce")))
          $this->cfg["LIST"][]=$n;
        }
      }
    }

    $this->check_rights();

    return $this->cfg["LIST"];
  }

  function get_parent() {
    if($this->parent_path===null)
      return null;

    if(!$this->parent)
      $this->parent=get_page($this->parent_path);

    return $this->parent;
  }

  function read_list() {
    global $extensions_images;
    global $extensions_movies;
    global $file_path;

    if($this->cfg["LIST"])
      return;
    $section="global";
    $this->cfg["LIST"]=array();

    $index=0;
    $id=0;
    $subindex=0;

    $fp=fopen("$file_path/$this->filename", "r");
    while($f=fgets($fp, 8192)) {
      $f=chop($f);
      //print "$f\n";

      if(($p=strpos($f, "\"\"\""))!==false) {
        $pre=substr($f, 0, $p);
        $f=substr($f, $p+3);

        $read=1;
        while(($read)&&(!ereg("\"\"\"$", $f))) {
          $read=fgets($fp, 8192);
          $f.="\n".chop($read);
        }
        $f=substr($f, 0, strlen($f)-3);

        $f="$pre$f";
      }

      if(eregi("^\\[([A-Za-z0-9]+)\\]", $f, $m)) {
        $section=$m[1];
      }
      elseif($section=="global") {
        if($f=="") {
          $section="imglist";
        }
      }
      elseif(($section=="imglist")||($section=="subdir")) {
        //print "<!-- xxx mem used ".memory_get_usage()." byte ; diff: ".(memory_get_usage()-$lastmem)." -- $f -->\n";
        if(eregi("^[^ ]*\.(".implode("|", $extensions_images).")", $f)) {
          if(!eregi("^index_", $f))
            $this->cfg["LIST"][]=new ImgChunk(&$this, $f, &$index, &$id);
          else
            $this->cfg["LIST"][]=new HeaderChunk($this, $f, &$index, &$id);
        }
        elseif(eregi("^[^ ]*\.(".implode("|", $extensions_movies).")", $f)) {
          $this->cfg["LIST"][]=new MovieChunk(&$this, $f, &$index, &$id);
        }
        elseif(eregi("^=", $f)) {
          $this->cfg["LIST"][]=new HeaderChunk($this, $f, &$index, &$id);
        }
        elseif(eregi("^[^ ]*@", $f)) {
          $this->cfg["LIST"][]=new SeriesChunk($this, $f, &$index, &$id);
        }
        elseif(eregi("^[^ ]*/", $f)) {
          $this->cfg["LIST"][]=new SubdirChunk($this, $f, &$index, &$id);
        }
        else {
          $this->cfg["LIST"][]=new TextChunk($this, $f, &$index, &$id);
        }
        //print "<!-- $path $series mem used ".memory_get_usage()." byte ; diff: ".(memory_get_usage()-$lastmem)." -- $f -->\n";
        //$lastmem=memory_get_usage();
      }
      elseif($section=="rights") {
      }
    }
    fclose($fp);

    if($this->cfg[SORT]=="alpha") {
      $list=array();
      foreach($this->cfg["LIST"] as $el) {
        $list[]=$el->file_name();
      }

      if($dir=@opendir("$file_path/$path/$index_res/")) {
        $filelist=array();
        while($file=readdir($dir)) {
          if(eregi("(".implode("|", array_merge($extensions_images, $extensions_movies)).")$", $file)) {
            $filelist[]=$file;
          }
        }
        closedir($dir);
      }

      sort($filelist);

      foreach($filelist as $file) {
        if(!in_array($file, $list))
          if(eregi("\.(".implode("|", $extensions_images).")$", $file))
            $this->cfg["LIST"][]=new ImgChunk($file);
          else
            $this->cfg["LIST"][]=new MovieChunk($file);
      }
    }

    return $this->cfg;
  }

  function Page($path="", $series="") {
    global $file_path;
    global $index_res;

    //print "new Page: $path = $series<br>\n";
    $cache=array();
    
    $this->inh_rights=array();
    $this->rights=array();

    $this->series=$series;
    if($path=="")
      $path=".";
    $this->path=$path;
    $this->parent=null;
    $this->parent_path=null;

    if($series=="") {
      $this->filename="$path/fotocfg.txt";
      $main_page=1;
      //print "$path<br>\n";
      if(eregi("^(.*)/[^/]*$", $path, $m)) {
        $this->parent_path=$m[1];
      }
      elseif(eregi("^[^/\.]+$", $path)) {
        $this->parent_path=".";
      }
    }
    else {
      $this->filename="$path/$series.lst";
      $main_page=0;
      $this->parent_path=$path;
    }
    //print "filename: $this->filename\n";

    $section="global";
    $this->cfg["LIST"]=0;
    $this->cfg["TITLE"]=$path;

    $index=0;
    $id=0;
    $subindex=0;

    $fp=fopen("$file_path/$this->filename", "r");
    while($f=fgets($fp, 8192)) {
      $f=chop($f);

      if(($p=strpos($f, "\"\"\""))!==false) {
        $pre=substr($f, 0, $p);
        $f=substr($f, $p+3);

        $read=1;
        while(($read)&&(!ereg("\"\"\"$", $f))) {
          $read=fgets($fp, 8192);
          $f.="\n".chop($read);
        }
        $f=substr($f, 0, strlen($f)-3);

        $f="$pre$f";
      }

      if(eregi("^\\[([A-Za-z0-9]+)\\]", $f, $m)) {
        $section=$m[1];
      }
      elseif($section=="global") {
        if($f=="") {
          $section="imglist";
        }

        if(ereg("^([A-Z_]+) (.*)$", $f, $m)) {
          $this->cfg[$m[1]]=$m[2];
        }
      }
      elseif(($section=="imglist")||($section=="subdir")) {
      }
      elseif($section=="rights") {
        $x=explode(" ", $f);
        $this->cfg["RIGHTS"][$x[0]]=explode(",", $x[1]);
      }
    }
    fclose($fp);
    
    if($this->cfg["RIGHTS"]) foreach($this->cfg["RIGHTS"] as $u=>$r) {
      if(substr($u, 0, 1)=="@") {
	$g=substr($u, 1);
        $this->cfg["GROUP_RIGHTS"][$g]=$r;
        $this->cfg["GROUP_LIST"][$g]=get_group($g);
      } 
    }

    if($this->cfg[MAIN_PICTURE])
      $this->cfg[MAIN_PICTURE]="$this->path/{$this->cfg[MAIN_PICTURE]}";

    return $this->cfg;
  }

  function toolbox() {
    global $cols;
    global $rows;
    global $lang_str;
    global $language;

    if($this->get_right($_SESSION[current_user], "edit")) {
      $ret ="<form action='".url_script($this->path, $this->series, "page_edit.php", "")."' method='get'>\n";
      $ret.="<input type='hidden' name='page' value=\"$this->path\">\n";
      $ret.="<input type='hidden' name='series' value=\"$this->series\">\n";
      $ret.="<input type='submit' value='$lang_str[tool_edit_page]' class='toolbox_input'><br>\n";
      $ret.="</form>\n";
      add_toolbox_item("album_admin", $ret);
      
      $ret ="<form action='".url_script($this->path, $this->series, "new_page.php", "")."' method='get'>\n";
      $ret.="<input type='hidden' name='page' value=\"$this->path\">\n";
      $ret.="<input type='hidden' name='series' value=\"$this->series\">\n";
      $ret.="<input type='submit' value='$lang_str[tool_new_page]' class='toolbox_input'>\n";
      $ret.="</form>\n";
      add_toolbox_item("album_admin", $ret);

      $ret ="<form action='".url_script($this->path, $this->series, "upload_image.php", "")."' method='get'>\n";
      $ret.="<input type='hidden' name='page' value=\"$this->path\">\n";
      $ret.="<input type='hidden' name='series' value=\"$this->series\">\n";
      $ret.="<input type='submit' value='$lang_str[tool_upload_pict]' class='toolbox_input'>\n";
      $ret.="</form>\n";
      add_toolbox_item("album_admin", $ret);
    }

    return "";
  }

  function header() {
    global $lang_str;

    $ret ="<div class='header'>\n";
    if($this->get_parent()) {
      $ret.=$this->parent->get_path($this);
      if($this->series)
        $ret.=" - <br>";
      else
        $ret.=" /<br>";
    }

    $ret.="<span class='header_head'>";
    $ret.="<a href='".url_page($this->path, $this->series, "index.php")."'>";
    $ret.="{$this->cfg[TITLE]}</a></span><br>\n";
    $ret.="<span class='header_kurzbeschreibung'>";
    if($this->cfg[DATE])
      $ret.="{$this->cfg[DATE]}.<br>\n";
    if($this->cfg[PHOTOS_BY])
      $ret.="$lang_str[info_photosby] {$this->cfg[PHOTOS_BY]}.<br>\n";
    $c=$this->count_pictures();
    if($c==1)
      $ret.="$c $lang_str[nav_pict].";
    elseif($c>1)
      $ret.="$c $lang_str[nav_picts].";
    $ret.="</span>\n";
    $ret.="</div>\n";

    return $ret;
  }

  function short_header() {
    $ret ="<div class='short_header'>";
    $ret.=$this->cfg[TITLE];
    $ret.="</div>\n";

    return $ret;
  }

  function welcome() {
    if(!$this->cfg[WELCOME_TEXT])
      return "";

    $ret ="<div class='welcome_text'>\n";
    $ret.=strtr($this->cfg[WELCOME_TEXT], array("\n"=>"<br>\n"))."</div>\n";
    //$ret.=$this->cfg[WELCOME_TEXT];
    $ret.="</div>\n";

    return $ret;
  }

  function count_pictures() {
    $i=0;

// TODO: Die Bilder aller Ansichten zusammenzaehlen
    foreach($this->cfg["LIST"] as $el) {
      if($el->count_as_picture())
        $i++;
    }

    return $i;
  }

  function count_subdirs() {
    $i=0;
    $this->get_sublist();

    foreach($this->cfg["LIST"] as $el) {
      if($el->count_as_subdir())
        $i++;
    }

    return $i;
  }

  function get_path($child=0) {
    global $page;
    $ret="";

    if($this->get_parent()) {
      $ret=$this->parent->get_path($this);
      if($this->series)
        $ret.="- ";
      else
        $ret.="/ ";
    }

    $param["page"]=$this;
    $addparam="";
    if(($page->path==$this->path)&&($page->series==$this->series)) {
      global $img;

      if($img) {
        $param["img"]=$img;
        $addparam="#img_$img";
      }
    }
    elseif($child) {
      $i=substr($child->path, strrpos($child->path, "/")+1);
      $param["img"]="img_$i";
      $addparam="#img_$i";
    }
    $ret.="<a href='".url_page($param)."$addparam'>"."{$this->cfg[TITLE]}</a>\n";

    return $ret;
  }

  function show_path() {
    if($this->cache[PATH])
      return $this->cache[PATH];

    $ret ="<div class='pathinfo'>\n";
    $ret.=$this->get_path();
    $ret.="</div>\n";

    $this->cache[PATH]=$ret;
    return $ret;
  }
  /*
    $ret="";
    $l="";

    if($this->cache[PATH]) {
      return $this->cache[PATH];
    }

    $ret.="<div class='pathinfo'>\n";
    $p="../";
    while(file_exists("{$p}fotocfg.txt")) {
      $subpage=new Page($p);
      $l="<a href='$p'>{$subpage->cfg[TITLE]}</a> / $l";
      $p.="../";
    }
    $ret.="$l <a href='.'>{$this->cfg[TITLE]}</a>\n";
    $ret.="</div>\n";

    $this->cache[PATH]=$ret;
    return $ret;
  }
*/

  function get_album_nav() {
    global $series;
    global $lang_str;

    $ret ="<table class='nav'>\n";

    $ret.="<tr><td class='nav_home'>\n";

    $ret.="<a href='".url_page($this->path, $this->series, "index.php")."' target='_top'>".
          "<img src='".url_img("house.png")."' class='nav_home' alt='$lang_str[nav_home]' title='$lang_str[nav_home]'></a>";

    $ret.="</td></tr></table>\n";

    return $ret;
  }

  function get_chunk_nav($img) {
    global $lang_str;

    $ret ="<table class='nav'>\n";
    $ret.="<tr><td class='nav_left'>\n";
    if($img==0)
      $ret.="<img src='".url_img(array("page"=>$this, "imgname"=>"arrow_left_dark.png"))."' class='nav_left' alt='&lt;' title='$lang_str[nav_prev]'> ";
    else
      $ret.="<a onClick='notify_list()' href='".url_script(array("page"=>$this, "script"=>"image.php", "img"=>$img-1))."' accesskey='p'>".
            "<img src='".url_img(array("page"=>$this, "imgname"=>"arrow_left.png"))."' class='nav_left' class='nav_left' alt='&lt;' title='$lang_str[nav_prev]'></a> ";

    $ret.="</td><td class='nav_text'>\n";

    $ret.=($img+1)." / ";
    $ret.=$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->get_index()+1;

    $ret.="</td><td class='nav_right'>\n";

    if($img==$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->get_index())
      $ret.="<img src='".url_img(array("page"=>$this, "imgname"=>"arrow_right_dark.png"))."' class='nav_right' alt='&gt;' title='$lang_str[nav_next]'> ";
    else
      $ret.="<a onClick='notify_list()' href='".url_script(array("page"=>$this, "script"=>"image.php", "img"=>$img+1))."' accesskey='n'>".
            "<img src='".url_img(array("page"=>$this, "imgname"=>"arrow_right.png"))."' class='nav_right' alt='&gt;' title='$lang_str[nav_next]'></a> ";

    $ret.="</td><td class='nav_home'>\n";

    $ret.="<a accesskey='h' href='".url_page(array("page"=>$this, "img"=>$img))."#img_$img' target='_top'>".
          "<img src='".url_img(array("page"=>$this, "imgname"=>"view_album.png"))."' class='nav_home' alt='$lang_str[nav_home]' title='$lang_str[nav_home]'></a>";

    //$ret.="</td><td class='nav_album'>\n";

    //$ret.="<a href='album.php?series={$this->series}#img_$img' target='_top'><img src='$img_path/view_album.png' class='nav_album' alt='Album' alt='Home'></a>";

    $ret.="</td><td class='nav_album'>\n";

    $ret.="<a href='".url_script(array("page"=>$this, "script"=>"frame.php", "img"=>$img))."' target='_top' id='nav_frame_a'>".
          "<img src='".url_img(array("page"=>$this, "imgname"=>"view_frame.png"))."' class='nav_album' ".
          "alt='$lang_str[nav_frame]' title='$lang_str[nav_frame]' ".
          "id='nav_frame_img'></a>";

    $ret.="</td></tr></table>\n";

    return $ret;
  }

  function load_data() {
    $this->get_viewlist();
    return $this->cfg;
  }

  function save_data($data) {
    global $fields;
    global $file_path;

    $save="";

    foreach(array_keys($fields) as $k) {
      $v=$data[$k];
      // Die Werte von einigen Keys nicht abspeichern
      if(!in_array($k, array("LIST", "RIGHTS", "chunk_order"))) {
        $v=stripslashes($v);
        if(strpos($v, "\n"))
          $save.="$k \"\"\"$v\"\"\"\n";
        else
          $save.="$k $v\n";
      }
    }

    $save.="[rights]\n";
    foreach($data["RIGHTS"] as $k=>$v) {
      $save.="$k ".implode(",", $v)."\n";
    }

    $save.="[imglist]\n";
    foreach($data["LIST"] as $k=>$v) {

      if(is_object($v))
        $v=get_object_vars($v);

      switch($v[type]) {
        case "ImgChunk":
          $v[text]=stripslashes($v[text]);
          if($v[text]) {
            if(strpos($v[text], "\n")===false)
              $save.="$v[img] $v[text]\n";
            else
              $save.="$v[img] \"\"\"$v[text]\"\"\"\n";
          }
          else
            $save.="$v[img]\n";
          break;
        case "MovieChunk":
          $v[text]=stripslashes($v[text]);
          if($v[text]) {
            if(strpos($v[text], "\n")===false)
              $save.="$v[mov] $v[text]\n";
            else
              $save.="$v[mov] \"\"\"$v[text]\"\"\"\n";
          }
          else
            $save.="$v[mov]\n";
          break;
        case "SubdirChunk":
          if(preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\-\.]*$/", $v[dir])) {
            $save.="$v[dir]/\n";

            if(!file_exists("$file_path/$this->path/$v[dir]/")) {
              mkdir("$file_path/$this->path/$v[dir]");
              $newseries=fopen("$file_path/$this->path/$v[dir]/fotocfg.txt", "w");
              fputs($newseries, "TITLE $v[TITLE]\n");
              fputs($newseries, "\n");
              fclose($newseries);
            }
          }
          else {
            print "\"$v[dir]\" $lang_str[error_invalid_chars].<br>\n";
          }
          break;
        case "SeriesChunk":
          if(eregi("^[a-z0-9_\\-]+$", $v[dir])) {
            $save.="$v[dir]@\n";

            if(!file_exists("$file_path/$this->path/$v[dir].lst")) {
              $newseries=fopen("$file_path/$this->path/$v[dir].lst", "w");
              fputs($newseries, "TITLE $v[TITLE]\n");
              fclose($newseries);
            }
          }
          else {
            print "\"$v[dir]\" $lang_str[error_invalid_chars].<br>\n";
          }
          break;
        case "TextChunk":
          if(strpos($v[text], "\n")===false)
            $save.="\"$v[text]\"\n";
          else
            $save.="\"\"\"$v[text]\"\"\"\n";
          break;
        case "HeaderChunk":
          $save.="=$v[text]=\n";
          break;
      }
    }

    if(!($f=fopen("$file_path/$this->filename", "w"))) {
      return "Can't open file for writing";
    }
    fputs($f, $save);
    fclose($f);
    return 0;
  }

  function set_page_edit_data($data) {
    global $lang_str;
    global $file_path;

    $str="";
    if(!($f=fopen("$file_path/$this->filename", "w"))) {
      print "<status>Can't open file for writing</status>\n";
      return 0;
    }

    //print "<pre>\n";
    //print_r($data);
    //print "</pre>\n";

    foreach($data as $k=>$v) {
      // Die Werte von einigen Keys nicht abspeichern
      if(!in_array($k, array("LIST", "RIGHTS", "chunk_order"))) {
        $v=stripslashes($v);
        if(strpos($v, "\n"))
          fputs($f, "$k \"\"\"$v\"\"\"\n");
        else
          fputs($f, "$k $v\n");
      }
    }
    fputs($f, "\n[rights]\n");
    foreach($data["RIGHTS"] as $u=>$d) {
      $r=array();

      foreach($d as $rk=>$rg) {
        if($rg==1)
          $r[]=$rk;
        elseif($rg==-1)
          $r[]="-$rk";
      }

      if(sizeof($r))
        fputs($f, "$u ".implode(",", $r)."\n");
    }

    fputs($f, "\n[imglist]\n");
//print_r($data[chunk_order]);
//print_r($data["LIST"]);
    foreach($data[chunk_order] as $k) {
      if($k=="start_unused")
        break;

    //foreach($data["LIST"] as $k=>$v) {
      if($_REQUEST["delete"][$k]) {
      }
      else {
        $v=$data["LIST"][$k];
        //print "<br>$k:\n";
        //print_r($v);
	switch($v[type]) {
	  case "ImgChunk":
            $v[text]=stripslashes($v[text]);
	    if($v[text]) {
              if(strpos($v[text], "\n")===false)
                fputs($f, "$v[img] $v[text]\n");
              else
                fputs($f, "$v[img] \"\"\"$v[text]\"\"\"\n");
            }
	    else
	      fputs($f, "$v[img]\n");
	    break;
	  case "MovieChunk":
            $v[text]=stripslashes($v[text]);
	    if($v[text]) {
              if(strpos($v[text], "\n")===false)
                fputs($f, "$v[mov] $v[text]\n");
              else
                fputs($f, "$v[mov] \"\"\"$v[text]\"\"\"\n");
            }
	    else
	      fputs($f, "$v[mov]\n");
	    break;
          case "SubdirChunk":
            if(preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\-\.]*$/", $v[dir])) {
              fputs($f, "$v[dir]/\n");

              if(!file_exists("$file_path/$this->path/$v[dir]/")) {
                mkdir("$file_path/$this->path/$v[dir]");
                $newseries=fopen("$file_path/$this->path/$v[dir]/fotocfg.txt", "w");
                fputs($newseries, "TITLE $v[TITLE]\n");
                fputs($newseries, "\n");
                fclose($newseries);
              }
            }
            else {
              print "\"$v[dir]\" $lang_str[error_invalid_chars].<br>\n";
            }
            break;
          case "SeriesChunk":
            if(eregi("^[a-z0-9_\\-]+$", $v[dir])) {
              fputs($f, "$v[dir]@\n");

              if(!file_exists("$file_path/$this->path/$v[dir].lst")) {
                $newseries=fopen("$file_path/$this->path/$v[dir].lst", "w");
                fputs($newseries, "TITLE $v[TITLE]\n");
                fclose($newseries);
              }
            }
            else {
              print "\"$v[dir]\" $lang_str[error_invalid_chars].<br>\n";
            }
            break;
          case "TextChunk":
            if(strpos($v[text], "\n")===false)
              fputs($f, "\"$v[text]\"\n");
            else
              fputs($f, "\"\"\"$v[text]\"\"\"\n");
            break;
          case "HeaderChunk":
            fputs($f, "=$v[text]=\n");
            break;
	}
      }
    }

    //while($r=fgets($f, 8192)) {
//      if(
//      if(strpos($r, $_REQUEST[img])===0) {
//        $str.="$_REQUEST[img] $data\n";
//      }
//      else
//        $str.=$r;
    //}
    fclose($f);
    return 1;
  }

  function page_edit_load_unused_images($quiet=0) {
    global $orig_path;
    global $extensions_images;
    global $extensions_movies;
    global $file_path;

    $data=$this->cfg;

    $max_id=0;
    $full_list=array();
    if(@$d=opendir("$file_path/$this->path/$orig_path")) {
      while($f=readdir($d)) {
        if(eregi("\.(".implode("|", array_merge($extensions_images, $extensions_movies)).")$", $f))
          $full_list[]=$f;
      }
      closedir($d);
    }

    foreach($data["LIST"] as $d) {
      if(($i=array_search($d->file_name(), $full_list))!==false) {
        $full_list=array_merge(array_slice($full_list, 0, $i), 
                               array_slice($full_list, $i+1));

      }
      $max_id=$d->id;
    }

    $unused=array();
    foreach($full_list as $f) {
      if(eregi("\.(".implode("|", $extensions_images).")$", $f))
        $d=new ImgChunk($this, $f, $index, $max_id+1);
      else
        $d=new MovieChunk($this, $f, $index, $max_id+1);
      $unused[]=$d;
      $max_id=$d->id;
    }

    if(!$quiet)
      print "<script type='text/javascript'>\n<!--\n max_chunk={$max_id};\n//-->\n</script>\n";

    return $unused;
  }

  function show_page_edit_form($data=0) {
    global $fields;
    global $orig_path;
    global $rights;
    global $lang_str;

    if(!$data) {
      $data=$this->cfg;
    }

    //print_r($data);

    print "<div class='page_edit'>\n";
    print "<span id='tab_page1' class='page_edit_choose_page_chose' onClick='page_edit_show_page(\"page1\")'>$lang_str[page_edit_page_main]</span>\n";
    print "<span id='tab_page2' class='page_edit_choose_page' onClick='page_edit_show_page(\"page2\")'>$lang_str[page_edit_page_pict]</span>\n";
    print "<span id='tab_page3' class='page_edit_choose_page' onClick='page_edit_show_page(\"page3\")'>$lang_str[page_edit_page_rights]</span>";
    print "<form action='page_edit.php' method='post' id='page_edit_form'>\n";
    print "<input type='hidden' name='page' value=\"$this->path\">\n";
    print "<input type='hidden' name='series' value=\"$this->series\">\n";

    print "<div class='page_edit_page' id='page1'>\n";
    print "<table>\n";

    foreach($fields as $f=>$txt) {
      print "<tr><td>$txt:</td>\n";
      print "<td>\n";
      switch($f) {
        case "WELCOME_TEXT":
          print "<textarea class='page_edit_input' name='data[$f]'>{$data[$f]}</textarea>\n";
          break;
        case "MAIN_PICTURE":
          $m=$data[$f];
          if(strpos($m, "$this->path/")===0)
            $m=substr($m, strlen("$this->path/"));
          print "<input class='page_edit_input' name='data[$f]' value=\"$m\">\n";
          break;
        default:
          print "<input class='page_edit_input' name='data[$f]' value=\"{$data[$f]}\">\n";
      }
      print "</td></tr>\n";
    }

    //print "<td><textarea class='page_edit_input' name='data[WELCOME_TEXT]'>{$data[WELCOME_TEXT]}</textarea></td></tr>\n";

    print "</table>\n";
    print "</div>\n"; // page1

    print "<div class='page_edit_page' id='page2' style='display: none'>\n";

    print "<div class='move_img_list' id='move_img_list'>\n";
    print "</div>\n";

    print "<div class='' id='page_edit_show_pic' style='display: none' onMouseMove='page_edit_move_pic(event)'>\n";
    print "<img src='' border='0'>";
    print "</div>\n";

    print "<div class='edit_img_list' id='edit_img_list' onClick='page_edit_img_list_clicked(event, this)' onMouseDown='page_edit_img_list_down(event, this)'>\n";
    print "$lang_str[page_edit_pict_list]:<br>\n";
    foreach($data["LIST"] as $d) {
//      if(($i=array_search($d->file_name(), $full_list))!==false) {
//        $full_list=array_merge(array_slice($full_list, 0, $i), 
//                               array_slice($full_list, $i+1));
//
//      }
      print "<div class='edit_img_spacer' edit_type='spacer' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)'></div>\n";
      print "<div class='edit_img_chunk' id='chunk_$d->id' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>\n";
      print "<input type='hidden' name='data[chunk_order][]' value='$d->id'>\n";
      print $d->edit_show();
      print "</div>\n\n";
    }

    $unused=$this->page_edit_load_unused_images();

    print "<div class='edit_img_spacer' edit_type='spacer' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)'></div>\n";
    print "<div class='edit_img_chunk' id='end_img_list' style='display: none'>\n";
    print "</div>\n";
    print "</div>\n";

    print "<div class='edit_img_connect'><table height='100%' border='0' cellpadding='0' cellspacing='0'><tr><td class='edit_img_connect'>\n";
    print "<a href='javascript:move_to_list(\"edit_img_list\")'>&lt;</a><br>\n";
    print "<a href='javascript:move_to_list(\"unused_img_list\")'>&gt;</a>\n";
    print "</td></tr></table>\n";
    print "</div>\n";

    print "<input type='hidden' name='data[chunk_order][]' value='start_unused'>\n";
    print "<div class='edit_img_list' id='unused_img_list' onClick='page_edit_img_list_clicked(event, this)' onMouseDown='page_edit_img_list_down(event, this)'>\n";
    print "$lang_str[page_edit_pict_unused]:<br>\n";
    foreach($unused as $d) {
      print "<div class='edit_img_spacer' edit_type='spacer' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)'></div>\n";
      print "<div class='edit_img_chunk' id='chunk_$d->id' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>\n";
      print "<input type='hidden' name='data[chunk_order][]' value='$d->id'>\n";
      print $d->edit_show();
      print "</div>\n\n";
    }

    print "<div class='edit_img_spacer' edit_type='spacer' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)'></div>\n";
    print "<div class='edit_img_chunk' id='end_unused_img_list' style='display: none'>\n";
    print "</div>\n";
    print "</div>\n";

    print "<div class=''>\n";
    print "<div id='debug'>\n";
    print "&nbsp;</div>\n";
    print "$lang_str[page_edit_pict_new]<br>";
    print "<a href='javascript: page_edit_new(\"new_text\")'>$lang_str[page_edit_pict_new_text]</a><br>\n";
    print "<a href='javascript: page_edit_new(\"new_header\")'>$lang_str[page_edit_pict_new_heading]</a><br>\n";
    print "<a href='javascript: page_edit_new(\"new_series\")'>$lang_str[page_edit_pict_new_series]</a><br>\n";
    print "<a href='javascript: page_edit_new(\"new_subdir\")'>$lang_str[page_edit_pict_new_subdir]</a>\n";

    // Template fuer "new text"
?>
<div style='display: none' class='edit_img_chunk' id='new_text' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>
<input type='hidden' name='data[chunk_order][]' value='XXXX'>
<textarea class='edit_input_textchunk' onKeyUp='resize_textarea(this)' name='data[LIST][XXXX][text]' onFocus='input_get_focus(this)' onLoad='resize_textarea(this)' onMouseOut='page_edit_input_leave(this)'></textarea>
<input type='hidden' name='data[LIST][XXXX][type]' value='TextChunk'>
<br style='clear: left;'>
</div>
<?

    // Template fuer "new header"
?>
<div style='display: none' class='edit_img_chunk' id='new_header' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>
<input type='hidden' name='data[chunk_order][]' value='XXXX'>
<input name='data[LIST][XXXX][text]' class='edit_input_headerchunk' value='' onFocus='input_get_focus(this)' onMouseOver='page_edit_input_enter(this)' onMouseOut='page_edit_input_leave(this)'>
<input type='hidden' name='data[LIST][XXXX][type]' value='HeaderChunk'>
<br style='clear: left;'>
</div>
<?

    // Template fuer "new series"
    print "<div style='display: none' class='edit_img_chunk' id='new_series' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>\n";
    print "<input type='hidden' name='data[chunk_order][]' value='XXXX'>\n";
    print "$lang_str[page_edit_pict_chunk_new_view]:<br>\n";
    print "$lang_str[new_page_view]: <input name='data[LIST][XXXX][dir]' value=''><br>\n";
    print "$lang_str[new_page_title]: <input name='data[LIST][XXXX][TITLE]' value=''><br>\n";
    print "<input type='hidden' name='data[LIST][XXXX][type]' value='SeriesChunk'>\n";
    print "<br style='clear: left;'>\n";
    print "</div>\n";

    // Template fuer "new subdir"
    print "<div style='display: none' class='edit_img_chunk' id='new_subdir' edit_type='chunk' onMouseOver='page_edit_mouse_enter(event, this)' onMouseOut='page_edit_mouse_leave(event, this)' onMouseDown='page_edit_img_chunk_clicked(event, this)'>\n";
    print "<input type='hidden' name='data[chunk_order][]' value='XXXX'>\n";
    print "$lang_str[page_edit_pict_chunk_new_subdir]:<br>\n";
    print "$lang_str[new_page_dir]: <input name='data[LIST][XXXX][dir]' value=''><br>\n";
    print "$lang_str[new_page_title]: <input name='data[LIST][XXXX][TITLE]' value=''><br>\n";
    print "<input type='hidden' name='data[LIST][XXXX][type]' value='SubdirChunk'>\n";
    print "<br style='clear: left;'>\n";
    print "</div>\n";

    // Ende der Templates
    print "</div>\n";
    print "<br style='clear: left;'>\n";
    print "</div>\n";

    print "<div class='page_edit_page' id='page3' style='display: none'>\n";
    ?>
    Folgende Abstufungen der Rechte gibt es:<table>
    <tr><th>announce</th><td>Unterverzeichnis wird im darueberliegenden Verzeichnis angezeigt bzw. Ansicht in der Bilderliste</td>
    <tr><th>view</th><td>Bilderliste wird angezeigt, wenn nicht, kommt ein Login-Fenster</td>
    <tr><th>addcomment</th><td>BenutzerIn darf neue Kommentare hinzufuegen</td>
    <tr><th>editdesc</th><td>BenutzerIn darf die Beschreibung des Bildes veraendern</td>
    <tr><th>new</th><td>BenutzerIn darf neue Unterverzeichnisse und neue Ansichten anlegen (und bekommt dort dann edit-Rechte)</td>
    <tr><th>edit</th><td>BenutzerIn darf Bilder uploaden, rotieren, Reihenfolge veraendern und Bilder aus der Auswahl entfernen</td>
    <tr><th>rights</th><td>BenutzerIn darf Zugriffsrechte fuer diese Seite modifizieren</td>
    </table>
    Die Farben haben folgende Bedeutung:<table>
    <tr><td class='page_edit_rights_grant'></td><td>Dieses Recht wird an BenutzerIn/Gruppe explizit vergeben</td>
    <tr><td class='page_edit_rights_notgrant'></td><td>Dieses Recht wird BenutzerIn/Gruppe explizit entzogen</td>
    <tr><td class='page_edit_rights_inh_grant'></td><td>Dieses Recht wird von Gruppe oder darueberliegender Seite geerbt</td>
    <tr><td class='page_edit_rights_inh_notgrant'></td><td>Dieses Recht wird von Gruppe oder darueberliegender Seite nicht geerbt</td>
    </table>
    <?
    //print_r($this->get_inherited_rights("anonymous"));
    print "<table>\n";
    print "<tr>\n";
    print "  <th></th>\n";
    foreach($rights as $r) {
      print "  <th>$r</th>\n";
    }
    print "</tr>\n";

    function show_rights($u, $t) {
      global $rights;

      $ir=$t->get_inherited_rights($u);
      //$r=$this->get_rights($u);

      print "<tr>\n";
      print "  <th>$u</th>\n";
      foreach($rights as $r) {
        if(in_array($r, $ir)) {
          $c="page_edit_rights_inh_grant";
          $i=1;
        }
        else {
          $c="page_edit_rights_inh_notgrant";
          $i=-1;
        }

        $o=0;
        if($t->cfg["RIGHTS"]&&sizeof($t->cfg["RIGHTS"][$u])) {
          if(in_array($r, $t->cfg["RIGHTS"][$u])) {
            $c="page_edit_rights_grant";
            $o=1;
          }
          elseif(in_array("-$r", $t->cfg["RIGHTS"][$u])) {
            $c="page_edit_rights_notgrant";
            $o=-1;
          }
        }

        print "  <td id='rights_td_{$u}_{$r}' class='{$c}' onClick='page_edit_modify_rights(\"$u\", \"$r\")'>\n";
        print "    <input type='hidden' id='rights_input_{$u}_{$r}' name='data[RIGHTS][$u][$r]' value='$o'>\n";
        print "    <input type='hidden' id='rights_inherited_{$u}_{$r}' value='$i'>\n";
        print "  </td>\n";
      }
      print "</tr>\n";
    }

    show_rights("anonymous", $this);

    $ul=array_keys(user_list());
    sort($ul);
    foreach($ul as $u) {
      if($u!="anonymous")
        show_rights($u, $this);
    }

    $gl=array_keys(group_list());
    sort($gl);
    foreach($gl as $g) {
      show_rights("@$g", $this);
    }
    print "</table>\n";

    print "</div>\n";


    print "<br style='clear: left;'>\n";
    print "<input type='submit' name='submit[ok]' value='$lang_str[nav_save]'>\n";
    
    print "</form>\n";

    print "</div>\n";
  }

  function show_album($img=0) {
    global $cols;

//    $album_pages=$this->split_album_pages();
//    $album_page=$this->find_album_page($img);
    //$list=$this->get_viewlist();
    $list=$this->show_list;
    
    $td_size=100.0/$cols;
    unset($cur_index);

    print "<table class='album_table' width='100%' id='table_album'>\n";
    $pos=$cols;
    $i=0;
    if($list) foreach($list as $el) {
      $colspan=$el->colspan();
      if($colspan>$cols)
        $colspan=$cols;

      if($pos<$colspan) {
        while($pos>0) {
          print "  <td class='imglist_empty' width='$td_size%'></td>\n";
          $pos--;
        }
        $pos=$cols;
        print "</tr>\n";
      }

      if($pos==$cols)
        print "<tr>\n";

      print "  <td colspan='$colspan' class='album_".$el->html_class()."' width='".
            ($td_size*$colspan)."%' id='chunk_$i' photopages_id='$el->id' photopages_index='$el->index'>\n";

      if($el->get_index()!=$cur_index) {
        $cur_index=$el->get_index();
        print "    <a name='img_$cur_index'></a>\n";
        if($file_name=$el->file_name())
          print "    <a name='img_$file_name'></a>\n";
      }

      print "    ".$el->album_show();
      print "</td>\n";

      $pos-=$colspan;
      if($pos==0) {
        $pos=$cols;
        print "</tr>\n";
      }

      $i++;
    }

    while($pos>0) {
      print "  <td class='imglist_empty' width='$td_size%'></td>\n";
      $pos--;
    }
    print "</tr>\n";
    print "</table>\n";
  }

  function export_album() {
    $ret="";
    $ret.="<html>\n";
    $ret.="<head><title>{$this->cfg[TITLE]}</title>\n";
    $ret.="<link rel=stylesheet type='text/css' href='".url_img(array("page"=>$this, "imgname"=>"style.css"))."'>\n";
    $ret.="</head><body>\n";
    $ret.=$this->header();

    $cols=4;

    $list=$this->get_viewlist();
    $td_size=100.0/$cols;
    unset($cur_index);

    $ret.="<table class='album_table' width='100%' id='table_album'>\n";
    $pos=$cols;
    $i=0;
    foreach($list as $el) {
      $colspan=$el->colspan();
      if($colspan>$cols)
        $colspan=$cols;

      if($pos<$colspan) {
        while($pos>0) {
          $ret.="  <td class='imglist_empty' width='$td_size%'></td>\n";
          $pos--;
        }
        $pos=$cols;
        $ret.="</tr>\n";
      }

      if($pos==$cols)
        $ret.="<tr>\n";

      $ret.="  <td colspan='$colspan' class='album_".$el->html_class()."' width='".
            ($td_size*$colspan)."%' id='chunk_$i' photopages_id='$el->id' photopages_index='$el->index'>\n";

      if($el->get_index()!=$cur_index) {
        $cur_index=$el->get_index();
        $ret.="    <a name='img_$cur_index'></a>\n";
      }

      $ret.="    ".$el->export_album();
      $ret.="</td>\n";

      $pos-=$colspan;
      if($pos==0) {
        $pos=$cols;
        $ret.="</tr>\n";
      }

      $i++;
    }

    while($pos>0) {
      $ret.="  <td class='imglist_empty' width='$td_size%'></td>\n";
      $pos--;
    }
    $ret.="</tr>\n";
    $ret.="</table>\n";

    $ret.="</body>\n";
    $ret.="</html>\n";
    return $ret;
  }


  function export_image($img) {
    $ret="";
    $ret.="<html>\n";
    $ret.="<head><title>{$this->cfg[TITLE]} :: Bild ".($img+1)."</title>\n";
    $ret.="<link rel=stylesheet type='text/css' href='".url_img(array("page"=>$this, "imgname"=>"style.css"))."'>\n";
    $ret.="</head><body>\n";

    $ret.=$this->get_chunk_nav($img);
    $ret.=$this->get_path();

    foreach($this->cfg["LIST"] as $el) {
      if($el->get_index()==$img) {
        $ret.="<div index='$img' class='imageview_".$el->html_class()."'>\n";
        $ret.=$el->export_imageview();
        $ret.="</div>\n";
      }
    }

    $ret.="</body>\n";
    $ret.="</html>\n";
    return $ret;
  }

  function export_html($export_path, $rel_path="") {
    global $file_path;
    global $export_img;
    global $exported_pages;
    global $export_page;
    global $url_relative;
    $url_relative=$this->path;
    $export_page_local=array();
    $export_img=array();

print "starting export: $this->path $this->series -> $export_path<br>\n";
    $exported_pages[]="$this->path#$this->series";

    if(!$export_path)
      $export_path="/tmp/export";
    mkdir($export_path);

    symlink("$file_path/$this->filename", "$export_path/".substr($this->filename, strrpos($this->filename, "/")+1));
    symlink("$file_path/{$this->cfg["MAIN_PICTURE"]}", "$export_path/main.jpg");

    $f=fopen("$export_path/index.html", "w");
    fputs($f, $this->export_album());
    fclose($f);

    $last_img=-1;
    if(sizeof($this->cfg["LIST"]))
    for($i=0; $i<$this->cfg["LIST"][sizeof($this->cfg["LIST"])-1]->get_index(); $i++) {
      $f=fopen("$export_path/image_$i.html", "w");
      fputs($f, $this->export_image($i));
      fclose($f);
    }

    foreach($export_img as $res=>$img) {
      mkdir("$export_path/$res");
      foreach(array_unique($img) as $i) {
        symlink("$file_path/$this->path/$res/$i", "$export_path/$res/$i");
      }
    }

    $export_page_local=$export_page;
    $export_page=array();
    foreach($export_page_local as $p) {
      $p->get_subpage();
      if(!in_array("{$p->subpage->path}#{$p->subpage->series}", $exported_pages)) {
        print "exporting {$p->subpage->path}<br>\n";
        $p->subpage->export_html($export_path."/".
                 substr($p->subpage->path, strrpos($p->subpage->path, "/")+1));
      }
    }

    foreach($exported_pages as $p) {
      print $p->path."<br>\n";
    }
  }

}

// Here starts the basic initialisation of vars
include "inc/user.php";
if(!$_SESSION[current_user])
  $_SESSION[current_user]=get_user("anonymous");

session_start();
include "inc/url.php";
include "inc/html_header.php";
use_javascript("global");
include "inc/group.php";
include "lang.php";
require "inc/extensions.php";
include "inc/toolbox.php";
include "inc/text.php";
include "inc/hooks.php";
include "inc/vars.php";
include_extensions($extensions_page);

//chdir("$file_path");

if($_REQUEST[username]) {
  $test=get_user($_REQUEST[username]);
  if($test->authenticate($_REQUEST[password])) {
    $_SESSION[current_user]=$test;
  }
}

if(!$_SESSION[current_user]) {
  $_SESSION[current_user]=get_user("anonymous");
  session_register("current_user");
}

$series=$_REQUEST[series];
if(!($series=check_series($series))) {
  unset($series);
}
elseif($series==1) {
  unset($series);
}

unset($page);
#print_r($_SERVER);
#print "SERVERPATHINFO: $_SERVER[PATH_INFO]<br>";
#while(substr($_SERVER[PATH_INFO], 0, 1)=="/")
#  $_SERVER[PATH_INFO]=substr($_SERVER[PATH_INFO], 1);
//print "bla1\n";
if(!$_REQUEST[page])
  $_REQUEST[page]="";
if(!file_exists("$file_path/$_REQUEST[page]"))
  $_REQUEST[page]="";

// Doppelte / und / am Schluss entfernen
do {
  $p=$_REQUEST[page];
  $_REQUEST[page]=implode("/", explode("//", $_REQUEST[page]));
} while($p!=$_REQUEST[page]);
while(substr($_REQUEST[page], strlen($_REQUEST[page])-1, 1)=="/") {
  $_REQUEST[page]=substr($_REQUEST[page], 0, strlen($_REQUEST[page])-1);
}

$page=get_page($_REQUEST[page], $series);
//print "bla2\n";
$page->get_viewlist();
// print "bla3\n";
if(!$series) {
  $page->get_sublist();
}

//$rights=$page->get_rights($_SESSION[current_user]);

#  $cfg1=get_values("$series.lst");
#  foreach($this->cfg1 as $c=>$v) {
#    if(in_array($c, $this->cfg1["LIST"])) {
#      if($v||(!$this->cfg[$c]))
#        $this->cfg[$c]=$v;
#    }
#    else {
#      $this->cfg[$c]=$v;
#    }

# Damit beim Rotieren und so immer die richtigen Bilder kommen
if(!is_array($_SESSION[img_version])) {
  $_SESSION[img_version]=array();
  session_register("img_version");
}

}

function http_date($t=0) {
  setlocale(LC_TIME, "POSIX");
  return gmstrftime ("%a, %d %b %Y %T %Z", $t);
  //setlocale(LC_TIME, 
}

function rights_merge($rights, $addrights) {
  if(!$rights)
    $rights=array();

  foreach($addrights as $r) {
    if(substr($r, 0, 1)=="-") {
      if(($p=array_search(substr($r, 1), $rights))!==false)
	$rights=array_slice($rights, 0, $p)+array_slice($rights, $p+1);
    }
    else
      if(!in_array($r, $rights))
	$rights=array_merge($rights, array($r));
  }

  return $rights;
}

function list_dir($dir) {
  global $upload_path;
  global $lang_str;
  global $extensions_images;
  global $extensions_movies;
  
  if(!$dir)
    $dir="/";
  $ret="";

  $ret.="$lang_str[upload_image_dir]: ";
  $l=array();
  $l[]="<a href='javascript:list_dir(\"/\")'>$upload_path</a>";
  $di="";
  foreach(explode("/", $dir) as $d) {
    $di.="/$d";
    $l[]="<a href='javascript:list_dir(\"$di\")'>$d</a>\n";
  }
  $ret.=implode("/", $l);
  $ret.="<input type='hidden' name='dir' value='$dir'>\n";

  $ret.="<div class='filelisting'>\n";
  $ret.="<table class='filelisting' width='100%'>\n";
  $d=opendir("$upload_path/$dir");
  $odd=0;
  $list_dir=array();
  $list_file=array();
  while($x=readdir($d)) {
    if(substr($x, 0, 1)!=".") {
      if(is_dir("$upload_path/$dir/$x"))
        $list_dir[]=$x;
      elseif(eregi("\.(".implode("|", array_merge($extensions_images, $extensions_movies)).")$", $x))
        $list_file[]=$x;
    }
  }

  sort($list_dir);
  sort($list_file);

  foreach($list_dir as $x) {
    $odd=($odd+1)%2;
    $ret.="<tr class='filelisting_$odd'>";
    $ret.="<td><a class='upload_dir' href='javascript:list_dir(\"$dir/$x\")'>$x/</a></td>\n";
    $st=stat("$upload_path/$dir/$x");
    $ret.="<td>";
    //$ret.=print_r($st, 1);
    $ret.="</td>\n";
    $ret.="</tr>\n";
  }

  foreach($list_file as $x) {
    $odd=($odd+1)%2;
    $ret.="<tr class='filelisting_$odd'>";
    $ret.="<td><span class='upload_file' onClick='upload_image_mark(this)'><input type='checkbox' name='upload_file[]' value='$x' class='upload_file'>$x</span></td>\n";
    $st=stat("$upload_path/$dir/$x");
    $ret.="<td>";
    //$ret.=print_r($st, 1);
    $ret.="</td>\n";
    $ret.="</tr>\n";

  }

  $ret.="</table></div>\n";
  closedir($d);

  return $ret;
}

function replace_invalid_chars($name) {
  $res="";

  for($i=0; $i<strlen($name); $i++) {
    if(eregi("^[a-zA-Z0-9_\-\.]*$", substr($name, $i, 1)))
      $res.=substr($name, $i, 1);
    else
      $res.="_";
  }

  return $res;
}

function implode_vars($vars) {
  $ret=array();

  foreach($vars as $k=>$v) {
    $ret[]="$k=\"$v\"";
  }

  return implode(" ", $ret);
}


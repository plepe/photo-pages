<?
class SeriesChunk extends Chunk {
  var $dir;
  var $subpage;

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

    $ret.="<div class='edit_img'><img src='".url_photo($this->subpage->path, "", "index.php", "main", "main.jpg", 64, $_SESSION[img_version][$this->img])."'></div>\n";
    if($this->path!=$this->page->path)
      $ret.="<input type='hidden' name='data[LIST][$this->id][path]' value='$this->path'>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][dir]' value='$this->dir'>\n";
    $ret.="$lang_str[nav_view]: $subdata[TITLE]<br />";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='SeriesChunk'>\n";
    if($this->path!=$this->page->path)
      $ret.="<div class='edit_img_details'>$this->path/$this->dir@</div>";
    else
      $ret.="<div class='edit_img_details'>$this->dir@</div>";
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }
};



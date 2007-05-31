<?
class SubdirChunk extends Chunk {
  var $dir;
  var $subpage;
  var $page;

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

    if($this->path)
      return $this->subpage=get_page("{$this->path}/$this->dir");
    else
      return $this->subpage=get_page("$this->dir");
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

    $ret.="<div class='edit_img'><img src='".url_photo($this->subpage->path, "", "index.php", "main", "main.jpg", 64, $_SESSION[img_version][$this->img])."'></div>\n";
    if($this->path!=$this->page->path)
      $ret.="<input type='hidden' name='data[LIST][$this->id][path]' value='$this->path'>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][dir]' value='$this->dir'>\n";
    $ret.="$lang_str[nav_subdir]: $subdata[TITLE]";
    $ret.="<input type='hidden' name='data[LIST][$this->id][type]' value='SubdirChunk'>\n";
    if($this->path!=$this->page->path)
      $ret.="<div class='edit_img_details'>$this->path/$this->dir/</div>";
    else
      $ret.="<div class='edit_img_details'>$this->dir/</div>";
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



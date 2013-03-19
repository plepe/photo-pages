<?
class ImgChunk extends Chunk {
  var $text;
  var $img;
  var $comments;

  function ImgChunk($page, $text, &$i, $j) {
    $this->type="ImgChunk";
    $this->index=$i++;
    $this->id=$j++;
    $this->page=$page;
    $this->path=$this->page->path;

    if(is_array($text)) {
      $this->img=$text[img];
      $this->text=$text[text];
      if(isset($text[path]))
        $this->path=$text[path];
    }
    else {
      if(eregi("^([^ ]*) (.*)$", $text, $m)) {
        $this->img=$m[1];
        $this->text=$m[2];
      }
      else {
        $this->img=$text;
      }
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
  }

  function count_as_picture() { return 1; }

  function get_largest_path($file=0) {
    global $file_path;
    global $generated_path;
    global $orig_path;

    if(!$file)
      $file=$this->img;

    if(file_exists("$file_path/$this->path/$generated_path/$file"))
      $find_path=$generated_path;
    else
      $find_path=$orig_path;

    return $find_path;
  }

  function colspan() {
    return 1;
  }

  function is_shown() { return 1; }

  function html_class() {
    return "image";
  }

  function read_comments() {
    global $lang_str;
    global $file_path;

    if((!isset($this->comments))&&file_exists("$file_path/{$this->page->path}/comments/$this->img")) {
      $fp=fopen("$file_path/{$this->page->path}/comments/$this->img", "r");
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

    $ret ="<a href='".url_script(array("page"=>$this->page, "img"=>$this->index, "script"=>"image.php", "imgname"=>$this->img))."'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."'";
    
    #$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    $ret.="class='album_image' width='$r[0]' height='$r[1]'></a><br>";
    #$ret="<a href='image.php?img=$this->index&series=".$this->page->series.
    #     "'><img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    #     "class='album_image'></a><br>";

    if($this->text)
      $desc=strtr($this->text, array("\n"=>"<br>\n"))."<br>\n";
    call_hooks("album_desc", &$desc, $page, $this);
    $ret.=$desc;

    $this->read_comments();
    if($this->comments) foreach($this->comments as $c) {
      $ret.="<span class='$c[age_color]'>$c[name]</span>: $c[text]";
    }

    return $ret;
  }

  function get_image_details() {
    global $file_path;
    global $orig_path;

    $largest_path=$this->get_largest_path($this->img);

    $r=getimagesize("$file_path/{$this->path}/$largest_path/$this->img");
    $e=exif_read_data("$file_path/{$this->path}/$orig_path/$this->img");

    $ret["filename"]="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $largest_path, $_SESSION[img_version][$this->img])."'>".implode(" ", explode("_", $this->img))."</a>";

    //$ret["filename"]="<a href='orig/$this->img'>$this->img</a>";
    $ret["filesize"]=sprintf("%.1f kB", filesize("$file_path/{$this->path}/$largest_path/$this->img")/1024.0);
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

      // Calculate aperture
      $x=explode("/", $e['FNumber']);
      $ret["aperture"]="f/".($x[0]/$x[1]);

      $ret["iso"]=$e['ISOSpeedRatings'];
    }

    call_hooks("img_details", &$ret, $this->page, $this);

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
    }
    if($this->page->get_right($_SESSION[current_user], "editdesc")) {
      $ret1.="<input accesskey='e' class='toolbox_input' type='submit' id='toolbox_input_desc' value='$lang_str[tool_editdesc_name]' onClick='start_desc_edit()' title=\"$lang_str[tooltip_editdesc]\"><br>\n";
    }
    if($this->page->get_right($_SESSION[current_user], "addcomment")) {
      $ret1.="<form accesskey='c' action='comment.php?id=$this->id' method='post'><input class='toolbox_input' type='submit' id='toolbox_input_comment' value='$lang_str[tool_comments_name]' onClick='start_add_comment()' title=\"$lang_str[tooltip_addcomment]\"></form>\n";
    }

    add_toolbox_item("imageview_admintools", $ret1);

    $ret.=show_toolbox("imageview_admintools");
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

    $largest_path=$this->get_largest_path();

    $imgres=getimagesize("$file_path/{$this->path}/$normal_res/$this->img");

    if(!$_SESSION[img_version][$this->img])
      $_SESSION[img_version][$this->img]=0;

    $ret.="<script type='text/javascript'>\n".
          "<!--\n".
          "var img_version={$_SESSION[img_version][$this->img]};\n".
          "var img_orig=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, $largest_path, $_SESSION[img_version][$this->img])."\";\n".
          "var img_size_url=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, "%SIZE%", $_SESSION[img_version][$this->img])."\";\n".
          "var img_url=\"".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, $normal_res, $_SESSION[img_version][$this->img])."\";\n".
          "var series=\"$series\";\n".
          "var page=\"{$this->page->path}\";\n".
          "var index_id=\"{$this->index_id}\";\n".
          "var imgnum=\"$this->id\";\n";
          "var imgchunk=\"$this->index\";\n";

    $ret.="img_width={$imgres[0]};\n".
          "img_height={$imgres[1]};\n";

    $ret.="//-->\n</script>\n";

    $img_params[width]=$imgres[0];
    $img_params[height]=$imgres[1];

    call_hooks("imageview", &$img_params, $this->page, $this);

    $ret.="<div id='imageview_main'>\n";
    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $largest_path, $_SESSION[img_version][$this->img])."' target='_top'>";

//    $ret.="<a href='$orig_path/$this->img?{$_SESSION[img_version][$this->img]}' target='_top'>".
    //$ret.="<img src='$normal_res/$this->img?{$_SESSION[img_version][$this->img]}' ";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $normal_res, $_SESSION[img_version][$this->img])."' target='_top' ";
    $ret.="id='img' class='imageview_image' ".implode_vars($img_params)." onLoad='notify_img_load()'></a>\n";

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
    call_hooks("image_description", &$text, $this->page, $this);
    $ret.=$text;

    $ret.="</div>\n";
// 
    $ret.="<div class='toolbox'>\n";

    $det=$this->get_image_details();
    $ret.="<table class='imageview_image_details' width='100%'>\n";
    foreach($det as $name=>$value) {
      $ret.="<tr><td>".$lang_str["info_$name"].":</td><td>$value</td></tr>\n";
    }
    $ret.="</table></div>\n";

    $ret1=$this->toolbox();
    call_hooks("image_toolboxes", &$ret1, $this->page, $this);
    $ret.=$ret1;

    $ret.="<br style='clear: left;'>\n";

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

    $largest_path=$this->get_largest_path($this->img);

    if(!$text)
      $text=$this->text;

    $ret.="<div class='edit_img' ".
          "onMouseOver='page_edit_show_pic(this, \"$this->img\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, 600, 
                  $_SESSION[img_version][$this->img])."\", \"".
          url_photo($this->page->path, $this->page->series, 
                  "get_image.php", $this->id, $this->img, $largest_path, 
                  $_SESSION[img_version][$this->img])."\")' ".
          "onMouseOut='page_edit_leave_image(this)' ".
          "onMouseMove='page_edit_move_pic(event)' ".
	  "onClick='page_edit_photo_click(event)' ".
	  ">\n";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "get_image.php", $this->id, $this->img, 64, $_SESSION[img_version][$this->img])."' ".
          "id='chunk_{$this->id}_img'>";
    $ret.="</div>\n";
    $ret.="<input type='hidden' name='data[LIST][$this->id][img]' value='$this->img'>\n";
    if($this->path!=$this->page->path)
      $ret.="<input type='hidden' name='data[LIST][$this->id][path]' value='$this->path'>\n";
    $ret.="<textarea name='data[LIST][$this->id][text]' class='edit_input_imgchunk' onFocus='input_get_focus(this)' rows='1' onKeyUp='resize_textarea(this)' onMouseOut='page_edit_input_leave(this)'>$text</textarea>\n";
    if($this->path!=$this->page->path)
      $ret.="<div class='edit_img_details'>$this->path/$this->img</div>";
    else
      $ret.="<div class='edit_img_details'>$this->img</div>";
    $ret.="<input type='button' value='edit' onClick='page_edit_edit_img($this->id)'>\n";
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

  function save_data($data) {
    global $file_path;
    global $orig_path;
    global $generated_path;
    $save="";

    $save.=$this->img;
    if($this->text) {
      if(strpos($this->text, "\n")===false)
        $save.=" $this->text";
      else
        $save.=" \"\"\"$this->text\"\"\"";
    }

    $image_modify=0;
    call_hooks("image_modify_save_request", &$image_modify, $this, $data);

    if($image_modify) {
      $filename="$file_path/$this->path/$orig_path/$this->img";
      call_hooks("image_modify_save_start", &$filename, $this);
      rename("$filename", "$file_path/$this->path/$generated_path/$this->img");
      scale_img($this->path, $this->img, 1);
      $_SESSION[img_version][$this->img]++;
    }

    return $save;
  }

};

register_chunk_type(ImgChunk, "ImgChunk", 
  "^[^ ]*\.(".implode("|", $extensions_images).")");

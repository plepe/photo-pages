<?
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
    global $lang_str;

    $r=getimagesize("$file_path/{$this->path}/$index_res/$this->img");

    $ret ="<a href='".url_script($this->page->path, $this->page->series, "image.php", $this->index)."'>";
    $ret.="<img src='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->img, $index_res, $_SESSION[img_version][$this->img])."'";
    
    #$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    $ret.="class='album_image' width='$r[0]' height='$r[1]'></a><br>";
    #$ret="<a href='image.php?img=$this->index&series=".$this->page->series.
    #     "'><img src='$index_res/$this->img?{$_SESSION[img_version][$this->img]}' ".
    #     "class='album_image'></a><br>";

    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->mov, "movie", $_SESSION[img_version][$this->img])."'>$lang_str[moviechunk_download]</a><br>\n";

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
    $_SESSION["tmp_$tmp_id"]["size"]="flv";
    session_register("tmp_$tmp_id");

    $url=url_script(array("script"=>"get_image.php", "tmp_id"=>$tmp_id)); //"$web_path/get_image.php?tmp_id=$tmp_id";
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

    $ret.="<a href='".url_photo($this->page->path, $this->page->series, "image.php", $this->id, $this->mov, "movie", $_SESSION[img_version][$this->img])."'>$lang_str[moviechunk_download]</a><br>\n";

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



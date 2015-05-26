<?
class HeaderChunk extends Chunk {
  var $level;
  var $text;

  function HeaderChunk($page, $text, &$i, &$j) {
    $this->type="HeaderChunk";
    $this->index=$i;
    $this->id=$j++;
    $this->page=$page;
    
    if(is_array($text)) {
      $this->text=$text[text];
      $this->level=$text[level];
      if(!$this->level)
        $this->level=1;
    }
    else {
      if(eregi("^index_[^ ]* (.*)$", $text, $m)) {
        $this->level=1;
        $this->text=$m[1];
      }
      elseif(eregi("^(=+)([^=].*[^=])(=+)$", $text, $m)) {
        $this->text=$m[2];
        $this->level=strlen($m[1]);
      }
    }
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
    $ret.="<br style='clear: left;'>\n";

    return $ret;
  }

  function export_album() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function export_imageview() {
    return strtr($this->text, array("\n"=>"<br>\n"));
  }

  function save_data($data) {
    $save="";

    $save.=str_repeat("=", $this->level).
           "$this->text".
           str_repeat("=", $this->level);

    return $save;
  }
};

register_chunk_type(HeaderChunk, "HeaderChunk", "^=");

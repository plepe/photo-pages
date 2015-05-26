<?
class TextChunk extends Chunk {
  var $text;

  function TextChunk($page, $text, &$i, &$j) {
    $this->type="TextChunk";
    $this->index=$i;
    $this->id=$j++;
    $this->page=$page;

    if(is_array($text)) {
      $this->text=$text[text];
    }
    else {
      if(ereg("^\"(.*)\"$", $text, $m))
        $this->text=$m[1];
      else
        $this->text=$text;
    }
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

    if($this->text) {
      if(strpos($this->text, "\n")===false)
        $save.=$this->text;
      else
        $save.="\"\"\"$this->text\"\"\"";
    }

    return $save;
  }
};

register_chunk_type(TextChunk, "TextChunk", 0);

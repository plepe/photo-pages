<?
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



<?
function search_create_field(&$list, $page) {
  global $lang_str;
  $search_query=$_REQUEST[search_query];

  $ret ="<form method='get'>\n";
  $ret.="<input type='hidden' name='page' value='$page->path'>\n";
  $ret.="<input type='hidden' name='series' value='$page->series'>\n";
  $ret.="$lang_str[search_name]: <input name='search_query' value='$search_query' class='toolbox_input search_field'></form>\n";
  add_toolbox_item("album_toolbox", $ret);

  if($search_query) {
    $new_list=array();
    foreach($list as $l) {
      if(stristr($l->text, $search_query)!==false)
        $new_list[]=$l;
      else {
        $found=0;
        call_hooks("search", $found, $page, $l);
        if($found)
          $new_list[]=$l;
      }
    }

    $list=$new_list;
  }
}

register_hook("album_modify_list", search_create_field);

if($_REQUEST[search_query])
  add_all_urls("search_query", $_REQUEST[search_query]);

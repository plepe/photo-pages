<?
function category_modify(&$data, $page) {
  if(eregi("^category:(.*)$", $page->path, $m)) {
  }

  if($data[CATEGORIES])
    $data[CATEGORIES]=explode(":", $data[CATEGORIES]);
  else
    $data[CATEGORIES]=array();
}

function category_save_page($data, $page) {
  global $file_path;
  $not_new_cat=array();

  if($data[CATEGORIES])
    $data[CATEGORIES]=explode(":", $data[CATEGORIES]);
  else
    $data[CATEGORIES]=array();

  foreach($page->cfg[CATEGORIES] as $c) {
    $content="";
    $f=fopen("{$file_path}/category:{$c}/fotocfg.txt", "r");
    while($r=fgets($f)) {
      $r=chop($r);
      if($r=="{$page->path}/") {
	if(in_array($page->path, $data[CATEGORIES])) {
	  array_push($not_new_cat, $c);
	  $content.="$r\n";
	}
      }
      else
	$content.="$r\n";
    }
    fclose($f);

    $f=fopen("{$file_path}/category:{$c}/fotocfg.txt", "w");
    fputs($f, $content);
    fclose($f);
  }

   foreach(array_diff($data[CATEGORIES], $not_new_cat) as $c) {
    print "$c\n";
    if(!is_dir("$file_path/category:$c")) {
      @mkdir("$file_path/category:$c");
      $f=fopen("{$file_path}/category:{$c}/fotocfg.txt", "w");
      fputs($f, "TITLE Category:{$c}\n\n");
      fclose($f);
    }

    $f=fopen("{$file_path}/category:{$c}/fotocfg.txt", "a");
    fputs($f, "/{$page->path}/\n");
    fclose($f);
  }
}

function category_form(&$text, $page, $data) {
  global $categories;

  if(!$data["CATEGORIES"])
    $data["CATEGORIES"]=array();

  $text.="<tr><td>Categories:</td>\n";
  $text.="<td><input class='page_edit_input' name='data[CATEGORIES]' value=\"".implode(":", $data["CATEGORIES"])."\"></td>\n";
  $text.="<td>:-separated list of categories</td>\n";
  $text.="</tr>\n";
}

function category_heading(&$text, $page) {
  if(sizeof($page->cfg["CATEGORIES"])) {
    $text.="Categories: ";

    $list=array();
    foreach($page->cfg["CATEGORIES"] as $c) {
      $list[]="<a href='".url_page(array("page"=>"category:$c"))."'>$c</a>";
    }
    $text.=implode(", ", $list);
  }
}

register_hook("load", category_modify);
register_hook("album_heading", category_heading);
register_hook("save_page_start", category_save_page);
register_hook("page_edit_main_form", category_form);

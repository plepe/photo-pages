<?

$rotate_data=array();

function rotate_hook_modify_request($modify, $image) {
  global $rotate_data;

  if($_REQUEST[rotate]!=$rotate_data[$image]) {
    $modify=1;
  }
}

function rotate_hook_modify_save_request($modify, $image, $data) {
  global $rotate_data;

  if($data[rotate]!=$rotate_data[$image->img]) {
    $modify=1;
    $rotate_data[$image->img]=$data[rotate];
  }
}

function rotate_hook_modify($filename, $image) {
  if($image->type=="ImgChunk") {
    system("convert -rotate $_REQUEST[rotate] -background \"#333333\" -resize $_REQUEST[size]x$_REQUEST[size] $filename /tmp/bla.jpg");
    $filename="/tmp/bla.jpg";
  }
}

function rotate_hook_save_modify($filename, $image) {
  global $rotate_data;

  if($image->type=="ImgChunk") {
    system("convert -rotate {$rotate_data[$image->img]} -background \"#333333\" $filename /tmp/bla.jpg");
    $filename="/tmp/bla.jpg";
  }
}

function rotate_save_page($data, $page) {
  global $file_path;
  global $orig_path;
  global $generated_path;
  global $rotate_data;

  @mkdir("$file_path/$page->path/$generated_path");
//  foreach($data["LIST"] as $id=>$d) {
////    print $id." "; print_r($d);
//    if($d[rotate]!=$rotate_data[$d[img]]) {
//      //system("convert -rotate $d[rotate] $file_path/$page->path/$orig_path/$d[img] $file_path/$page->path/$generated_path/$d[img]");
//      $rotate_data[$d[img]]=$d[rotate];
//    }
//  }

  $f=fopen("$file_path/$page->path/$generated_path/rotate.data", "w");
  foreach($rotate_data as $img=>$rot) {
    fputs($f, "$img\t$rot\n");
  }
  fclose($f);
}

function rotate_load_page($cfg, $page) {
  global $file_path;
  global $generated_path;
  global $rotate_data;

  if(!file_exists("$file_path/$page->path/$generated_path/rotate.data"))
    return;

  $f=fopen("$file_path/$page->path/$generated_path/rotate.data", "r");
  while($r=fgets($f)) {
    $r=chop($r);
    $e=explode("\t", $r);
    $rotate_data[$e[0]]=$e[1];
  }
  fclose($f);
}

function rotate_show_chunk($text, $page, $img) {
  global $rotate_data;

  if($img->type=="ImgChunk") {
    if($rotate_data[$img->img])
      $text.="<input type='hidden' name='data[LIST][$img->id][rotate]' value='{$rotate_data[$img->img]}'>\n";
  }
}

global $extensions_page;
if($extensions_page=="get_image") {
  register_hook("image_modify_request", rotate_hook_modify_request);
  register_hook("image_modify_start", rotate_hook_modify);
}
if($extensions_page=="page_edit") {
  register_hook("image_modify_save_request", rotate_hook_modify_save_request);
  register_hook("image_modify_save_start", rotate_hook_save_modify);
  register_hook("save_page_end", rotate_save_page);
  register_hook("load", rotate_load_page);
  register_hook("page_edit_show_chunk", rotate_show_chunk);
}

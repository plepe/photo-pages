<?
$extensions_data=array();

function set_extension_views($ext, $views) {
  global $extensions_data;

  $extensions_data[$ext]=array("views"=>$views);
}

function include_extensions($view) {
  global $extensions_data;
  global $extensions;

  foreach($extensions as $ext) {
    include "extensions/{$ext}_data.php";
  }

  foreach($extensions as $ext) {
    if(($extensions_data[$ext]["views"]==0)||
       (in_array($view, $extensions_data[$ext]["views"]))) {
      @include "extensions/{$ext}_lang.php";
      @include "extensions/{$ext}.php";
      if(file_exists("extensions/{$ext}.js"))
        use_javascript("extensions/{$ext}");
      if(file_exists("extensions/{$ext}.css"))
        use_css("extensions/{$ext}");
    }
  }
}

function include_extension($ext, $before=0) {
  global $extensions;

  if(!in_array($ext, $extensions))
    $extensions[]=$ext;
}

function set_extension_description($ext, $name, $text) {
}

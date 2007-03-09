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
    if(in_array($view, $extensions_data[$ext]["views"])) {
      include "extensions/{$ext}_lang.php";
      include "extensions/{$ext}.php";
      if(file_exists("extensions/{$ext}.js"))
        use_javascript("extensions/{$ext}");
      if(file_exists("extensions/{$ext}.css"))
        use_css("extensions/{$ext}");
    }
  }
}


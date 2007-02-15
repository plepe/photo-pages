<?
global $lang_str;

function fullscreen_calculate($vars) {
  if($_SESSION[window_width]&&$_SESSION[fullscreen_mode]) {
    $w=$_SESSION[window_width];
    $h=$_SESSION[window_height];
    $ratio=$vars[width]/$vars[height];

    if($w/$ratio>$h) {
      $vars[height]=$h;
      $vars[width]=$vars[height]*$ratio;
    }
    else {
      $vars[width]=$w;
      $vars[height]=$vars[width]/$ratio;
    }
  }

  return $vars;
}

add_toolbox_item("toolbox", "<input accesskey='f' class='".
      ($_SESSION[fullscreen_mode]?"toolbox_input_active":"toolbox_input").
      "' type='submit' id='toolbox_input_fullscreen' value='$lang_str[tool_fullscreen_name]' onClick='set_fullscreen()' title=\"$lang_str[tooltip_fullscreen]\"><br>\n");

html_export_var(array("fullscreen_mode"=>$_SESSION[fullscreen_mode]));
add_hook("imageview", fullscreen_calculate);

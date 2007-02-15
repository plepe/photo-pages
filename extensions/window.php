<?

session_register("window_width");
session_register("window_height");
html_export_var(array("window_width"=>$_SESSION[window_width], "window_height"=>$_SESSION[window_height]));

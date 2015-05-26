<?
function magnify_export_size(&$text, $page, $img) {
  global $file_path;

  $large_path=$img->get_largest_path();
  $x=getimagesize("$file_path/$img->path/$large_path/$img->img");
  html_export_var(array("mag_sizex"=>$x[0]));
}

register_hook("image_description", magnify_export_size);

add_toolbox_item("imageview_toolbox", "<input accesskey='m' type='submit' class='toolbox_input' id='toolbox_input_mag' onClick='start_mag()' value='$lang_str[tool_magnify_name]' title=\"$lang_str[tooltip_mag]\"><br>\n");

<?
@mkdir("$file_path/$page->path/details");
$f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "a");
fwrite($f, "$_REQUEST[x]:$_REQUEST[y]:$_REQUEST[desc]\n");
fclose($f);


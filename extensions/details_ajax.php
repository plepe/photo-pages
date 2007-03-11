<?
if($_REQUEST[todo]=="remove") {
  $newcontent="";
  $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "r");
  $i=0;
  while($r=fgets($f)) {
    if($i==$_REQUEST[detail_nr]) {
    }
    else {
      $newcontent.=$r;
    }
    $i++;
  }
  fclose($f);

  $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "w");
  fwrite($f, $newcontent);
  fclose($f);
}
elseif($_REQUEST[detail_nr]=="new") {
  @mkdir("$file_path/$page->path/details");
  $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "a");
  fwrite($f, "$_REQUEST[x]:$_REQUEST[y]:$_REQUEST[desc]\n");
  fclose($f);
}
else {
  $newcontent="";
  $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "r");
  $i=0;
  while($r=fgets($f)) {
    if($i==$_REQUEST[detail_nr]) {
      $newcontent.="$_REQUEST[x]:$_REQUEST[y]:$_REQUEST[desc]\n";
    }
    else {
      $newcontent.=$r;
    }
    $i++;
  }
  fclose($f);

  $f=fopen("$file_path/$page->path/details/{$page->cfg["LIST"][$_REQUEST[img]]->img}", "w");
  fwrite($f, $newcontent);
  fclose($f);
}


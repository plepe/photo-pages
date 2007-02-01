function new_page_new_list(xmldata) {
  var i;
  var a="";
  var a=ajax_read_value(xmldata, "dir");

  var ob=document.getElementById("dir_list");
  ob.innerHTML=a;
}

function list_dir(dir) {
  start_xmlreq("toolbox.php?todo=read_upload_dir&dir="+dir, "", new_page_new_list);
}

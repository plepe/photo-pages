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

function upload_image_mark(ob) {
  var check;

  if(ob.className=="upload_file_marked") {
    ob.className="upload_file";
    check=false;
  }
  else {
    ob.className="upload_file_marked";
    check=true;
  }

  var inputs=ob.getElementsByTagName("input");
  for(i=0;i<inputs.length;i++) {
    inputs[i].checked=check;
  }
}

function upload_image_mark_all() {
  var obs=document.getElementsByTagName("span");
  var i;

  for(i=0; i<obs.length; i++) {
    if(obs[i].className=="upload_file") {
      obs[i].className="upload_file_marked";

      var inputs=obs[i].getElementsByTagName("input");
      var j;
      for(j=0;j<inputs.length;j++) {
        inputs[j].checked=true;
      }
    }
  }
}

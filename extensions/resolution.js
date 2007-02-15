function set_normal_res() {
  var res=document.getElementById("resolution_choice");
  res=res.value;
  
  var img=document.getElementById('img');
  img.src=img_size_url.replace(/%SIZE%/, res);
  //res +"/" + imgurl + "?" + img_version;

//  var el=document.getElementById("res_" + cur_res);
//  el.className="toolbox_input";
//
//  var el=document.getElementById("res_" + res);
//  el.className="toolbox_input_active";

  normal_res=res;

  set_session_vars({ normal_res: res });
}



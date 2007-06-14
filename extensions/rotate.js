var rotate_value;
var rotate_show_timeout;

function rotate_img_done() {
  var el=document.getElementById("rotate_value");
  el.value=rotate_value;
}

function rotate_img_start() {
  var img=document.getElementById("img");
  clearTimeout(rotate_show_timeout);
  img.src=url_photo({ "page": page, "series": series, "script": "get_image.php", "img": page_edit_id, "imgname": "bla.jpg", "size": 600, "version": 0, "rotate": rotate_value });
}

function rotate_img(rot) {
  rotate_value+=rot;
  clearTimeout(rotate_show_timeout);
  rotate_show_timeout=window.setTimeout("rotate_img_start()", 100);
}

function rotate_img_data() {
  var el=document.getElementById("rotate_value");
  rotate_value=parseFloat(el.value);
  rotate_img_start();
}

function rotate_toolbox() {
  var el=document.getElementsByName("data[LIST]["+page_edit_id+"][rotate]");
  if(el.length>0)
    rotate_value=parseFloat(el[0].value);
  else
    rotate_value=0;

  add_toolbox_item("page_edit_edit_toolbox",
    "Rotate: <input id='rotate_value' name='data[LIST]["+page_edit_id+"][rotate]' value='"+rotate_value+"' size='4' onBlur='rotate_img_data()'>&deg;<br>"+
    "<input type='button' onClick='rotate_img(-90)' value='90&deg;'>"+
    "<input type='button' onClick='rotate_img(-1)'  value='1&deg;'>"+
    "&#x21BA; "+
    "&#x21BB;"+
    "<input type='button' onClick='rotate_img(1)'   value='1&deg;'>"+
    "<input type='button' onClick='rotate_img(90)'  value='90&deg;'><br>\n");
}

function rotate_ready() {
  var img=document.getElementById("img");
  register_event(img, "load", rotate_img_done);
}

function rotate_save(params) {
  params["rotate"]=rotate_value;
}

function rotate_init_fun() {
  if(extensions_mode=="page_edit") {
    register_hook("page_edit_edit_toolbox", rotate_toolbox);
    register_hook("page_edit_edit_ready", rotate_ready);
    register_hook("page_edit_edit_get_add_params", rotate_save);
  }
}

register_initfun(rotate_init_fun);

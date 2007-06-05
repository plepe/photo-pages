function description_show(ret, id) {
  var input_name="data[LIST]["+id+"][text]";
  ret.text+="<textarea name='"+input_name+"'>";
  ret.text+=document.getElementsByName(input_name)[0].value;
  ret.text+="</textarea>\n";
}

function description_init_fun() {
  if(extensions_mode=="page_edit") {
    register_hook("page_edit_image_description", description_show);
  }
}

register_initfun(description_init_fun);

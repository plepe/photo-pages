function ImgChunk_edit_img(ret, id) {
  var url={ "page": page, "series": series, "script": "get_image.php", "img": page_edit_id, "imgname": "bla.jpg", "size": 600, "version": 0 };
  for(var key in page_edit_add_params[page_edit_id]) {
    url[key]=page_edit_add_params[page_edit_id][key];
  }
  ret.text+="<div><img id='img' src='"+url_photo(url)+"'></div>";
  call_hooks("page_edit_image_description", ret, id, "ImgChunk");
}

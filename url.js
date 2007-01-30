function url_page(path, series, skript) {
}

function url_photo(path, series, skript, imgnum, imgname, size, imgversion) {
}

function url_script(path, series, skript, imgnum) {
  ret=v_url_script;

  ret=ret.replace("%1$s", path);
  ret=ret.replace("%2$s", series);
  ret=ret.replace("%3$s", skript);
  ret=ret.replace("%4$s", imgnum);

  return ret;
}

function url_javascript(path, series, skript, imgnum) {
}

function url_img(imgfile) {
}

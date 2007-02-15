function window_resize() {
  var img=document.getElementById("img");
  var p=get_abs_pos(img);

  var w=window.innerWidth-p[0]*2;
  var h=window.innerHeight-p[1]-40;

  if((w!=window_width)||(h!=window_height)) {
    set_session_vars({ window_width: window_width, window_height: window_height });
  }
}

function window_init() {
  register_hook(window, "onresize", window_resize);
  window_resize();
}

register_initfun(window_init);

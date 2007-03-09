function set_fullscreen() {
  var ob=document.getElementById("toolbox_input_fullscreen");
  if(fullscreen_mode) {
    fullscreen_mode=null;
    ob.className="toolbox_input";
  }
  else {
    fullscreen_mode=1;
    ob.className="toolbox_input_active";
  }

  fullscreen_reshape();

  set_session_vars({ fullscreen_mode: fullscreen_mode });
}

function fullscreen_reshape() {
  var img=document.getElementById("img");

  var width=img.naturalWidth;
  var height=img.naturalHeight;

  if(window_width&&fullscreen_mode) {
    var w=window_width;
    var h=window_height;
    var ratio=width/height;

    if(w/ratio>h) {
      height=h;
      width=height*ratio;
    }
    else {
      width=w;
      height=width/ratio;
    }
  }

  img.width=width;
  img.height=height;
}

function fullscreen_imgload() {
  fullscreen_reshape();
}

function fullscreen_init() {
  register_event("img", "load", fullscreen_imgload);
  register_event(window, "resize", fullscreen_imgload);
}

register_initfun(fullscreen_init);

var slideshow_timeout;

function slideshow_next() {
  location.href=url_script({ page: page, series: series, img: parseInt(imgchunk)+1, script: "image.php" });
}

function slideshow_init() {
  if(slideshow_active)
    slideshow_timeout=window.setTimeout("slideshow_next()", slideshow_time*1000);
}

function set_slideshow_time() {
  var ob1=document.getElementById("slideshow_time");

  slideshow_time=parseInt(ob1.value);

  set_session_vars({ slideshow_active: slideshow_active, slideshow_time: slideshow_time });
  if(slideshow_active) {
    clearTimeout(slideshow_timeout);
    slideshow_timeout=window.setTimeout("slideshow_next()", slideshow_time*1000);
  }
}

function toggle_slideshow() {
  clearTimeout(slideshow_timeout);

  var ob1=document.getElementById("slideshow_button");

  if(slideshow_active)
    slideshow_active=null;
  else {
    slideshow_active=1;
    slideshow_timeout=window.setTimeout("slideshow_next()", slideshow_time*1000);
  }

  ob1.className=slideshow_active?"toolbox_input_active":"toolbox_input";

  set_session_vars({ slideshow_active: slideshow_active, slideshow_time: slideshow_time });
}

register_initfun(slideshow_init);

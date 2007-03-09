var details_text=null;
var details_choose;
var details_new_pos;
var details_new_enter;
var details_timeout;
var details=new Array();
var details_size;

function details_hide() {
  if(details_text) {
    details_text.parentNode.removeChild(details_text);
    details_text=null;
  }
}

function details_show() {
  var img=document.getElementById("img");
  if(details_text)
    details_hide();

  d=this.getAttribute("details_id");
  var ob=document.createElement("div");
  details_text=ob;
  ob.style.position="absolute";
  p=get_abs_pos(img);
  ob.style.left=(p[0]+(img.offsetWidth*details[d].x/1000)+details_size/2)+"px";
  ob.style.top=(p[1]+(img.offsetHeight*details[d].y/1000)+details_size/2)+"px";
  ob.className="details_desc";
  img.parentNode.appendChild(ob);
  ob.innerHTML=details[d].desc;
}

function details_show_markers() {
  var img=document.getElementById("img");

  if(details.length>0)
    if(details[0].ob)
      return;

  for(var d in details) {
    var ob=document.createElement("img");
    if(img.height>img.width)
      details_size=img.height;
    else
      details_size=img.width;
    details_size=details_size/20;

    ob.src=url_script({ script: "extensions/details_kasterl.php", page: page, series: series, img: imgchunk, size: details_size});
    ob.style.position="absolute";
    p=get_abs_pos(img);
    ob.style.left=(p[0]+(img.offsetWidth*details[d].x/1000)-details_size/2)+"px";
    ob.style.top=(p[1]+(img.offsetHeight*details[d].y/1000)-details_size/2)+"px";
    ob.className="details_marker";
    ob.style.background="none";
    details[d].ob=ob;
    details[d].ob.setAttribute("details_id", d);
    img.parentNode.appendChild(ob);
    details[d].ob.onmouseover=details_show;
    details[d].ob.onmouseout =details_hide;
  }
}

function details_hide_markers_call() {
  clearTimeout(details_timeout);
  details_timeout=window.setTimeout("details_hide_markers()", 100);
}

function details_hide_markers(force) {
  if(details_text)
    return;

  for(var d in details) {
    if(details[d].ob) {
      details[d].ob.parentNode.removeChild(details[d].ob);
      details[d].ob=null;
    }
  }
}

function details_show_markers_call() {
  details_show_markers();

  clearTimeout(details_timeout);
  details_timeout=window.setTimeout("details_hide_markers()", 1000);
}

function details_init() {
  var img=document.getElementById("img");

  register_event(img.parentNode, "mouseover", details_show_markers);
  register_event(img.parentNode, "mouseout", details_hide_markers_call);
  register_event(img.parentNode, "mousemove", details_show_markers_call);
}

function details_choosepos_move(event) {
  details_choose.style.left=(event.clientX-details_size/2)+"px";
  details_choose.style.top=(event.clientY-details_size/2)+"px";
}

  //details_choose.parentNode.removeChild(details_choose);

function details_choose_saved() {
  var el;

  if(el=document.getElementById("details_button"))
    el.className='toolbox_input';
}

function details_choose_enter() {
  var ob=document.getElementsByName("details_desc");
  details_new_pos["desc"]=ob[0].value;

  start_xmlreq(url_script({script: "ajax.php", extension: "details", page: page, img: imgchunk, x: details_new_pos.x.toFixed(0), y: details_new_pos.y.toFixed(0), desc: details_new_pos.desc}), 0, details_choose_saved);

  details.push(details_new_pos);
  details_new_enter.parentNode.removeChild(details_new_enter);
  details_choose.parentNode.removeChild(details_choose);
}

function details_choosepos_click(event) {
  var img=document.getElementById("img");

  unregister_event(img.parentNode, "mousemove", details_choosepos_move);
  unregister_event(img.parentNode, "click", details_choosepos_click);

  p=get_abs_pos(img);

  details_new_pos=
    { x: (event.clientX-p[0])/img.offsetWidth*1000,
      y: (event.clientY-p[1])/img.offsetHeight*1000 };

  details_new_enter=document.createElement("div");
  details_new_enter.className="details_new_form";
  details_new_enter.style.left=(p[0]+(img.offsetWidth*details_new_pos.x/1000)+details_size/2)+"px";
  details_new_enter.style.top=(p[1]+(img.offsetHeight*details_new_pos.y/1000)+details_size/2)+"px";

  details_new_enter.innerHTML="<form action='javascript:details_choose_enter()'><input name='details_desc' value=''></form>\n";
  img.parentNode.parentNode.appendChild(details_new_enter);

  var ob=document.getElementsByName("details_desc");
  ob[0].focus();

  return false;
}

function details_choosepos(event) {
  var img=document.getElementById("img");
  var el;

  var ob=document.createElement("img");
  if(img.height>img.width)
    details_size=img.height;
  else
    details_size=img.width;
  details_size=details_size/20;

  ob.src=url_script({ script: "extensions/details_kasterl.php", page: page, series: series, img: imgchunk, size: details_size});

  ob.style.position="absolute";
  p=get_abs_pos(img);
  ob.style.left="0px";
  ob.style.top="0px";
  ob.className="details_choose";
  img.parentNode.appendChild(ob);
  details_choose=ob;
  //details_choosepos_move(event);
  register_event(img.parentNode, "mousemove", details_choosepos_move);
  register_event(img.parentNode, "click", details_choosepos_click);

  if(el=document.getElementById("details_button"))
    el.className='toolbox_input_active';
}

register_initfun(details_init);
details=new Array(2);
//details[0]= { x: 500, y: 500, desc: "Test" };
//details[1]= { x: 200, y: 500, desc: "Test 1" };

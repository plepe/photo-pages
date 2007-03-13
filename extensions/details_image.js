var details_text=null;
var details_choose;
var details_new_pos;
var details_new_enter;
var details_timeout;
var details_desc;
var details_size;
var details_changed=0;

function details_move_start(event) {
  var img=document.getElementById("img");

  register_event(img.parentNode, "mousemove", details_choosepos_move);
  details_choose=this;

  return false;
}

function details_move_end(event) {
  if(details_choose.getAttribute("moved")!=1)
    return true;

  details_choose.setAttribute("moved", 0);

  var img=document.getElementById("img");
  var d=parseInt(details_text.getAttribute("detail"));

  unregister_event(img.parentNode, "mousemove", details_choosepos_move);

  details_new_pos=
    { x: ((event.clientX-p[0])/img.offsetWidth*1000).toFixed(0),
      y: ((event.clientY-p[1])/img.offsetHeight*1000).toFixed(0),
      detail_nr: d,
      desc: details_desc[d].desc,
      ob: details_desc[d].ob };

  if((details_new_pos.x>1000)||(details_new_pos.y>1000)) {
    details_remove(d);
  }
  else {
    details_save(details_new_pos);
  }

  details_hide();

  return false;
}

function details_click() {
  if(!details_text)
    return false;

  var d=parseInt(details_text.getAttribute("detail"));
  details_new_enter=details_text;
  details_text.innerHTML="<form action='javascript:details_change_enter()'><input name='details_desc' value='"+details_desc[d].desc+"'></form>\n";
  details_new_pos=details_desc[d];
  details_new_pos.detail_nr=d;

  var ob=document.getElementsByName("details_desc");
  ob[0].focus();

  details_desc[d].ob.onmouseout =null;
  return false;
}

function details_show_single_marker(d) {
  if(details_desc[d].ob)
    return;

  var img=document.getElementById("img");

  var ob=document.createElement("img");
  if(img.height>img.width)
    details_size=img.height;
  else
    details_size=img.width;
  details_size=details_size/20;

  ob.src=url_script({ script: "extensions/details_kasterl.php", page: page, series: series, size: details_size});
  ob.style.position="absolute";
  p=get_abs_pos(img);
  ob.style.left=(p[0]+(img.offsetWidth*details_desc[d].x/1000)-details_size/2)+"px";
  ob.style.top=(p[1]+(img.offsetHeight*details_desc[d].y/1000)-details_size/2)+"px";
  ob.className="details_marker";
  ob.style.background="none";
  details_desc[d].ob=ob;
  details_desc[d].ob.setAttribute("details_id", d);
  img.parentNode.appendChild(ob);
  details_desc[d].ob.onmouseover=details_show;
  details_desc[d].ob.onmouseout =details_hide;
  details_desc[d].ob.onmousedown=details_move_start;
  details_desc[d].ob.onmouseup  =details_move_end;
  details_desc[d].ob.onclick    =details_click;
}

function details_show_single(d) {
  details_hide_markers();
  details_show_single_marker(d);
}

function details_hide_single(d) {
  if(details_desc[d].ob) {
    details_desc[d].ob.parentNode.removeChild(details_desc[d].ob);
    details_desc[d].ob=null;
  }
}

function details_hide() {
  if(details_text) {
    details_text.parentNode.removeChild(details_text);
    details_text=null;
  }
}

function details_show_desc(d) {
  var img=document.getElementById("img");

  var ob=document.createElement("div");
  details_text=ob;
  ob.style.position="absolute";
  p=get_abs_pos(img);
  ob.style.left=(p[0]+(img.offsetWidth*details_desc[d].x/1000)+details_size/2)+"px";
  ob.style.top=(p[1]+(img.offsetHeight*details_desc[d].y/1000)+details_size/2)+"px";
  ob.className="details_desc";
  img.parentNode.parentNode.appendChild(ob);
  ob.innerHTML=details_desc[d].desc;
  details_text.setAttribute("detail", d);
}

function details_show() {
  if(details_text)
    details_hide();

  d=this.getAttribute("details_id");
  details_show_desc(d);
}

function details_show_markers() {
  var img=document.getElementById("img");

  if(details_desc.length>0)
    if(details_desc[0].ob)
      return;

  for(var d in details_desc) {
    details_show_single_marker(d);
  }
}

function details_hide_markers_call() {
  clearTimeout(details_timeout);
  details_timeout=window.setTimeout("details_hide_markers()", 100);
}

function details_hide_markers(force) {
  if(details_text&&(!force))
    return;

  clearTimeout(details_timeout);
  for(var d in details_desc) {
    details_hide_single(d);
  }
}

function details_show_markers_call() {
  details_show_markers();

  clearTimeout(details_timeout);
  details_timeout=window.setTimeout("details_hide_markers()", 1000);
}

function details_leave_page() {
  if(details_changed) {
    if(confirm("Details wurden veraendert. Speichern?")) {
      var ret="";
      for(var d in details_desc) {
        ret+=details_desc[d].x+":"+details_desc[d].y+":"+details_desc[d].desc+"\n";
      }

      start_xmlreq(url_script({script: "ajax.php", extension: "details_image", page: page, img: imgnum, data: ret }), 0, 0, true);
      details_changed=0;
      return false;
    }
  }
}

function details_init() {
  var img=document.getElementById("img");

  register_event(img.parentNode, "mouseover", details_show_markers);
  register_event(img.parentNode, "mouseout", details_hide_markers_call);
  register_event(img.parentNode, "mousemove", details_show_markers_call);
  register_event(window, "unload", details_leave_page);
}

function details_choosepos_move(event) {
  if(!details_choose)
    return;
  details_choose.setAttribute("moved", 1);
  details_choose.style.left=(event.clientX-details_size/2)+"px";
  details_choose.style.top=(event.clientY-details_size/2)+"px";
}

  //details_choose.parentNode.removeChild(details_choose);

function details_choose_saved() {
  var el;

  if(el=document.getElementById("details_button"))
    el.className='toolbox_input';
}

function details_change_enter() {
  var ob=document.getElementsByName("details_desc");
  details_new_pos["desc"]=ob[0].value;

  details_save(details_new_pos);

  details_new_enter.parentNode.removeChild(details_new_enter);

  details_choose=null;
  if(details_text) {
    details_text=null;
  }
}

function details_choose_enter() {
  var ob=document.getElementsByName("details_desc");
  details_new_pos["desc"]=ob[0].value;

  details_save(details_new_pos);

  details_new_enter.parentNode.removeChild(details_new_enter);

  if(details_choose) {
    details_choose.parentNode.removeChild(details_choose);
    details_choose=null;
  }
  if(details_text) {
    details_text=null;
  }
}

function details_remove(d) {
  details_hide_markers(1);
  //start_xmlreq(url_script({script: "ajax.php", extension: "details", todo: "remove", page: page, img: imgchunk, detail_nr: details_new_pos.detail_nr }), 0, details_choose_saved);
  details_changed=1;
  details_desc=details_desc.slice(0, d).concat(details_desc.slice(d+1));

  var t=document.getElementById("detail_list");
  t.innerHTML="Details: "; // TODO: lang_str

  for(var detail_nr in details_desc) {
    if(detail_nr!=0)
      t.innerHTML=t.innerHTML+", ";

    t.innerHTML=t.innerHTML+"<span onMouseOver='details_show_single("+detail_nr+")' onMouseOut='details_hide_single("+detail_nr+")' id='detail_"+detail_nr+"'>"+details_desc[detail_nr].desc+"</span>";
  }

  details_choose_saved();
}

function details_save(details_new_pos) {
  //start_xmlreq(url_script({script: "ajax.php", extension: "details", todo: "update", page: page, img: imgchunk, x: details_new_pos.x, y: details_new_pos.y, desc: details_new_pos.desc, detail_nr: details_new_pos.detail_nr }), 0, details_choose_saved);
  details_changed=1;

  if(details_new_pos.detail_nr!="new") {
    details_desc[details_new_pos.detail_nr]=details_new_pos;

    var t=document.getElementById("detail_"+details_new_pos.detail_nr);
    t.innerHTML=details_new_pos.desc;
  }
  else {
    var detail_nr;
    var t=document.getElementById("detail_list");

    if(!details_desc.length) {
      details_desc=new Array(details_new_pos);
      detail_nr=0;
      t.innerHTML=t.innerHTML+"Details: "; // TODO: lang_str
    }
    else {
      detail_nr=details_desc.length;
      details_desc.push(details_new_pos);
      t.innerHTML=t.innerHTML+", ";
    }

    t.innerHTML=t.innerHTML+"<span onMouseOver='details_show_single("+detail_nr+")' onMouseOut='details_hide_single("+detail_nr+")' id='detail_"+detail_nr+"'>"+details_new_pos.desc+"</span>";

  }

  details_choose_saved();
}

function details_choosepos_click(event) {
  var img=document.getElementById("img");

  unregister_event(img.parentNode, "mousemove", details_choosepos_move);
  unregister_event(img.parentNode, "click", details_choosepos_click);

  p=get_abs_pos(img);

  details_new_pos=
    { x: ((event.clientX-p[0])/img.offsetWidth*1000).toFixed(0),
      y: ((event.clientY-p[1])/img.offsetHeight*1000).toFixed(0),
      detail_nr: "new" };

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
  var el=document.getElementById("details_button");

  if(el&&(el.className=="toolbox_input_active")) {
    el.className='toolbox_input';
    unregister_event(img.parentNode, "mousemove", details_choosepos_move);
    unregister_event(img.parentNode, "click", details_choosepos_click);

    if(details_choose) {
      details_choose.parentNode.removeChild(details_choose);
      details_choose=null;
    }
  }
  else {
    var ob=document.createElement("img");
    if(img.height>img.width)
      details_size=img.height;
    else
      details_size=img.width;
    details_size=details_size/20;

    ob.src=url_script({ script: "extensions/details_kasterl.php", page: page, series: series, size: details_size});

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

    if(el)
      el.className='toolbox_input_active';
  }
}

register_initfun(details_init);

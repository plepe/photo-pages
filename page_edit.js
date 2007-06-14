/* page_edit.js
 * - Some JavaScript code that is used for several pages
 *
 * Copyright (c) 1998-2006 Stephan Plepelits <skunk@xover.mud.at>
 *
 * This file is part of Skunks' Photosscripts 
 * - http://xover.mud.at/~skunk/proj/photo
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

var page_edit_current_over=null;
var page_edit_button_down=0;
var page_edit_moving=0;
var page_edit_marked=new Array();
var page_edit_was_marked=new Array();
var page_edit_enlarged=null;
var page_edit_over_input;
var page_edit_fix_large=0;
var page_edit_page;
var page_edit_over_image;

function page_edit_new_spacer() {
  var ret;

  ret=document.createElement("div");
  ret.setAttribute("edit_type", "spacer");
  ret.className="edit_img_spacer";
  ret.onmouseover=page_edit_mouse_enter;
  ret.onmouseout =page_edit_mouse_leave;

  return ret;
}

function page_edit_input_enter(ob) {
  page_edit_over_input=ob;
}

function page_edit_input_leave(ob) {
  page_edit_over_input=null;
}

function page_edit_mouse_enter(event, ob) {
  if(!ob)
    ob=this;

//  var debug=document.getElementById("debug");
//  debug.innerHTML=ob.id;

  if(page_edit_enlarged) {
    page_edit_enlarged.style.height="3px";
    page_edit_enlarged=null;
  }

  page_edit_current_over=ob;

  if(page_edit_moving) {
    if(in_array(page_edit_current_over, page_edit_marked)) {
      page_edit_current_over=null;
      return ;
    }

    if(page_edit_current_over.getAttribute("edit_type")=="chunk") {
      if(page_edit_enlarged=prev_sibl(page_edit_current_over))
        if(page_edit_enlarged.getAttribute("edit_type")!="spacer")
          page_edit_enlarged=null;
    }
    else if(page_edit_current_over.getAttribute("edit_type")=="spacer") {
      page_edit_enlarged=page_edit_current_over;
    }

    if(page_edit_enlarged)
      page_edit_enlarged.style.height="30px";
  }
}

function page_edit_mouse_leave(event) {
  if(page_edit_enlarged) {
    page_edit_enlarged.style.height="3px";
    page_edit_enlarged=null;
  }

  page_edit_current_over=null;
}

function page_edit_mouse_down(event) {
  //page_edit_button_down=1;

//  if(!event.ctrlKey) {
//    if(!in_array(page_edit_current_over, page_edit_marked))
//      page_edit_clear_marked();
//  }

//  if(page_edit_current_over&&(page_edit_current_over.getAttribute("edit_type")=="chunk")) {
//    page_edit_current_over.className="edit_mark_img_chunk";
//    page_edit_marked.push(page_edit_current_over);
//  }

//  if(page_edit_over_input)
//    return true;

  if((page_edit_page=="page2")&&(page_edit_over_image))
    return false;
  else
    return true;
}

function page_edit_img_list_down(event, list) {
}

function page_edit_img_list_clicked(event, list) {
  if(!event.ctrlKey) {
    if(!in_array(page_edit_current_over, page_edit_marked))
      page_edit_clear_marked();
  }

  return false;
}

function page_edit_img_chunk_clicked(event, ob) {
  page_edit_button_down=1;

  if(!event.ctrlKey) {
    if(!in_array(ob, page_edit_marked))
      page_edit_clear_marked();
  }

  if(!in_array(ob, page_edit_marked)) {
    page_edit_marked.push(ob);
    ob.className="edit_mark_img_chunk";
  }

  return false;
}

function page_edit_clear_marked() {
  page_edit_was_marked=page_edit_marked;
  for(var i=0; i<page_edit_marked.length; i++) {
    page_edit_marked[i].className="edit_img_chunk";
  }
  page_edit_marked=new Array();
}

function page_edit_return_moving() {
  var move_ob=document.getElementById("move_img_list");

  // Zurueck zu den Originalplaetzen
  for(var i=move_ob.childNodes.length-1;i>=0;i--) {
    var m=move_ob.childNodes[i];
    if((m.nodeType!=3)&&(m.getAttribute("edit_type")=="spacer")) {
      move_ob.removeChild(m);
    }
    else if(m.nodeType!=3) {
      var n=m.getAttribute("page_edit_next_img");
      if(n) {
        n=document.getElementById(n);
        n.parentNode.insertBefore(m, n);

        var s=page_edit_new_spacer();
        n.parentNode.insertBefore(s, n);
      }
      else {
        var list=m.getAttribute("page_edit_list");
        if(list) {
          list=document.getElementById(list);
          list.appendChild(m);

          var s=page_edit_new_spacer();
          list.appendChild(s);
        }
        else {
          m.parentNode.removeChild(m);
        }
      }
    }
  }

  page_edit_moving=0;

  if(page_edit_enlarged) {
    page_edit_enlarged.style.height="3px";
    page_edit_enlarged=null;
  }
}

function page_edit_mouse_up(event) {
  page_edit_button_down=0;
  var move_ob=document.getElementById("move_img_list");

  if(page_edit_moving) {
    if(page_edit_current_over) {
      // Vor dem aktuellen Element einfuegen
      var ins=page_edit_current_over;
      if(ins.getAttribute("edit_type")=="spacer")
        ins=next_sibl(ins);

      while(move_ob.childNodes.length) {
        var m=move_ob.firstChild;
        if((m.nodeType!=1)||(m.getAttribute("edit_type")=="spacer")) {
          move_ob.removeChild(m);
        }
        else {
          if(m!=ins) {
            ins.parentNode.insertBefore(m, ins);

            var s=page_edit_new_spacer();
            ins.parentNode.insertBefore(s, ins);
          }
          else {
            // das sollte eigentlich nicht vorkommen
            // Zurueck zum Originalplatz
            var n=m.getAttribute("page_edit_next_img");
            if(n) {
              n=document.getElementById(n);
              n.parentNode.insertBefore(m, n);

              var s=page_edit_new_spacer();
              n.parentNode.insertBefore(s, n);
            }
            else {
              var list=m.getAttribute("page_edit_list");
              list=document.getElementById(list);
              list.appendChild(m);

              var s=page_edit_new_spacer();
              list.appendChild(s);
            }
          }
        }
      }
    }
    else {
      page_edit_return_moving();
    }
    //page_edit_clear_marked();
    page_edit_moving=0;

    if(page_edit_enlarged) {
      page_edit_enlarged.style.height="3px";
      page_edit_enlarged=null;
    }
  }
  else {
//    if(page_edit_over_input)
//      page_edit_over_input.focus();
  }
}

function page_edit_mouse_move(event) {
  var move_ob=document.getElementById("move_img_list");
  move_ob.style.top=(event.pageY+2)+"px";
  move_ob.style.left=(event.pageX+2)+"px";
  move_ob.style.display="block";
  if((page_edit_button_down)&&(!page_edit_moving)) {
    page_edit_moving=1;

    while(move_ob.firstChild)
      move_ob.removeChild(move_ob.firstChild);

    page_edit_dont_show_pic();

    for(var i=0;i<page_edit_marked.length;i++) {
      var m=page_edit_marked[i];
      var n;

      n=page_edit_marked[i].nextSibling;
      while((n)&&(n.className!="edit_img_chunk")&&(n.className!="edit_mark_img_chunk")) {
        var n1=n.nextSibling;
        n.parentNode.removeChild(n);
        n=n1;
      }

      if(n)
        page_edit_marked[i].setAttribute("page_edit_next_img", n.id);
      else
        page_edit_marked[i].setAttribute("page_edit_next_img", null);

      if(page_edit_marked[i].parentNode.id!="move_img_list")
        page_edit_marked[i].setAttribute("page_edit_list",
                                         page_edit_marked[i].parentNode.id);

      move_ob.appendChild(page_edit_marked[i]);

      var s=page_edit_new_spacer();
      move_ob.appendChild(s);
    }

    return false;
  }
    return false;
}

function page_edit_show_page(page) {
  var obs=document.getElementsByTagName("div");
  for(i=0;i<obs.length;i++) {
    if(obs[i].className=='page_edit_page')
      obs[i].style.display="none";
  }

  for(i=1;i<=tab_count;i++) {
    var ob=document.getElementById("tab_page"+i);
    ob.className='page_edit_choose_page';
  }
  var ob=document.getElementById("tab_"+page);
  ob.className='page_edit_choose_page_chose';

  var ob=document.getElementById(page);
  ob.style.display="block";

  page_edit_page=page;
}

function page_edit_modify_rights(user, right) {
  input=document.getElementById("rights_input_"+user+"_"+right);
  td=document.getElementById("rights_td_"+user+"_"+right);

  switch(input.value) {
    case "1":
      input.value="-1";
      td.className='page_edit_rights_notgrant';
      break;
    case "-1":
      input.value="-2";
      td.className='page_edit_rights_grant_children';
      break;
    case "-2":
      input.value="0";
      if(document.getElementById("rights_inherited_"+user+"_"+right).value=="-1")
        td.className='page_edit_rights_inh_notgrant';
      else
        td.className='page_edit_rights_inh_grant';
      break;
    default:
      input.value="1";
      td.className='page_edit_rights_grant';
  }
}

function page_edit_key_down(event) {
  if((page_edit_moving)&&(event.keyCode==27)) {
    page_edit_return_moving();
  }

//  if(event.keyCode=='X') {
//  }
}

document.captureEvents(Event.MOUSEMOVE);
document.onmousemove=page_edit_mouse_move;
document.captureEvents(Event.MOUSEDOWN | Event.MOUSEUP);
document.onmousedown=page_edit_mouse_down;
document.onmouseup  =page_edit_mouse_up;
document.captureEvents(Event.KEYDOWN);
document.onkeydown=page_edit_key_down;

var page_edit_pic_timeout;
var page_edit_pic_over;

function page_edit_show(off) {
  var ob=document.getElementById("page_edit_show_pic");
  var ob2=ob.getElementsByTagName("img");

  // Kill remaining timeouts
  if(!ob2[0].complete) {
    page_edit_pic_timeout=window.setTimeout("page_edit_show("+off+")", 50);
    return;
  }

  // Move image into window
  h=window.innerHeight;
  //debug_write("h="+h+" pos="+ob.style.top.replace("px", "")+" height="+ob2[0].height+" p+height="+(parseInt(ob.style.top.replace("px", ""))+ob2[0].height));
  if(parseInt(ob.style.top.replace("px", ""))+ob2[0].height>h-4) {
    ob.style.top=(h-ob2[0].height-4) + "px";
  }

  // Show the image
  ob.style.display="block";
}

function page_edit_show_pic(img, chunk, pict_url, pict_orig_url) {
  if(page_edit_over_image==img)
    return;
  page_edit_over_image=img;

//  page_edit_dont_show_pic(1);
  end_mag();

  //if(page_edit_pic_over!=img)
//
  if(page_edit_moving)
    return;

  var ob=document.getElementById("page_edit_show_pic");
  var ob2=ob.getElementsByTagName("img");
  //alert(ob2[0].getAttribute("page_edit_src")+ " "+pict_url);
//  if(ob2[0].getAttribute("page_edit_src")==pict_url)
//    return;

  page_edit_fix_large=0;

  var p=get_abs_pos(img);
  page_edit_pic_over=img;
  ob.style.position="absolute";
  ob2[0].src=pict_url;
  ob2[0].setAttribute("page_edit_src", pict_url)
  par=img.parentNode;
  while(par&&(par.className!="edit_img_list")) {
    par=par.parentNode;
  }

  ob.style.top=(p[1]-par.scrollTop)+"px";
  ob.style.left=(p[0]+64+6)+"px";

  // fuer magnify
  ob2[0].id="img";
  img_orig=pict_orig_url;

  clearTimeout(page_edit_pic_timeout);
  page_edit_pic_timeout=window.setTimeout("page_edit_show()", 500);

  return true;
}

function page_edit_photo_click(event) {
  page_edit_fix_large=1;

  //fuer magnify
  start_mag();

  return false;
}

function page_edit_move_pic(event) {
  if(!page_edit_pic_over)
    return true;

  //page_edit_dont_show_pic();

  clearTimeout(page_edit_pic_timeout);

  p=get_abs_pos(page_edit_pic_over);
  par=page_edit_pic_over.parentNode;
  while(par&&(par.className!="edit_img_list")) {
    par=par.parentNode;
  }

  if(par)
    p[1]-=par.scrollTop;

//alert(p[0]+" "+p[1]+"  "+event.clientX+" "+event.clientY+" "+page_edit_pic_over.offsetWidth);
  if((p[0]<=event.pageX)&&(event.pageX<p[0]+page_edit_pic_over.offsetWidth)&&
     (p[1]<=event.pageY)&&(event.pageY<p[1]+page_edit_pic_over.offsetHeight)) {
    clearTimeout(page_edit_pic_timeout);
    page_edit_pic_timeout=window.setTimeout("page_edit_show()", 500);
  }
  else {
    page_edit_pic_over=null;
  }

  return true;
}

function page_edit_leave_image(img) {
  if(!page_edit_fix_large) {
    page_edit_over_image=null;
    page_edit_dont_show_pic();
  }
}

function page_edit_dont_show_pic(force) {
  if((!page_edit_fix_large)||(force)) {
    var ob=document.getElementById("page_edit_show_pic");
    ob.style.display="none";

    end_mag();

    clearTimeout(page_edit_pic_timeout);
    page_edit_fix_large=0;
  }

  return true;
}

function resize_textarea(ob) {
  ob.style.height=ob.scrollHeight+"px";
}

function name_change(ob, orig, ch) {
  if(ob.name)
    ob.name=ob.name.replace(orig, ch);
  if(ob.id)
    ob.id=ob.id.replace(orig, ch);
  if(ob.value)
    ob.value=ob.value.replace(orig, ch);

  var subob=ob.firstChild;
  while(subob) {
    name_change(subob, orig, ch);
    subob=subob.nextSibling;
  }
}

function page_edit_new(something) {
  max_chunk=max_chunk+1;

//  if(clicked_ob)
//    for(i=0;i<clicked_ob.length;i++)
//      clicked_ob[i].className='edit_img_chunk';
  clicked_ob=Array();

  clicked_ob.push(document.getElementById(something).cloneNode(true));
  clicked_ob[0].id="chunk_XXXX";

  name_change(clicked_ob[0], "XXXX", max_chunk);
  clicked_ob[0].style.display='block';
  var ob=document.getElementById("move_img_list")
  ob.style.display="block";
  ob.appendChild(clicked_ob[0]);

  offset_clicked=Array(2);
  offset_clicked[0]=5;
  offset_clicked[1]=5;
  
  page_edit_button_down=1;
  page_edit_moving=1;
}

function input_get_focus() {
}

function init_page_edit() {
  var ob=document.getElementById("wait_screen");
  ob.parentNode.removeChild(ob);
  page_edit_page="page1";
}

function move_to_list(list) {
  var i;
  var new_marked=new Array();
  var moveto_list=document.getElementById(list);

  // Suchen, wo wir einfuegen wollen
  var bef=moveto_list.firstChild;
  var top=moveto_list.scrollTop;
  while((bef)&&((bef.nodeType!=1)||(bef.getAttribute("edit_type")!="chunk"))) {
    bef=next_sibl(bef);
  }

  if((bef)&&(bef.offsetTop<top)) {
    while(bef.offsetTop<top) {
      bef=next_sibl(bef);
      while((bef)&&((bef.nodeType!=1)||(bef.getAttribute("edit_type")!="chunk"))) {
        bef=next_sibl(bef);
      }
    }

    bef=next_sibl(bef);
    while((bef)&&((bef.nodeType!=1)||(bef.getAttribute("edit_type")!="chunk"))) {
      bef=next_sibl(bef);
    }
  }

  for(i=0; i<page_edit_marked.length; i++) {
    var ob=page_edit_marked[i];

    var m=next_sibl(ob);
    if((m.nodeType!=1)||(m.getAttribute("edit_type")=="spacer")) {
      m.parentNode.removeChild(m);
    }

    ob.className="edit_mark_img_chunk";
    new_marked.push(ob);

    if(bef!=null) {
      moveto_list.insertBefore(ob, bef);
      moveto_list.insertBefore(page_edit_new_spacer(), bef);
    }
    else {
      moveto_list.appendChild(ob);
      moveto_list.appendChild(page_edit_new_spacer());
    }
  }

  page_edit_marked=new_marked;
}

var page_edit_div;
var page_edit_add_params=new Array(); // array of additional params to imag-urls

function page_edit_edit_hide() {
  if(page_edit_div) {
    page_edit_div.parentNode.removeChild(page_edit_div);
  }
}

function page_edit_edit_img_copy_data(id, orig, to) {
  var c=orig;
  while(c) {
    if(c.firstChild)
      page_edit_edit_img_copy_data(id, c.firstChild, to);

    if(c.name) {
      var cel=document.getElementsByName(c.name);

      if((cel.length>0)&&(cel[0]==c)) {
        if(cel.length>1)
          cel=new Array(cel[1]);
        else
          cel=new Array();
      }

      if(cel.length==0) {
        cel[0]=document.createElement("input");
        cel[0].type="hidden";
        cel[0].name=c.name;
        to.appendChild(cel[0]);
      }
      cel[0].value=c.value;
    }
    c=c.nextSibling;
  }
}

function page_edit_edit_img_ok() {
  var c;
  var el=document.getElementById("chunk_"+page_edit_id);

  call_hooks("page_edit_edit_save", ret, page_edit_id, page_edit_type);
  if(!page_edit_add_params[page_edit_id])
      page_edit_add_params[page_edit_id]=new Object();
  call_hooks("page_edit_edit_get_add_params", page_edit_add_params[page_edit_id], page_edit_id, page_edit_type);

  page_edit_edit_img_copy_data(page_edit_id, page_edit_div.firstChild, el);
  var img=document.getElementById("chunk_"+page_edit_id+"_img");
  var url={ "page": page, "series": series, "script": "get_image.php", "img": page_edit_id, "imgname": "bla.jpg", "size": 64, "version": 0 };
  for(var key in page_edit_add_params[page_edit_id]) {
    url[key]=page_edit_add_params[page_edit_id][key];
  }
  img.src=url_photo(url);

  page_edit_edit_hide();
}

function page_edit_edit_img_cancel() {
  page_edit_edit_hide();
}

function page_edit_edit_as_mainpicture(id) {
  var el1=document.getElementsByName("data[MAIN_PICTURE]");
  var el2=document.getElementsByName("data[LIST]["+id+"][img]");
  el1[0].value=el2[0].value;
}

var page_edit_id;
var page_edit_type;
function page_edit_edit_img(id) {
  page_edit_id=id;
  page_edit_div=document.createElement("div");
  var ret=new Object();
  ret.text="";
  page_edit_type=document.getElementsByName("data[LIST]["+id+"][type]")[0].value;

  page_edit_div.className="page_edit_edit_img";
  page_edit_div.setAttribute("page_edit_id", id);
  document.body.appendChild(page_edit_div);

  ret.text+="<div style='float: left; margin-right: 5px;'>\n";
  f=new Function("ret", "id", "return "+page_edit_type+"_edit_chunk(ret, id);");
  f(ret, id);
  ret.text+="</div>\n";

  var el1=document.getElementsByName("data[MAIN_PICTURE]");
  var el2=document.getElementsByName("data[LIST]["+id+"][img]");

  reset_toolbox("page_edit_edit_toolbox");
  call_hooks("page_edit_edit_toolbox", ret, id, page_edit_type);
  add_toolbox_item("page_edit_edit_toolbox", "<input type='button' class='toolbox_input"+(el1[0].value==el2[0].value?"_active":"")+"' value='"+lang_str["page_edit_edit_as_mainpicture"]+"' onClick='page_edit_edit_as_mainpicture("+id+")'>\n");
  ret.text+=show_toolbox("page_edit_edit_toolbox");

  ret.text+="<div style='text-align: right; clear: left;'>\n";
  ret.text+="  <input type='button' value='"+lang_str["nav_ok"]+"' onClick='page_edit_edit_img_ok()'>\n";
  ret.text+="  <input type='button' value='"+lang_str["nav_cancel"]+"' onClick='page_edit_edit_img_cancel()'>\n";
  ret.text+="</div>\n";

  page_edit_div.innerHTML=ret.text;

  call_hooks("page_edit_edit_ready", ret, id, page_edit_type);
}


register_initfun(init_page_edit);

/* image.js
 * - Some JavaScript code that is used on the slideshow
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
var rot_ob;

function list_update() {
  // Wenn wir in der Diaansicht sind
  if(parent.list) {
    if(parent.list.jump) {
      // In der Liste zum aktuellen Bild hupfen
      //alert ('list.php?series='+series+'#img_'+imgurl);
      parent.list.location.href='list.php?series='+series+'#img_'+imgchunk;
    }

    // Den Link zur Frameansicht durch einen Link zur Diaansicht ersetzen
    var el=document.getElementById('nav_frame_img');
    el.src=img_path+'/view_slide.png';
    var el=document.getElementById('nav_frame_a');
    el.href='image.php?series='+series+'&img='+imgurl;
  }
}

function notify_img_load() {
  if(rot_ob)
    rot_ob.className='toolbox_input';

  //set_auto_img_size();
}

function start_rotate(url, _rot_ob) {
  rot_ob=_rot_ob;
  rot_ob.className="toolbox_input_active";

  start_xmlreq(url, "Bild wird rotiert");
}

function start_desc_edit() {
  var el=document.getElementById("toolbox_input_desc");
  el.className='toolbox_input_active';
  var el=document.getElementById("desc");
  el.style.display='none';
  var el=document.getElementById("desc_edit");
  el.style.display='block';
  var el=document.getElementById("input_img_desc");
  el.focus();
}

function end_desc_edit(xmldata) {
  var el=document.getElementById("desc_edit");
  el.style.display='none';
  var el=document.getElementById("desc");
  el.style.display='block';
//  el.firstChild.nodeValue=document.getElementById("input_img_desc").value;
  el.innerHTML=document.getElementById("input_img_desc").value;
  var el=document.getElementById("toolbox_input_desc");
  el.className='toolbox_input';

  window.focus();
}

function save_desc_edit(series) {
  var el=document.getElementById("desc");
  var el1=document.getElementById("input_img_desc");
  /* alert("toolbox.php?img=" + imgurl + "&"+
               "todo=edit_desc&" + 
               "page="+page+"&"+
               "series="+series+"&"+
               "data=" + el1.value +"&"+
               "orig_data=" + el.firstChild.nodeValue); */
  start_xmlreq(url_script({ script: "toolbox.php", page: page, series: series, data: el1.value, orig_data: el.firstChild.nodeValue, todo: "edit_desc", index_id: index_id }), "Beschreibung wird gespeichert", end_desc_edit);
 /* 
  "toolbox.php?img=" + imgurl + "&"+
               "todo=edit_desc&" + 
               "page="+page+"&"+
               "series="+series+"&"+
               "data=" + el1.value +"&"+
               "orig_data=" + el.firstChild.nodeValue, 
               "Beschreibung wird gespeichert",
               end_desc_edit); */
}

function start_add_comment() {
  var el=document.getElementById("toolbox_input_comment");
  el.className='toolbox_input_active';
  el.type="button";
  var el=document.getElementById("add_comment");
  el.style.display='block';
  var el=document.getElementById("input_comment_name");
  el.focus();
  el=document.getElementById("input_comment_name");
  el.value="";
  el=document.getElementById("input_comment");
  el.value="";

  return false;
}

function end_comment(xmldata) {
  var text;
  var el=document.getElementById("add_comment");
  el.style.display='none';
  var el=document.getElementById("image_view_comments");

  text=document.getElementById("input_comment_name").value;
  if(!text) {
    text="Ano Nym";
  }
  el1=el.appendChild(document.createElement("span"));
  el1.appendChild(document.createTextNode(text));
  el1.className='comment_name_day';
  text=document.getElementById("input_comment").value;
  if(!text)
    text="";
  el.appendChild(document.createTextNode(": " + text));
  el.appendChild(document.createElement("br"));
  var el=document.getElementById("toolbox_input_comment");
  el.className='toolbox_input';
}

function save_comment(series) {
  var el1=document.getElementById("input_comment_name");
  var el2=document.getElementById("input_comment");
  start_xmlreq(url_script({ script: "toolbox.php", page: page, series: series, comment_name: el1.value, comment: el2.value, todo: "add_comment", index_id: index_id }), "Beschreibung wird gespeichert", end_comment);

  /*start_xmlreq("toolbox.php?img=" + imgurl + "&todo=add_comment&" + "series="+series+"&"+
               "comment_name=" + el1.value + "&comment=" + el2.value, 
               "Kommentar wird gespeichert",
               end_comment); */
}

function notify_list() {
  if(parent.list) {
    parent.list.jump=1;
  }
}

register_initfun(list_update);

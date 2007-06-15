/* magnify.js
 * - JavaScript code for the Magnifier
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

var mag;
var divmag;
var mag_pos_x, mag_pos_y;

function show_mag() {
  var img=document.getElementById('img');
  var magzoom=mag_sizex/img.width;
  var magsize=200;

  mag.onload=null;

  mag_pos_x+=window.scrollX;
  mag_pos_y+=window.scrollY;

  if(divmag.parentNode.style.position=="absolute") {
    var m=get_abs_pos(divmag.parentNode);
  }
  else {
    var m=new Array(0, 0);
  }

  divmag.style.left=mag_pos_x-magsize/2-m[0] + "px";
  divmag.style.top=mag_pos_y-magsize/2-m[1] + "px";

  // Absoluten Abstand zur Seite bestimmen
//    var x=0; var y=0; var n=img;
//    while(n) {
//      if(n.offsetTop) {
//        x+=n.offsetLeft;
//        y+=n.offsetTop;
//      }
//      n=n.offsetParent;
//    }
  var p=get_abs_pos(img);
  var x=p[0];
  var y=p[1];

  var px=(mag_pos_x-x)*magzoom-(magsize/2);
  var py=(mag_pos_y-y)*magzoom-(magsize/2);
  mag.style.left=(-px)+"px";
  mag.style.top =(-py)+"px";

  if((x<=mag_pos_x)&&(mag_pos_x<x+img.width)&&
     (y<=mag_pos_y)&&(mag_pos_y<y+img.height))
    divmag.style.display='block';
  else
    divmag.style.display='none';

}

function mag_move(event) {
  if(mag) {
    ///var el=document.getElementById("desc");
    ///el.firstChild.nodeValue=event.clientX + " " +event.clientY + " " + 
    ///  divmag.style.display;
    mag_pos_x=event.clientX;
    mag_pos_y=event.clientY;
    show_mag();
  }
}

function mag_key(key) {
  alert(key);
}

function end_mag() {
  var el;
  if(!mag)
    return;

  if(el=document.getElementById("toolbox_input_mag"))
    el.className='toolbox_input';
  var img=document.getElementById('img');
  divmag.removeChild(mag);
  img.parentNode.removeChild(divmag);
  mag=null;
  divmag=null;
}

function start_mag(event) {
  if(mag) {
    end_mag();
  }
  else {
    var img=document.getElementById('img');
    var el;
    divmag=document.createElement("div");
    mag=document.createElement("img");
    divmag.style.position='absolute';
    divmag.style.overflow='hidden';
    divmag.style.display='none';
    divmag.className="magnify";
    divmag.onclick=magnify_activate_onclick;

    mag.src=img_orig;
    mag.style.position='absolute';
    mag.style.border='0px';
    mag.onkeydown=mag_key;

    img.parentNode.appendChild(divmag);
    divmag.appendChild(mag);

    if(el=document.getElementById("toolbox_input_mag"))
      el.className='toolbox_input_active';

    if(event) {
      mag_pos_x=event.clientX;
      mag_pos_y=event.clientY;
      show_mag();
      mag.onload=show_mag;
    }
  }
}

register_event(window, "mousemove", mag_move);

function magnify_page_edit_toolbox(ret, id, type) {
  add_toolbox_item("page_edit_edit_toolbox", "<input accesskey='m' type='submit' class='toolbox_input' id='toolbox_input_mag' onClick='start_mag()' value='"+lang_str["tool_magnify_name"]+"' title=\""+lang_str["tooltip_mag"]+"\"><br>\n");
  img_orig=url_photo({ "page": page, "series": series, "script": "get_image.php", "img": id, "imgname": "bla.jpg", "size": "orig", "version": 0});
}

function magnify_activate_onclick(event) {
  start_mag(event);
}

function magnify_init_fun() {
  if(extensions_mode=="page_edit") {
    register_hook("page_edit_edit_toolbox", magnify_page_edit_toolbox);
  }
  if(extensions_mode=="imageview") {
    var img=document.getElementById('img');
    var a=img.parentNode;
    a.removeAttribute("href");
    register_event(img, "click", magnify_activate_onclick);
  }
}

register_initfun(magnify_init_fun);

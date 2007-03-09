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

function mag_move(event) {
  if(mag) {
    var img=document.getElementById('img');
    var magzoom=mag.width/img.width;
    var magsize=200;
    if(divmag.parentNode.style.position=="absolute") {
      var m=get_abs_pos(divmag.parentNode);
    }
    else {
      var m=new Array(0, 0);
    }

    divmag.style.left=event.clientX-magsize/2-m[0] + "px";
    divmag.style.top=event.clientY-magsize/2-m[1] + "px";

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

    var px=(event.clientX-x)*magzoom-(magsize/2);
    var py=(event.clientY-y)*magzoom-(magsize/2);
    mag.style.left=(-px)+"px";
    mag.style.top =(-py)+"px";

    if((x<=event.clientX)&&(event.clientX<x+img.width)&&
       (y<=event.clientY)&&(event.clientY<y+img.height))
      divmag.style.display='block';
    else
      divmag.style.display='none';

    ///var el=document.getElementById("desc");
    ///el.firstChild.nodeValue=event.clientX + " " +event.clientY + " " + 
    ///  divmag.style.display;
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

function start_mag() {
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

    mag.src=img_orig;
    mag.style.position='absolute';
    mag.style.border='0px';
    mag.onkeydown=mag_key;

    img.parentNode.appendChild(divmag);
    divmag.appendChild(mag);

    if(el=document.getElementById("toolbox_input_mag"))
      el.className='toolbox_input_active';
  }
}

register_event(window, "mousemove", mag_move);

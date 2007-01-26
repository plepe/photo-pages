/* global.js
 * - JavaScript code that is used globally
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
 */

var xmlhttp_req;
var xmlhttp_callback;

var imgurl; // todo: weg
var img_version; // todo: weg

function get_abs_pos(ob) {
  var p=new Array(2);

  if(ob.x) {
    p[0]=ob.x;
    p[1]=ob.y;
    return p;
  }

  p[0]=0; p[1]=0;
  var par=ob.offsetParent;

  if(par)
    p=get_abs_pos(par);

  p[0]+=ob.offsetLeft;
  p[1]+=ob.offsetTop;

  return p;
}

function processReqChange() {
  var now = new Date();

  if(xmlhttp_req.readyState==4) {
    var xmldata=xmlhttp_req.responseXML;

    var status=xmldata.getElementsByTagName('status');
    if(status) {
      status=status[0].firstChild.nodeValue;
      if(status!="success") {
        alert(status);
      }
    }

    if((xmldata.getElementsByTagName('changed_image').length)&&
       (xmldata.getElementsByTagName('changed_image')[0].firstChild.nodeValue=="yes")) {
      var img=document.getElementById("img");

      if(img) {
        img_version++;
        img.src=cur_res + "/" + imgurl + "?" + img_version;
      }

      if(parent.list) {
        img=parent.list.document.getElementById(imgurl);
        img.src="$orig_path/" + imgurl + "?" + img_version;
      }

      w=img_width;
      img_width=img_height;
      img_height=w;
    }

    todo=xmldata.getElementsByTagName('todo')[0].firstChild.nodeValue;
    if(xmlhttp_callback)
      xmlhttp_callback(xmldata);

    switch(todo) {
      case 'login':
        location.reload();
      default:
    }

    var tb=document.getElementById("message");
    if(tb) {
      tb.style.display="none";
    }
  }
}

function start_xmlreq(url, msg, ready_callback) {
  xmlhttp_req = false;
  //alert(url);

  // branch for native XMLHttpRequest object
  if(window.XMLHttpRequest) {
    try {
      xmlhttp_req = new XMLHttpRequest();
    }
    catch(e) {
      xmlhttp_req = false;
    }
    // branch for IE/Windows ActiveX version
  } else if(window.ActiveXObject) {
    try {
      xmlhttp_req = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch(e) {
      try {
        xmlhttp_req = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e) {
        xmlhttp_req = false;
      }
    }
  }

  if(xmlhttp_req) {
    xmlhttp_callback=ready_callback;
    xmlhttp_req.onreadystatechange = processReqChange;
    xmlhttp_req.open("GET", url, true);
    xmlhttp_req.send("");

    var tb=document.getElementById("message");
    if(tb&&msg) {
      tb.firstChild.nodeValue=msg;
      tb.style.display="block";
    }
  }
}

function debug_write(s) {
  var ob=document.getElementById("debug");
  if(ob.firstChild) {
    var Rest = document.createElement("br");
    ob.insertBefore(Rest, ob.firstChild);
  }

  var Rest = document.createTextNode(s); //"found el "+clicked_ob.className+ "\n");
  ob.insertBefore(Rest, ob.firstChild);
}

function debug_clear() {
  var ob=document.getElementById("debug");
  while(ob.firstChild) {
    ob.removeChild(ob.firstChild);
  }
}

function in_array(el, arr) {
  if(!arr)
    return false;

  for(i=0;i<arr.length;i++) {
    if(arr[i]==el)
      return true;
  }

  return false;
}

function next_sibl(ob) {
  if(!ob)
    return null;

  ob=ob.nextSibling;
  while(((ob)&&(ob.nodeType!=1))) {
    ob=ob.nextSibling;
  }

  return ob;
}

function prev_sibl(ob) {
  if(!ob)
    return null;

  ob=ob.previousSibling;
  while(((ob)&&(ob.nodeType!=1))) {
    ob=ob.previousSibling;
  }

  return ob;
}

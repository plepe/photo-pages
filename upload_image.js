/* upload_image.js
 * - JavaScript code for the Upload Page
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
function new_page_new_list(xmldata) {
  var i;
  var a="";
  var a=ajax_read_value(xmldata, "dir");

  var ob=document.getElementById("dir_list");
  ob.innerHTML=a;
}

function list_dir(dir) {
  start_xmlreq("toolbox.php?todo=read_upload_dir&dir="+dir, "", new_page_new_list);
}

function upload_image_mark(ob) {
  var check;

  if(ob.className=="upload_file_marked") {
    ob.className="upload_file";
    check=false;
  }
  else {
    ob.className="upload_file_marked";
    check=true;
  }

  var inputs=ob.getElementsByTagName("input");
  for(i=0;i<inputs.length;i++) {
    inputs[i].checked=check;
  }
}

function upload_image_mark_all() {
  var obs=document.getElementsByTagName("span");
  var i;

  for(i=0; i<obs.length; i++) {
    if(obs[i].className=="upload_file") {
      obs[i].className="upload_file_marked";

      var inputs=obs[i].getElementsByTagName("input");
      var j;
      for(j=0;j<inputs.length;j++) {
        inputs[j].checked=true;
      }
    }
  }
}

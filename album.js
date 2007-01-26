/* album.js
 * - Javascript code for the album-view
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

function init_album() {
}

function change_cols(c) {
  ob=document.getElementById("table_album");

  if(c>cols) {
    for(i=0;i<ob.rows.length;i++) {
      if(ob.rows[i].cells[0].colSpan>2) {
        ob.rows[i].cells[0].colSpan=c;
      }
      else {
            //alert(i+ " " + ob.rows[i].length+ " " +a);
        var h=0;
        while(ob.rows[i].cells.length==0) {
          ob.deleteRow(i);
        }
        for(j=0;j<ob.rows[i].cells.length;j++) {
          h+=ob.rows[i].cells[j].colSpan;
        }

        for(;h<c;) {
          var a=1;
          if(ob.rows.length>i+a) {
            while(ob.rows[i+a].cells.length<1)
              a++;

            if(ob.rows[i+a].cells[0].colSpan>2) {
              var newob=document.createElement("td");
              h++;
              newob.className="imglist_empty";
              ob.rows[i].appendChild(newob);
            }
            else {
              h+=ob.rows[i+a].cells[0].colSpan;
              ob.rows[i].appendChild(ob.rows[i+a].cells[0]);
              if(ob.rows[i+a].cells.length==0) {
                ob.deleteRow(i+a);
              }
            }
          }
          else {
            var newob=document.createElement("td");
            newob.className="imglist_empty";
            ob.rows[i].appendChild(newob);
            h++;
          }
        }
      }
    }
  }
  else {
  }
  cols=c;
}



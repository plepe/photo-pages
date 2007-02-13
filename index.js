/* main.php
 * - The right page in a frameview with no picture shown
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

function change_rows(c) {
  if(ob=document.getElementById("rows_"+rows))
    ob.className='toolbox_input';

  rows=c;
  ob=document.getElementById("rows_"+rows);
  ob.className='toolbox_input_active';

  start_xmlreq("toolbox.php?todo=set_rows&rows="+rows, "Zeilenzahl wird gespeichert");

  location.reload();
}

function change_cols(c) {
  ob=document.getElementById("table_album");

  // Wenn die Anzahl der Spalten vergroessert werden soll
  if(c>cols) {

    // Alle Reihen durchgehen
    for(i=0;i<ob.rows.length;i++) {
      // Leerzellen loeschen
      for(j=0;j<ob.rows[i].cells.length;j++) {
        if(ob.rows[i].cells[j].className=='imglist_empty') {
          ob.rows[i].deleteCell(j);
          j--;
        }
      }
    }

    // Alle Reihen durchgehen
    for(i=0;i<ob.rows.length;i++) {

      // Zellen mit colspan>2 werden auf die ganze Breite aufgeblasen
      if((ob.rows[i].cells.length>0)&&(ob.rows[i].cells[0].colSpan>2)) {
        ob.rows[i].cells[0].colSpan=c;
      }

      // alle anderen Zeilen
      else {
        // Zaehlen, wie breit die Zeile ist
        var h=0;
        for(j=0;j<ob.rows[i].cells.length;j++) {
          h+=ob.rows[i].cells[j].colSpan;
        }

        // Solang die Reihe nicht voll ist
        while(h<c) {
          // Wenn noch eine Reihe nach der aktuellen Reihe ist
          if(ob.rows.length>i+1) {

            // Wenn die naechste Reihe leer ist, loeschen
            while(ob.rows[i+1].cells.length<1)
              ob.deleteRow(i+1);

            // Wenn in der naechsten Reihe eine Zelle ueber die volle Breite der
            // Tabelle ist, mit Leerfeldern auffuellen.
            if(ob.rows[i+1].cells[0].colSpan>2) {
              while(h<c) {
                var newob=document.createElement("td");
                h++;
                newob.className="imglist_empty";
                ob.rows[i].appendChild(newob);
              }
            }

            // Wenn die Reihe zu voll werden wuerde (weil colspan zu viel ist),
            // mit Leerfeldern auffuellen.
            else if(h+ob.rows[i+1].cells[0].colSpan>c) {
              while(h<c) {
                var newob=document.createElement("td");
                h++;
                newob.className="imglist_empty";
                ob.rows[i].appendChild(newob);
              }
            }

            // Die Zelle in die aktuelle Reihe nach vor holen
            else {
              h+=ob.rows[i+1].cells[0].colSpan;
              ob.rows[i].appendChild(ob.rows[i+1].cells[0]);

              if(ob.rows[i+1].cells.length==0) {
                ob.deleteRow(i+1);
              }
            }
          }
          else { // (keine Reihe mehr vorhanden)
            while(h<c) {
              var newob=document.createElement("td");
              newob.className="imglist_empty";
              ob.rows[i].appendChild(newob);
              h++;
            }
          }
        }

        // Sollte die Reihe keine Zellen mehr haben, dann die ganze Reihe loeschen
        while(ob.rows[i].cells.length==0) {
          ob.deleteRow(i);
        }
      }
    }
  }
  else if(c<cols) {
    // Alle Reihen durchgehen
    for(i=0;i<ob.rows.length;i++) {
      // Leerzellen loeschen
      for(j=0;j<ob.rows[i].cells.length;j++) {
        if(ob.rows[i].cells[j].className=='imglist_empty') {
          ob.rows[i].deleteCell(j);
          j--;
        }
      }
    }

    for(i=0; i<ob.rows.length;i++) {
      // Zellen mit colspan>2 werden auf die neue Breite angepasst
      if((ob.rows[i].cells.length>0)&&(ob.rows[i].cells[0].colSpan>2)) {
        ob.rows[i].cells[0].colSpan=c;
      }

      // alle anderen Zeilen
      else {
        // Zaehlen, wie breit die Zeile ist
        var h=0;
        for(j=0;j<ob.rows[i].cells.length;j++) {
          h+=ob.rows[i].cells[j].colSpan;
        }

        // Solange zuviele Bilder in der Reihe sind
        while(h>c) {
          if(ob.rows[i].cells.length>0) {
            // Wenn keine naechste Zeile oder naechste Zeile ein volle Breite
            // Objekt ist eine Zeile einfuegen
            if((ob.rows.length<=i+1)||
               ((ob.rows[i+1].cells.length>0)&&(ob.rows[i+1].cells[0].colSpan>2))) {
              x=ob.rows.length;
              ob.insertRow(i+1);
            }

            // Das letzte Objekt der Zeile in die naechste moven
            ob1=ob.rows[i].cells[ob.rows[i].cells.length-1];
            h-=ob1.colSpan;
            ob.rows[i+1].insertBefore(ob1, ob.rows[i+1].cells[0]);
          }
        }

        // Falls noch Platz ist, mit Leerfeldern auffuellen
        while(h<c) {
          var newob=document.createElement("td");
          h++;
          newob.className="imglist_empty";
          ob.rows[i].appendChild(newob);
        }
      }
    }
  }

  // Die Breite aller Zellen anpassen
  w=100/c;
  for(i=0;i<ob.rows.length;i++) {
    // Leerzellen loeschen
    for(j=0;j<ob.rows[i].cells.length;j++) {
      ob.rows[i].cells[j].width=(w*ob.rows[i].cells[j].colSpan)+'%';
    }
  }


  ob=document.getElementById("cols_"+cols);
  ob.className='toolbox_input';

  cols=c;
  ob=document.getElementById("cols_"+cols);
  ob.className='toolbox_input_active';

  start_xmlreq("toolbox.php?todo=set_cols&cols="+cols, "Spaltenzahl wird gespeichert");
}



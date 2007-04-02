/* url.js
 * - Generates correct URLs in JavaScript
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
function build_url(template, params) {
  var p=new Array();
  var erg=template;

  params["web_path"]=web_path;

  for(var key in params) {
    if(erg.search("%"+key+"%")!=-1)
      erg=erg.replace("%"+key+"%", params[key]);
    else
      p.push(key+"="+encodeURIComponent(params[key]));
  }

  if(p.length)
    return erg+"?"+p.join("&");
  else
    return erg;
}

function url_page(path, series, skript) {
}

function url_photo(path, series, skript, imgnum, imgname, size, imgversion) {
}

function url_script(path, series, skript, imgnum, todo) {
  ret=v_url_script;

  if(typeof path=="object")
    ret=build_url(ret, path);
  else
    ret=build_url(ret, { "page": path, "series": series, "script": script, "imgnum": imgnum, "todo": todo });
//  ret=ret.replace("%1$s", path);
//  ret=ret.replace("%2$s", series);
//  ret=ret.replace("%3$s", skript);
//  ret=ret.replace("%4$s", imgnum);

  return ret;
}

function url_javascript(path, series, skript, imgnum) {
}

function url_img(imgfile) {
  ret=v_url_img;

  if(typeof path=="object")
    ret=build_url(ret, imgfile);
  else
    ret=build_url(ret, { "imgname": imgfile });
//  ret=ret.replace("%1$s", path);
//  ret=ret.replace("%2$s", series);
//  ret=ret.replace("%3$s", skript);
//  ret=ret.replace("%4$s", imgnum);

  return ret;
}

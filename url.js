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
function url_page(path, series, skript) {
}

function url_photo(path, series, skript, imgnum, imgname, size, imgversion) {
}

function url_script(path, series, skript, imgnum) {
  ret=v_url_script;

  ret=ret.replace("%1$s", path);
  ret=ret.replace("%2$s", series);
  ret=ret.replace("%3$s", skript);
  ret=ret.replace("%4$s", imgnum);

  return ret;
}

function url_javascript(path, series, skript, imgnum) {
}

function url_img(imgfile) {
}

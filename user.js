/* user.js
 * - JavaScript code to authenticate a user
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

function do_authenticate() {
  var el1=document.getElementById("small_login_username");
  var el2=document.getElementById("small_login_password");
  start_xmlreq(url_script({ page: page, series: series, script: "toolbox.php", todo: "login", username: el1.value, password: el2.value}), "Login-Daten werden ueberprueft", authenticate_finish);
  return false;
}

function do_logout() {
  start_xmlreq(url_script({ page: page, series: series, script: "toolbox.php", todo: "logout"}), 0, authenticate_finish);
  return false;
}

function authenticate_finish(xmldata, status) {
  if(!status) {
    location.reload();
  }
}

function authenticate_user(ob) {
  var el=document.getElementById("small_login");
  el.style.display='block';

  var el=document.getElementById("user_login_submit");
  el.onclick=do_authenticate;

  var el=document.getElementById("user_login_logout");
  el.onclick=do_logout;

  var el=document.getElementById("small_login_username");
  el.focus();
}

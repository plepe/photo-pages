<?
/* lang.php
 * - Includes the correct language
 *
 * Copyright (c) 1998-2007 Stephan Plepelits <skunk@xover.mud.at>
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

if($_REQUEST[language]) {
  session_register("language");
  $_SESSION[language]=$_REQUEST[language];
}

if($_SESSION[language])
  $language=$_SESSION[language];

include "lang_$language.php";

function lang_list() {
  return array("de"=>"Deutsch", "en"=>"English");
}

$lang_ids=array();

function set_lang($lang, $lang_id, $str) {
  global $language;
  global $lang_str;
  global $lang_ids;

  if(($lang==$language)||(substr($lang, 0, strpos($language, "_")-1)==$lang)) {
    $lang_str=array_merge($lang_str, $str);
  }
  elseif(!in_array($lang_id, $lang_ids)) {
    $lang_str=array_merge($str, $lang_str);
    array_push($lang_ids, $lang_id);
  }
}

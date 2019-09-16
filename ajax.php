<?
/* ajax.php
 * - This script is called via XMLHttpRequest and calls extensions
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

Header("content-type: text/xml; charset: utf-8");
$request_type="xml";
print "<xml version=\"1.0\" encoding=\"UTF-8\">\n";

require "data.php";

if (preg_match("/^[a-zA-Z0-9_]+$/", $_REQUEST['extension'])) {
  include "extensions/{$_REQUEST['extension']}_ajax.php";
}

print "</xml>\n";


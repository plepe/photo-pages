<?
/* group.php
 * - User Administration (Group)
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
 // http://xover.mud.at/~florian/testbed/p1/svg-map.js

class Group {
  var $groupname;
  var $members;

  function Group($groupname) {
    global $group_file;

    $f=fopen($group_file, "r");
    while($r=fgets($f)) {
      $r=chop($r);

      if(($r!="")&&(substr($r, 0, 1)!="#")) {
        $r=explode(":", $r);
        if($r[0]==$groupname) {
          $this->groupname=$groupname;
          $this->members=explode(" ", $r[1]);
        }
      }
    }
  }

  function is_member($name) {
    global $anon_user;
    global $default_group;

    if(is_string($name))
      $username=$name;
    else
      $username=$name->username;

    if(($name!=$anon_user)&&($this->groupname=="$default_group"))
      return 1;

    if(!$this->members)
      return 0;

    return in_array($username, $this->members);
  }
};

$group_list=0;

function group_list() {
  global $group_file;
  global $group_list;

  if($group_list)
    return $group_list;
  else
    $group_list=array();

  $f=fopen($group_file, "r");
  while($r=fgets($f)) {
    $r=chop($r);
    if(($r!="")&&(substr($r, 0, 1)!="#")) {
      $r=explode(":", $r);

      $group_list[$r[0]]=0;
    }
  }

  return $group_list;
}

function get_group($groupname) {
  global $group_list;

  group_list();

  if(!$group_list[$groupname])
    $group_list[$groupname]=new Group($groupname);

  return $group_list[$groupname];
}



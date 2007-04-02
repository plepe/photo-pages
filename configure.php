<? 
/* index.php
 * - The entry page to a photopage
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

function set_extension_views($ext, $values) {
}

function include_extension($ext) {
}

function set_extension_description($ext, $name, $text) {
  global $extensions;
  if(!$extensions)
    $extensions=array();

  print "<tr><td><input type='checkbox' name='data[extensions][$ext]'";
  if(in_array($ext, $extensions))
    print " checked";
  print "></td>\n";
  print "<td>$name</td>\n";
  print "<td>$text</td></tr>\n";
}

function archive_writeable($c) {
  if($c=="")
    return "no \$file_path";

  return is_writeable($c)?0:"\$file_path not writeable";
}

function check_resolutions($c) {
  return 0;
}

?>
<html>
<head>
<title>Photo Pages - Configuration</title>
</head>

<body>

<?

//phpinfo();

$params=array(
  "file_path"=>array("type"=>"string", "desc"=>"This is the path, were your photos reside. This directory should not be reachable by the webserver for security reasons.", "check"=>archive_writeable),
  "web_path"=>array("type"=>"string", "desc"=>"This is the WWW-path of your scripts"),
  "upload_path"=>array("type"=>"string", "desc"=>"This is the file system path, were you can upload pictures. They can be imported with Upload Pictures. Leave it blank, if you don't like this feature."),
  "passwd_file"=>array("type"=>"string", "desc"=>"This is the path of your password-file"),
  "group_file"=>array("type"=>"string", "desc"=>"This is the path of your group-file"),
  "max_res"=>array("type"=>"int", "desc"=>"Resolution for importing. Usually the original file is dropped, just a scaled down version is kept. Leave blank, if you want to keep the original files."),
  "normal_res"=>array("type"=>"int", "desc"=>"Default resolution when viewing an image"),
  "index_res"=>array("type"=>"int", "desc"=>"Default resolution when viewing an album"),
  "thumb_res"=>array("type"=>"int", "desc"=>"Resolution for thumbnail view (e.g. page editing)"),
  "resolutions"=>array("type"=>"array_int", "desc"=>"Space seperated list of available resolutions.", "check"=>check_resolutions),
  "convert_options"=>array("type"=>"string", "desc"=>"Which options should be used when rescaling"),
  "anon_user"=>array("type"=>"string", "desc"=>"What's the name of the anonymous user?"),
  "default_group"=>array("type"=>"string", "desc"=>"Which group are all users (!= anonymous user) added to automatically"),
  "language"=>array("type"=>"select", "values"=>array("en"=>"English", "de"=>"Deutsch"), "desc"=>"Which is the default language")
);

if($_REQUEST[submit_ok]) {
  $data=$_REQUEST[data];

  unset($error);
  foreach($params as $key=>$conf) {
    switch($conf[type]) {
      case "array_int":
        $data[$key]=explode(" ", $data[$key]);
        break;
    }

    if($conf[check]) {
      if($erg=$conf[check]($data[$key]))
	$error[]=$erg;
    }
  }

  if($error) {
    unset($_REQUEST[submit_ok]);
    print "Errors:<ul>\n";
    foreach($error as $e) {
      print "<li>$e</li>\n";
    }
    print "</ul>\n";

    foreach($params as $key=>$conf) {
      $$key=$data[$key];
    }
  }
  else {
    print "Successful.<br>\n";

    $file="/tmp/conf.php-".Date("YmdHM");
    $f=fopen($file, "w");
    fputs($f, "<?\n\n");

    foreach($params as $key=>$conf) {
      fputs($f, "// $conf[desc]\n");
      switch($conf[type]) {
	case "string":
	case "select":
	  fputs($f, "\$$key = \"$data[$key]\";");
	  break;
	case "int":
	  fputs($f, "\$$key = $data[$key];");
	  break;
	case "array_int":
	  fputs($f, "\$$key = array(".implode(", ", $data[$key]).");");
	  break;
        default:
      }

      fputs($f, "\n\n");
    }

    fputs($f, "// Contains a list of extensions - check the README file for available extensions\n");
    if($data[extensions]) {
      fputs($f, "\$extensions = array(\"".implode("\", \"", array_keys($data[extensions]))."\");\n\n");
    }
    else {
      fputs($f, "\$extensions = array();\n\n");
    }

    fputs($f, "//// CUSTOM DEFINITIONS - BEGIN ////\n");
    fputs($f, $data[own_define]."\n");
    fputs($f, "//// CUSTOM DEFINITIONS - END ////\n");

    print "Move the file $file to the Photo Pages directory.<br>\n";
    print "<a href='$data[web_path]'>To your new Photo Pages</a>\n";
  }
}
else {
  if(file_exists("conf.php")) {
    include("conf.php");
    $f=fopen("conf.php", "r");
    $m=0;
    $own_define="";
    while($r=fgets($f)) {
      $r=chop($r);
      if($r=="//// CUSTOM DEFINITIONS - BEGIN ////") {
	$m=1;
      }
      elseif($r=="//// CUSTOM DEFINITIONS - END ////") {
	$m=2;
      }
      elseif($m==1) {
	$own_define.="$r\n";
      }
    }
  }
  else {
    $web_path=implode("/", array_slice(explode("/", $_SERVER["REQUEST_URI"]), 0, -1));
    $passwd_file="\$file_path/passwd";
    $group_file="\$file_path/group";

    $max_res=1280;
    $normal_res=600;
    $index_res=200;
    $thumb_res=64;
    $resolutions=array(64, 200, 600);
    $convert_options="-filter Hamming -quality 85 -interlace PLANE";
    $anon_user="anonymous";
    $default_group="users";
    $extensions=array();
  }
}


if(!$_REQUEST[submit_ok]) {
  print "<form method='post'>\n";
  print "<table>\n";
  foreach($params as $key=>$conf) {
    print "<tr><td>\${$key}=</td>\n<td>";
    switch($conf[type]) {
      case "string":
      case "int":
	print "<input name='data[$key]' value=\"{$$key}\">\n";
	break;
      case "array_int":
	print "<input name='data[$key]' value=\"".implode(" ", $$key)."\">\n";
	break;
      case "select":
	print "<select name='data[$key]'>\n";
	foreach($conf[values] as $k=>$v) {
	  print "<option value='$k'>$v</option>\n";
	}
	print "</select>\n";
      default:
    }
    print "</td>\n";

    print "<td>\n";
    if($conf[check]) {
      $erg=$conf[check]($$key);
      if(!$erg)
	print "+";
      else
	print "-";
    }
    else
      print "+";
    print "</td>\n";

    print "<td>$conf[desc]</td></tr>\n";
  }
  print "</table>\n";

  print "<table>\n";
  print "<tr><th colspan='4'>Extensions</th></tr>\n";
  $d=opendir("extensions/");
  while($f=readdir($d)) {
    if(ereg("_data.php$", $f)) {
      include("extensions/$f");
    }
  }

  print "<tr><th colspan='3'>Custom Definitions</th></tr>\n";
  print "<tr><td colspan='3'>\n";
  print "<textarea cols='80' rows='10' name='data[own_define]'>$own_define</textarea>\n";
  print "</td></tr>\n";
  print "</table>\n";
  print "<input type='submit' name='submit_ok' value='Ok'>\n";
  print "</form>\n";
}


?>
</body></html>

<?
function html_var_to_js($v) {
  if(!isset($v))
    return "null";

  switch(gettype($v)) {
    case "integer":
    case "double":
      $ret=$v;
      break;
    case "boolean":
      $ret=$v?"true":"false";
      break;
    case "string":
      $ret="\"".implode("\\n", explode("\n", addslashes($v)))."\"";
      break;
    case "array":
      $ar_keys=array_keys($v);
      if(($ar_keys[0]=="0")&&($ar_keys[sizeof($ar_keys)-1]==(string)(sizeof($ar_keys)-1))) {
        $ret1=array();
        foreach($v as $k1=>$v1) {
          $ret1[]=html_var_to_js($v1);
        }
        $ret="new Array(".implode(", ", $ret1).")";
      }
      else {
        $ret1=array();
        foreach($v as $k1=>$v1) {
          $ret1[]="$k1:".html_var_to_js($v1);
        }
        $ret="{ ".implode(", ", $ret1)." }";
      }
      break;
    default:
      $ret="";
  }

  return $ret;
}

function html_add_formated_text($key, $text) {
  $text=strtr(utf8_encode($text), array(">"=>"&gt;", "<"=>"&lt;", "&"=>"&amp;"));

  $i=0;
  while($i+1024<strlen($text)) {
    $anz=1024;
    
    if(($a=strrpos(substr($text, $i, $anz), "\n"))!==false) {
      $anz=$a;
    }

    while(eregi("&[a-zA-Z0-9]*$", substr($text, $i, $anz))) {
      $anz--;
    }

    print "<$key>".substr($text, $i, $anz)."</$key>\n";
    $i+=$anz;
  }

  print "<$key>".substr($text, $i)."</$key>\n";
}

$export_vars_todo=array();

function html_export_var($vars) {
  global $request_type;
  global $finished_http_header;
  global $export_vars_todo;

  $export_vars_todo=array_merge($export_vars_todo, $vars);

  if($request_type!="xml") {
    if(!$finished_http_header)
      return;

    print "<script type='text/javascript'>\n<!--\n";
    foreach($export_vars_todo as $k=>$v) {
      print "var $k=".html_var_to_js($v).";\n";
    }
    print "//-->\n</script>\n";
  }
  else {
    foreach($export_vars_todo as $key=>$value) {
      //print "<$key>".html_var_to_js($value)."</$key>\n";
      html_add_formated_text($key, html_var_to_js($value));
    }
  }
  $export_vars_todo=array();
}



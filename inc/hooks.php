<?
$hooks=array();

function modify_var($why, $vars) {
  global $hooks;

  if($hooks[$why])
    foreach($hooks[$why] as $h) {
      $h(&$vars);
    }
}

function add_hook($why, $fun) {
  global $hooks;

  $hooks[$why][]=$fun;
}


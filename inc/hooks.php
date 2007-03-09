<?
$hooks=array();

function call_hooks($why, $vars) {
  global $hooks;

  if($hooks[$why])
    foreach($hooks[$why] as $h) {
      $h(&$vars);
    }
}

function register_hook($why, $fun) {
  global $hooks;

  $hooks[$why][]=$fun;
}

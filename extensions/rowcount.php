<?
function rowcount_split_album_pages($list) {
  global $cols;
  global $rows;
  global $rowcount_album_pages;

  if($rowcount_album_pages)
    return $rowcount_album_pages;

  $rowcount_album_pages=array();
  $cur_page=0;
  $count_rows=0;
  $count_this_row=0;

  if($rows==-1)
    return array($list);

  $pos=$cols;
  if($list) foreach($list as $el) {
//print "$count_rows $cur_page $pos $cols<br>";
    $colspan=$el->colspan();
    if($colspan>$cols)
      $colspan=$cols;

    if($pos<$colspan) {
      if($count_rows>=$rows-1) {
        $cur_page++;
        $count_rows=0;
      }
      else {
        if($colspan!=$cols)
          $count_rows++;
      }

      $pos=$cols;
    }

    if($pos==$cols) {
    }

    $rowcount_album_pages[$cur_page][]=$el;
    $pos-=$colspan;
  }

//    foreach($album_pages as $cur_page=>$els) {
//      print "<h4>$cur_page</h4>\n";
//      foreach($els as $e) {
//        print $e->type." ".$e->file_name()."<br>\n";
//      }
//    }

  return $rowcount_album_pages;
}

function rowcount_find_album_page($list, $img) {
  global $rowcount_album_pages;
  global $rows;

  if($rows==-1)
    return 0;

  rowcount_split_album_pages($list);

  for($i=1; $i<sizeof($rowcount_album_pages); $i++) {
    if($img<$rowcount_album_pages[$i][0]->get_index())
      return $i-1;
  }

  return sizeof($rowcount_album_pages)-1;
}

function rowcount_album_nav($list, $img) {
  global $rowcount_album_pages;
  global $page;

  $album_pages=rowcount_split_album_pages($list);

  $album_page=rowcount_find_album_page($list, $img);
  $anz=sizeof($album_pages);

  if($anz<=1)
    return "";

  $ret.="<table class='album_nav' align='center'>\n";
  $ret.="<tr><td class='nav_left'>\n";
  if($album_page==0)
    $ret.="<img src='".url_img("arrow_left_dark.png")."' class='nav_left' alt='&lt;' title='$lang_str[nav_prev]'> ";
  else
    $ret.="<a href='".url_page(array("page"=>$page, "img"=>$album_pages[$album_page-1][0]->get_index()))."' accesskey='p'>".
          "<img src='".url_img("arrow_left.png")."' class='nav_left' class='nav_left' alt='&lt;' title='$lang_str[nav_prev]'></a> ";
  $ret.="</td><td class='nav_text'>\n";
  $start=$album_page-5;
  $end=$album_page+5;
  if($start<0) {
    $end-=$start;
    $start=0;
  }
  if($end>=$anz) {
    $start+=($anz-$end);
    $end=$anz-1;
  }
  if($start<0)
    $start=0;

  if($start>0) {
    $start+=2;
    $ret.="<a href='".url_page(array("page"=>$page, "img"=>0))."'>1</a> ";
    $ret.="... ";
  }

  if($end<$anz-1) {
    $end-=2;
  }

  for($num=$start; $num<=$end; $num++) {
    if($num==$album_page) {
      $ret.=($num+1)." ";
    }
    else {
      $ret.="<a href='".url_page(array("page"=>$page, "img"=>$album_pages[$num][0]->get_index()))."'>".($num+1)."</a> ";
    }
  }

  if($end<$anz-1) {
    $ret.="... ";
    $ret.="<a href='".url_page(array("page"=>$page, "img"=>$album_pages[$anz-1][0]->get_index()))."'>$anz</a>";
  }

  $ret.="</td><td class='nav_right'>\n";
  if($album_page==$anz-1)
    $ret.="<img src='".url_img("arrow_right_dark.png")."' class='nav_right' alt='&gt;' title='$lang_str[nav_next]'> ";
  else
    $ret.="<a href='".url_page(array("page"=>$page, "img"=>$album_pages[$album_page+1][0]->get_index()))."' accesskey='n'>".
          "<img src='".url_img("arrow_right.png")."' class='nav_right' class='nav_right' alt='&gt;' title='$lang_str[nav_next]'></a> ";

  $ret.="</td></tr>\n";
  $ret.="</table>\n";

  return $ret;
}

function rowcount_modify_list($list) {
  $img=$_REQUEST["img"];

  $list=rowcount_split_album_pages($list);
  $p=rowcount_find_album_page($list, $img);
  $list=$list[$p];
  $nav=rowcount_album_nav($list, $img);
  add_text_item("album_subheading", $nav);
  add_text_item("album_end", $nav);
}

global $lang_str;
global $rows;
global $cols;

if(!$cols)
  $cols=$_SESSION["cols"];
if(!$cols)
  $cols=4;

if(!$rows)
  $rows=$_SESSION["rows"];
if(!$rows) {
  $rows=6;
  $_SESSION["rows"]=6;
  session_register("rows");
}

$ret="";
$ret.="$lang_str[nav_rows]:\n";
for($i=4;$i<7;$i++) {
  $ret.="<input type='submit' onClick='rowcount_change_rows($i)' class='";
  $ret.=($rows==$i?"toolbox_input_active":"toolbox_input");
  $ret.="' id='rows_$i' title=\"$lang_str[tooltip_change_rows]\" value=\"$i\">\n";
}
$ret.="<input type='submit' onClick='rowcount_change_rows(-1)' class='";
$ret.=($rows==-1?"toolbox_input_active":"toolbox_input");
$ret.="' id='rows_-1' title=\"$lang_str[tooltip_change_rows]\" value=\"&infin;\">\n";
$ret.="<br>\n";

add_toolbox_item("album_toolbox", $ret);
register_hook("album_modify_list", rowcount_modify_list);

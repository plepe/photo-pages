function rowcount_done_change(xmldata) {
  location.reload();
}

function rowcount_change_rows(c) {
  if(ob=document.getElementById("rows_"+rows))
    ob.className='toolbox_input';

  rows=c;
  ob=document.getElementById("rows_"+rows);
  ob.className='toolbox_input_active';

  set_session_vars({ rows: rows }, rowcount_done_change);
}



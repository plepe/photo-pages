function langchoose_reload() {
  location.reload();
}

function langchoose_change() {
  var ob=document.getElementById("langchoose_select");

  language=ob.value;

  set_session_vars({ language: language }, langchoose_reload);
}

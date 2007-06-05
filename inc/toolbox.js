toolbox_data=new Array();

function reset_toolbox(toolbox) {
  toolbox_data[toolbox]="";
}

function show_toolbox(toolbox) {
  var ret;

  if((toolbox_data[toolbox])&&(toolbox_data[toolbox]!="")) {
    ret ="<div class='toolbox' id='toolbox_"+toolbox+"'>\n";
    ret+=toolbox_data[toolbox];
    ret+="</div>\n";
  }

  return ret;
}

function add_toolbox_item(toolbox, item) {
  toolbox_data[toolbox]+=item;
}

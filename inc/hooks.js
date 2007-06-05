var hooks=new Array();

function call_hooks(why, vars, param1, param2, param3, param4) {
  if(hooks[why])
    for(var i=0; i<hooks[why].length; i++) {
      hooks[why][i](vars, param1, param2, param3, param4);
    }
}

function register_hook(why, fun) {
  if(!hooks[why])
    hooks[why]=new Array();

  hooks[why].push(fun);
}

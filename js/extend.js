var Extend = {
  items: {},

  add: function(tag, func, priority)
  {
    if (priority == undefined) {
      priority  = 10;
    }

    if (!Extend.items[tag]) {
      Extend.items[tag] = new Array();
    }

    if (!Extend.items[tag][priority]) {
      Extend.items[tag][priority] = new Array();
    }

    Extend.items[tag][priority].push(func);
  },

  apply: function(tag, thisArg, arguments)
  {
    if (Extend.items[tag] == undefined) {
      return;
    }

    if (thisArg == undefined) {
      thisArg = this;
    }

    Extend.items[tag].sort(function(a,b){return a - b});
    for (var priority in Extend.items[tag]) {
      for (var func in Extend.items[tag][priority]) {
        Extend.items[tag][priority][func].call(thisArg, arguments);
      }
    }
  }
};
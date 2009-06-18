
/**
 * See: http://www.softwareishard.com/blog/firebug/tracing-console-for-firebug/
 */

FBL.ns(function() { with (FBL) {
  
  Firebug.ZendToolbarModule = extend(Firebug.Module,
  {
      initialize: function()
      {
          Firebug.Module.initialize.apply(this, arguments);
  
          // Open console automatically if the pref says so.
          if (Firebug.getPref("extensions.Zend_Toolbar", "alwaysOpenTraceConsole")) {
            Firebug.TraceModule.openConsole("extensions.Zend_Toolbar");
          }
      },
  
      shutdown: function()
      {
          Firebug.Module.shutdown.apply(this, arguments);
      }
  });
  
  
  Firebug.registerModule(Firebug.ZendToolbarModule);

}});



var ZFTrace = Components.classes["@joehewitt.com/firebug-trace-service;1"];
if(ZFTrace) {
    ZFTrace = ZFTrace.getService(Components.interfaces.nsISupports)
                     .wrappedJSObject.getTracer("extensions.Zend_Toolbar");
}

Zend_Toolbar.LIB.console.enabled = true;
Zend_Toolbar.LIB.console.dump = function(label, variable, group) {
    if(!group) group = 'Temporary';
    if(ZFTrace && ZFTrace['DBG_'+group]) {
      ZFTrace.dump("extensions.Zend_Toolbar", '[' + group + '] ' + label, variable);
    }    
};

var Zend_Toolbar = {};

(function()
{
    // Registration
    var namespaces = [];
    this.ns = function(fn)
    {
        var ns = {};
        namespaces.push(fn, ns);
        return ns;
    };
    
    // Initialization
    this._initialize = function()
    {
        for (var i = 0; i < namespaces.length; i += 2) {
            var fn = namespaces[i];
            var ns = namespaces[i+1];
            fn.apply(ns);
        }
        
        Zend_Toolbar.initialize();
    };
    
    // Clean up
    this._shutdown = function()
    {
        Zend_Toolbar.shutdown();
        
        window.removeEventListener('load', Zend_Toolbar._initialize, false);
        window.removeEventListener('unload', Zend_Toolbar._shutdown, false);
    };
    
    // Register handlers to maintain extension life cycle.
    window.addEventListener('load', Zend_Toolbar._initialize, false);
    window.addEventListener('unload', Zend_Toolbar._shutdown, false);
}).apply(Zend_Toolbar);

Zend_Toolbar.LIB = {
    // Extension singleton shortcut
    app: Zend_Toolbar,

    // XPCOM shortcuts
    Cc: Components.classes,
    Ci: Components.interfaces,
    Cr: Components.results,

    // In case firebug (tracing console) is not installed (see README)
    console: {
        enabled: false,
        dump: function() {}
    },
};



    

/**
 * Zend_Toolbar Bootstrap
 */

Zend_Toolbar.ns(function() { with (Zend_Toolbar.LIB) {

  
  var pageListener = {

    onStateChange: function(aWebProgress, aRequest, aFlag, aStatus)
    {
      // TODO: Only respond to top-level page loads 
      if(aFlag & Ci.nsIWebProgressListener.STATE_STOP)
      {
        console.dump('Received Response from: ' + aRequest.name, aRequest, 'Wildfire');
        
        aRequest.visitResponseHeaders({
          visitHeader: function(name, value)
          {
            app.wildfireToolbarPlugin.channel.messageReceived(name, value);
          }
        });
        app.wildfireToolbarPlugin.channel.allMessagesReceived();

        if(app.wildfireToolbarPlugin.hasData()) {

          var data = app.wildfireToolbarPlugin.getData();

          console.dump('Data received', data, 'Wildfire');


          // TODO: Deal with data

          
        } else {
          console.dump('No data received', null, 'Wildfire');
        }
      }
      return 0;
    },
  
    onLocationChange: function(aProgress, aRequest, aURI) {return 0;},
    onProgressChange: function() {return 0;},
    onStatusChange: function() {return 0;},
    onSecurityChange: function() {return 0;},
    onLinkIconAvailable: function() {return 0;},
    
    QueryInterface: function(aIID)
    {
      if (aIID.equals(Ci.nsIWebProgressListener) ||
          aIID.equals(Ci.nsISupportsWeakReference) ||
          aIID.equals(Ci.nsISupports)) {
        return this;
      }
      throw Cr.NS_NOINTERFACE;
    }
  }
  
  Zend_Toolbar.initialize = function() {

    app.wildfireToolbarPlugin = new Wildfire.Plugin.FrameworkToolbar();
    app.wildfireToolbarPlugin.init();

    gBrowser.addProgressListener(pageListener, Ci.nsIWebProgress.NOTIFY_STATE_DOCUMENT);
    
  }

  Zend_Toolbar.shutdown = function() {

    gBrowser.removeProgressListener(pageListener, Ci.nsIWebProgress.NOTIFY_STATE_DOCUMENT);
    
  }

}});

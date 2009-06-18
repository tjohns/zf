
/**
 * See: http://www.softwareishard.com/blog/planet-mozilla/firefox-extensions-global-namespace-pollution/
 */

var Zend_Toolbar = {};

(function() {
// Registration
var namespaces = [];
this.ns = function(fn) {
    var ns = {};
    namespaces.push(fn, ns);
    return ns;
};
// Initialization
this._initialize = function() {
    for (var i=0; i<namespaces.length; i+=2) {
        var fn = namespaces[i];
        var ns = namespaces[i+1];
        fn.apply(ns);
    }
    Zend_Toolbar.initialize();
};
// Clean up
this._shutdown = function() {
    Zend_Toolbar.shutdown();
    window.removeEventListener("load", Zend_Toolbar._initialize, false);
    window.removeEventListener("unload", Zend_Toolbar._shutdown, false);
};
// Register handlers to maintain extension life cycle.
window.addEventListener("load", Zend_Toolbar._initialize, false);
window.addEventListener("unload", Zend_Toolbar._shutdown, false);
}).apply(Zend_Toolbar);


Zend_Toolbar.LIB = {

    // Extension singleton shortcut
    app: Zend_Toolbar,

    // XPCOM shortcuts
    Cc: Components.classes,
    Ci: Components.interfaces,

    // In case firebug (tracing console) is not installed (see README)
    zfconsole: { dump: function() {} },

};

    
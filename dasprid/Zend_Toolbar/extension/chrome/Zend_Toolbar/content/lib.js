
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


    
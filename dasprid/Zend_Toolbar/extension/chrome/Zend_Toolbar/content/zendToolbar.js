/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Toolbar
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Toolbar
 *
 * @category   Zend
 * @package    Zend_Toolbar
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
var Zend_Toolbar = function()
{   
    /**
     * Public methods and properties
     */
    var public = {
        /**
         * Initialize the toolbar
         */
        construct: function()
        {
            private.wildfireToolbarPlugin = new Wildfire.Plugin.FrameworkToolbar();
            private.wildfireToolbarPlugin.init();
                   
            gBrowser.addProgressListener(private.pageListener, private.Ci.nsIWebProgress.NOTIFY_STATE_DOCUMENT);
            
            document.getElementById('zend-toolbar-version').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 0');
            document.getElementById('zend-toolbar-variables').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 1');
            document.getElementById('zend-toolbar-queries').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 2');
            document.getElementById('zend-toolbar-errors').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 3');
            document.getElementById('zend-toolbar-filesize').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 4');
            document.getElementById('zend-toolbar-runtime').setAttribute('oncommand', 'document.getElementById("zend-toolbar-deck").hidden = false; document.getElementById("zend-toolbar-deck").selectedIndex = 5'); 
        },

        /**
         * Shutdown the toolbar
         */
        destruct: function()
        {
            gBrowser.removeProgressListener(private.pageListener, private.Ci.nsIWebProgress.NOTIFY_STATE_DOCUMENT);
        }
    }
    
    /**
     * Private methods and properties
     */
    var private = {
        /**
         * XPCOM shotcuts
         */
        Cc: Components.classes,
        Ci: Components.interfaces,
        Cr: Components.results,
        
        /**
         * Wildfire toolbar plugin
         */
        wildfireToolbarPlugin: null,
        
        show: function() {
        },
        
        /**
         * Display the ZF version
         *
         * @param string version
         */
        showVersion: function(version)
        {
            document.getElementById('zend-toolbar-version').label = version;
        },

        /**
         * Display all variables
         *
         * @param object variables
         */
        showVariables: function(variables)
        {
            var tree = document.getElementById('zend-toolbar-variables-tree');

            while (tree.childNodes[1].hasChildNodes()) {
                tree.childNodes[1].removeChild(tree.childNodes[1].childNodes[0]);
            }

            private.addVariableChildren(tree.childNodes[1], variables);
        },
        
        /**
         * Add variable children recursively
         *
         * @param object treeChildren
         * @param object variables
         */
        addVariableChildren: function(treeChildren, variables)
        {
            for (var name in variables) {
                var item = document.createElement('treeitem');
                var row  = document.createElement('treerow');

                var cell = document.createElement('treecell');
                cell.setAttribute('label', name);
                row.appendChild(cell);

                var cell = document.createElement('treecell');
                cell.setAttribute('label', variables[name]);
                row.appendChild(cell);

                item.appendChild(row);
                Firebug.Console.log('test');
                if (variables[name] instanceof Object) {
                    if (variables[name] instanceof Array && variables[name].length == 0) {
                        cell.setAttribute('label', 'Array (empty)');
                    } else {
                        cell.setAttribute('label', 'Array');
                    }
                
                    item.setAttribute('container', 'true');
                    
                    var children = document.createElement('treechildren');
                    
                    private.addVariableChildren(children, variables[name]);
                    
                    item.appendChild(children);
                }

                treeChildren.appendChild(item);
            }
        },
            
        /**
         * Page load listener
         * 
         * @todo Only respond to top-level page loads
         */
        pageListener: {
            onStateChange: function(aWebProgress, aRequest, aFlag, aStatus)
            {
                if (aFlag & private.Ci.nsIWebProgressListener.STATE_STOP) {
                    aRequest.QueryInterface(Ci.nsIHttpChannel);
                    aRequest.visitResponseHeaders({
                        visitHeader: function(name, value) 
                        {
                            private.wildfireToolbarPlugin.channel.messageReceived(name, value);
                        }
                    });

                    private.wildfireToolbarPlugin.channel.allMessagesReceived();

                    if (private.wildfireToolbarPlugin.hasData()) {
                        var data = private.wildfireToolbarPlugin.getData();

                        // Show the toolbar
                        document.getElementById('zend-toolbar-statusbar').hidden = false;
                        
                        private.showVersion(data.version);
                        private.showVariables(data.variables);
                    } else {
                        document.getElementById('zend-toolbar-statusbar').hidden = true;
                    }
                }
                
                return 0;
            },
        
            onLocationChange: function(aProgress, aRequest, aURI)
            {
                return 0;
            },
            
            onProgressChange: function()
            {
                return 0;
            },
            
            onStatusChange: function()
            {
                return 0;
            },
            
            onSecurityChange: function()
            {
                return 0;
            },
            
            onLinkIconAvailable: function()
            {
                return 0;
            },
          
            QueryInterface: function(aIID)
            {
                if (aIID.equals(private.Ci.nsIWebProgressListener) ||
                    aIID.equals(private.Ci.nsISupportsWeakReference) ||
                    aIID.equals(private.Ci.nsISupports))
                {
                    return this;
                }
                
                throw private.Cr.NS_NOINTERFACE;
            }
        }
    };
    
    return public;
}();   

/**
 * Register constructor and destructor
 */
window.addEventListener('load',   Zend_Toolbar.construct, false);
window.addEventListener('unload', Zend_Toolbar.destruct,  false);

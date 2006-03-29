var __CLASSES = new Array();
 function isArray(a)
 {
     return(a && a.constructor == Array);
 }
var Class = {
    create:function(name, initObject) {
	var superClass = initObject ? initObject.superClass : null;
	if (typeof superClass == "undefined")
	    superClass = null;

	if (! superClass) {
	    superClass = Class.getClassFromName('_Object');
	    if (typeof superClass == "undefined")
		superClass = null;
	} 
	var newClass = new Object();
	Class.extend(newClass, Class);
	newClass.superClass = superClass;

	newClass.name = name;
	newClass.constructor = 
	    function() { 
		this.__construct.apply(this, arguments);
	    };
	__CLASSES[name] = newClass;
	eval(name + " =  newClass.constructor");
	if (superClass)
	    Class.extend(newClass.constructor.prototype, superClass.prototype);

	Class.extend(newClass.constructor.prototype, { 
	    getClass: function() { 
		          return(Class.getClassFromName(name));
	            }
         });

	var variables = initObject ? initObject.variables : null;
	if (typeof variables == "undefined")
	    variables = null;
	var methods = initObject ? initObject.methods : null;
	if (typeof methods == "undefined")
	    methods = null;
	if (! methods || (methods && !methods.__construct)) {
	    Class.extend(newClass.constructor.prototype, 
	                 {__construct: function () {
			     Class.extend(this, variables);
                         }});
	}
	if (methods) {
	    Class.extend(newClass.constructor.prototype, methods);
	}
	var staticMethods = initObject ? initObject.staticMethods : null;
	if (typeof staticMethods == "undefined")
	    staticMethods = null;
	if (staticMethods) {
	    Class.extend(newClass.constructor, staticMethods);
	}

	var constants = initObject ? initObject.constants : null;
	if (typeof constants == "undefined")
	    constants = null;
	if (constants) {
	    Class.extend(newClass.constructor, constants);
	}
	return(newClass.constructor);
    },
    extend: function(cls, superCls) {
	for (property in superCls) {
	    cls[property] = superCls[property];
	}
    },
    getName: function() {
	return(this.name);
    },
    getClassFromName: function(name) {
	return(__CLASSES[name]);
    },
    getSuper: function() {
	return(this.superClass);
    },
    injectClasses : function(object) {
	if (! (typeof object == "object")) return;
	var cls = null;
	if (object.__className && (cls = __CLASSES[object.__className])) { 
	    Class.extend(object, cls.constructor.prototype);
	    for (var obj in object) {
		Class.injectClasses(obj);
	    }
	}
    }

}
Class.create('_Object');

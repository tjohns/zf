var isIE = (window.navigator.appName == "Microsoft Internet Explorer");
var ZAjaxEngine = {

    Version : '0.1',

    AJAX_JSON_ENDPOINT: 'ajaxInvocation.php',
    REPLACE : 1,
    PREPEND : 2,
    APPEND  : 3,
    VALIDATION_FAILURE : 1,
    CLEAN_PARAM : '=', 
    PARAM_SEPARATOR : '&',
    PARAM_START : '?',

    invokeRemoteMethod: function(obj, methodName, argNames, argValues) {
	var request = new ZAjaxEngine.Request(ZAjaxEngine.AJAX_JSON_ENDPOINT, 
					      ZAjaxEngine.Request.POST,
					      false);
	var body;
	body = "__methodName=" + encodeURIComponent(methodName);
	body += "&__self=" + 
	    encodeURIComponent(ZAjaxEngine.JSONEncoder.encode(obj));
	for (var i = 0; i < argNames.length; i++) {
	    body += "&" + encodeURIComponent(argNames[i]) + "=" + 
	      encodeURIComponent(ZAjaxEngine.JSONEncoder.encode(argValues[i]));
	}

	var result = request.sendRequest(body);
	if (result.status == 200) {
	    resultObject = ZAjaxEngine.JSONEncoder.decode(result.responseText);
	    if (typeof resultObject == "object") {
		Class.injectClasses(resultObject);
		var cls;
		if (resultObject.getClass &&  (cls = resultObject.getClass())) {
		    if (cls.getName() == 'ZJSONExceptionWrapper') {
			throw resultObject.getMessage();
		    }
		}
	    }
	    return(resultObject);
	} else {
	    throw "Transport error:" + result.status;
	}
    },
    invokeURL: function(url,  params, method, async, eventHandlers) {
	aync = true;
	if (! method) {
	    method = ZAjaxEngine.Request.GET;
	}
	if (typeof params == "object") {
	    var newParams = '';
	    for (key in params) {
		if (newParams != '') {
		    newParams += ZAjaxEngine.PARAM_SEPARATOR;
		}
		newParams += key + ZAjaxEngine.CLEAN_PARAM + encodeURIComponent(params[key]);
	    }
	    params = newParams;
	}
	if (method == ZAjaxEngine.Request.GET) {
	    if (url.charAt(url.length - 1) != ZAjaxEngine.PARAM_START) {
		url += ZAjaxEngine.PARAM_START;
	    }	    
	    url += params;
	} 
	var request = new ZAjaxEngine.Request(url, method,  async, eventHandlers);
	var result = request.sendRequest(method != ZAjaxEngine.Request.GET ? params : null);
	if (! async) {
	    if (result.status != 200) {
		throw "Transport Error:" + result.status + ", " + url;
	    }
	    return(result.responseText);
	}
	
    },
    addEventListener: function(sourceElement, eventName, target, capture) {
	if (isIE) {
	    sourceElement.attachEvent('on' + eventName, target);
	} else {
	    sourceElement.addEventListener(eventName, target, capture);
	}
    },
    getOffsetPosition: function(element) {
	var node = element;
	var left = 0, top = 0;
	do {
	    left += node.offsetLeft;
	    top += node.offsetTop;
	    node = node.offsetParent;
	} while (node);
	return({left:left, top:top});
    },
    getFormParameters: function (form) {
	var inputs = form.getElementsByTagName('INPUT');
	result = '';
	if (! inputs || inputs.length == 0)
	    return(result);
	for (var i = 0; i < inputs.length; i++) {
	    var element = inputs.item(i);
	    var type = element.getAttribute("TYPE");
	    if (! type) {
		continue;
	    }
	    var name = element.getAttribute("NAME");
	    if (! name) {
		continue;
	    }
	    var value = null;
	    switch (type.toUpperCase()) {
	    case "TEXT": 
	    case "PASSWORD":
	    case "HIDDEN":
		value = element.value;
		break;
	    case "CHECKBOX":
	    case "RADIO":
		if (element.checked != true) {
		    continue;
		}
		value = (element.value ? element.value : "on");
		break;
	    case "FILE":
		//@todo file attach?
		alert("FILE input types are currently not supported for AJAX submission");
		value = element.value;
		break;
	    default:
		continue;
	    }
	    if (result != '')
		result += '&';
	    result += name + "=" + value;
	}
	var inputs = form.getElementsByTagName('SELECT');
	if (! inputs || inputs.length == 0)
	    return(result);
	for (var i = 0; i < inputs.length; i++) {
	    var element = inputs.item(i);
	    var name = element.getAttribute("NAME");
	    var isMultiple = element.getAttribute("MULTIPLE");
	    if (isMultiple && name.length > 2 && 
		name.substr(name.length - 2, 2) == '[]') {
		for (var j = 0; j < element.options.length; j++) {
		    if (element.options[j].selected) {
			if (result != '') {
			    result += '&';
			}
			result += name + "=" + element.options[j].value;
		    }
		}
	    } else if (element.selectedIndex) {
		if (result != '') {
		    result += '&';
		}
		result += name + "=" + element.options[element.selectedIndex].value;
	    }
	}
	return(result);
    }
}
Function.prototype.bind = function(object) {
  var __method = this;
  return function() {
    return __method.apply(object, arguments);
  }
}
Function.prototype.bindAsEventListener = function(object) {
  var __method = this;
  return function(event) {
    return __method.call(object, event || window.event);
  }
}
Class.create('ZAjaxEngine.Request', {
    constants: {
        POST        : "POST",
        GET         : "GET",
	REPLACE	    : 1,
	PREPEND     : 2,
        APPEND      : 3

    }, 
    methods : {
        __construct: function(url, method, async, eventHandler, targetID, position) {
            this.url = url;
	    eventHandler = eventHandler ? eventHandler : this.getBoundEventHandler();
	    this.events = [null, 
			   eventHandler.onLoading,
			   eventHandler.onLoaded,
			   eventHandler.onInteractive,
			   eventHandler.onComplete];
	    this.method = (method ? 
			   method.toUpperCase() : 
			   ZAjaxEngine.Request.POST);
	    this.async = async;
	    this.setTarget(targetID, position);
        },
        sendRequest : function(params, headers) {
	    params = (params ? params : '');
	    if (typeof params == "object") {
		var newParams = '';
		for (key in params) {
		    if (newParams != '') {
			newParams += ZAjaxEngine.PARAM_SEPARATOR;
		    }
		    newParams += key + ZAjaxEngine.CLEAN_PARAM + encodeURIComponent(params[key]);
		}
		params = newParams;
	    }
	    headers = (headers ? headers : new Array());
	    this.request = this.getRequest();
    	    if (this.method == ZAjaxEngine.Request.GET && params) {
	        this.url += (this.url.indexOf('?') >= 0 ? '&' : '?') + params;
	    }
	    if (this.async) 
              this.request.onreadystatechange = this.onStateChange.bind(this);
	    this.request.open(this.method, this.url, this.async);
	    if (this.method == ZAjaxEngine.Request.POST) {
		headers['Content-type'] = 'application/x-www-form-urlencoded';
	    }
	    try {
	        for (var header in headers) {
		    this.request.setRequestHeader(header, headers[header]);
	        }
		this.request.send(this.method == ZAjaxEngine.Request.POST ?  
			          params : null);
	    } catch (ex) {
		// TBD: Error Handling
		alert("Fixme:" + ex);
	    }
	    this.onStateChange();
	    return(this.request);
        },
	getBoundEventHandler: function() {
	    var result = {};
	    result.onComplete = this.onComplete.bind(this);
	    result.onLoading  = this.onLoading.bind(this);
	    result.onLoaded   = this.onLoaded.bind(this);
	    result.onInteractive = this.onInteractive.bind(this);
	    return(result);
	    
	},
	onComplete: function() {
	    this.deliverPayload(this.request.responseText);
	},
        onLoading: function() {
	},
	onLoaded: function() {
	},
	onInteractive: function() {
	},
        invoke: function(params, headers) {
	    var result = this.sendRequest(params, headers);
	},
        setParameters: function(params) {
	    this.parameters = params;
	},
	getParameters: function() {
	    return(this.parameters);
	},
	deliverPayload: function(payload) {
	    if (this.htmlID) {
		var el = document.getElementById(this.htmlID);
		if (! el) {
		    throw "Element not found:" + this.htmlID;
		}
		var html = el.innerHTML;
		switch (this.position) {
		case ZAjaxEngine.Request.REPLACE: break;
		case ZAjaxEngine.Request.PREPEND: payload = payload + html; break;
		case ZAjaxEngine.Request.APPEND: payload = html + payload; break;
		}
		el.innerHTML = payload;
	    }
	},
	setTarget: function(htmlID, position) {	
	    if (htmlID) {
		this.htmlID = htmlID;
	    }
	    this.position = (position ? position : ZAjaxEngine.Request.REPLACE);
	},
        getRequest : function() {
	    var result = null;
	    if (isIE) {
	        try {
		    result = new ActiveXObject('Msxml2.XMLHTTP');
	        } catch (ex) {
		    result = ActiveXObject('Microsoft.XMLHTTP');
	        }
	    } else {
	        result = new XMLHttpRequest();
	    }
	    return(result);
        },
        onStateChange: function () {
	    var handler = this.events[this.request.readyState];
	    if (handler && typeof handler != "undefined")
		handler(this.request);
        }
    }
});


Class.create('ZAjaxEngine.JSONEncoder', {
    staticMethods: {
	encode : function(value) {
	    if (! value)
		return("null");
	    visited = new Array();
	    result = this.encodeValue(value, visited);
	    return(result);
	},
        encodeValue : function(value, visited) {
	    if (! value)
		return("null");
	    var type = typeof value;
	    if (isArray(value)) 
		return(this.encodeArray(value, visited));
	    else if (type == "object")
		return(this.encodeObject(value, visited));
	    else
		return(this.encodeDatum(value, visited));
	},
        encodeObject : function(value, visited) {
	    if (! value)
		return("null");
	    visited[visited.length] = value;
	    var result = '{';
	    var clsName = (value.getClass ? value.getClass().getName() : null);
	    var cnt = 0;
	    if (clsName) {
		result += "\"__className\" : \"" + clsName + "\"";
		cnt++;
	    }
	    for (var propName in value) {
		var propValue = value[propName];
		if ((typeof propValue) == "function")
		    continue;
		if (propValue) {
		    if (cnt)
			result += ", ";
		    cnt++;
		    result += '"' + propName + '" : ';
		    result += this.encodeValue(propValue, visited);
		}
	    }
	    result +=  '}';
	    return(result);
        },
        encodeArray: function(value, visited) {
	    var result = '[';
	    var length = value.length;
	    for (i = 0; i < length; i++) {
	      if (i) result += ', ';
	      result += this.encodeValue(value[i], visited);	
	    }
	    result += ']';
	    return(result);
	},
	encodeDatum:  function(value, visited) {
	    var result = '';
	    if ((typeof value) == 'string') {
	      result += '"' +  value.replace(/\"/, '\"') + '"';	    
	    } else if ((typeof value) == 'number') {
	      result += value;
	    } else {
	      //TBD: What?
	    }
	    return(result);
        },
	decode : function(objSource) {
	    var obj = eval("(" + objSource + ")");
	    return(obj);
	}
    }
});

Class.create('ZJSONExceptionWrapper', {
 methods: {
     getMessage: function() {
	 return(this.msg);
     }
 },
 variables: {
     msg : null
 }
});

Class.create('ZAjaxEngine.Updater', {
 methods: {
     __construct: function(request, interval, loopCallback) {
	 this.request = request;
	 this.interval = (interval ? interval : 1000);
	 this.loopCallback = loopCallback;
	 this.stop = false;
	 this.cnt = 0;
    },
    getRequest: function() {
	return(this.request);
    },
    start: function() {
	 window.setTimeout(this.onTimeout.bind(this), this.interval);
    },
    stop: function() {
	this.stop = true;
    },
    onTimeout: function() {
	if (this.stop) {
	    return;
	}
	var cont = true;
	this.cnt ++;
	if (this.loopCallback) {
	    cont = this.loopCallback(this);
	}
	this.request.sendRequest(this.request.getParameters());
	if (cont) {
	    window.setTimeout(this.onTimeout.bind(this), this.interval);
	}
    }
 }
});

function voidFunction() {
}

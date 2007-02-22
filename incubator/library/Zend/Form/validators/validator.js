
Class.create('ZAjaxEngine.Validator', {
 constants : {
     ALL_VALIDATORS : new Array()
 },
 variables: {
         srcElement : null,
	 eventName  : null,	 
         callback   : null
 },
 methods: {
     __construct: function(srcElement, eventName, callback) {
	 this.srcElement = srcElement;
	 this.eventName = eventName;
	 this.callback = callback.bindAsEventListener(this);
	 ZAjaxEngine.addEventListener(srcElement, 
				      eventName, 
				      this.callback);
	 ZAjaxEngine.Validator.ALL_VALIDATORS[srcElement.getAttribute("ID")] = this;
     },
    addError: function(sourceElement, type, dspName, message) {
	if (! message) {
	    message = "General validation error";
	}
	var element = document.getElementById(dspName); 
	if (element) {
	    element.innerHTML = message;
	} else
	    alert(message);
    },
    clearError: function(sourceElement, type, dspName) {
	var element = document.getElementById(dspName); 
	if (element) {
	    element.innerHTML = '';
	} 
    },
    validate: function() {
	var event = { target : this.srcElement};
	this.callback(event);
    }
},
staticMethods: {
    validateTree: function(srcElement){
	if (srcElement.getAttribute) {
	    var validator = ZAjaxEngine.Validator.ALL_VALIDATORS[srcElement.getAttribute("ID")];
	    if (validator) {
		validator.validate();
	    }
	}
	if (srcElement.hasChildNodes()) {
	    for (var i = 0; i < srcElement.childNodes.length; i++) {
		this.validateTree(srcElement.childNodes.item(i));
	    }
	}
    }
}
});

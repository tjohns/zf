var ZAjaxEngine = {

    attachEvent: function(el, eventName, generalEventHandler) {

	if (! generalEventHandler)	   

	    generalEventHandler = ZAjaxEngine.generalEventHandler;

	if (window.Element) {

	    el.addEventListener(eventName, generalEventHandler, false);

	} else

	    el.attachEvent('on' + eventName, generalEventHandler);

    },

    fullName : function(el) {

	var result = el.id;

	el = el.parentNode;

	while (el) {

	    if (el.id) 

		result = el.id + "." + result;

	    el = el.parentNode;

	}

	return(result);

    },

    generalEventHandler: function(evt) {

	if (evt.type == 'click') {

	    var target = evt.target ? evt.target : evt.srcElement;	    

	    if (target.type == 'button') {

		var parent = target.parentNode;

		while (parent && parent.tagName.toLowerCase() != "form") 

		    parent = parent.parentNode;

		if (parent) {

		    var inputs = parent.getElementsByTagName("INPUT");

		    for (var i = 0; inputs && i < inputs.length; i++) {

			if (inputs.item(i).name.indexOf("__hiddendata") != -1) {

			    inputs.item(i).value = target.name + ":" + evt.type;

			    parent.submit();

			}			

		    }

		}

	    }

	}

    }

};




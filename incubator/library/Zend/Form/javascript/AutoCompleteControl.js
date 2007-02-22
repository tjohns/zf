var ZAjaxWidgets = new Object();
Class.create('ZAjaxWidgets.AutoCompleteBehavior', {
 constants : {
     ALL_CONTROLS : new Array(),
     ESCAPE : 27,
     UP     : 38,
     RETURN : 13,
     DOWN   : 40,
     TAB    : 9
 },
 staticMethods : {
     hideOptions : function() {
	 for (var key in this.ALL_CONTROLS) {
	     this.ALL_CONTROLS[key].hideOptions();
	 }
     }
},
 methods: {
     __construct: function(id, requestOrData, numberOfItems, cacheData) {
	 if (id) {
	     this.id = id;
	 }
	 this.cacheData = (cacheData ? true : false);
	 this.requestOrData = requestOrData;
	 this.oldAutoComplete = false;
	 this.element = null;
	 this.numberOfItems = (numberOfItems ? numberOfItems : -1);
	 this.selectedIndex = -1;
	 var el = document.getElementById(this.id);	
	 if (! el ) {
	     throw "Element Not found: " + this.id + ", cannot attach autocomplete behavior";	    
	 }
	 this.element = el;
	 this.captureKeyEvents();

	 this.oldAutoComplete = el.getAttribute('autocomplete');
	 el.setAttribute('autocomplete', false);

	 ZAjaxWidgets.AutoCompleteBehavior.ALL_CONTROLS[this.id] = this;

	 var pos = ZAjaxEngine.getOffsetPosition(el);
	 optionsPos = {left: pos.left, top: el.offsetHeight + pos.top};

	 this.options = document.createElement("DIV");
	 this.options.className = 'autocomplete';
	 el.parentNode.insertBefore(this.options, el);
	 this.options.style.left = optionsPos.left  + 'px';
	 this.options.style.top = optionsPos.top  + 'px';
	 this.options.style.width = (el.offsetWidth - (isIE ? 0 : 2))  + 'px';
	 this.options.style.zIndex = '1000';
	 this.options.style.visibility = 'hidden';
	 ZAjaxEngine.addEventListener(this.options, 'click', this.onOptionClick.bindAsEventListener(this), false);
	 ZAjaxWidgets.AutoCompleteBehavior.hideOptions();
	
    },
    onOptionClick  : function(evt) {
	var target = evt.srcElement ? evt.srcElement : evt.target;
	var index = target.getAttribute('index');
	this.hightlight(parseInt(index), -1);
	evt.returnValue = false;
	evt.cancelBubble = true;
	this.element.focus();

    },
    captureKeyEvents: function() {
	ZAjaxEngine.addEventListener(document, 'click', this.onClick.bindAsEventListener(this), false);
	ZAjaxEngine.addEventListener(this.element, 'keydown', this.onKeyDown.bindAsEventListener(this), false);
	ZAjaxEngine.addEventListener(this.element, 'keypress', this.onKeyPress.bindAsEventListener(this), false);
	ZAjaxEngine.addEventListener(this.element, 'keyup', this.onKeyUp.bindAsEventListener(this), false);
    },
    setDataSource : function(requestOrData) {
	this.requestOrData;
    },
    queryData: function() {
	var value = this.element.value;	
	var data = null;
	if (! this.data || !this.cacheData) {
	    if (isArray(this.requestOrData)) {
		data = this.requestOrData;
	    } else if (typeof this.requestOrData == "function") {
		data = this.requestOrData(this.element);
	    } else if (typeof this.requestOrData == "string") {
		data = eval(this.requestOrData);
	    } else
		throw "Illegal queryData provided for AutoComplete component";
	    this.data = data;
	}
	var result = new Array();
	for (i = 0; i < this.data.length; i++) {
	    if (this.data[i].substring(0, value.length) == value) {
		if (this.numberOfItems != -1 && result.length >= this.numberOfItems)
		    break;
		result[result.length] = this.data[i];
	    }
	}
	return(result);	
    },
    hideOptions: function() {
	this.options.style.visibility = 'hidden';
	this.isVisible = false;

    },
    showOptions: function() {

	var data = this.queryData();
	while (this.options.childNodes[0]) {
	    this.options.removeChild(this.options.childNodes[0]);
	}
	if (! data || data.length == 0) {
	    this.options.style.visibility = 'hidden';
	    return;
	}
	for (var i = 0; i < data.length; i++) {
	    var option = document.createElement("DIV");
	    option.appendChild(document.createTextNode(data[i]));
	    option.style.zIndex = '1000';
	    option.style.width = this.element.offsetWidth - (isIE ? 0 : 8);
	    option.className = 'autocomplete_item';
	    this.options.appendChild(option);
	    option.setAttribute("index", i);
	}
	this.selectedIndex = -1;
	this.options.style.visibility = 'visible';
	this.isVisible = true;
	var pos = ZAjaxEngine.getOffsetPosition(this.element);
	this.options.style.left = pos.left;
	this.options.style.top = pos.top + this.element.offsetHeight;
	
    },
    filterList: function() {
	this.showOptions();
    },
    onClick: function(evt) {
        ZAjaxWidgets.AutoCompleteBehavior.hideOptions();
    },   
    hightlight: function(id, delta) {
	if (this.selectedIndex != -1) {
	    if (this.options.childNodes && 
		this.options.childNodes.length >= this.selectedIndex) {
		if (this.options.childNodes.item(this.selectedIndex)) {
		    this.options.childNodes.item(this.selectedIndex).className = 'autocomplete_item';
		}
	    }
	} 
	if (id == -1) {
	    this.selectedIndex += delta;
	} else {	
	    this.selectedIndex  = id;
	}
	if (this.selectedIndex < 0) {
	    this.selectedIndex = this.options.childNodes.length - 1;
	} else if (this.selectedIndex >= this.options.childNodes.length) {
	    this.selectedIndex = 0;
	}

	if (this.options.childNodes && 
	    this.options.childNodes.length >= this.selectedIndex) {
	    if (this.options.childNodes.item(this.selectedIndex)) {
		this.options.childNodes.item(this.selectedIndex).className = 'autocomplete_item_highlighted';
		this.element.value = this.options.childNodes.item(this.selectedIndex).innerHTML;
	    }
	}

    },
    onKeyDown: function(evt) {
	var key = evt.keyCode;
	switch (key) {
	case ZAjaxWidgets.AutoCompleteBehavior.ESCAPE:
	    this.hideOptions();
	    break;
        case ZAjaxWidgets.AutoCompleteBehavior.TAB:
	    this.hideOptions();
	    break;
	}
	return(true);
    },
    onKeyPress: function(evt) {
	var key = evt.keyCode;
	switch (key) {
        case ZAjaxWidgets.AutoCompleteBehavior.DOWN:
	    if (! this.isVisible) {
		this.showOptions(true);
	    } else {
		this.hightlight(-1, 1);
	    }
	    break;
        case ZAjaxWidgets.AutoCompleteBehavior.UP:
	    if (! this.isVisible) {
		this.showOptions(true);
	    } else {
		this.hightlight(-1, -1);
	    }
	    break;
	}
	return(true);
    },
    onKeyUp: function(evt) {
	var key = evt.keyCode;
	switch (key) {
        case ZAjaxWidgets.AutoCompleteBehavior.DOWN:
	    if (isIE) {
		if (! this.isVisible) {
		    this.showOptions(true);
		} else {
		    this.hightlight(-1, 1);
		}
	    }
	    break;
        case ZAjaxWidgets.AutoCompleteBehavior.UP:
	    if (isIE) {
		if (! this.isVisible) {
		    this.showOptions(true);
		} else {
		    this.hightlight(-1, -1);
		}
	    }
	    break;
	case ZAjaxWidgets.AutoCompleteBehavior.ESCAPE:
	    break;
        case ZAjaxWidgets.AutoCompleteBehavior.RETURN:
	    this.hideOptions();
	    break;
	default:
	    this.filterList();	    
	    break;
	}
	return(true);
    }
 }
});



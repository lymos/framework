/**
 * javascript framework
 * admin-111
 * vincentroot4
 * Usage: lymos().function(); or lymos.function();
 */
'use strict';
var lymos = function(selector, context){
	return new lymos.fun.init(selector, context);	// new object for each time
},
	reg_rftrim = /^[\s]+|[\s]+$/g,
	reg_email = /^[a-z0-9\-]*[\.]{0,1}[a-z0-9\-]+@[a-z0-9]+\.[a-z0-9]{2,4}$/g,
	selector_reg = /^(<[\w\W]+>)|#([\w]*)$/;

lymos.fun = lymos.prototype = {
	version: 1,
	getVersion: function(){
		return this.version;
	},
	name: function(){
		alert("name");
		return this;
	},
	get: function(){
		alert("get");
		return this;
	}	
}
/**
 * init html tag or class or id
 * return the selector object
 */
var init = lymos.fun.init = function(selector, context){
	var match,
		elem;
	if(! selector){
		return this;
	}
	match = selector_reg.exec(selector);
	console.log(match);
	if(match && (match[1] || ! context)){
		if(match[1]){

		}else{
			// html find by id 
			elem = document.getElementById(match[2]);
			if(elem){
				this.length = 1;
				this[0] = elem;
			}else{
				this.length = 0;
			}
			this.selector = selector;
			this.context = document;
			return this;
		}
	}

	//return this;
};
init.prototype = lymos.prototype;	// push lymos.prototype into init.prototype 

/*
 push the element to object
*/
lymos.extend = lymos.fun.extend = function(){
	var i = 1,
		length = arguments.length,
		target = arguments[0] || {};
	if(i === length){
		target = this;	// this -> lymos or lymos.fun
		i--;
	}
	for( ; i < length; i++){
		var options = arguments[i];
		if(options != null){
			for(var  name in options){
				target[name] = options[name];
			}
		}
	}
	return target;
}
// extend lymos function or object
lymos.extend({
	// usage: lymos.error();
	error: function(param){
		throw new Error(param);
	},
	isEmptyObject: function(param){
		var key;
		for(key in param){
			return false;
		}
		return true;
	},
	isNumeric: function(param){
		// parseFloat("6.77a") return 6.77; "6.77a" - 6.77 + 1 = NaN > 0 is false
		return ! lymos.isArray(param) && (param - parseFloat(param) + 1) > 0;
	},
	isArray: Array.isArray,
	/**
	 * item search value
	 * arr array
	 * start index
	 */
	inArray: function(item, arr, start){
		return arr == null ? -1 : lymos.searchInArray(arr, item, start);
	},
	type: function(param){
		return param == null ? param + "" : typeof param;
	},
	searchInArray: function(list, item, start){
		var i = 0,
			len = list.length;
		if(start){
			i = start;
		}
		for( ; i < len; i++){
			if(list[i] === item){
				return i;
			}
		}
		return -1;
	},
	trim: function(string){
		return string == null ? "" : (string + "").replace(reg_rftrim, "");
	},
	isFunction: function(param){
		return lymos.type(param) === "function";
	},
	isObject: function(param){
		return lymos.type(param) === "object";
	},
	merge: function(first, second){
		var len = second.length,
			k = 0,
			i = first.length;
		for( ; k < len; k++){
			first[i++] = second[k];
		}
		return first;
	},
	isEmail: function(email){
		return email == null ? false : reg_email.test(email + "");
	},
	// foreach element use callback
	each: function(obj, callback, args){
		var i = 0,
			len = obj.length,
			result;

		if(lymos.isArray(obj)){
			for( ; i < len; i++){
				callback.call(obj[i], i, obj[i]);
				if(result === false){
					break;
				}
			}
		}else{
			for(i in obj){
				callback.call(obj[i], i, obj[i]);
				if(result === false){
					break;
				}
			}
		}
		return obj;
	}
});

// extend lymos.fun function or object
lymos.fun.extend({
	// usage: lymos().isInt();
	isInt: function(){
		alert("int");
	},
	each: function(callback, args){
		return lymos.each(this, callback, args);
	},
	// binding event
	on: function(context, evt_name, callback){
		lymos.event.add(context, evt_name, callback);	
	},
	// trigger event
	trigger: function(evt_name){

	}
});

/**
 * bind events
 */
var event_list = "click dblclick focus focusin focusout load resize scroll change select submit mousedown " + 
	"mouseup mouseover mouseout mouseenter mouseleave keypress keyup keydown error contextment blur unload",
	event_arr = event_list.split(" ");
lymos.each(event_arr, function(index, name){
	lymos.fun[name] = function(callback){
		console.log(this);
		return arguments.length > 0 ? this.on(this, name, callback) : this.trigger(name);
	}
});


// callback function with queue
/*
	Usage:
	var c = lymos.callbacks();
	var f = function(){
		alert(1);
	}
	var g = function(){
		alert(2);
	}
	c.add(f);
	c.add(g);
	c.exec();
*/
lymos.callbacks = function(){
	var callback_list = [],
		exec_start,
		exec = function(){
			var exec_length = callback_list.length,
				exec_index = exec_start || 0;
			for( ; callback_list && exec_index < exec_length; exec_index++){
				callback_list[exec_index].apply();
			}
		},
		self = {
			add: function(){
				if(callback_list){
					(function _add(args){
						for(var i in args){
							var arg = args[i];
							if(typeof arg === "function")
								callback_list.push(arg);
						}
						
					})(arguments);
				}
			},
			remove: function(){

			},
			// call with params
			execWith: function(context, args){
				if(callback_list){
					exec(args);
				}
				return this;
			},
			exec: function(){
				self.execWith(this, arguments);
				return this;
			}
		};
	return self;
};

// Events
lymos.event = {
	add: function(elem, evt_name, callback){
		if(elem){
			elem[0].addEventListener(evt_name, callback);
		}
	},
	remove: function(){

	},
	trigger: function(){

	},
	handler: function(){

	}
};

// cache 
lymos.cacheObj = {

}

// ajax
lymos.xhr = function(){
	try{
		return new XMLHttpRequest();
	}catch(e){

	}
};
lymos.ajaxObj = {
	version: 1,
	xhr: lymos.xhr(),
	setRequestHeader: function(){
		var xhr = this.xhr;
		
		console.log(this.xhr);
	}
};

lymos.ajaxObj.setRequestHeader();




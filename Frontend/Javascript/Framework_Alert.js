/**
 * Framework Alert.js
 * Usage:
 * 		$(action_element).alert() or the action element has attr('[data-close="alert"]')
 *  	eg: $('.close').alert();
 *			or  <button class="close" data-close="alert">close</button>
 */
+function($){
	'use strict';

	var action_ele = '[data-close="alert"]'
	var Alert = function(ele){
		$(ele).on('click', this.close);
	};
	Alert.prototype.close = function(e){
		var $this = $(this);
		if(e){
			// prevent the default event
			e.preventDefault();
		}
		var target = $this.attr('data-target');
		if(! target){
			target = $this.attr('href');
			target = target && target.replace('/.*(?=#[^\s]*$)/', '');
		}
		var $parent = $(target);

		// still has no target
		if(! $parent.length){
			$parent = $this.closest('.alert');
		}
		// trigger the event close.before before close the element
		e = $.Event('close.alert.before');
		$parent.trigger(e);

		if(e.isDefaultPrevented())
			return ;
		function removeEle(){
			// trigger the event close.alter alter close the element
			$parent.detach().trigger('close.alert.after').remove();
		}

		removeEle();	// remove the element
	};

	function init(option){
		return this.each(function(){
			var $this = $(this);
			var data = $this.data('bs.alert');
			if(! data)
				$this.data('bs.alert', data = new Alert(this));
			// if option == 'close' call this function
			if(typeof(option) == 'string')
				data[option].call($this);
		});
	}
	$.fn.alert = init;

	// bind the event
	$(document).on('click.bs.alert', action_ele, Alert.prototype.close);
}(jQuery);
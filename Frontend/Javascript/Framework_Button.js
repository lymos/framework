/**
 * Framework js
 */

// button
+function($){
	var button = function(element, options){
		this.$element = $(element);
		this.options = $.extend({}, button.defaults, options);
		this.is_loading = false;
	};
	button.defaults = {
		loading_text: 'Loading...'
	};

	/*
	 * usage: 
	 * $(ele).button('loading');
	 * alter delay second time or do yourself buccess
	 * $(ele).button('reset');
	 */
	button.prototype.setState = function(state){
		var disa = 'disabled';
		var $ele = this.$element;
		var data = $ele.data();
		var val = $ele.is('input') ? 'val' : 'html';
		if(data.reset_text == null){
			$ele.data('reset_text', $ele[val]());
		}
		state += '_text';
		// $.proxy use old obj 'this'
		setTimeout($.proxy(function(){
			$ele[val](data[state] == null ? this.options[state] : data[state]);
			if(state == 'loading_text'){
				this.is_loading = true;
				$ele.addClass(disa).attr(disa, disa);
			}else if(this.is_loading){
				this.is_loading = false;
				$ele.removeClass(disa).removeAttr(disa);
			}
		}, this), 0);
		
	};

	button.prototype.toggle = function(){
		var changed = true;
		var $parent = this.$element.closest('[data-toggle="button"]');
		if($parent.length){
			var $input = $parent.find('input');
			// radio
			if($input.prop('type') == 'radio'){
				if($input.prop('checked')) 
					changed = false;
				$parent.find('.active').removeClass('active');
				this.$element.addClass('active');
			}
			// checkbox
			if($input.prop('type') == 'checkbox'){
				this.$element.toggleClass('active');
				if($input.prop('checked') !== this.$element.hasClass('active'))
					changed = false;
			}
			// checked or not
			this.$element.prop('checked', this.$element.hasClass('active'));
			if(changed)
				$input.trigger('change');
		}else{
			this.$element.toggleClass('active');
		}
	};

	function init(option){
		return this.each(function(){
			var options = typeof(option) == 'object' && option;
			var $this = $(this);
			var data = $this.data('buttons');
			if(! data)
				$this.data('buttons', data = new button(this, options));
			if(option == 'toggle')
				data.toggle();
			else
				data.setState(option);
		});
	};

	$.fn.button = init;

}(jQuery);
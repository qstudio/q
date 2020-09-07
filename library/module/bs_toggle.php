<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
// use q\asset;

// load it up ##
\q\module\bs_toggle::__run();

class bs_toggle extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// h::log( core\option::get('bs_toggle') );
		if ( 
			! isset( core\option::get('module')->bs_toggle )
			|| true !== core\option::get('module')->bs_toggle 
		){

			h::log( 'd:>Toggle is not enabled.' );

			return false;

		}

		// add html to footer ##
		\add_action( 'wp_footer', function(){
			\q\asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
				// 'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
			]);
		}, 3 );

		// add CSS to header ##
		// \add_action( 'wp_head', function(){
		// 	asset\css::ob_get([
		// 		'view'      => get_class(), 
		// 		'method'    => 'css',
		// 		// 'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
		// 	]);
		// }, 3 );

		// add reference to _source/scss/module/index.scss
		\add_action( 'wp_head', function(){
			\q\asset\scss::add([
		 		'class'     => get_class(), 
		 		'type'    	=> 'module', // add modules to scss list ##
			]);
		}, 3 );

    }




	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_toggle'] = 'Bootstrap ~ Toggle';

		// make it selected ##
		// $field['default_value'][0] = 'bs_toggle';
		
		return $field;

	}

    
    
    
    /**
    * JS for Toggle
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

?>
<script>
/*\
|*| ========================================================================
|*| Bootstrap Toggle: bootstrap4-toggle.js v3.7.0
|*| https://gitbrent.github.io/bootstrap4-toggle/
|*| ========================================================================
|*| Copyright 2018-2019 Brent Ely
|*| Licensed under MIT
|*| ========================================================================
\*/

+function ($) {
 	'use strict';

	// TOGGLE PUBLIC CLASS DEFINITION
	// ==============================

	var Toggle = function (element, options) {
		this.$element  = $(element)
		this.options   = $.extend({}, this.defaults(), options)
		this.render()
	}

	Toggle.VERSION  = '3.7.0-beta'

	Toggle.DEFAULTS = {
		on: 'On',
		off: 'Off',
		onstyle: 'primary',
		offstyle: 'light',
		size: 'normal',
		style: '',
		width: null,
		height: null
	}

	Toggle.prototype.defaults = function() {
		return {
			on: this.$element.attr('data-on') || Toggle.DEFAULTS.on,
			off: this.$element.attr('data-off') || Toggle.DEFAULTS.off,
			onstyle: this.$element.attr('data-onstyle') || Toggle.DEFAULTS.onstyle,
			offstyle: this.$element.attr('data-offstyle') || Toggle.DEFAULTS.offstyle,
			size: this.$element.attr('data-size') || Toggle.DEFAULTS.size,
			style: this.$element.attr('data-style') || Toggle.DEFAULTS.style,
			width: this.$element.attr('data-width') || Toggle.DEFAULTS.width,
			height: this.$element.attr('data-height') || Toggle.DEFAULTS.height
		}
	}

	Toggle.prototype.render = function () {
		this._onstyle = 'btn-' + this.options.onstyle
		this._offstyle = 'btn-' + this.options.offstyle
		var size
			= this.options.size === 'large' || this.options.size === 'lg' ? 'btn-lg'
			: this.options.size === 'small' || this.options.size === 'sm' ? 'btn-sm'
			: this.options.size === 'mini'  || this.options.size === 'xs' ? 'btn-xs'
			: ''
		var $toggleOn = $('<label for="'+ this.$element.prop('id') +'" class="btn">').html(this.options.on)
			.addClass(this._onstyle + ' ' + size)
		var $toggleOff = $('<label for="'+ this.$element.prop('id') +'" class="btn">').html(this.options.off)
			.addClass(this._offstyle + ' ' + size)
		var $toggleHandle = $('<span class="toggle-handle btn btn-light">')
			.addClass(size)
		var $toggleGroup = $('<div class="toggle-group">')
			.append($toggleOn, $toggleOff, $toggleHandle)
		var $toggle = $('<div class="toggle btn" data-toggle="toggle" role="button">')
			.addClass( this.$element.prop('checked') ? this._onstyle : this._offstyle+' off' )
			.addClass(size).addClass(this.options.style)

		this.$element.wrap($toggle)
		$.extend(this, {
			$toggle: this.$element.parent(),
			$toggleOn: $toggleOn,
			$toggleOff: $toggleOff,
			$toggleGroup: $toggleGroup
		})
		this.$toggle.append($toggleGroup)

		var width = this.options.width || Math.max($toggleOn.outerWidth(), $toggleOff.outerWidth())+($toggleHandle.outerWidth()/2)
		var height = this.options.height || Math.max($toggleOn.outerHeight(), $toggleOff.outerHeight())
		$toggleOn.addClass('toggle-on')
		$toggleOff.addClass('toggle-off')
		this.$toggle.css({ width: width, height: height })
		if (this.options.height) {
			$toggleOn.css('line-height', $toggleOn.height() + 'px')
			$toggleOff.css('line-height', $toggleOff.height() + 'px')
		}
		this.update(true)
		this.trigger(true)
	}

	Toggle.prototype.toggle = function () {
		if (this.$element.prop('checked')) this.off()
		else this.on()
	}

	Toggle.prototype.on = function (silent) {
		if (this.$element.prop('disabled')) return false
		this.$toggle.removeClass(this._offstyle + ' off').addClass(this._onstyle)
		this.$element.prop('checked', true)
		if (!silent) this.trigger()
	}

	Toggle.prototype.off = function (silent) {
		if (this.$element.prop('disabled')) return false
		this.$toggle.removeClass(this._onstyle).addClass(this._offstyle + ' off')
		this.$element.prop('checked', false)
		if (!silent) this.trigger()
	}

	Toggle.prototype.enable = function () {
		this.$toggle.removeClass('disabled')
		this.$toggle.removeAttr('disabled')
		this.$element.prop('disabled', false)
	}

	Toggle.prototype.disable = function () {
		this.$toggle.addClass('disabled')
		this.$toggle.attr('disabled', 'disabled')
		this.$element.prop('disabled', true)
	}

	Toggle.prototype.update = function (silent) {
		if (this.$element.prop('disabled')) this.disable()
		else this.enable()
		if (this.$element.prop('checked')) this.on(silent)
		else this.off(silent)
	}

	Toggle.prototype.trigger = function (silent) {
		this.$element.off('change.bs.toggle')
		if (!silent) this.$element.change()
		this.$element.on('change.bs.toggle', $.proxy(function() {
			this.update()
		}, this))
	}

	Toggle.prototype.destroy = function() {
		this.$element.off('change.bs.toggle')
		this.$toggleGroup.remove()
		this.$element.removeData('bs.toggle')
		this.$element.unwrap()
	}

	// TOGGLE PLUGIN DEFINITION
	// ========================

	function Plugin(option) {
		var optArg = Array.prototype.slice.call( arguments, 1 )[0]

		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('bs.toggle')
			var options = typeof option == 'object' && option

			if (!data) $this.data('bs.toggle', (data = new Toggle(this, options)))
			if (typeof option === 'string' && data[option] && typeof optArg === 'boolean') data[option](optArg)
			else if (typeof option === 'string' && data[option]) data[option]()
			//else if (option && !data[option]) console.log('bootstrap-toggle: error: method `'+ option +'` does not exist!');
		})
	}

	var old = $.fn.bootstrapToggle

	$.fn.bootstrapToggle             = Plugin
	$.fn.bootstrapToggle.Constructor = Toggle

	// TOGGLE NO CONFLICT
	// ==================

	$.fn.toggle.noConflict = function () {
		$.fn.bootstrapToggle = old
		return this
	}

	// TOGGLE DATA-API
	// ===============

	$(function() {
		$('input[type=checkbox][data-toggle^=toggle]').bootstrapToggle()
	})

	$(document).on('click.bs.toggle', 'div[data-toggle^=toggle]', function(e) {
		var $checkbox = $(this).find('input[type=checkbox]')
		$checkbox.bootstrapToggle('toggle')
		e.preventDefault()
	})
}(jQuery);

</script>
<?php

    }


    
    public static function css()
    {

?>
<style>
/*\
|*| ========================================================================
|*| Bootstrap Toggle: bootstrap4-toggle.css v3.7.0
|*| https://gitbrent.github.io/bootstrap4-toggle/
|*| ========================================================================
|*| Copyright 2018-2019 Brent Ely
|*| Licensed under MIT
|*| ========================================================================
\*/

/*
* @added 3.0.0: Return support for "*-xs" removed in Bootstrap-4
* @see: [Comment](https://github.com/twbs/bootstrap/issues/21881#issuecomment-341972830)
*/
.btn-group-xs > .btn, .btn-xs {
	padding: .35rem .4rem .25rem .4rem;
	font-size: .875rem;
	line-height: .5;
	border-radius: .2rem;
}

.checkbox label .toggle, .checkbox-inline .toggle {
	margin-left: -1.25rem;
	margin-right: .35rem;
}

.toggle {
	position: relative;
	overflow: hidden;
}
.toggle.btn.btn-light, .toggle.btn.btn-outline-light {
	/* bootstrap-4 - add a border so toggle is delineated */
	border-color: rgba(0, 0, 0, .15);
}
.toggle input[type="checkbox"] {
	display: none;
}
.toggle-group {
	position: absolute;
	width: 200%;
	top: 0;
	bottom: 0;
	left: 0;
	transition: left 0.35s;
	-webkit-transition: left 0.35s;
	-moz-user-select: none;
	-webkit-user-select: none;
}
.toggle-group label, .toggle-group span { cursor: pointer; }
.toggle.off .toggle-group {
	left: -100%;
}
.toggle-on {
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 50%;
	margin: 0;
	border: 0;
	border-radius: 0;
}
.toggle-off {
	position: absolute;
	top: 0;
	bottom: 0;
	left: 50%;
	right: 0;
	margin: 0;
	border: 0;
	border-radius: 0;
	box-shadow: none; /* Bootstrap 4.0 Support via (Issue #186)[https://github.com/minhur/bootstrap-toggle/issues/186]) */
}
.toggle-handle {
	position: relative;
	margin: 0 auto;
	padding-top: 0px;
	padding-bottom: 0px;
	height: 100%;
	width: 0px;
	border-width: 0 1px;
	background-color: #fff;
}

.toggle.btn-outline-primary .toggle-handle {
	background-color: var(--primary);
	border-color: var(--primary);
}
.toggle.btn-outline-secondary .toggle-handle {
	background-color: var(--secondary);
	border-color: var(--secondary);
}
.toggle.btn-outline-success .toggle-handle {
	background-color: var(--success);
	border-color: var(--success);
}
.toggle.btn-outline-danger .toggle-handle {
	background-color: var(--danger);
	border-color: var(--danger);
}
.toggle.btn-outline-warning .toggle-handle {
	background-color: var(--warning);
	border-color: var(--warning);
}
.toggle.btn-outline-info .toggle-handle {
	background-color: var(--info);
	border-color: var(--info);
}
.toggle.btn-outline-light .toggle-handle {
	background-color: var(--light);
	border-color: var(--light);
}
.toggle.btn-outline-dark .toggle-handle {
	background-color: var(--dark);
	border-color: var(--dark);
}
.toggle[class*="btn-outline"]:hover .toggle-handle {
	background-color: var(--light);
	opacity: 0.5;
}

/* NOTE: Must come first, so classes below override as needed */
/* [default] (bootstrap-4.1.3 - .btn - h:38px) */
.toggle.btn { min-width: 3.7rem; min-height: 2.15rem; }
.toggle-on.btn { padding-right: 1.5rem; }
.toggle-off.btn { padding-left: 1.5rem; }

/* `lg` (bootstrap-4.1.3 - .btn - h:48px) */
.toggle.btn-lg { min-width: 5rem; min-height: 2.815rem; }
.toggle-on.btn-lg { padding-right: 2rem; }
.toggle-off.btn-lg { padding-left: 2rem; }
.toggle-handle.btn-lg { width: 2.5rem; }

/* `sm` (bootstrap-4.1.3 - .btn - h:31px) */
.toggle.btn-sm { min-width: 3.125rem; min-height: 1.938rem; }
.toggle-on.btn-sm { padding-right: 1rem; }
.toggle-off.btn-sm { padding-left: 1rem; }

/* `xs` (bootstrap-3.3 - .btn - h:22px) */
.toggle.btn-xs { min-width: 2.19rem; min-height: 1.375rem; }
.toggle-on.btn-xs { padding-right: .8rem; }
.toggle-off.btn-xs { padding-left: .8rem; }
</style>
<?php

    }


}

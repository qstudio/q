<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_toggle::__run();

class bs_toggle extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// h::log( core\option::get('bs_toggle') );
		if ( 
			! isset( core\option::get('extension')->bs_toggle )
			|| true !== core\option::get('extension')->bs_toggle 
		){

			h::log( 'd:>Toggle is not enabled.' );

			return false;

		}

        // add html to footer ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 3 );

        // add CSS to header ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 3 );

    }



    public static function args( $args = false )
    {

        #helper::log( 'passed args to modal' );
        // helper::log( $args );

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

    }



	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_extension( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_toggle'] = 'Bootstrap Toggle';

		// make it selected ##
		// $field['default_value'][0] = 'bs_toggle';
		
		return $field;

	}

    
    
    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        \q\asset\javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 3,
            'handle'    => 'BS Toggle'
		]);

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
|*| Bootstrap Toggle: bootstrap4-toggle.js v3.6.1
|*| https://gitbrent.github.io/bootstrap4-toggle/
|*| ========================================================================
|*| Copyright 2018-2019 Brent Ely
|*| Licensed under MIT
|*| ========================================================================
\*/
!function(a){"use strict";function l(t,e){this.$element=a(t),this.options=a.extend({},this.defaults(),e),this.render()}l.VERSION="3.6.0",l.DEFAULTS={on:"On",off:"Off",onstyle:"primary",offstyle:"light",size:"normal",style:"",width:null,height:null},l.prototype.defaults=function(){return{on:this.$element.attr("data-on")||l.DEFAULTS.on,off:this.$element.attr("data-off")||l.DEFAULTS.off,onstyle:this.$element.attr("data-onstyle")||l.DEFAULTS.onstyle,offstyle:this.$element.attr("data-offstyle")||l.DEFAULTS.offstyle,size:this.$element.attr("data-size")||l.DEFAULTS.size,style:this.$element.attr("data-style")||l.DEFAULTS.style,width:this.$element.attr("data-width")||l.DEFAULTS.width,height:this.$element.attr("data-height")||l.DEFAULTS.height}},l.prototype.render=function(){this._onstyle="btn-"+this.options.onstyle,this._offstyle="btn-"+this.options.offstyle;var t="large"===this.options.size||"lg"===this.options.size?"btn-lg":"small"===this.options.size||"sm"===this.options.size?"btn-sm":"mini"===this.options.size||"xs"===this.options.size?"btn-xs":"",e=a('<label for="'+this.$element.prop("id")+'" class="btn">').html(this.options.on).addClass(this._onstyle+" "+t),s=a('<label for="'+this.$element.prop("id")+'" class="btn">').html(this.options.off).addClass(this._offstyle+" "+t),o=a('<span class="toggle-handle btn btn-light">').addClass(t),i=a('<div class="toggle-group">').append(e,s,o),l=a('<div class="toggle btn" data-toggle="toggle" role="button">').addClass(this.$element.prop("checked")?this._onstyle:this._offstyle+" off").addClass(t).addClass(this.options.style);this.$element.wrap(l),a.extend(this,{$toggle:this.$element.parent(),$toggleOn:e,$toggleOff:s,$toggleGroup:i}),this.$toggle.append(i);var n=this.options.width||Math.max(e.outerWidth(),s.outerWidth())+o.outerWidth()/2,h=this.options.height||Math.max(e.outerHeight(),s.outerHeight());e.addClass("toggle-on"),s.addClass("toggle-off"),this.$toggle.css({width:n,height:h}),this.options.height&&(e.css("line-height",e.height()+"px"),s.css("line-height",s.height()+"px")),this.update(!0),this.trigger(!0)},l.prototype.toggle=function(){this.$element.prop("checked")?this.off():this.on()},l.prototype.on=function(t){if(this.$element.prop("disabled"))return!1;this.$toggle.removeClass(this._offstyle+" off").addClass(this._onstyle),this.$element.prop("checked",!0),t||this.trigger()},l.prototype.off=function(t){if(this.$element.prop("disabled"))return!1;this.$toggle.removeClass(this._onstyle).addClass(this._offstyle+" off"),this.$element.prop("checked",!1),t||this.trigger()},l.prototype.enable=function(){this.$toggle.removeClass("disabled"),this.$toggle.removeAttr("disabled"),this.$element.prop("disabled",!1)},l.prototype.disable=function(){this.$toggle.addClass("disabled"),this.$toggle.attr("disabled","disabled"),this.$element.prop("disabled",!0)},l.prototype.update=function(t){this.$element.prop("disabled")?this.disable():this.enable(),this.$element.prop("checked")?this.on(t):this.off(t)},l.prototype.trigger=function(t){this.$element.off("change.bs.toggle"),t||this.$element.change(),this.$element.on("change.bs.toggle",a.proxy(function(){this.update()},this))},l.prototype.destroy=function(){this.$element.off("change.bs.toggle"),this.$toggleGroup.remove(),this.$element.removeData("bs.toggle"),this.$element.unwrap()};var t=a.fn.bootstrapToggle;a.fn.bootstrapToggle=function(o){var i=Array.prototype.slice.call(arguments,1)[0];return this.each(function(){var t=a(this),e=t.data("bs.toggle"),s="object"==typeof o&&o;e||t.data("bs.toggle",e=new l(this,s)),"string"==typeof o&&e[o]&&"boolean"==typeof i?e[o](i):"string"==typeof o&&e[o]&&e[o]()})},a.fn.bootstrapToggle.Constructor=l,a.fn.toggle.noConflict=function(){return a.fn.bootstrapToggle=t,this},a(function(){a("input[type=checkbox][data-toggle^=toggle]").bootstrapToggle()}),a(document).on("click.bs.toggle","div[data-toggle^=toggle]",function(t){a(this).find("input[type=checkbox]").bootstrapToggle("toggle"),t.preventDefault()})}(jQuery);
//# sourceMappingURL=bootstrap4-toggle.min.js.map
</script>
<?php

    }


    /**
     * Deal nicely with CSS
     */
    public static function wp_head()
    {

        \q\asset\css::ob_get([
            'view'      => get_class(), 
            'method'    => 'css',
            'priority'  => 40,
            'handle'    => 'BS Toggle'
        ]);

    }



    
    public static function css()
    {

?>
<style>
/*\
|*| ========================================================================
|*| Bootstrap Toggle: bootstrap4-toggle.css v3.6.1
|*| https://gitbrent.github.io/bootstrap4-toggle/
|*| ========================================================================
|*| Copyright 2018-2019 Brent Ely
|*| Licensed under MIT
|*| ========================================================================
\*/
.btn-group-xs>.btn,.btn-xs{padding:.35rem .4rem .25rem .4rem;font-size:.875rem;line-height:.5;border-radius:.2rem}.checkbox label .toggle,.checkbox-inline .toggle{margin-left:-1.25rem;margin-right:.35rem}.toggle{position:relative;overflow:hidden}.toggle.btn.btn-light,.toggle.btn.btn-outline-light{border-color:rgba(0,0,0,.15)}.toggle input[type=checkbox]{display:none}.toggle-group{position:absolute;width:200%;top:0;bottom:0;left:0;transition:left .35s;-webkit-transition:left .35s;-moz-user-select:none;-webkit-user-select:none}.toggle-group label,.toggle-group span{cursor:pointer}.toggle.off .toggle-group{left:-100%}.toggle-on{position:absolute;top:0;bottom:0;left:0;right:50%;margin:0;border:0;border-radius:0}.toggle-off{position:absolute;top:0;bottom:0;left:50%;right:0;margin:0;border:0;border-radius:0;box-shadow:none}.toggle-handle{position:relative;margin:0 auto;padding-top:0;padding-bottom:0;height:100%;width:0;border-width:0 1px;background-color:#fff}.toggle.btn-outline-primary .toggle-handle{background-color:var(--primary);border-color:var(--primary)}.toggle.btn-outline-secondary .toggle-handle{background-color:var(--secondary);border-color:var(--secondary)}.toggle.btn-outline-success .toggle-handle{background-color:var(--success);border-color:var(--success)}.toggle.btn-outline-danger .toggle-handle{background-color:var(--danger);border-color:var(--danger)}.toggle.btn-outline-warning .toggle-handle{background-color:var(--warning);border-color:var(--warning)}.toggle.btn-outline-info .toggle-handle{background-color:var(--info);border-color:var(--info)}.toggle.btn-outline-light .toggle-handle{background-color:var(--light);border-color:var(--light)}.toggle.btn-outline-dark .toggle-handle{background-color:var(--dark);border-color:var(--dark)}.toggle[class*=btn-outline]:hover .toggle-handle{background-color:var(--light);opacity:.5}.toggle.btn{min-width:3.7rem;min-height:2.15rem}.toggle-on.btn{padding-right:1.5rem}.toggle-off.btn{padding-left:1.5rem}.toggle.btn-lg{min-width:5rem;min-height:2.815rem}.toggle-on.btn-lg{padding-right:2rem}.toggle-off.btn-lg{padding-left:2rem}.toggle-handle.btn-lg{width:2.5rem}.toggle.btn-sm{min-width:3.125rem;min-height:1.938rem}.toggle-on.btn-sm{padding-right:1rem}.toggle-off.btn-sm{padding-left:1rem}.toggle.btn-xs{min-width:2.19rem;min-height:1.375rem}.toggle-on.btn-xs{padding-right:.8rem}.toggle-off.btn-xs{padding-left:.8rem}
</style>
<?php

    }


}

<?php

namespace q\module;

use q\core;
use q\core\helper as h;
use q\asset;

// load it up ##
\q\module\bs_helper::__run();

class bs_helper extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bs_helper )
			|| true !== core\option::get('module')->bs_helper 
		){

			// h::log( 'd:>Helper is not enabled.' );

			return false;

		}

        // add <script> block to global js asset ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
			]);
		}, 3 );

		// add html ##
		\add_action( 'wp_footer', [ get_class(), 'html' ], 100000 );

    }


	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_helper'] = 'Bootstrap Helper';

		// make it selected ##
		$field['default_value'][0] = 'bs_helper';

		// kick back ##
		return $field;

	}



	/**
	 * Add html element to page for debugging feedback
	 * 
	 */
	public static function html(){

		// we should never run this module if debugging is off ##
		if( 
			// false === self::$debug 
			false === \Q::$debug
		){

			h::log( 'BootStrap Helper is disabled when debugging is disabled...' );

			return false;

		}

?>
<script>
if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		// enable tooltips ##
		// jQuery('[data-toggle="tooltip"]').tooltip();

		q_bootstrap_tooltip( true, false );

		// hide on load ##
		// $output.tooltip('hide');

		// on resize ##
		// jQuery(window).on( "resize", q_update_breakpoint );

	});

	// resizing ##
	jQuery(window).on('resize', function(){

		q_bootstrap_tooltip( false, true );

	});

	// BS breakpoint ##
	function q_bootstrap_tooltip( load, resize ) {

		// assign target ##
		output = jQuery("#breakpoint");

		// console.log( 'here..' );

		// sizes ##
		vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
		vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
		// vw = jQuery(window).width();
		// vh = jQuery(window).height();

		// resize ##
		resize = resize || false;
		load = load || false;

		// update attrs ##
		output.attr('title', 'Width: '+ vw +'px<br />Height: '+ vh +'px' );
		output.html( q_bootstrap_breakpoint().name );

		if ( load ) {

			// trigger tooltip ##
			output.tooltip('show');

			setTimeout(function(){
				output.tooltip('hide');
			}, 3000);

		}

		// possible delay, for resizing ##
		if ( resize ) {	

			// trigger tooltip ##
			output.tooltip('show');

			// console.log( 'Set timeount in 2 secs..' );

			setInterval(function(){
				var vw = jQuery(window).width();
				var vh = jQuery(window).height();
				jQuery('.tooltip-inner').html( 'Width: '+ vw +'px<br />Height: '+ vh +'px' );
			}, 30);

			window.addEventListener("resize",q_bootstrap_debounce(function(e){
				
				// console.log("end of resizing");

				setTimeout(function(){
					output.tooltip('hide');
				}, 3000 );

			}));

		}

	}

	function q_bootstrap_debounce( func ){
		  
		var timer;
		return function(event){
			if(timer) clearTimeout(timer);
			timer = setTimeout(func,100,event);
		};

	}

};
</script>
<span 
	id="breakpoint" 
	class="badge badge-warning" 
	data-toggle="tooltip" 
	data-placement="top"
	data-html="true"
	title="Tooltip on top"
	style="position: fixed; left: 0; bottom: 0; padding: 20px;"
	>~@~
</span>
<?php
			
	}

    
    
    /**
    * JS for modal
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

		// we should never run this module if debugging is off ##
		if( 
			// false === self::$debug 
			false === \Q::$debug
		){

			h::log( 'BootStrap Helper is disabled when debugging is disabled...' );

			return false;

		}

?>
<script>

/*
https://cdn.jsdelivr.net/npm/bootstrap-detect-breakpoint/src/bootstrap-detect-breakpoint.js
*/
function q_bootstrap_breakpoint() {
    const breakpointNames = ["xl", "lg", "md", "sm", "xs"]
    let breakpointValues = []
    for (const breakpointName of breakpointNames) {
        breakpointValues[breakpointName] = window.getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-' + breakpointName)
    }
    let i = breakpointNames.length
    for (const breakpointName of breakpointNames) {
        i--
        if (window.matchMedia("(min-width: " + breakpointValues[breakpointName] + ")").matches) {
            return {
				name: breakpointName, 
				height: vh,
				width: vw,
				index: i
			}
        }
    }
    return null
}
</script>
<?php

    }


}

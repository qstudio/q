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

        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
			]);
		}, 3 );

		\add_action( 'wp_footer', [ get_class(), 'html' ], 10 );

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
	<span id="breakpoint" class="badge badge-warning" style="position: fixed; right: 0; bottom: 0; padding: 20px; ">-x-</span>
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
            return {name: breakpointName, index: i}
        }
    }
    return null
}


if( typeof jQuery !== 'undefined' ) {

	jQuery(window).ready(function(){

		// assign target ##
		var $output = jQuery("#breakpoint")

		// BS breakpoint ##
		function q_update_breakpoint() {

			$output.html( q_bootstrap_breakpoint().name )
			
		}
	
		q_update_breakpoint();

		jQuery(window).on( "resize", q_update_breakpoint );

	});

};
</script>
<?php

    }


}

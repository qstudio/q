<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\anspress::__run();

class anspress extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('anspress') );
		if ( 
			! isset( core\option::get('module')->anspress )
			|| true !== core\option::get('module')->anspress 
		){

			// h::log( 'd:>AnsPress is not enabled.' );

			return false;

		}
		
        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
				// 'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
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
		$field['choices']['anspress'] = 'Plugin ~ Anspress';

		// make it selected ##
		// $field['default_value'][0] = 'anspress';

		// kick back ##
		return $field;

	}


    
    
    /**
    * JS for modal
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

    // helper::log( self::$args );

?>
<script>
if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		jQuery('.ap-btn-newcomment, .ap-btn-submit').addClass('mt-2 btn btn-primary').removeClass('ap-btn ap-btn-submit');
		
	});

	jQuery( document ).ajaxStop(function() {

		jQuery('.ap-btn-submit').addClass('btn btn-primary').removeClass('ap-btn ap-btn-submit');

	});

};
</script>
<?php

    }


}

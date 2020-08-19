<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_accordion::__run();

class bs_accordion extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bs_accordion )
			|| true !== core\option::get('module')->bs_accordion 
		){

			// h::log( 'd:>accordion is not enabled.' );

			return false;

		}
		
        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
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
		$field['choices']['bs_accordion'] = 'Bootstrap Accordion / Collapse';

		// make it selected ##
		// $field['default_value'][0] = 'bs_accordion';

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

		/*
		// store open tab in localstorage ## 
		jQuery('a[data-accordion="tab"]').on('shown.bs.tab', function (e) {
			localStorage.setItem('activeTab', jQuery(e.target).attr('href'));
			console.log( 'Store tab: '+jQuery(e.target).attr('href') );
		});

		var activeTab = localStorage.getItem('activeTab');
		if(activeTab){
			jQuery('.nav-accordion a[href="' + activeTab + '"]').tab('show');
		}
		*/

		// check for accordion hash ##
		accordion_hash = q_get_hash_value_from_key( 'accordion' );
		console.log( 'accordion hash: '+accordion_hash );
		var accordion_loaded = false;
		
		if ( accordion_hash ) {

			if ( jQuery('.bs-accordion').find('[data-hash="accordion/'+accordion_hash+'"]').length ){

				// console.log( 'accordion found: '+accordion_hash );

				jQuery('.bs-accordion').find('[data-hash="accordion/'+accordion_hash+'"]').trigger( 'click' );
				
				accordion_loaded = true;

			}

		} 

		/*
		if( false === accordion_loaded ) {

			// console.log( 'accordion_loaded == false' );

			// on load, if no tab active, make first tab-content active/show ##
			if( ! jQuery( '.bs-accordion > .nav-link' ).hasClass('active') ){
				// console.log( 'NO active tab...' );
				jQuery( '.bs-accordion .nav-link' ).first().addClass('active show');
				$first = jQuery( '.bs-accordion .nav-link' );
				// // console.log( $first.attr('aria-controls') )
				jQuery( '#'+$first.attr('aria-controls') ).addClass('active show');
			}

		}

		// allow external accordion triggers ##
		jQuery( '[data-trigger="accordion"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			e.preventDefault();
			window.location.hash = href;
			jQuery( '[data-accordion="accordion"][href="' + href + '"]' ).trigger( 'click' );
		} );

		*/

		// update hash value when bs4 accordions are used ##
		jQuery('.bs-accordion button').click(function (e) {
			window.location.hash = jQuery(this).data('hash');
			console.log( 'Clicked here..'+jQuery(this).data('hash') );
			jQuery( '.bs-accordion .nav-link' ).removeClass('active show');
		});
		

	});

};
</script>
<?php

    }


}

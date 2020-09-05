<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_collapse::__run();

class bs_collapse extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bs_collapse )
			|| true !== core\option::get('module')->bs_collapse 
		){

			// h::log( 'd:>collapse is not enabled.' );

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
		$field['choices']['bs_collapse'] = 'Bootstrap ~ Collapse';

		// make it selected ##
		// $field['default_value'][0] = 'bs_collapse';

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

		// check for collapse hash ##
		collapse_hash = q_get_hash_value_from_key( 'collapse' );
		// console.log( 'collapse hash: '+collapse_hash );
		var collapse_loaded = false;
		
		if ( collapse_hash ) {

			if ( jQuery('.bs-collapse').find('[data-hash="collapse/'+collapse_hash+'/scroll/'+collapse_hash+'"]').length ){

				// console.log( 'collapse found: '+collapse_hash );

				jQuery('.bs-collapse').find('[data-hash="collapse/'+collapse_hash+'/scroll/'+collapse_hash+'"]').trigger( 'click' );
				
				collapse_loaded = true;

			}

		} 

		/*
		if( false === collapse_loaded ) {

			// console.log( 'collapse_loaded == false' );

			// on load, if no tab active, make first tab-content active/show ##
			if( ! jQuery( '.bs-collapse > .nav-link' ).hasClass('active') ){
				// console.log( 'NO active tab...' );
				jQuery( '.bs-collapse .nav-link' ).first().addClass('active show');
				$first = jQuery( '.bs-collapse .nav-link' );
				// // console.log( $first.attr('aria-controls') )
				jQuery( '#'+$first.attr('aria-controls') ).addClass('active show');
			}

		}

		// allow external collapse triggers ##
		jQuery( '[data-trigger="collapse"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			e.preventDefault();
			window.location.hash = href;
			jQuery( '[data-collapse="collapse"][href="' + href + '"]' ).trigger( 'click' );
		} );

		*/

		// update hash value when bs4 collapses are used ##
		jQuery('.bs-collapse button').click(function (e) {
			window.location.hash = jQuery(this).data('hash');
			// console.log( 'Clicked here..'+jQuery(this).data('hash') );
			// jQuery( '.bs-collapse .nav-link' ).removeClass('active show');
		});
		

	});

};
</script>
<?php

    }


}

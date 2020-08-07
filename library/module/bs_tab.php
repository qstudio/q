<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_tab::__run();

class bs_tab extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('extension')->bs_tab )
			|| true !== core\option::get('extension')->bs_tab 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}
		
        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
				'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
			]);
		}, 3 );

    }


	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_extension( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_tab'] = 'Bootstrap Tabs';

		// make it selected ##
		$field['default_value'][0] = 'bs_tab';

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
		jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			localStorage.setItem('activeTab', jQuery(e.target).attr('href'));
			console.log( 'Store tab: '+jQuery(e.target).attr('href') );
		});

		var activeTab = localStorage.getItem('activeTab');
		if(activeTab){
			jQuery('.nav-tabs a[href="' + activeTab + '"]').tab('show');
		}
		*/

		// on load, if no tab active, make first tab-content active/show ##
		if( ! jQuery( '.bs-tabs > .nav-item' ).hasClass('active') ){
			// console.log( 'NO active tab...' );
			$first = jQuery( '.bs-tabs > .nav-item' ).first().addClass('active show');
			// console.log( $first.attr('aria-controls') )
			jQuery( '#'+$first.attr('aria-controls') ).addClass('active show');
		}

		// read hash from page load and change tab
		var hash = document.location.hash;
		var prefix = "tab_";
		if (hash) {
			jQuery('.bs-tabs a[href="'+hash.replace(prefix,"")+'"]').tab('show');
		} 

		// allow external tab triggers ##
		jQuery( '[data-trigger="tab"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			e.preventDefault();
			window.location.hash = href;
			jQuery( '[data-toggle="tab"][href="' + href + '"]' ).trigger( 'click' );
		} );

		// update hash value when bs4 tabs are used ##
		jQuery('.bs-tabs a').click(function (e) {
			window.location.hash = this.hash;
		});

	});

};
</script>
<?php

    }


}
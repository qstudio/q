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
			! isset( core\option::get('extension')->tab )
			|| true !== core\option::get('extension')->tab 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}
		

        // add assets ##
        // \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ], 1000000 );

        // add html to footer ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 3 );

        // add CSS to header ##
        // \add_action( 'wp_head', [ get_class(), 'wp_head' ], 3 );

        // add JS to footer ##
        // \add_action( 'wp_footer', [ get_class(), 'run_javascript' ], 10000000 );

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

        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

		// pop on a new choice ##
		$field['choices']['tab'] = 'Bootstrap Tabs';
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		$field['default_value'][0] = 'tab';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}


    
    
    /**
    * Load assets
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function wp_enqueue_scripts()
    {

		$min = ( true === \Q::$debug ) ? '' : '.min' ;

        // toast JS ##
        \wp_register_script( 'toast-js', h::get( "asset/js/vendor/toast$min.js", 'return' ), array( 'jquery' ), self::version, true );
        \wp_enqueue_script( 'toast-js' );

		// toast CSS ##
        \wp_register_style( 'toast-css', h::get( "asset/css/vendor/toast$min.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'toast-css' );

    }



    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        asset\javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 5,
            'handle'    => 'Tab'
		]);

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
	/*
	jQuery(document).ready(function() {

	// modern browsers 
	jQuery( window ).bind( 'hashchange', function( e ) {

		// console.log( 'Doing hash change...' );

		history.navigationMode = 'compatible';
		e.preventDefault();
		$the_hash = q_toggle_hash();
		if($the_hash) q_toggle( $the_hash );

	});

	});
	*/

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

		// read hash from page load and change tab
		var hash = document.location.hash;
		var prefix = "tab_";
		if (hash) {
			jQuery('.nav-tabs a[href="'+hash.replace(prefix,"")+'"]').tab('show');
		} 

		// allow external tab triggers ##
		jQuery( '[data-trigger="tab"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			e.preventDefault();
			window.location.hash = href;
			jQuery( '[data-toggle="tab"][href="' + href + '"]' ).trigger( 'click' );
		} );

		// update hash value when bs4 tabs are used ##
		jQuery('.nav-tabs a').click(function (e) {
			window.location.hash = this.hash;
		});

	});

};
</script>
<?php

    }



    /**
     * Deal nicely with CSS
     */
    public static function wp_head()
    {

        css::ob_get([
            'view'      => get_class(), 
            'method'    => 'css',
            'priority'  => 40,
            'handle'    => 'Toast'
        ]);

    }



    
    public static function css()
    {

?>
<style>
    .featherlight {
        background: rgba(0,0,0,.8) !important;
    }
</style>
<?php

    }


}

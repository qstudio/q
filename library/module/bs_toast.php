<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_toast::__run();

class bs_toast extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('toast') );
		if ( 
			! isset( core\option::get('extension')->bs_toast )
			|| true !== core\option::get('extension')->bs_toast 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		

        // add assets ##
        \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ], 1000000 );

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
		$field['choices']['bs_toast'] = 'Bootstrap Toast';
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		$field['default_value'][0] = 'bs_toast';
		
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
            'priority'  => 4,
            'handle'    => 'BS Toast'
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



function q_snack( options ){

	// check if the object exists ##
	if ( typeof jQuery.snack === 'undefined' ) {

		// console.log( 'No snacks available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		// console.log( 'No snack content' );

		return false;

	}

	// global config ##
	defaults = { 
		'content'		: false,
		'style' 		: 'info',
		'timeout'		: 5000,
		'position'		: 'bottom-right',
		'dismissible'	: true,
		'stackable'		: true, // stacking is ok ##
		'hover'			: true
	};

	// merge passed options ##
	jQuery.extend( defaults, options );

	/*
	@TODO - define global settings ##
	$.toastDefaults.position = options.position; // 'bottom-right';
	$.toastDefaults.dismissible = options.dismissible; // true;
	$.toastDefaults.stackable = options.stackable; // true;
	$.toastDefaults.pauseDelayOnHover = options.hover; // true;
	*/

	// snack time ##
	jQuery.snack( defaults.style, defaults.content, defaults.timeout );

}



function q_toast( options ){

	// check if the object exists ##
	if ( typeof jQuery.toast === 'undefined' ) {

		// console.log( 'No toast available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		// console.log( 'No toast content' );

		return false;

	}

	// relative time ##
	var date = q_timestamp_to_human( + new Date() );

	// global config ##
	defaults = { 
		'title'			: 'Notice',
		'subtitle'		: date +' ago', // date ##
		'content'		: false,
		'style' 		: 'info',
		'timeout'		: 5000,
		'position'		: 'bottom-right',
		'dismissible'	: true,
		'stackable'		: true, // stacking is ok ##
		'hover'			: true,
		'img'			: false /*{
							src: 'https://via.placeholder.com/20',
							alt: 'Image'
						}*/
	};

	// merge passed options ##
	jQuery.extend( defaults, options );

	/*
	@TODO - define global settings ##
	$.toastDefaults.position = options.position; // 'bottom-right';
	$.toastDefaults.dismissible = options.dismissible; // true;
	$.toastDefaults.stackable = options.stackable; // true;
	$.toastDefaults.pauseDelayOnHover = options.hover; // true;
	*/

	// console.dir( defaults );

	// snack time ##
	jQuery.toast({
		type		: defaults.style, 
		title		: defaults.title, 
		subtitle 	: defaults.subtitle, 
		content		: defaults.content, 
		delay		: defaults.timeout,
		img			: defaults.img, 
	});

}

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

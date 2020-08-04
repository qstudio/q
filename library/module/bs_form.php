<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_form::__run();

class bs_form extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('toast') );
		if ( 
			! isset( core\option::get('extension')->bs_form )
			|| true !== core\option::get('extension')->bs_form 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		

        // add assets ##
        // \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ], 1000000 );

        // add js to footer ##
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
		$field['choices']['bs_form'] = 'Bootstrap Form';
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		$field['default_value'][0] = 'bs_form';
		
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
        // \wp_register_script( 'toast-js', h::get( "asset/js/vendor/toast$min.js", 'return' ), array( 'jquery' ), self::version, true );
        // \wp_enqueue_script( 'toast-js' );

		// toast CSS ##
        // \wp_register_style( 'toast-css', h::get( "asset/css/vendor/toast$min.css", 'return' ), '', self::version, 'all' );
        // \wp_enqueue_style( 'toast-css' );

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
            'handle'    => 'BS Form'
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
// BS Form validation
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
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

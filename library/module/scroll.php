<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
// use q\controller\generic as generic;
use q\asset\javascript as javascript;
use q\asset\css as css;

// load it up ##
\q\module\scroll::__run();

class scroll extends \Q {
    
    public static $args = [];

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->scroll )
			|| true !== core\option::get('module')->scroll 
		){

			// h::log( 'd:>scroll is not enabled.' );

			return false;

		}

        // add JS to footer if debugging or single q.theme.js script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 5 ); 

        // add css ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 4 ); 

    }


	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['scroll'] = 'JS Scroll Control';

		// make it selected ##
		$field['default_value'][0] = 'scroll';

		// kick back ##
		return $field;

	}




    /**
    * Build scroll UI navigation
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function render( Array $args = null )
    {

        // assign ##
        self::$args = isset( $args ) ? (array) $args: [] ;

        #helper::log( self::$args );

        // check if we have any elements to scroll over ##
        if ( 
            ! isset( self::$args['elements'] )
            || empty( array_filter( self::$args['elements'] ) ) 
        ) {

            helper::log( 'No elements' );

            return false;

        }

        // add inline CSS ##
        #self::css();

        // add navigation ##
        echo self::navigation();

        // add JS ##
        #self::javascript();

    }



    /**
    * Markup for navigation
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function navigation()
    {

        // get markup model ##
        $markup = self::$args['markup'];

        // empty rows ##
        $rows = '';

        // loop over paassed rows adding details ##
        foreach( self::$args['elements'] as $key => $value ){

            $row = [];
            $row['slug'] = $key;
            $row['title'] = $value;

            $rows .= markup::apply( self::$args['markup_row'], $row );

        }

        // compile markup ##
        $markup = str_replace( '%rows%', $rows, $markup );

        // return ##
        return $markup;

    }




    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            // 'priority'  => 5,
            // 'handle'    => 'Scroll'
        ]);

    }



    /**
    * JS for scroll UI
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

?>
<script>

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    // BINDERS
    jQuery( 'body' ).on( 'click', '.q_scroll a', function(e) {
        
        $the_hash = jQuery( this ).attr('data-scroll-nav');
        if ($the_hash) q_scroll( $the_hash );

    });

    jQuery( window ).bind( "load", function(){

        $the_hash = q_scroll_hash();
        
        if($the_hash) q_scroll( $the_hash );

    });

    jQuery(document).ready(function() {
        
        // modern browsers
        var $the_hash = '';

        jQuery( window ).bind( 'hashchange', function( e ) {

            // console.log( 'Doing hash change...' );

            history.navigationMode = 'compatible';
            e.preventDefault();
            $the_hash = q_scroll_hash();
            if($the_hash) q_scroll( $the_hash );

        });

        jQuery('a[href^="#"]').on('click', function(e) {
			// reset hash ##
			window.location.hash = '';
            $the_hash = q_push_hash();
            if($the_hash) q_scroll( $the_hash );
        });

    });


    function q_scroll( data_id ){

        // remove all highlights ##
        jQuery( ".q_scroll > span" ).removeClass( 'current' );

        // try to locate data element matching retreived hash value ##
        if ( jQuery( "[data-scroll-slug='" + data_id + "']" ).length ) {

            // locate ##
            var target = jQuery( "[data-scroll-slug='" + data_id + "']" );
            var targetOffset = ( target.offset().top ) - 40;

            // scroll ##
            jQuery('html,body').animate({ 
                scrollTop: targetOffset + "px"
            }, 500, 'swing'); 

            // highlight ##
            jQuery( "#scroll-nav-"+data_id ).parent('span').addClass( 'current' );

			// // reset hash ##
			// window.location.hash = '';

        }

    }

    
    /*
    Check for passed hash value
    */
    function q_scroll_hash()
    {

        // get new hash string ##
        // var $hash = window.location.hash.substring(1);

        // if ( $hash.indexOf('scroll/') == 0 ) {

        //     console.log( 'No scroll...' );

        //     return false;

        // }

        // // remove "/filter/" from string ##
        // $hash = $hash.replace( '/scroll/','' ).trim().replace(/\//g, ''); //catches a bit more possible string weirdness like spaces and trailing slash
        // q_select_hash_value = window.location.hash.substring(1);

        // get new hash string ##
        var $hash = q_get_hash_value_from_key( 'scroll' );

        if ( ! $hash ) {

            return false;

        }

        // console.log( 'hash is: '+$hash );

        return $hash;

    }

}
</script>
<?php

    }


    

    public static function wp_head()
    {

        css::ob_get([
            'view'      => get_class(), 
            'method'    => 'css',
        ]);

    }



    /**
     * Render inline CSS
     * 
     * @since   2.0.0
     * @return  String
     */
    public static function css(  )
    {

?>
    <style>
        .q_scroll {     
            position: fixed;
            right: 0px;
            top: 50%;
            z-index: 1000; 
        }
        .q_scroll > span {
            list-style-type: none;
            list-style: none;
            margin: 0;
            text-align: center;
        }
        .q_scroll > span > a { 
            width: 40px;
            height: 40px;
            display: block;
        }
        .q_scroll > span > a:hover {
            text-decoration: none;
        }
        .q_scroll > span > a:before { 
            content:"\00b0"; 
            line-height: 2.5;
        }
    </style>
<?php

    }



}

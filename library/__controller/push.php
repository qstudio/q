<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\generic as generic;
use q\controller\javascript as javascript;
use q\controller\css as css;

// load it up ##
\q\controller\push::run();

class push extends \Q {
    
    public static $args = [];

    public static function run()
    {

        // add JS to footer if debugging or single q.theme.js script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 5 ); 

        // add css ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 4 ); 

    }


    /**
    * Build UI
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
            ! isset( self::$args['target'] )
        ) {

            helper::log( 'No target' );

            return false;

        }

        // compile markup ##
        $markup = str_replace( '%target%', $args['target'], $args['markup'] );

        // echo ##
        echo $markup;

    }



    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 50,
            'handle'    => 'Push'
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
    jQuery( 'body' ).on( 'click', '.q-push a', function(e) {
        
        $the_hash = jQuery( this ).attr('data-push-nav');
        if ($the_hash) q_push( $the_hash );

    });

    jQuery( window ).bind( "load", function(){

        $the_hash = q_push_hash();
        if($the_hash) q_push( $the_hash );
    
    });

    jQuery(document).ready(function() {
        
        // modern browsers 
        jQuery( window ).bind( 'hashchange', function( e ) {

            // console.log( 'Doing hash change...' );

            history.navigationMode = 'compatible';
            e.preventDefault();
            $the_hash = q_push_hash();
            if($the_hash) q_push( $the_hash );

        });

    });


    function q_push( data_id ){

        // remove all highlights ##
        // jQuery( ".q_push > span" ).removeClass( 'current' );

        // try to locate data element matching retreived hash value ##
        if ( jQuery( "[data-scroll-slug='" + data_id + "']" ).length ) {
            
            // locate ##
            var target = jQuery( "[data-scroll-slug='" + data_id + "']" );
            var targetOffset = ( target.offset().top );

            // test ##
            console.log( 'Element found: '+data_id );
            console.log( 'Push ScrollTo: '+targetOffset );

            // push ##
            jQuery('html,body').animate({ 
                scrollTop: targetOffset + "px"
            }, 500, 'swing'); 

            // highlight ##
            // jQuery( "#scroll-nav-"+data_id ).parent('span').addClass( 'current' );

        }

    }

    
    /*
    Check for passed hash value
    */
    function q_push_hash()
    {

        // get new hash string ##
        var $hash = window.location.hash.substring(1);

        if ( $hash.indexOf('push/') == 0 ) {

            console.log( 'No push...' );

            return false;

        }

        // remove "/push/" from string ##
        $hash = $hash.replace( '/push/','' ).trim().replace(/\//g, ''); // catches a bit more possible string weirdness like spaces and trailing slash

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
            'priority'  => 40,
            'handle'    => 'Push'
        ]);

    }



    /**
     * Render inline CSS
     * 
     * @todo - Viktor to style ##
     * 
     * @since   2.0.0
     * @return  String
     */
    public static function css( )
    {

?>
    <style>
        .q_push {     
            position: fixed;
            right: 0px;
            top: 50%;
            z-index: 1000; 
        }
    </style>
<?php

    }



}
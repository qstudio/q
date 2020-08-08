<?php

namespace q\controller;

use q\core\core;
use q\core\helper as h;
// use q\core\config as config;

// use q\controller\generic as generic;
use q\asset\javascript;
use q\asset\css;

// load it up ##
\q\controller\toggle::run();

class toggle extends \Q {
    
    public static $args = [];

    public static function run()
    {

        // add scripts ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 30 );

        // add CSS to header ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 3 );

    }



    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            // 'priority'  => 30,
            // 'handle'    => 'Toggle'
        ]);

    }




    /**
    * JS for toggle UI
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

?>
<script>

// jQuery ##
// if ( typeof jQuery !== 'undefined' ) {

jQuery( window ).bind( "load", function(){

    $the_hash = q_toggle_hash();
    if( $the_hash ) {
        
        q_toggle( $the_hash );

    } else {

        q_toggle_default();

    }

});

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

// };

function q_toggle_default(){

    // console.log( 'No hash..' );

    jQuery('.q-toggle-target').hide().addClass('q-toggle-hidden').removeClass('q-toggle-current');
    jQuery('.q-toggle-trigger').removeClass('q-toggle-current');

    jQuery( '.q-toggle-trigger:first-child' ).addClass('q-toggle-current');
    jQuery( '.q-toggle-target:first-child' ).removeClass('q-toggle-hidden').addClass('q-toggle-current').show();

}

function q_toggle( data_id ){
    
    $target = jQuery( "[data-toggle-target='"+data_id+"']" );

    if( $target.length ){
        
        // console.log( 'Show: '+data_id );

        // hide all targets ##
        jQuery('.q-toggle-target').each( function(){

            // console.log( 'hide..' );
            
            jQuery(this).hide().addClass('q-toggle-hidden').removeClass('q-toggle-current');

        });

        // remove highlight from all triggers ##
        jQuery( ".q-toggle-trigger" ).removeClass('q-toggle-current');

        // show target ##
        $target.show().addClass('q-toggle-current').removeClass('q-toggle-hidden');
        jQuery( "[data-toggle-trigger='"+data_id+"']" ).addClass('q-toggle-current');

    }
}   

/*
Check for passed hash value
*/
function q_toggle_hash(){

    // get new hash string ##
    var $hash = q_get_hash_value_from_key( 'toggle' );

    if ( ! $hash ) {

        // console.log( 'No toggle...' );

        return false;

    }

    return $hash;

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
            // 'priority'  => 3,
            // 'handle'    => 'Toggle'
        ]);

    }




    /**
     * Render inline CSS
     * 
     * @since   2.0.0
     * @return  String
     */
    public static function css()
    {

?>
    <style>
        .q-toggle-target:not(:first-child) {     
            display: none;
        }
        .q-toggle-hidden{
            display: none;
        }
        .q-toggle-current{
            color: red;
        }
    </style>
<?php

    }



}

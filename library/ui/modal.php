<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\javascript as javascript;
use q\controller\css as css;

// load it up ##
\q\controller\modal::run();

class modal extends \Q {
    
    static $args = array();

    public static function run()
    {

        // add assets ##
        \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ], 1000000 );

        // add JS to footer ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 3 );

        // add CSS to header ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 3 );

        // add JS to footer ##
        \add_action( 'wp_footer', [ get_class(), 'run_javascript' ], 10000000 );

    }



    public static function args( $args = false )
    {

        #helper::log( 'passed args to modal' );
        // helper::log( $args );

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

    }


    
    
    /**
    * Load assets
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function wp_enqueue_scripts()
    {

        // helper::log( helper::get( "theme/javascript/featherlight.min.js", 'return' ) );

        // featherlight JS ##
        \wp_register_script( 'featherlight-js', helper::get( "theme/javascript/featherlight.min.js", 'return' ), array( 'jquery' ), self::version, true );
        \wp_enqueue_script( 'featherlight-js' );

        \wp_register_script( 'featherlight-gallery-js', helper::get( "theme/javascript/featherlight.gallery.js", 'return' ), array( 'jquery' ), self::version, true );
        \wp_enqueue_script( 'featherlight-gallery-js' );

        // featherlight css
        \wp_register_style( 'featherlight-css', helper::get( "theme/css/featherlight.min.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'featherlight-css' );
        
        \wp_register_style( 'featherlight-gallery-css', helper::get( "theme/css/featherlight.gallery.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'featherlight-gallery-css' );

    }



    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 3,
            'handle'    => 'Modal'
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
// hash ##
$q_modal_hash_value = false;
$q_modal_key = false;
$q_modal_args = false;

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function() {

        // console.log( 'q_modal loaded..' );

        // modern browsers 
        jQuery( window ).bind( 'hashchange', function( e ) {

            // console.log( 'Doing hash change...' );

            e.preventDefault();

            q_modal_toggle( $q_modal_args );

        });

    });

}


/*
Run modal... ##
*/
function q_modal( $args )
{

    $args = $args || false ;

    if ( ! $args ) {

        // console.log( 'No args passed.' );

        return false;

    }

    // store those args ##
    $q_modal_args = $args;

    // console.dir( $q_modal_args );

    // load up modal if hash value passed on load ##
    q_modal_toggle( $q_modal_args )

}


function q_modal_hash( $q_modal_args )
{

    $q_modal_key = q_get_hash_value_from_key( 'modal' );

    // console.log( 'q_modal_key: '+$q_modal_key );

    return $q_modal_key;

}



var q_modal_close = function q_modal_close_function( e ){

    q_modal_do_close(e);

}

var q_modal_get_device = function (){

    $dev = jQuery('body').first().hasClass('device-mobile') ? 'handheld' : 'desktop';
    jQuery('.featherlight-content').parent().addClass( 'modal-' + $dev ); 

}

function q_modal_do_close(e){

    // console.log( e );
    // console.log( 'Closed Modal for key: '+$q_modal_key );
    // console.dir( jQuery( '[data-modal-key="'+$q_modal_key+'"]') );

    // no hash changes on close ##
    $hash = '';

    // check for a defined close data element ##
    if ( $close = jQuery( '[data-modal-key="'+$q_modal_key+'"]').find('span').attr( 'data-modal-close' ) ) {

        $hash = $close;

    } 

    // search for a scroll to data element ##
    if ( 
        $scroll = jQuery( '[data-modal-key="'+$q_modal_key+'"]').find('span').attr( 'data-modal-scroll' )
    ) {

        // console.log( 'scroll found: '+$scroll );

        // enable scroll only for desktop
        if ( jQuery( '[data-scroll="'+$scroll+'"]').length && !jQuery('body').first().hasClass('device-mobile')) {

            // // check if we have a defined scrollto position ##
            // $scrollto =
            //     jQuery( '[data-scroll="'+$scroll+'"]').data( 'scroll-position' ) ?
            //     jQuery( '[data-scroll="'+$scroll+'"]').data( 'scroll-position' ) :
            //     jQuery( '[data-scroll="'+$scroll+'"]').offset().top ;

            // jQuery('html,body').delay(2000).animate({
            //     scrollTop: jQuery( '[data-scroll="'+$scroll+'"]').offset().top
            // }, 500);

            // console.log( 'scrollto element found: '+$scroll  );
            // console.log( 'position: '+jQuery( '[data-scroll="'+$scroll+'"]').offset().top );

            $hash = $hash + '/scroll/' + $scroll

        }

    }

    // remove html tag ##
    jQuery('html').removeClass( 'modal-open' );
    jQuery('.featherlight').removeClass( 'modal-'+$q_modal_key );

    // console.log( 'Hash set to: '+$hash )

    // update hash ##
    window.location.hash = $hash;
    
    // negative ##
    return false;

}



function q_modal_toggle( $q_modal_args )
{

    // get new hash ##
    $q_modal_key = q_modal_hash();

    // console.log( 'q_modal_toggle: '+$q_modal_key );

    // kill all pre-existing instances of featherlight #
    // console.log( 'Kill featherlight' );
    jQuery('.featherlight').remove();

    if ( ! $q_modal_key ) {

        // console.log( 'no key, so kicking..' );

        return false;

    }

    // check for callback ##
    q_modal_callback( $q_modal_args, $q_modal_key );

    // add tracking classes ##
    jQuery('html').addClass( 'modal-open' );
    
    // get content ##
    $content = jQuery( '[data-modal-key="'+$q_modal_key+'"]').html();

    // decode ##
    //$content = q_html_decode( $content );

    // check for close data ##
    $q_modal_close = 

        // open with featherlight ##
        jQuery.featherlight( 
            
            // content ##
            $content, 
            
            // config ##
            { 
                type: 'html',
                beforeOpen: q_modal_get_device,
                afterClose: q_modal_close,
                afterOpen: q_do_lazy, // lazy load content in modal ##
                variant: 'modal-'+$q_modal_key
            } 
        
        );

}


function q_html_decode( input )
{
    var doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
}


function q_modal_callback( $q_modal_args, $q_modal_key ) {

    $q_modal_args = $q_modal_args || false;
    $q_modal_key = $q_modal_key || false

    if ( 
        ! $q_modal_args 
        || ! $q_modal_key 
    ) {

        // console.log( 'Callback Error.' );
        // console.dir( $q_modal_args );
        // console.dir( $q_modal_key );

        return false;

    }

    if ( $q_modal_args.callback ) {

        // build function ##
        _function = $q_modal_args.callback;

        // console.log( 'checking for callback:'+ _function );

        // load up modal engine ##
        if ( window[_function] ) {

            // console.log( 'calling callback: '+_function );
            window[_function]( $q_modal_key );

        } else {

            // console.log( 'callback not available' );

        }

    } else {

        //  console.log( 'callback not defined' )

    }

}

</script>
<?php

    }

    

    /**
    * JS for modal
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function run_javascript( $args = null )
    {

    // helper::log( self::$args );

?>
<script>
jQuery(document).ready(function() {

    // load up modal engine ##
    if ( typeof q_modal === 'function') {
        q_modal(<?php echo json_encode( self::$args ); ?>);
    }

    jQuery('.q-gallery').featherlightGallery({
        previousIcon: '❮',
        nextIcon: '❯',
        galleryFadeIn: 300,
        openSpeed: 300,
        variant: 'featherlight-gallery'
    });
});
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
            'handle'    => 'Modal'
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
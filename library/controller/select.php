<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\javascript as javascript;
use q\controller\css as css;

// load it up ##
\q\controller\select::run();

class select extends \Q {
    
    public static 
        $options,
        $args = [];

    public static function run()
    {

        // add JS ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 10 );

        // add CSS to header ##
        \add_action( 'wp_head', [ get_class(), 'wp_head' ], 4 );

    }



    public static function hook( $args = null )
    {

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

        // test ##
        #helper::log( self::$args );

        // instatiate JS ##
        \add_action( 'wp_footer', [ get_class(), 'run_javascript' ], 1000000 );

    }


    /**
    * Render select element
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function render()
    {

        // check if args are good ##
        if ( 
            empty( array_filter( self::$args ) )
        ) {

            helper::log( 'Missing Args.' );

            return false;

        }

        if ( 
            false == self::is_enabled()
        ){

            helper::log( "kicked - field group disabled in admin..." );

            return false;

        }  

        // assign ##
        #self::$args = $args;
        #helper::pr( $options );

        if ( ! self::$options = self::get_options() ) {

            #helper::log( 'Missing Options.' );

            return false;

        }

?>
        <select id="q-select">
<?php
        // loop over results ##
        foreach( self::$options as $option ) {

            // prepare ##
            $value = is_array( $option ) ? $option['value'] : self::tidy( $option ) ;
            $label = is_array( $option ) ? $option['label'] : $option ;

?>
            <option value="<?php echo $value; ?>" data-select="<?php echo $value; ?>"><?php echo $label; ?></option>
<?php

        }

?>
        </select>
<?php

    }



    public static function is_enabled()
    {

        // check we have all the field data we need ##
        if (
            isset( self::$args['enable'] ) // function set to enable disabling ##
        ) { 

            #self::log( "checking enable status of group: ".self::$args['group'] );
            #self::log( "post ID: ".get_the_ID() );
            
            $value = \get_field( self::$args['enable'], get_the_ID() );

            #self::log( "we got the enable value: ".$value );

            if ( 
                false === $value
                || '0' == $value 
                || ! $value
            ) {
        
                #self::log( "Group is not enabled: ".self::$args['group'] );

                return false;

            }

        }

        // default to yes ##
        return true;

    }



    public static function tidy( $string = null )
    {

        if ( is_null( $string ) ) {

            return false;

        }

        return strtolower( \sanitize_file_name( $string ) );

    }


    /**
    * Get options from defined points
    *
    * @since    2.0.0
    */
    public static function get_options()
    {

        #helper::log( self::$args['key'] );

        // check if args are good ##
        if ( 
            is_null( self::$args )
            || ! isset( self::$args['method'] )
        ) {

            helper::log( 'Missing Args or mal-formed.' );

            return false;

        }

        if ( ! $the_post = wordpress::the_post() ) {

            helper::log( "No post" );
            
            return false;

        }

        // blank ##
        $options = array();

        switch ( self::$args['method'] ) {

            case "get_field" ;
            default ;

                if( \have_rows( self::$args['field']['parent'], $the_post->ID ) ):
                    
                    while ( \have_rows( self::$args['field']['parent'], $the_post->ID ) ) : 
                    
                        \the_row();

                        $options[] = \get_sub_field( self::$args['field']['sub'], $the_post->ID );

                    endwhile;

                endif;

            break ;

            case "pattern" ;

                // get all post_meta ##
                $post_meta = \get_post_custom( $the_post->ID );

                // check ##
                if ( 
                    ! $post_meta 
                    || ! is_array( $post_meta )
                ) {

                    helper::log( "No post_meta" );
                    
                    return false;            

                }

                // check what we got ##
                // helper::log( $post_meta );

                /*
                Now we need to loop over all the returned post_meta and pick out ones which match the passed key 

                For example:
                $key = 'team_branches_%d_name';
                */
                foreach( $post_meta as $key => $value ) {

                    if ( preg_match( self::$args['pattern'], $key ) ){

                        if ( 
                            ! isset( self::$args["default"] ) 
                            && 0 == count( $options ) 
                        ) {

                            #helper::log( 'setting default: '.self::tidy( $value[0] ) );
                            self::$args['default'] = self::tidy( $value[0] );

                        }

                        helper::log( 'adding meta value for: '.$key );

                        // it matches - so add to our array ##
                        $options[] = $value[0];

                    }

                }

            break ;

        }

        // remove duplicates ##
        $options = array_unique( $options, SORT_REGULAR );

        // check ##
        #helper::log( $options );

        #helper::log( $options );
        // if( count( $options ) > 0 ) {

        //     // add some scripting ##
        //     \add_action( 'wp_footer', array( get_class(), 'javascript' ) );

        // }

        // kick it back ##
        return 0 == count( $options ) ? false : $options ;

    }


    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 4,
            'handle'    => 'Select'
        ]);

    }


    /**
    * Add Inline JS
    *
    * @since    2.0.0
    */
    public static function javascript()
    {
        
?>
<script>
// vars ##
var $q_select_hash_value;
var $q_select_args = false;

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function() {

        // modern browsers 
        jQuery( window ).bind( 'hashchange', function( e ) {

            // console.log( 'Doing hash change...' );

            // hash ##
            q_select_hash();

            // toggle ##
            q_select_change();

        });

    });

    // bind change event to select  ##
    jQuery( document ).on( 'change', 'select#q-select', function(e){
        
        // console.log( 'Select change..' );

        $value = jQuery(this).val();

        $show = jQuery( "div.q-select [data-select='" + $value + "']");

        // just once ##
        $shown = false;

        if ( $show && ! $shown ) {

            // hide all open modals ##
            // console.log( 'Hiding all modals' );
            jQuery( '.modal-data' ).removeClass('shown').addClass('hidden').hide(0);
            
            // kill all pre-existing instances of featherlight #
            jQuery('.featherlight').remove();

            // console.log( 'Showing: '+$value );
            jQuery( "div.q-select > *" ).fadeOut('fast');
            $show.fadeIn('fast');
            
            // update hash ##
            window.location.hash = '/filter/'+$value;

            // track ##
            $shown = true;

        }

    });

}

/*
Select control function 
*/
function q_select( $args )
{

    $args = $args || false ;

    // console.log( 'Select Loaded..' );

    if ( false == $args ) {

        // console.log( 'No args passed.' );

        return false;

    }

    // save args to global var ##
    $q_select_args = $args;

    // console.dir( $q_select_args );

    $hash = window.location.hash.substring(1);
    // console.log( 'hash is: '+$hash );

    // we should not run on page load when a modal is open ##
    if ( $hash.toLowerCase().indexOf( 'modal' ) >= 0 ) { 

        // console.log( 'Modal open, so bulk..' );

        return false;

    }

    // hash ##
    q_select_hash();

    // set default - not run if hash set ##
    q_select_default();

    // change ##
    q_select_change( true );

}


/*
Check for passed hash values and update selected:option
*/
function q_select_hash()
{

    $q_select_hash_value = q_get_hash_value_from_key( 'filter' );

    // q_select_hash_value = window.location.hash.substring(1);

    if ( ! $q_select_hash_value ) {

        // console.log( 'hash is not a filter' );

        $q_select_hash_value = false;

        return false;

    }

    return true;

}


/*
Change filtered content on select Change
*/
function q_select_change()
{

    if ( 
        false == $q_select_args 
        || false == $q_select_hash_value
    ) {

        // console.log( 'Missing args' );

        return false;

    }

    // console.log( 'q_select: change: '+$q_select_args );

    // scroll to below filters on first load, if filters set ##
    if ( $scroll = q_get_hash_value_from_key( 'scroll' ) ) {

        // console.log( 'q_select - scroll: '+$scroll );

        jQuery('html,body').delay(2000).animate({ 
            scrollTop: jQuery( '[data-scroll="'+$scroll+'"]').offset().top - 400
        }, 500);   
        
    }

    // hide all ##
    jQuery( 'div.q-select > *' ).hide(0);   

    // show selected meta group ##
    jQuery( "div.q-select [data-select='" + $q_select_hash_value + "']").show(0);

    // change option value ##
    jQuery( '#q-select').find('option[value="'+ $q_select_hash_value +'"]').prop('selected', 'selected');

}

/*
Set-up select and elements based on defined default
*/
function q_select_default()
{

    if ( false == $q_select_args ) {

        // console.log( 'Missing args' );

        return false;

    }

    // console.log( 'q_select: default: ' +$q_select_args.default );
    // console.log( 'hash is now..: '+$q_select_hash_value );

    if ( $q_select_hash_value ) {

        // console.log( 'No default setting if hash value set.' );

        return false;

    }

    // show selected meta group ##
    jQuery( "div.q-select [data-select='" + $q_select_args.default + "']").show(0);

    // change option value ##
    jQuery( '#q-select').find('option[value="'+ $q_select_args.default +'"]').prop('selected', 'selected');

    // update hash ##
    window.location.hash = '/filter/'+$q_select_args.default;

}

/*
function q_select_get_url() {

    return window.location.protocol + "//" + window.location.host + window.location.pathname;

}
*/
</script>
<?php

    }



    
    /**
    * JS for select
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

    jQuery(document).ready(function() {
        
        // load up select engine ##
        if ( typeof q_select === 'function') q_select(<?php echo json_encode( self::$args ); ?>);
    
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
            'priority'  => 4,
            'handle'    => 'Select'
        ]);

    }



    
    public static function css()
    {

?>
<style>
ul.q-select > ul /* all select content */
{
    display: none;
}
</style>
<?php

    }


}
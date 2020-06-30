<?php

namespace q\ui;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
// use q\theme\ui\ui\generic as generic;
// use q\controller\generic as q_generic;
use q\theme\ui as ui;
use q\controller\javascript as javascript;

// load it up ##
\q\ui\filter::run();

class filter extends \Q {
    
    public static 
        $args = [];


    public static function run()
    {

        // add JS ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 10 );

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
    * Build filter UI based on available posts types and year taxonomy values
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function select( $args = null )
    {

        // open tag ##
        ui::get_tag( $args['tag'], array( $args['class'], 'filters' ) );

        foreach ( $args['select'] as $select ) {

            // check if we have a pre-build array to render ##
            if ( is_array( $select ) ) {

                #helper::log( $select );

                // do select ##
                self::render_select( $select['title'], $select['array'], $args );

            // if not - check if this is a branch - and render ##
            } else if ( 'branch' == $select ) {

                // get branches ##
                $branches = \get_terms( array(
                    'taxonomy'      => 'branch', 
                    'hide_empty'    => true,
                    'parent'        => 0, // parent branches only ##
                    'exclude'       => array( \get_term_by( 'slug', 'general', 'branch' )->term_id ),
                ));

                // format response data ##
                #helper::log( $branches );
                
                $array = array();
                $array['all'] = \__( 'Filter by Branch' );

                foreach( $branches as $item ) {

                    $array[ $item->slug ] = $item->name;

                }

                // do select ##
                self::render_select( $select, $array, $args );

            }

        }

        // close tag ##
        ui::get_tag( $args['tag'], '', 'close' );

    }



    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            'priority'  => 1,
            'handle'    => 'Filter'
        ]);

    }



    /**
    * JS for filters
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript()
    {

?>
<script>
// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    // FUNCTIONS ##
    function q_filter_disable_options(){

        /*
        - loop over all available <select> elements - get tag
        - loop over each option - check for matching tag + value in html
        - if not match found, disable
        - continue... 
        */
        jQuery( 'select.q-filter' ).each(function() {

            jQuery( "select.q-filter > option:not(:selected)" ).each(function() {

                // get select type ##
                var type = jQuery(this).parent('select').data( 'filter-type' );
                // console.log( 'Select filter type: '+type );

                // data-{type} ##
                var $data = 'data-'+type;
                // console.log( 'data: '+$data );

                var $value = this.value;
                // console.log( 'value: '+$value );

                // leave 'all' alone ##
                if( 'all' == $value ) {

                    // console.log( 'Skipping all' );

                    return;

                }

                // test ##
                // console.dir( jQuery( '.q-filter' ).find("["+$data+"='" + $value + "']") );

                // try to find matching data in .q-filter-item with data-{type} = this.value ##
                if ( 0 == jQuery( '.q-filter' ).find('['+$data+'=' + $value + ']').length ) {
             
                    // console.log( 'there is no matching row for: '+type+' = ' +$value );
                    jQuery(this).prop("disabled", true);

                } else {

                    // console.log( 'there is a matching row for: '+type+' = ' +$value );
                    jQuery(this).prop("disabled", false);

                }

            });

        });

    }

    function q_get_filters(){
    
        // filter_branch = jQuery('select.filter-branch').val();
        q_filters = jQuery('.filters').children();
        if(typeof q_filters == 'undefined' || q_filters.length == 0 ){
            return false;
            } else if ( q_filters.length == 1 ) {
                q_filters = jQuery(q_filters[0]).val();
            } else {
                var temp_array = new Array();
                for (z=0; z < q_filters.length; z++ ){
                    if ( typeof jQuery( q_filters[z] ).val() !== 'undefined' ) { 
                        temp_array.push( jQuery( q_filters[z] ).val() ); 
                    }
                }
                q_filters = temp_array;
            }
        if( q_filters.length > 0 ) {
            return q_filters;
        }  else { 
            return false; 
        } 

    }

    function q_get_hash( hash ) { 
        
        hash = hash || window.location.hash.substring(1) ;

        console.log( 'q_filter:q_load_content:' + hash );

        if ( hash.indexOf('filter') !== 1 ) {

           console.log( 'Nothing cooking..' );

            return 'all';

        }

        // remove "/filter/" from string ##
        hash = hash.replace( '/filter/', '' );

        if( hash.indexOf('/') > 0 ) {
            
            // why is this required @benny ? ## 
            if ( hash.indexOf('/modal/') > 0 ) { 
                hash = hash.substring(0, hash.indexOf('/modal/') );
            } else {
                hash = hash.replace(/\/$/, ""); //pop off any trailing '/'' -- if modal this is accomplished on line 153
            }

            hash = hash.split('/');
            if ( hash.length == 1) hash = hash[0];
        }

        if( typeof hash !== 'undefined' && hash !== null && hash !== '' ) {
            console.dir( hash );
            return hash;
        } else { 
            return false; 
        }
    
    }
    
    function q_update_hash( ar ){
        
        if( Array.isArray( ar ) ) ar = ( ar.join('/') );                  
        
        // if(self::$debug) console.dir( ar );

        // update hash
        // window.location.hash = ar; 
        
        // update hash with '/filter/' prefix ##
        window.location.hash = '/filter/'+ar; 

    }

    function q_do_load( val ){          

        if( Array.isArray( val ) ) {

            var frag = jQuery(document.createDocumentFragment());
            var results = false;

            //if we have an array of filters make sure the args['select'] is set correctly to match the filters
            var filter_array = new Array(); //DIMENSIONAL FILTER ARRAY

            for( x=0; x<val.length; x++ ) { //return only matched criteria or 'all' for each data-type
            
                 console.log(results);
                var d_type = 'data-' + filter_class_types[x];
                 console.log(d_type);
                var d_val = val[x];
                 console.log(d_val);
                
                //set <select> option ##
                q_select_option( filter_class_types[x], d_val );

                //if we have results loop through and shrink them
                if( results ) {
                    
                   //console.log( 'doing this...' );

                    if( d_val == 'all' ) { 

                        var result = jQuery( '.item[' + d_type + ']' );
                        results = jQuery( results ).filter( result );
                        //results = jQuery( results ).find('.item[' + d_type + ']'); 

                    } else {
                        
                        var result = jQuery( '.item[' + d_type + '="' + d_val + '"]') ; 
                        results = jQuery( results ).filter( result );

                    }

                // if not establish initial set of filter results ##
                } else {

                   console.log( 'doing that...' );

                    if( d_val == 'all' ) { 

                        var results = jQuery( '.item[' + d_type + ']' );

                    } else {

                        var results = jQuery( '.item[' + d_type + '="' + d_val + '"]' );

                    }
                }
            }
                
            jQuery( results ).removeClass('hidden').addClass('shown').fadeIn('fast');          

        // if only one filter in hash ##
        } else { 

           //console.log( 'doing other...' );

             console.log( 'Val: '+val );

            // set <select> option ##
            q_select_option( filter_class_types, val );

            if( Array.isArray( filter_class_types ) ) { 
               //console.log( 'one' );
                var data_query = filter_class_types[0];
            } else { 
               //console.log( 'two' );
                var data_query = filter_class_types;
               //console.log( data_query );
            }
            data_query = '[data-' + data_query + '="' + val + '"]';
            jQuery( '.item' + data_query ).removeClass('hidden').addClass('shown').fadeIn('fast');

        } 

        // AND FINALLY ##
        jQuery( filter_class_filterable ).each( function() {
            
            if( ! jQuery(this).children('*').hasClass('shown') ){
                // console.log( 'Showing empty message..' );
                jQuery(this).find('.filter-no-results').removeClass('hidden').addClass('shown').fadeIn("fast");

            }
             
        });

        $leadership = jQuery('ul.team-branch').first();
        console.log( $leadership );
        console.log( 'end of do load: is visible? ' + $leadership.is(":visible") );

    }
            
    function q_load_content( hash_val, scroll ){

        scroll = scroll || false ;

        console.log( 'q_load_content' );

        // Is hidden yet?
        $leadership = jQuery('ul.team-branch').first();
        console.log( $leadership );
        console.log( 'is visible? ' + $leadership.is(":visible") );
        // HIDE EVERYTHING ##
        jQuery( '.filter-no-results' ).removeClass('shown').addClass('hidden').hide(0);
        jQuery( '.modal-data' ).removeClass('shown').addClass('hidden').hide(0);
        jQuery( filter_class_filterable ).find( '> .item' ).addClass('hidden').removeClass('shown').hide(0);
        
        // check for unavailable options and disable ##
        q_filter_disable_options();

        // console.log('load_content hides everything')
        
        // scroll to below filters on first load, if filters set ##
        if ( scroll && hash_val ) {

            // console.log( 'q_filter: scroll' );            
     
            jQuery('html,body').animate({ 
                scrollTop: jQuery( filter_class_filter ).offset().top - 60
            }, 500);
            
        }

        // if first page load with no hash or if hash value is a single = 'all' ##
        if( ! hash_val || hash_val == 'all' ) { 

            // console.log('doing filter load...');

            // set <select> option to all ##
            q_select_option( filter_class_types, 'all' );

            // show first x rows ##
            jQuery( filter_class_filterable ).find( '> .item' ).slice( 0, filter_show_on_load ).addClass( 'shown' ).fadeIn( 'fast' );
            
        } else { 
            
            q_do_load( hash_val ); 
        
        }

        // callback ##
        q_filter_callback();

        // // reset position of elements ##
        // jQuery('[data-scroll]').each(function(){

        //     console.log( 'Value:'+ jQuery(this).data('scroll') +' Position: '+ jQuery(this).offset().top );

        //     jQuery(this).attr( 'data-scroll-position', jQuery(this).offset().top ) ;

        // });

    }

    function q_select_option( element, option ){

        // change option value ##
        jQuery( 'select.filter-'+element ).find('option[value="'+ option +'"]').prop('selected', 'selected');

    }

    function q_filter_callback()
    {

        // callback is defined as an array, so loop over each ##
        // console.dir( filter_callback );

        // load up modal engine ##
        if ( 
            filter_callback 
            && typeof( filter_callback ) == "object" 
        ) {

            jQuery.each( filter_callback, function( index, value ){
                
                if ( window[value] ) {
        
                     console.log( 'calling callback from filter js: '+value );
        
                    window[value]();
        
                } else {
        
                     console.log( 'callback not available: '+value );
        
                }
        
            });

        }

    }

}
</script>
<?php

    }



    
    /**
    * JS for select
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function run_javascript()
    {

        // helper::log( self::$args );

        // check if args are good ##
        if ( 
            empty( array_filter( self::$args ) )
        ) {

            helper::log( 'Missing Args.' );

            return false;

        }

?>
<script>

// VARIABLES ##
filter_show_on_load = '<?php echo self::$args['show_on_load']; ?>';
filter_branch = false;
filter_query = false;
filter_class_filter = 'select.<?php echo self::$args["class"]; ?>-filter';                         
filter_class_filterable = '.<?php echo self::$args["class"]; ?>-filterable';
filter_class_types = JSON.parse('<?php echo json_encode( self::$args['select'] ); ?>');
filter_callback = JSON.parse('<?php echo json_encode( self::$args['callback'] ); ?>');
// console.dir( filter_callback );

// BINDERS ##
jQuery(document).ready(function() {

    // modern browsers 
    jQuery( window ).bind( 'hashchange', function( e ) {

        // console.log( 'Doing hash change...' );

        e.preventDefault();

        // console.log( q_get_hash );
        q_load_content( q_get_hash() );

    });

    // check for select change, update shows rows ##
    jQuery( document ).on( 'change', filter_class_filter, function(e){

        // Changes hash which triggers q_get_hash and q_load_content functions ##
        q_update_hash( q_get_filters() );

    });
            
    // LOAD IT UP ##
    // console.log( 'Loading up filters' );
    var start_data = q_get_hash();
    if( start_data == 'all') {
    
        q_load_content('all');
    
    } else {
            
        q_load_content( start_data, 'scroll' );  
        
    }

});
</script>
<?php

    }



    protected static function render_select( $class = null, $array = null, $args = null )
    {

        if ( is_null( $array ) ) {

            return false;

        }

?>
        <select class="q-filter <?php echo $args['class'] ;?>-filter filter-<?php echo $class; ?>" data-filter-type="<?php echo $class; ?>">
<?php
        
        foreach( $array as $key => $value ) {

?>
            <option value="<?php echo $key; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php

        }

?>
        </select>
<?php

    }



}
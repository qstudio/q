
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

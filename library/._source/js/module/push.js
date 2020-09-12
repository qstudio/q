// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    // binders ##
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
	
}

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

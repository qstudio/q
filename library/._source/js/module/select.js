/*
 * $ Calls v0.9 ##
 * (c) 2012 Q Studio - qstudio.us
 */

// vars ##
var $q_select_hash_value;
var $q_select_args = false;

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function() {

        // bind hash changes ##
        if ( ( "onhashchange" in window) && ! ( jQuery.browser.msie ) ) { 

            //modern browsers 
            jQuery( window ).bind( 'hashchange', function( e ) {

                // hash ##
                q_select_hash();

                // toggle ##
                q_select_change();

            });

        } else {

            //IE and browsers that don't support hashchange
            jQuery('a.hash-changer').bind('click', function() {

                // hash ##
                q_select_hash();

                // toggle ##
                q_select_change();

            });

        }

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
function q_select( $args ) {

	$args = $args || false;

    if ( false == $args ) {

        console.log( 'No args passed.' );

        return false;

    }

    // save args to global var ##
    $q_select_args = $args;

    // console.dir( args );

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

    // get new hash string ##
    $q_select_hash_value = window.location.hash.substring(1);
    // console.log( 'hash is: '+$q_select_hash_value );

    // remove "/filter/" from string ##
    $q_select_hash_value = $q_select_hash_value.replace( '/filter/','' );

    /*
    // check if hash includes any forward slashes and if so, get first value ##
    if ( $q_select_hash_value.toLowerCase().indexOf("modal") >= 0 ) {

        // if ( 
            $q_select_slash = $q_select_hash_value.split('/');

            $q_select_hash_value = $q_select_slash[1] ? $q_select_slash[1] : false ;

            console.log( 'hash contains modal command:' );
            console.log( 'bash hash: '+$q_select_hash_value );

            // return $q_select_key;

        // }

    }
    */

    // q_select_hash_value = window.location.hash.substring(1);

    if ( ! $q_select_hash_value ) {

        return false;

    }

    // console.log( 'q_select: hashing: '+$q_select_hash_value );

    // // hide all ##
    // jQuery( 'div.q-select > ul' ).hide(0);   

    // // show selected meta group ##
    // jQuery( "div.q-select [data-select='" + $q_select_hash_value + "']").show(0);

    // // change option value ##
    // jQuery( '#q-select').find('option[value="'+ $q_select_hash_value +'"]').prop('selected', 'selected');

    // if ( history.pushState ) {

    //     // window.history.pushState({path:newurl},'',newurl);

    //     var History = window.History;

    //     if ( ! History.enabled ) { 
            
    //         console.log( 'No History enabled' );
    //         return false; 
    // }        

    //     var currentIndex = History.getCurrentIndex();
    //     var internal = ( History.getState().data._index == ( currentIndex - 1 ) );

    //     // push state running internally ##
    //     if ( internal ) { 

    //         console.log( 'Internal PushState...' );
    //         return false;

    //     }
        
    //     // build new URL with fragments ##
    //     var newurl = q_select_get_url() +'#'+ $q_select_hash_value;

    //     // push to history object ##
    //     History.pushState(
    //         { content : $('#q-search').html() }, // save content ##
    //         "", // update title ##
    //         newurl // URL reference ##
    //     );

    // }

}


/*
Change filtered content on select Change
*/
function q_select_change( scroll ) {

	$scroll = $scroll || false;

    if ( 
        false == $q_select_args 
        || false == $q_select_hash_value
    ) {

        console.log( 'Q select hash value ' + 'Missing args' );

        return false;

    }

    // console.log( 'q_select: change: '+$q_select_args );

    // scroll to below filters on first load, if filters set ##
    if ( scroll ) {

        console.log( 'q_select: scroll' );

        jQuery('html,body').animate({ 
            scrollTop: jQuery( '.q-select' ).offset().top - 60
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

         console.log( ' q_select_default Missing args' );

        return false;

    } else { console.log( $q_select_args);}

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

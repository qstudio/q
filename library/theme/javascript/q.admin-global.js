function q_snackbar( options ){

    // check if the object exists ##
    if ( typeof jQuery.snackbar === 'undefined' ) {

        // console.log( 'No snacks available...');

        return false;

    }

    // global config ##
    defaults = {
        'content'	: 'Something went wrong :(',
        'class' 	: 'q-snackbar',
        'timeout'	: 5000,
        'id'		: 'q-snackbar',
        'stack'		: false // only show one at a time ##
    };

    // merge passed options ##
    jQuery.extend( defaults, options );

    // no stacking ##
    if ( ! options.stack ) {

        // console.log( 'Hiding Snacks' );

        // hide open snacks.. ##
        // jQuery('#'+options.id).hide().snackbar("hide");
        q_snackbar_delete();

    }

    // test ##
    // console.dir( options );

    // run the snackbar ##
    $snackbar = jQuery.snackbar(options);

    // kick something back ##
    return $snackbar;

}


function q_snackbar_delete(){

    jQuery('#snackbar-container > .snackbar').slideUp().remove();

}
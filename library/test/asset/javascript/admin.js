// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function($) {

        // user notification ##
        $(document).on( 'click', '.q-log-empty', function(e) {

            // console.log( 'Clicked Log Empty..' );

            e.preventDefault();
            var t = $(this);

            // console.log( 'File: '+$(this).data('log-file') );
            // console.log( 'Action: '+$(this).data('log-action') + '_empty_log' );
    
            $.ajax({
                url: q_test_admin.ajaxurl,
                type: 'POST',
                data: {
                    action:     $(this).data('log-action') + '_empty_log', //'email_empty_log',
                    log_file:   $(this).data('log-file'),
                    cache:      false,
                    nonce:      q_test_admin.nonce // @todo...
                },
                dataType: 'json',
                beforeSend: function () {

                    // console.log( 'url: '+ q_user.ajaxurl )
                    if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }
                    $(t).attr('disabled','disabled');

					t.html( 'Emptying Log...' );
                    // open snackbar ##
                    // q_snack({
                    //     content:    'Emptying Log...', // msg ##
                    //     timeout:    0, // never timeout ##
                    //     style:      'dark'
                    // });

                },
                success: function( response ) {

                    // console.dir( response )
                    if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }
                    if ( response ) {

                        // delete log file rows ##
                        $('p.q_support_log_format').remove();

                        // open snackbar ##
                        // q_snack({
                        //     content:    response.text, // msg ##
						// 	timeout:    0, // timeout ##
						// 	style:      'dark'
						// });
						
						t.html( response.text );

                    } else {

                        $(t).removeAttr('disabled');

                    }
                }

            });
        
            return false;
        
        });

            
    });

}

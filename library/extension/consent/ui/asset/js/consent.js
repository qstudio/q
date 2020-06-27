/*
 * $ Calls v0.9 ##
 * (c) 2012 Q Studio - qstudio.us
 */

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    (function ($) {
        // $( window ).on( "load", function(){
         //    $('.q-tab-trigger').removeClass('active');
		// 	$('.q-tab-current').addClass('active');
        // });
        //
        // $(document).on('click', '.q-tab-trigger', function() {
         //    $('.q-tab-trigger').removeClass('active');
         //    $('.q-tab-current').addClass('active');
		// });

        $( document.body).on( "click", ".q-consent-option .slider", function(e){
            // reject disabled option ##
            if ($(this).prev().is(':disabled')) {
                q_snackbar({
					content:    q_consent.disabled, // msg ##
					timeout:    5000, // never timeout ##
					style: 		'error'
				});

				return false;
			}
        });

		$( document.body).on( "change", ".q-consent-option input", function(e){
			var t = this;

			var $field = $(t).closest('.q-consent-option').data('q-consent-field'); // get field ##
			var $value = $(t).is(':checked') ? $(t).val() : 0; // get value ##
			// update tracking values ##
            $btn = $(t).closest('.q-consent-modal').find('.q-consent-set');
			// $btn.attr( 'disabled', false );
            $btn.data( 'q-consent-'+$field, $value );
			$btn.attr( 'data-q-consent-'+$field, $value );

			// $('[data-q-consent-="'+$field+'"]').val( $value );
			// $( '.q-consent-set' ).data( 'q-consent-marketing', $value );

		});

		// bootstrap-js hack - Bolts on the active class for bootstrap tabs - nothing else ##
        $( document.body ).on( "click", ".q-tab-trigger", function(e){
        	//this doesn't work. the existing q-tabs JS writes over it. That global JS should be updated to accomodate Bootstrap tab styles
        	var t = e.target;
        	$('.q-tab-trigger').removeClass('active');
        	$(t).addClass('active');

        });

		// save settings ##
        $( document.body ).on( "click", ".q-consent-set", function(e){

			e.preventDefault();
			var t = this;

			// collect data for process ##
			var $marketing = $(t).data('q-consent-marketing'); // get marketing ##
			var $analytics = $(t).data('q-consent-analytics'); // get analytics ##

			// console.log( 'Marketing: '+$marketing );
			// console.log( 'Analytics: '+$analytics );

			// log ##
            // console.log( "Saving Consent settings..." );

			// clear progress ##
			if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

			$.ajax({
				url: q_consent.ajax_url,
				type: 'POST',
				data: {
						action: 				'consent_set'
					,	q_consent_marketing: 	$marketing
					,	q_consent_analytics: 	$analytics
					,   nonce: 					q_consent.ajax_nonce
				},
				dataType: 'json',
				beforeSend: function () {

					if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }

				},
				success: function ( response ) {

					// console.dir( response );

					if ( '200' == response.status ) {

						q_snackbar({
							content:    response.message, // msg ##
							timeout:    5000, // never timeout ##
							style: 		'success'
						});

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

						// we should hide the consent bar, as this is not longer required - the user can "x" out of the modal to close the process ##
						$('.q-consent-bar').hide();

					} else {

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

						q_snackbar({
							content:    response.message, // msg ##
							timeout:    5000, // never timeout ##
							style: 		'error'
						});

					}

				}

			});

        });


		// clear cookie callback - for debugging ##
		$( document.body ).on( "click", ".q-consent-reset", function(e){

			e.preventDefault();

            // console.log( "Resetting Consent settings..." );

			// clear progress ##
			if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

			var self = $(this);
			$.ajax({
				url: q_consent.ajax_url,
				type: 'POST',
				data: {
						action: 'consent_reset'
					,   nonce: q_consent.ajax_nonce
				},
				dataType: 'json',
				beforeSend: function () {

					if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }

				},
				success: function (response) {

					if ( response ) {
					    self.closest('.q-consent-modal').find('.settings input[type=checkbox]').attr('checked', 'checked');

						q_snackbar({
							content:    response.message, // msg ##
							timeout:    5000, // never timeout ##
							style: 		'success'
						});

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

					} else {

						q_snackbar({
							content:    q_consent.error, // msg ##
							timeout:    5000, // never timeout ##
							style: 		'error'
						});

					}

				}

			});

        });


    })(jQuery);

}
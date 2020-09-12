/*
 * $ Calls v0.9 ##
 * (c) 2012 Q Studio - qstudio.us
 */

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery( document ).ready( function(){

        // bind consent triggers ##
        jQuery( document ).on( 'click', '[data-trigger="consent"]', function() {

			// console.log( 'Clicked Consent Trigger' );
			jQuery('.q-consent-open').trigger( 'click' );
			
		});

		// callback on q_moadl open ##
		jQuery('#q_modal').on('show.bs.modal', function (event) {

			// console.log( 'INIT toggle..' );
			jQuery('.q-toggle').bootstrapToggle();

		});

        jQuery( document.body ).on( "click", ".toggle.disabled", function(e){
			
			// console.log( 'Clicked DISABLED consent input' );
			q_snack({
				content:    q_module.consent_disabled, // msg ##
				timeout:    3000, // 5 ##
				style: 		'dark'
			});

			return false;
        });

		jQuery( document.body ).on( "change", ".q-consent-option input", function(e){

			// console.log( 'Clicked consent input' );
			var t = this;

			var $field = jQuery(t).closest('.q-consent-option').data('q-consent-field'); // get field ##
			var $value = jQuery(t).is(':checked') ? jQuery(t).val() : 0; // get value ##

			// console.log( 'field: '+$field + ' value: '+$value )

			jQuery( '.q-consent-set' ).data( 'q-consent-'+$field, $value );

		});

		// save settings ##
        jQuery( document.body ).on( "click", '[data-consent="set"]', function(e){

			// console.log( 'Clicked SET...' );

			e.preventDefault();
			var t = this;

			// collect data for process ##
			var $marketing = jQuery(t).data('q-consent-marketing'); // get marketing ##
			var $analytics = jQuery(t).data('q-consent-analytics'); // get analytics ##

			// console.log( 'Marketing: '+$marketing );
			// console.log( 'Analytics: '+$analytics );

			// log ##
            // console.log( "Saving Consent settings..." );

			// clear progress ##
			if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

			jQuery.ajax({
				url: q_module.ajaxurl,
				type: 'POST',
				data: {
						action: 				'consent_set'
					,	q_consent_marketing: 	$marketing
					,	q_consent_analytics: 	$analytics
					,   nonce: 					q_module.nonce
				},
				dataType: 'json',
				beforeSend: function () {

					if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }

				},
				success: function ( response ) {

					// console.dir( response );

					if ( '200' == response.status ) {

						q_snack({
							content:    response.message, // msg ##
							timeout:    3000, // never timeout ##
							style: 		'dark'
						});

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

						// we should hide the consent bar, as this is not longer required - the user can "x" out of the modal to close the process ##
						jQuery('.q-consent-bar').hide();

					} else {

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

						q_snack({
							content:    response.message, // msg ##
							timeout:    3000, // never timeout ##
							style: 		'error'
						});

					}

				}

			});

        });


		// clear cookie callback - for debugging ##
		jQuery( document.body ).on( "click", ".q-consent-reset", function(e){

			e.preventDefault();

            // console.log( "Resetting Consent settings..." );

			// clear progress ##
			if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

			var self = jQuery(this);
			jQuery.ajax({
				url: q_module.ajaxurl,
				type: 'POST',
				data: {
						action: 'consent_reset'
					,   nonce: q_module.nonce
				},
				dataType: 'json',
				beforeSend: function () {

					if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }

				},
				success: function (response) {

					if ( response ) {

						// toggles on ##
						jQuery('.q-toggle').bootstrapToggle('on');

						// set inputs to default ##
					    self.closest('#q_modal').find('.settings input[type=checkbox]').attr('checked', 'checked');

						q_snack({
							content:    response.message, // msg ##
							timeout:    3000, // never timeout ##
							style: 		'dark'
						});

						if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }

					} else {

						q_snack({
							content:    q_module.consent_error, // msg ##
							timeout:    3000, // never timeout ##
							style: 		'error'
						});

					}

				}

			});

        });


    });

}

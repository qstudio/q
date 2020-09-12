/* Q Module ~ Bootstrap Modal */

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

	// FINDING AND BINDING
	jQuery( document ).ready( function(){

		// open modal with dynamic data ##
		jQuery(document).on("click","[data-modal-target]", function (e) {

			// stop ##
			e.preventDefault();

			// get target ##
			var target = jQuery(this).attr('data-modal-target');
			// console.log('modal target: ' + target);

			var title = jQuery(this).attr('data-modal-title');
			var body = jQuery(this).attr('data-modal-body');
			var size = jQuery(this).attr('data-modal-size') ? jQuery(this).attr('data-modal-size') : 'modal-normal' ;

			// sanity ##
			if( ! title || ! body || ! size ){
				console.log( 'Error in passed params' );
				// jQuery(target).modal('dispose');
				// return false;
			}

			// console.log( 'size: '+size );

			// add data ##
			jQuery(target).find('.modal-title').html(title);
			jQuery(target).find('.modal-body').html(body);
			jQuery(target).find('.modal-dialog').addClass(size);

			// open modal ##
			jQuery(target).modal("show");

		});

	});

}


if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		// check for collapse hash ##
		collapse_hash = q_get_hash_value_from_key( 'collapse' );
		// console.log( 'collapse hash: '+collapse_hash );
		var collapse_loaded = false;
		
		if ( collapse_hash ) {

			if ( jQuery('.bs-collapse').find('[data-hash="collapse/'+collapse_hash+'/scroll/'+collapse_hash+'"]').length ){

				// console.log( 'collapse found: '+collapse_hash );

				jQuery('.bs-collapse').find('[data-hash="collapse/'+collapse_hash+'/scroll/'+collapse_hash+'"]').trigger( 'click' );
				
				collapse_loaded = true;

			}

		} 

		/*
		if( false === collapse_loaded ) {

			// console.log( 'collapse_loaded == false' );

			// on load, if no tab active, make first tab-content active/show ##
			if( ! jQuery( '.bs-collapse > .nav-link' ).hasClass('active') ){
				// console.log( 'NO active tab...' );
				jQuery( '.bs-collapse .nav-link' ).first().addClass('active show');
				$first = jQuery( '.bs-collapse .nav-link' );
				// // console.log( $first.attr('aria-controls') )
				jQuery( '#'+$first.attr('aria-controls') ).addClass('active show');
			}

		}

		// allow external collapse triggers ##
		jQuery( '[data-trigger="collapse"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			e.preventDefault();
			window.location.hash = href;
			jQuery( '[data-collapse="collapse"][href="' + href + '"]' ).trigger( 'click' );
		} );

		*/

		// update hash value when bs4 collapses are used ##
		jQuery('.bs-collapse button').click(function (e) {
			window.location.hash = jQuery(this).data('hash');
			// console.log( 'Clicked here..'+jQuery(this).data('hash') );
			// jQuery( '.bs-collapse .nav-link' ).removeClass('active show');
		});
		

	});

};

if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		/*
		// store open tab in localstorage ## 
		jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			localStorage.setItem('activeTab', jQuery(e.target).attr('href'));
			console.log( 'Store tab: '+jQuery(e.target).attr('href') );
		});

		var activeTab = localStorage.getItem('activeTab');
		if(activeTab){
			jQuery('.nav-tabs a[href="' + activeTab + '"]').tab('show');
		}
		*/
		
		/*
		// buffer the last scroll position
		var lastScrollPosition = jQuery(window).scrollTop();

		jQuery('.bs-tabs').on('shown.bs.tab', function (e) {
			location.replace(jQuery(e.target).attr("href"));
			// revert back to last scroll position
			jQuery(window).scrollTop(lastScrollPosition);
		});
		*/

		// read hash from page load and change tab
		var tab_hash = document.location.hash;
		var prefix = "tab_";
		var tab_loaded = false;
		if (tab_hash) {

			if ( jQuery('.bs-tabs a[href="'+tab_hash.replace(prefix,"")+'"]').length ){
				
				q_tab = jQuery('.bs-tabs a[href="'+tab_hash.replace(prefix,"")+'"]');

				// console.log( q_tab );
				
				tab_loaded = true;

				q_tab.tab('show')

			}

		} 

		if( false === tab_loaded ) {

			// console.log( 'tab_loaded == false' );

			// on load, if no tab active, make first tab-content active/show ##
			if( ! jQuery( '.bs-tabs > .nav-link' ).hasClass('active') ){
				// console.log( 'NO active tab...' );
				jQuery( '.bs-tabs .nav-link' ).first().addClass('active show');
				$first = jQuery( '.bs-tabs .nav-link' );
				// console.log( $first.attr('aria-controls') )
				jQuery( '#'+$first.attr('aria-controls') ).addClass('active show');
			}

		}

		// allow external tab triggers ##
		jQuery( '[data-trigger="tab"]' ).click( function( e ) {
			var href = jQuery( this ).attr( 'href' );
			window.location.hash = href;
			jQuery( '[data-toggle="tab"][href="' + href + '"]' ).trigger( 'click' );
		} );

		// update hash value when bs4 tabs are used ##
		jQuery('.bs-tabs a').click(function (e) {
			window.location.hash = this.hash;
			jQuery( '.bs-tabs .nav-link' ).removeClass('active show');
		});

	});

};

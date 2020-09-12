if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		jQuery('.ap-btn-newcomment, .ap-btn-submit').addClass('mt-2 btn btn-primary').removeClass('ap-btn ap-btn-submit');
		
	});

	jQuery( document ).ajaxStop(function() {

		jQuery('.ap-btn-submit').addClass('btn btn-primary').removeClass('ap-btn ap-btn-submit');

	});

};

/*
* https://rudrastyh.com/wordpress/load-more-comments.html
*
* Load comments via AJAX
*/

jQuery(function($){
 
	// load more button click event
	$('.q_comment_loadmore').click( function(){

		var button = $(this);
 
		// decrease the current comment page value
		cpage--;
 
		$.ajax({
			url : ajaxurl, // AJAX handler, declared before
			data : {
				'action'	: 'q_ajax_comment_load', // AJAX action
				'post_id'	: parent_post_id, // the current post
				// 'cpage' 	: cpage, // current comment page
				'nonce'		: q_ajax_comment_params.load_nonce
			},
			type : 'POST',

			beforeSend : function ( xhr ) {
				button.text('Loading...'); // preloader here

			},

			success : function( data ){

				if( data ) {

					$('div.comment-list').empty(); // remove all comments loaded ##
					$('div.comment-list').append( data );
					// button.text('Load More Comments'); 
					 // if the last page, remove the button
					// if ( cpage == 1 )
					button.remove();

				} else {

					button.remove();

				}
			}
		});

		return false;

	});
 
});


/*
 * https://rudrastyh.com/wordpress/ajax-comments.html
 * 
 * validation functions
 */
 jQuery.extend(jQuery.fn, {
	/*
	 * check if field value lenth more than 3 symbols ( for name and comment ) 
	 */
	validate: function () {
		if (jQuery(this).val().length < 3) {jQuery(this).addClass('error');return false} else {jQuery(this).removeClass('error');return true}
	},
	/*
	 * check if email is correct
	 * add to your CSS the styles of .error field, for example border-color:red;
	 */
	validateEmail: function () {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
		    emailToValidate = jQuery(this).val();
		if (!emailReg.test( emailToValidate ) || emailToValidate == "") {
			jQuery(this).addClass('error');return false
		} else {
			jQuery(this).removeClass('error');return true
		}
	},
});

// track reply ID ##
var comment_reply_id = false; 
var comment_belowelement = false; 

jQuery(function($, undefined){

	// wp does not provide a filter to affect html here, so hack-away ##
	$('#commentform').addClass('col-12');

	// track reply to iD ##
	$( document ).on( 'click', '.q_comment_reply', function(e) {

		// e.preventDefault;
		
		// hacky styles to comment reply ##
		$('.comment-respond').addClass('row');

		// get reply details ##
		comment_reply_id = $(this).data( 'commentid' );
		comment_belowelement = $(this).data( 'belowelement' );
		// console.log( 'comment_reply_id: '+comment_reply_id+ ' - below: '+comment_belowelement );

		// carry on ##
		// return true;

	});

	// remove html hacks when reply is cancelled ##
	$( document ).on( 'click', '#cancel-comment-reply-link', function(e){

		$('.comment-respond').removeClass('row');

	});

});
 
jQuery(function($){

	/*
	 * On comment form submit
	 */
	$( '#commentform' ).submit(function(){

		// we need to know what comment we are replying to, to add results in correct place ##
 
		// define some vars
		var button = $('#submit'), // submit button
			respond = $('#respond'), // comment form container
			// relative = $(this).closest('.reply').find('.inputQty');
		    commentlist = $('.comment-list'), // comment list container
		    cancelreplylink = $('#cancel-comment-reply-link');
 
		// if user is logged in, do not validate author and email fields
		if( $( '#author' ).length )
			$( '#author' ).validate();
 
		if( $( '#email' ).length )
			$( '#email' ).validateEmail();
 
		// validate comment in any case
		$( '#comment' ).validate();
 
		// if comment form isn't in process, submit it
		if ( 
			!button.hasClass( 'loadingform' ) 
			&& !$( '#author' ).hasClass( 'error' ) 
			&& !$( '#email' ).hasClass( 'error' ) 
			&& !$( '#comment' ).hasClass( 'error' ) 
		){
 
			// ajax request
			$.ajax({
				type : 'POST',
				url : q_ajax_comment_params.ajaxurl, // admin-ajax.php URL
				data: $(this).serialize() + '&action=ajaxcomments&nonce='+q_ajax_comment_params.post_nonce, // send form data + action parameter
				beforeSend: function(xhr){
					// what to do just after the form has been submitted
					button.addClass('loadingform').val('Loading...');
				},
				error: function (request, status, error) {
					if( status == 500 ){
						alert( 'Error while adding comment' );
					} else if( status == 'timeout' ){
						alert('Error: Server doesn\'t respond.');
					} else {
						// process WordPress errors
						var wpErrorHtml = request.responseText.split("<p>"),
							wpErrorStr = wpErrorHtml[1].split("</p>");
 
						alert( wpErrorStr[0] );
					}
				},
				success: function ( result ) {

					// parse JSON to JS object ##
					var data = JSON.parse(result);
					// console.dir( data );
					var comment = data.comment;
					// console.log( 'comment id: '+comment );
 
					// if this post already has comments
					if( commentlist.length > 0 ){

						// console.log( 'There are already '+commentlist.length+' comments...' );
 
						// if in reply to another comment
						if( 
							comment_belowelement
							// respond.parent().hasClass( 'comment' ) 
						){
 
							// console.log( 'Comment below set to '+comment_belowelement );

							// if other replies exist
							if( respond.parent().children( '.has-children' ).length ){	
								
								// console.log( 'Comment has '+respond.parent().children( '.has-children' ).length+' children' );

								respond.parent().children( '.child' ).last().append( data.result );

							} else {

								// console.log( 'Adding comment html after #'+comment_belowelement );
								// if no replies, add <div class="children">
								data.result = '<div class="child-comments children">' + data.result + '</div>';
								// respond.parent().append( data.result );
								$('#'+comment_belowelement).append( data.result );

							}

							// close respond form
							// cancelreplylink.trigger("click");

						} else {

							// console.log( 'Reply to top level comment, add at end of list' );

							// simple comment
							commentlist.append( data.result );

						}
					} else {

						// if no comments yet
						data.result = '<div class="comment-list">' + data.result + '</div>';
						respond.before( $(data.result) );

					}

					// clear textarea field
					$('#comment').val('');

					// close respond form
					cancelreplylink.trigger("click");

					// console.dir( data );
					// console.log( 'comment id: '+comment );

					// locate ##
					var target = jQuery( "#comment-" + comment );
					// console.dir( target );
					var targetOffset = ( target.offset().top )-100;
		
					// scroll ##
					jQuery('html,body').animate({ 
						scrollTop: targetOffset + "px"
					}, 500, 'swing'); 

				},
				complete: function(){

					// what to do after a comment has been added
					button.removeClass( 'loadingform' ).val( 'Post Comment' );

				}
			});
		}
		return false;
	});
});

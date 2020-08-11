<?php

// filter comment form ##
$commenter  = \wp_get_current_commenter();
$format 	= \current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
$req      	= \get_site_option( 'require_name_email' );
$aria_req 	= ( $req ? " aria-required='true'" : '' );
$html_req 	= ( $req ? " required='required'" : '' );
$html5    	= 'html5' === $format;

// return an array ##
return [ 'module' => [ 'comment' => [

	// config ##
	'args'			=> [

		// get comments ##
		'get'		=> [
			'number'  			=> \get_site_option( 'comments_per_page' ) ?: 2, // just 2 on load
			'status' 			=> 'approve', 
		],

		// comment list style ##
		'list'		=> [
			'style'         	=> 'div',
			'max_depth'     	=> 3,
			'short_ping'    	=> true,
			'avatar_size'   	=> '42',
			'per_page'			=> \get_site_option( 'comments_per_page' ) ?: 2, 
			'reverse_top_level'	=> false,
			'walker'        	=> new \Comment_Walker(),
			// 'callback'			=> new \Comment_Walker(),
		],

	],

	// text ##
	'text'	=> [
		'title'			=> [ 'One Comment', '%1$s Comments' ], // one || many ##
		'default'		=> '<p class="no-comments">'.__( 'No comments on this article yet.', 'q-textdomain' ).'</p>',
		'closed'		=> '<p class="no-comments">'.__( 'Comments are now closed.', 'q-textdomain' ).'</p>',
	],

	// comment form ##
	'form' 		=> [

		'title_reply'			=> __( 'Add a Comment', 'q-textdomain' ),
		'title_reply_before'   	=> '<h3 id="reply-title" class="col-12 comment-reply-title mt-3">',
		'class_submit' 			=> 'btn btn-primary submit',
		'comment_field' => '<p class="comment-form-comment"><label for="comment">'._x( 'Comment', 'noun' ).'</label> <textarea id="comment" name="comment" class="form-control" cols="45" rows="8" aria-required="true" required="required" oninvalid="this.setCustomValidity(\'We cannot read you mind, just yet :)\')" oninput="setCustomValidity(\'\')"></textarea></p>',

		'fields' 		=> [
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
			'<input id="author" name="author" class="form-control" type="text" value="' . \esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
			'<input id="email" name="email" class="form-control" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . \esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>',
			/*
			'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
			'<input id="url" name="url" class="form-control" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
			*/

		]

	],

	// markup ##
	'markup' 		=> [
		'template'	=> '
			<hr />
			<span class="anchor" data-scroll-slug="comments"></span>
			<div id="comments" class="col-12 comments-area mt-2">
				<div class="row mt-2">
					<h5 class="comments-title col-8">{{ title }}</h5>
					<span class="col-4 text-right"><div class="btn btn-primary q_comment_loadmore">Load All Comments</div></span>
				</div>
				<div class="row py-md-1 my-md-2 px-sm-0 mx-sm-0 comment-list">
					{{ comments }}
				</div>
			</div>
			{{ load }} <!-- REQUIRED JS -->
			<div class="col-12 mt-4 comment-reply">
				<div class="row">
					<div class="col-12">
						{{ reply }}
					</div>
				</div>
			</div>
		'
	]

]]];

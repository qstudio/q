<?php

// return an array ##
return [

	// ui header ##
	'ui__header'  			=> [
		'markup' => ''
	],

	// ui footer ##
	'ui__footer'  			=> [
		'markup' => ''
	],

	// ui open ##
	'ui__open'  			=> [
		'markup' => '
		<main class="container {{ classes }}">
			<div class="row">
		'
	],

	// ui close() ##
	'ui__close'  			=> [
		'markup' => '
			</div>
		</main>'
	],

	// // the_avatar() ##
	// 'ui__avatar'  => [
	// 	'markup'				=> '<div class="the-avatar">%src%</div>',
	// ],

	// // navigation ---------
	// 'ui__navigation'  => [
	// 	'post_type'             => 'page',
	// 	'add_parent'            => false,
	// 	'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
	// ],

	// // navigation ---------
	// 'ui__nav_menu'  => [
	// 	// no wrapping ##
	// 	'items_wrap'        	=> '%3$s',
	// 	// do not fall back to first non-empty menu
	// 	'theme_location'    	=> '__no_such_location',
	// 	// do not fall back to wp_page_menu()
	// 	'fallback_cb'       	=> false,
	// 	'container'         	=> false,
	// ],

	// // use your pagination ---------
	// 'ui__pagination'  			=> [
	// 	'markup'             	=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
	// 	'wrap'             		=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ content }}</ul></div>',
	// 	'end_size'				=> 0,
	// 	'mid_size'				=> 2,
	// 	'prev_text'				=> '&lsaquo; '.\__('Previous', 'q-textdomain' ),
	// 	'next_text'				=> \__('Next', 'q-textdomain' ).' &rsaquo;', 
	// 	'first_text'			=> '&laquo; '.\__('First', 'q-textdomain' ),
	// 	'last_text'				=> \__('Last', 'q-textdomain' ).' &raquo',
	// 	'li_class'				=> 'page-item',
	// 	'class_link_item'		=> 'page-link',
	// 	'class_link_first' 		=> 'd-none d-md-inline page-link page-first d-none d-md-block',
	// 	'class_link_last' 		=> 'd-none d-md-inline page-link page-last d-none d-md-block'
	// ],

];

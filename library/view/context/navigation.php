<?php

// return an array ##
return [ 'navigation' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	],

	// siblings ---------
	'siblings'  	=> [
		'args' 					=> [
			'post_type'         => 'page',
			'add_parent'        => false,
			'posts_per_page'    => \get_option( "posts_per_page", 10 ),// per page ##
		],
		'markup'             	=> [
			'template'			=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
			'wrap'             	=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ template }}</ul></div>'
		],
	],


	// next_back ---------
	'relative'  	=> [
		'args' 					=> [
			'post_type'         => 'page',
			'add_parent'        => false,
			'posts_per_page'    => \get_option( "posts_per_page", 10 ),// per page ##
		],
		'markup'             	=> [
			'template'			=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
			'wrap'             	=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ template }}</ul></div>'
		],
	],

	// navigation ---------
	'menu'  		=> [
		'config'				=> [
			// 'type'				=> ''
		],		
		'args' 					=> [
			'echo'				=> true,
		]
	],

	// use your pagination ---------
	'pagination'  	=> [
		'markup'             	=> [
			'template'			=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
			'wrap'             	=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ template }}</ul></div>'
		],
		'end_size'				=> 0,
		'mid_size'				=> 2,
		'prev_text'				=> '&lsaquo; '.\__('Previous', 'q-textdomain' ),
		'next_text'				=> \__('Next', 'q-textdomain' ).' &rsaquo;', 
		'first_text'			=> '&laquo; '.\__('First', 'q-textdomain' ),
		'last_text'				=> \__('Last', 'q-textdomain' ).' &raquo',
		'li_class'				=> 'page-item',
		'class_link_item'		=> 'page-link',
		'class_link_first' 		=> 'd-none d-md-inline page-link page-first d-none d-md-block',
		'class_link_last' 		=> 'd-none d-md-inline page-link page-last d-none d-md-block'
	],

]];

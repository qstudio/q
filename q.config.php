<?php
 
 namespace q\core;

 use q\core as core;
 use q\core\helper as h;

/* 
 * Configuration File, loaded by q\core\config::get()
*/

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// re-usable config ------ ##

// return an array ##
return [

	// comments are ok ##
	'allow_comments' 			=> true,

	// date format ##
	'date_format'       		=> 'F j, Y',

	// image sizes ## @todo - add sizes and function to add via add_image_sizes in config ##
	'src'						=> [ 

		// @ todo, make this a global controller ##
		// add srcset to src references ##
		'srcset' 				=> true, 
		
		// @ todo, make this a global controller ##
		// wrap src in 'picture' element, with srcset ##
		'picture' 				=> true,

		// holder as data ref ##
		'holder'				=> 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgZGF0YS1uYW1lPSJMYXllciAxIiBpZD0iTGF5ZXJfMSIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOm5vbmU7c3Ryb2tlOiMwODNiNDM7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS13aWR0aDoyMHB4O30uY2xzLTJ7ZmlsbDojMDgzYjQzO308L3N0eWxlPjwvZGVmcz48dGl0bGUvPjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMjU2IiBjeT0iMjY3LjQyIiByPSIzMy45OSIvPjxwb2x5Z29uIGNsYXNzPSJjbHMtMSIgcG9pbnRzPSIzMDIuMiAyMDIuNTIgMjg4LjkgMTc5LjY4IDIyMy4xIDE3OS42OCAyMDkuOCAyMDIuNTIgMTQ0IDIwMi41MiAxNDQgMzMyLjMyIDM2OCAzMzIuMzIgMzY4IDIwMi41MiAzMDIuMiAyMDIuNTIiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9IjMzNy44IiBjeT0iMjMyLjQ5IiByPSIxMS40OSIvPjwvc3ZnPg==',

		// Golden Ratio ##
		'ratio' 					=> 1.618,
		'open' 						=> 9999, // open size ##
		'open_height' 				=> 600, // open size ##
		'scale'						=> 1, // scale sizes ##
		'pixel'						=> 2, // double vision ##
		'default'					=> 'horizontal-lg', // default handle ##

		// shape handles ##
		'handles'					=> [
			'square' 				=> [
				'sizes'				=> 'all', // create in all sizes 
				'width'				=> 'equal', // height = width ##
				'height'			=> 'equal', // height = width ##
				'pixel'				=> true, // handle for pixel image ##
				'crop'				=> true, // hard crop ##
				'open'				=> false // no open sized format
			],
			'horizontal' 			=> [
			 	'sizes'				=> 'all', // create in all sizes 
			 	'width'				=> 'equal', // height = width \ ratio ##
				'height'			=> 'divide', // height = width \ ratio ## 
			 	'crop'				=> true, // hard crop ##
				'open'				=> 'width', // open sized image with no fixed width
				'pixel'				=> true, // handle for pixel image ##
			],
			'vertical' 			=> [
			 	'sizes'				=> 'all', // create in all sizes 
			 	'width'				=> 'equal', // height = width \ ratio ##
				'height'			=> 'multiply', // height = width * ratio ## 
			 	'crop'				=> true, // hard crop ##
				 'open'				=> 'height', // open sized image with no fixed width
				'pixel'				=> true, // handle for pixel image ##
			],
		],

		// image_size logic ##
		'sizes'			=> [
			'xs'				=> 300,
			'sm'				=> 576,
			'md'				=> 720,
			'lg'				=> 960,
			'xl'				=> 1200, 
		]
	],

	// the_container_open() ##
	'container_open'  			=> [
		'markup' => '<main class="container %classes%">
			<div class="row">'
	],

	// the_container_close() ##
	'container_close'  			=> [
		'markup' => '
			</div>
		</main>'
	],

	// acf field groups ##
	'group'  				=> [
		'config' 				=> [ 'run' => true ],
		// 'filter' => [ 'src' => true ] // add srcsets ##
	],

	// post_title ##
	'title'  					=> [
		'markup' 				=> '<h1 class="col-12 the-title text-uppercase">%title%</h1>',
	],

	// post_parent ##
	'parent'  					=> [
		'markup' 				=> '<h4 class="col-12 the-parent"><a href="%permalink%">%title%</a></h4>',
	],

	// post_excerpt ##
	'excerpt'					=> [
		'markup'  				=> '<div class="col-12 mb-3 the-excerpt">%content%</div>',
		'limit' 				=> 300, // default excerpt length ##
	],

	// post_content ##
	'content'  				=> [
		'markup'                => '<div class="col-12 the-content">%content%</div>',
	],

	// the_category() ## 
	// @todo.. we need to work out this with post_meta.. or forget that and just have templates in each template.. ## better
	'the_category'  => [
		'markup'                => '<span class="category ml-1 mr-1">in <a href="%permalink%">%title%</a></span>',
	],

	// get_posts() ##
	'posts'  => [

		// config ##
		'config'				=> [ 
									// 'run' => true, 
									'debug' => false, 
									// 'load' => 'the_posts'  // change loaded config ##
									// 'srcset' => true // add srcsets ##
								],
		
		// UI ##
		'markup'				=> [ 
								'template'=> 
									'<div class="col-12 the-posts">
										<div class="row"><h5 class="col-12 mt-2">%total% Results Found.</h5></div>
										<div class="row mt-3">%posts%</div>
										<div class="row"><div class="col-12">%pagination%</div></div>
									</div>',
								// post template ##
								'posts'	=> 
									'<div class="card p-0 col-12 col-md-6 col-lg-4 mb-3">
										<a href="%permalink%" title="%post_title%" class="mb-3">
											<img class="lazy fit card-img-top" style="height: 200px;" data-src="%src%" src="" />
										</a>
										<div class="card-body">
											<h5 class="card-title">
												<a href="%post_permalink%">
													%post_title%
												</a>
											</h5>
											<p class="card-text">%post_excerpt%</p>
											<p class="card-text">
												<small class="text-muted">Posted %post_date_human% ago</small>
												<small class="text-muted">in <a href="%category_permalink%" title="%category_name%">%category_name%</a> </small>    
											</p>
										</div>
									</div>',
								// no results ##
								'no_results'			
									=> '<div class="col-12"><p>We count not find any matching posts, please check again later.</p></div>', 

								],

		// config ##
		'wp_query_args'			=> [
									'post_type'				=> [ 'post' ], // post -- force no results ##
									'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
									'limit'                 => \get_option( "posts_per_page", 10 ), // posts to load ##
									// 'query_vars'            => false, // only wp_query what we pass in config ##
								],	
		'length'                => '200', // return limit for excerpt ##
		'handle'                => 'medium', // image handle ## srcset returns device sizes ##
		// 'date_format'           => 'U',
		// 'allow_comments'        => false, // show comment count - might slow up query ##
	],

	// search results ##
	'search'  => [

		// config ##
		'config'				=> [ 
									// 'run' => true, 
									'debug' => false, 
									// 'load' => 'the_posts'  // change loaded config ##
								],
		
		// UI ##
		'markup'				=> [ 
								// main template ##
								'template' => 
									'<div class="col-12 the-posts">
										<div class="row"><h5 class="col-12 mt-2">%total% Results Found.</h5></div>
										<div class="row mt-3">%posts%</div>
										<div class="row"><div class="col-12">%pagination%</div></div>
									</div>',

								// highlight ##
								'highlight' => 
									'<mark>%string%</mark>',

								// post template ##
								'posts'	=> 
									'<div class="card p-0 col-12 col-md-6 col-lg-4 mb-3">
										<a href="%permalink%" title="%post_title%" class="mb-3">
											<img class="lazy fit card-img-top" style="height: 200px;" data-src="%src%" src="" />
										</a>
										<div class="card-body">
											<h5 class="card-title">
												<a href="%post_permalink%">
													%post_title%
												</a>
											</h5>
											<p class="card-text">%post_excerpt%</p>
											<p class="card-text">
												<small class="text-muted">Posted %post_date_human% ago</small>
												<small class="text-muted">in <a href="%category_permalink%" title="%category_name%">%category_name%</a> </small>    
											</p>
										</div>
									</div>',
								
								// no results ##
								'no_results'			
									=> '<div class="col-12"><p>We count not find any matching posts, please check again later.</p></div>', 

								],

		// config ##
		'wp_query_args'			=> [
									'post_type'				=> [ 'page' ], // post -- force no results ##
									'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
									'limit'                 => \get_option( "posts_per_page", 10 ), // posts to load ##
									'query_vars'            => true, // only wp_query what we pass in config ##
								],	
		'highight'				=> true, // @todo - add to controls -- highlight results in excerpt ##
		'highlight_wrap'		=> '<mark>%string%</mark>', // @todo - passed to render -- markup to highlight result ##
		'length'                => '200', // return limit for excerpt ##
		'handle'                => 'medium', // image handle ## srcset returns device sizes ##
		// 'date_format'           => 'U',
		'allow_comments'        => false, // show comment count - might slow up query ##
	],

	// the_post_single() ##
	'post_single'  => [
		'allow_comments'        => 'allow_comments', // allow comments ##
		'next_back'            	=> ( h::device() == 'desktop' ) ? false : true, // next / home / back links ##
	],

	// the_post_meta() ##
	/*
	'post_meta'  => [
		'format'				=> [
									'loop' => 
										'<div class="the-post-meta">Posted %post_date_human% ago in %the_category%</div>',
									'single' => 
										'<div class="the-post-meta">
											Posted %post_date_human% ago in %the_category%, Tagged %the_tags% | %the_comments%
										</div>'
								]
	],
	*/

	// the_avatar() ##
	'avatar'  => [
		'markup'				=> '<div class="the-avatar">%src%</div>',
	],

	// get_post_by_meta() ##
	'get_post_by_meta' => [
		'meta_key'              => 'page_name',
		'post_type'             => 'page',
		'posts_per_page'        => 1,
		'order'					=> 'DESC',
		'orderby'				=> 'date'
	],

	// navigation ---------
	'navigation'  => [
		'post_type'             => 'page',
		'add_parent'            => false,
		'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
	],

	// navigation ---------
	'nav_menu'  => [
		// no wrapping ##
		'items_wrap'        	=> '%3$s',
		// do not fall back to first non-empty menu
		'theme_location'    	=> '__no_such_location',
		// do not fall back to wp_page_menu()
		'fallback_cb'       	=> false,
		'container'         	=> false,
	],

	// use your pagination ---------
	'pagination'  			=> [
		'item'             		=> '<li class="%li_class%%active-class%">%item%</li>',
		'markup'             	=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">%content%</ul></div>',
		'end_size'				=> 'desktop' == h::device() ? 0 : 0,
		'mid_size'				=> 'desktop' == h::device() ? 4 : 0,
		'prev_text'				=> 'desktop' == h::device() ? '&lsaquo; '.\__('Previous', 'q-textdomain' ) : '&lsaquo;',
		'next_text'				=> 'desktop' == h::device() ? \__('Next', 'q-textdomain' ).' &rsaquo;' : '&rsaquo;', 
		'first_text'			=> '&laquo; '.\__('First', 'q-textdomain' ),
		'last_text'				=> \__('Last', 'q-textdomain' ).' &raquo',
		'li_class'				=> 'page-item',
		'class_link_item'		=> 'page-link',
		'class_link_first' 		=> 'page-link page-first d-none d-md-block',
		'class_link_last' 		=> 'page-link page-last d-none d-md-block'
	],

];
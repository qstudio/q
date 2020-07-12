<?php

// return an array ##
return [ 'global' 		=> [

	'config'			=> [

		// // comments are ok ##
		// 'allow_comments' 	=> true,

		// // date format ##
		// 'date_format'      	=> 'F j, Y',

		// acf field groups ##
		'group'  			=> [
			'config' 				=> [ 'run' => true ],
			// 'filter' => [ 'src' => true ] // add srcsets ##
		],

		// single field ##
		'field'  			=> [
			'config' 				=> [ 'run' => true ],
			// 'markup' 				=> '<span class="the-field the-field-%field%">%value%</span>',
		],

		// // UI controls 
		// 'ui'  					=> [
		// 	'run' => true,
		// 	// 'markup' 				=> '<span class="the-field the-field-%field%">%value%</span>',
		// ],

	],

	/*
	// search results ##
	'search__query'  => [

		// config ##
		'config'				=> [ 
									// 'run' => true, 
									'debug' => false, 
									// 'load' => 'posts'  // change loaded config ##
								],
		
		// UI ##
		// main template ##
		'markup' 				=> [
			'template'			=>
									'<div class="row pb-1">
											<div class="col-12 the-posts">
											<div class="row"><h5 class="col-12 mt-2">{{ total }} Results Found.</h5></div>
											<div class="row mt-3">{{ posts }}</div>
											<div class="row"><div class="col-12">{{ pagination }}</div></div>
										</div>
									</div>',

			// highlight ##
			'highlight' => 
									'<mark>%string%</mark>',

			// post template ##
			'posts'	=> 
									'<div class="card p-0 col-12 col-md-6 col-lg-4 mb-3">
										<a href="%permalink%" title="{{ post_title }}" class="mb-3">
											<img class="lazy fit card-img-top" style="height: 200px;" data-src="{{ src }}" src="" />
										</a>
										<div class="card-body">
											<h5 class="card-title">
												<a href="{{ post_permalink }}">
													{{ post_title }}
												</a>
											</h5>
											<p class="card-text">{{ post_excerpt }}</p>
											<p class="card-text">
												<small class="text-muted">Posted {{ post_date_human }} ago</small>
												<small class="text-muted">in <a href="{{ category_permalink }}" title="{{ category_name }}">{{ category_name }}</a> </small>    
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
		'highlight_wrap'		=> '<mark>{{ string }}</mark>', // @todo - passed to render -- markup to highlight result ##
		'length'                => '200', // return limit for excerpt ##
		'handle'                => 'medium', // image handle ## srcset returns device sizes ##
		// 'date_format'           => 'U',
		'allow_comments'        => false, // show comment count - might slow up query ##
	],
	*/

]];

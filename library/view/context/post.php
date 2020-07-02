<?php

// return an array ##
return [

	// post_title ##
	'post__title'  				=> [
		'markup' 				=> '<h1 class="pb-2 col-12 the-title">{{ title }}</h1>',
	],

	// post_parent ##
	'post__parent'  				=> [
		'markup' 				=> '<h4 class="pb-1 col-12 the-parent"><a href="{{ permalink }}" title="Open {{ title }}">{{ title }}</a></h4>',
	],

	// post_excerpt ##
	'post__excerpt'				=> [
		'markup'  				=> '<div class="pb-1 col-12 mb-3 the-excerpt">{{ content }}</div>',
		'limit' 				=> 300, // default excerpt length ##
	],

	// post_content ##
	'post__content'  			=> [
		'markup'                => '<div class="pb-1 col-12 the-content">{{ content }}</div>',
	],

	// post_meta
	// Tag: <a href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>
	'post__data' 				=> [
		// 'type' 					=> 'single',
		'markup'				=> [
			'template'			=> '
								Posted {{ post_date_human }} ago 
								by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
								in <a href="{{ category_permalink }}" title="See posts in {{ category_title }}">{{ category_title }}</a>
								Tagged: {{# tags }}
									<a href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>&nbsp;
								{{/#}}
								Comments: <a href="{{ comment_permalink }}" title="Comments">{{ comment_title }} </a>
								',
			'wrap'				=> '<div class="post-meta col-12 mb-3">{{ content }}</div>'
		]
	],

	// post_comments
	'post__comment' 				=> [
		// 'markup'				=> [
		// 	'template'			=> '
		// 						Posted {{ post_date_human }} ago 
		// 						by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
		// 						in <a href="{{ category_permalink }}" title="See posts in {{ category_title }}">{{ category_title }}</a>
		// 						Tagged: {{# tags }}
		// 							<a href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>&nbsp;
		// 						{{/#}}
		// 						Comments: <a href="{{ comment_permalink }}" title="Comments">{{ comment_title }} </a>
		// 						',
		// 	'wrap'				=> '<div class="post-meta col-12 mb-3">{{ content }}</div>'
		// ]
	],

	// get_posts() ##
	'post__query'  => [

		// config ##
		'config'				=> [ 
									// 'run' => true, 
									'debug' => false, 
									// 'load' => 'posts'  // change loaded config ##
									// 'srcset' => true // add srcsets ##
								],
		
		// UI ##
		// wrapper ##
		'markup'=> [
			'template' 			=>
									'<div class="pb-1 col-12 the-posts">
										<div class="row"><h5 class="col-12 mt-2">{{ total }} Results Found.</h5></div>
										<div class="row mt-3">{{ posts }}</div>
										<div class="row"><div class="col-12">{{ pagination }}</div></div>
									</div>',
			// post template ##
			'posts'				=> 
									'<div class="card p-0 col-12 col-md-6 col-lg-4 mb-3">
										<a href="{{ post_permalink }}" title="{{ post_title }}" class="mb-3">
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
			'no_results'		=> 
									'<div class="col-12"><p>We cannot not find any matching posts, please check again later.</p></div>'
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

];

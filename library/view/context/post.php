<?php

// return an array ##
return [ 'post' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	],

	// post_title ##
	'title'  				=> [
		'markup' 				=> '<h1 class="pb-2 the-title">{{ title }}</h1>',
	],

	// post_excerpt ##
	'excerpt'				=> [
		'markup'  				=> '<div class="px-3 py-4 pt-1 mt-0 mb-3 the-excerpt text-white bg-dark blockquote">{{ content }}</div>',
		'limit' 				=> 300, // default excerpt length ##
	],

	// post_content ##
	'content'  			=> [
		'markup'                => '<div class="pb-1 the-content">{{ content }}</div>',
	],

	/*
	// post_meta
	'data' 				=> [
		'markup'				=> [
			'template'			=> '
								Posted {{ post_date_human }} ago 
								by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
								in <a href="{{ category_permalink }}" title="See posts in {{ category_title }}">{{ category_title }}</a>,
								tagged: {@ {: tags :}
									<a href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>&nbsp;
								@}
								<a href="{{ comment_permalink }}" title="Comments" class="btn btn-primary">{{ comment_title }}</a>
								',
			'wrap'				=> '<div class="post-meta-data col-12">{{ template }}</div>'
		]
	],

	// post author ##
	'author' 				=> [
		'markup'				=> [
			'template'			=> '
								Posted {{ post_date_human }} ago 
									by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
								</div>
								',
		]
	],

	// post date in human format - @ since ##
	'date_human' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-date-human">
									Posted {{ post_date_human }} ago 
								</span>
								',
		]
	],

	// post date in passed format - etc yyyy/mm/dd ##
	'date' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-date">
									Posted {{ post_date }} 
								</span>
								',
		]
	],

	'category' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-category">
									<a href="{{ category_permalink }}" title="See posts in {{ category_title }}">{{ category_title }}</a>,
								</span>
								',
		]
	],

	'tag' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-tag">
									<a class="btn btn-primary" href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>
								</span>
								',
		]
	],

	'tags' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-tags">
									{@ {: tags :}
										<a class="btn btn-primary" href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>&nbsp;
									@}
								</span>
								',
		]
	],

	// post_comments
	'comment' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-comment">
									<a class="btn btn-primary" href="{{ comment_permalink }}" title="{{ comment_title }}">{{ comment_count }}</a>
								</span>
								',
		]
	],
	*/

	// get_posts() ##
	'query'  => [

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
										<div class="row mt-3">{{ results }}</div>
										<div class="row"><div class="col-12">{{ pagination }}</div></div>
									</div>',
			// post template ##
			'results'				=> 
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
			'default'		=> 
									'<div class="col-12"><p>We could not find any matching posts, please check again later.</p></div>'
		], 

		// config ##
		'wp_query_args'			=> [
									'post_type'				=> [ 'post' ], // blah -- force no results ##
									'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
									'limit'                 => \get_option( "posts_per_page", 10 ), // posts to load ##
									// 'query_vars'            => false, // only wp_query what we pass in config ##
								],	
		'length'                => '200', // return limit for excerpt ##
		'handle'                => 'medium', // image handle ## srcset returns device sizes ##
		// 'date_format'           => 'U',
		// 'allow_comments'        => false, // show comment count - might slow up query ##
	],

]];

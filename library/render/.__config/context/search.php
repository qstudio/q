<?php
 
// return an array ##
return [ 'search' => [ 
'_global'			=> [

	// config ##
	'config'					=> [
		// 'application' 			=> 'posts', // internal ##
		// 'device'				=> '{{{ h::device }}}', // internal ##
		// 'table'	 				=> 'posts', // internal ##
		'load' 					=> false,  // run on load -- as hidden in UI ##
		'empty' 				=> true  // run on clear results -- show guide text ##
	]

],

// UI ##
'ui' => [
	
	// markup ##
	'markup' 					=> [
		'template'				=> '
								<div class="col-12 col-md-6 col-lg-4 ajax-loaded {{class }}">
									<a href="{{ post_permalink }}" title="{{ post_title }}">
										<div class="lazy card-img-top" data-src="{{ src }}" alt="Open {{ post_title }}" src="{{ src }}"></div>
									</a>
									<div class="card-body">
										<h5 class="card-title"><a href="{{ permalink }}" title="Read More">{{ post_title }}</a></h5>
										<p class="card-text">{{ post_excerpt }}</p>
										<p class="card-text">
											<small class="text-muted">{{ post_date_human }}</small>
											<small class="text-muted">in <a href="{{ category_permalink }}" title="{{ category_name }}">{{ category_name }}</a></small>    
										</p>
									</div>
								</div>',
	],
	
	// text ##
	'text'						=> [
		// 'widget_title'			=> 'Search',
		'results_class'	 		=> 'row mb-1',
		'results'				=> 'Item Found', 
		'result'				=> 'Items Found',
		'load_empty_title' 		=> 'Search Tool',
		'load_empty_body' 		=> 'Use the search option and filters to find results.',
		'no_results' 			=> 'No Items Found',
	],

	// UI ##
	'css'						=> [
		'button_class' 			=> 'row',
		'filter_type'			=> 'select',
		'grid_input' 			=> 'col-lg-4 col-12 mb-4 mb-lg-3',
		'grid_select' 			=> 'col-lg-4 col-12 mb-3 mb-lg-3', 
		// 'pagination'			=> true,
		'pagination_empty' 		=> true,
		'pagination_load' 		=> true,
		'show_input_text'  		=> true,
		'js_callback' 			=> 'q_search_callback',
	],

	// @needed ?? ##
	/*
	'deprecated'				=> [
		'hide_titles' 			=> 0,
		'show_count' 			=> false,
		'ajax_section'			=> true,
		'taxonomy/parent' 		=> '0',
	],
	*/

	// JS ##
	/*
	'js'						=> [
		'callback' 				=> 'q_search_callback',
	],
	*/
],
'query' => [
	// Query args ##
	'query_args'				=> [
		'order' 				=> 'DESC',
		'order_by' 				=> 'date',
		// 'category_name' 		=> '{{{ \get_query_var( "category_name" ) }}}', // what the F to do with functions.. ## 
		// 'author_name' 			=> \get_query_var( 'author_name' ), // these don't need to be options.. let's move to filters for most none-text options ##
		// 'tag' 					=> \get_query_var( 'tag', '' ),
		'posts_per_page' 		=> 6,
		'post_type' 			=> 'post',
		// 'role__not_in' 			=> 'Administrator', // @todo -- needs to be an array ##
		'meta_key' 				=> false,
		'args'					=> false,
	],

	// tax ##
	'taxonomies' 				=> [ 
		'category',
		'post_tag' 
	], 
	
	// empty args ##
	'empty_args' 		=> [
		'posts_per_page'        => 6,
		'post_type'             => 'post',
		'ignore_sticky_posts'   => false, // include sticky posts ##
		"post_status"           => "publish"
	],

	// default args ##
	'default_args' 		=> [
		'posts_per_page'        => 6,
		'post_type'             => 'post',
		'ignore_sticky_posts'   => false, // include sticky posts ##
		"post_status"           => "publish"
	],

]	

]];

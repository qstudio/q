<?php
 
//  namespace q\core;

//  use q\core as core;
use q\core\helper as h;

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// re-usable config ------ ##

// h::log( 't:>Check if extension->search array set, if not set when adding to cache.. to allow for simpler array format in config files??' ) ;

// return an array ##
return [ 'extension' => [ 'search' => [

		'config'			=> [
		
			// run context->task ##
			'run' 			=> true,
			
			// context->task debugging ##
			'debug'			=> false,

			// do not echo ##
			'return'		=> 'return'
	
		],

		// config ##
		'application' 		=> 'posts',
		'device'			=> h::device(), // function ##
		'table'	 			=> 'posts',
		
		'control' 			=> 
							[ 
								'load' 		=> '0',  // run on load -- as hidden in UI ##
								'empty' 	=> '1'  // run on clear results -- show guide text ##
							],
		
		// markup ##
		'markup' 			=> [

			'result'		=> '
								<div class="col-12 col-md-6 col-lg-4 ajax-loaded {{ class }}">
									<a href="{{ post_permalink }}" title="{{ post_title }}">
										<div class="lazy card-img-top" data-src="{{ src }}" alt="Open {{ post_title }}" src="{{ src }}"></div>
									</a>
									<div class="card-body">
										<h5 class="card-title"><a href="{{ post_permalink }}" title="Read More">{{ post_title }}</a></h5>
										<p class="card-text">{{ post_excerpt }}</p>
										<p class="card-text">
											<small class="text-muted">{{ post_date_human }}</small>
											<small class="text-muted">in <a href="{{ category_permalink }}" title="{{ category_name }}">{{ category_name }}</a></small>    
										</p>
									</div>
								</div>',
			'template'		=> '{{ scripts }}
								<div id="q-search-content" class="row row mt-3">
									{{ no_posts }}
									{{ filters }}
									{{ results }}
								</div>'
		],
		
		// text ##
		'widget_title'		=> 'Search',
		'results_class'	 	=> 'row mb-1',
		'results' 			=> 
							[ 
								'Item Found', 
								'Items Found' 
							],
		'load_empty'		=> 
							[
								'title' => 'Search Tool',
								'body' 	=> 'Use the search option and filters to find results.' 
							],
		// 'no_results' 		=> 'No Items Found',
		'no_results'		=> __( "No Results Found", 'q-search' ),

		// UI ##
		'src_handle'		=> 'square', // handle for image ##
		'button_class' 		=> 'row',
		'filter_type'		=> 'select',
		'grid_input' 		=> 'col-lg-4 col-12 mb-4 mb-lg-3',
		'grid_select' 		=> 'col-lg-4 col-12 mb-3 mb-lg-3', 
		'show_input_text' 	=> true,
		'pagination'		=> true,
		'pagination_empty' 	=> true,
		'pagination_load' 	=> true,
		'show_input_text'  	=> true,

		 // @needed ?? ##
		'hide_titles' 		=> 0,
		'show_count' 		=> false,
		'ajax_section'		=> true,
		'taxonomy/parent' 	=> '0',

		// JS ##
		'js_callback' 		=> 'q_search_callback',
		
		// Query args ##
		'order' 			=> 'DESC',
		'order_by' 			=> 'date',
		'category_name' 	=> \get_query_var( 'category_name' ),
		'author_name' 		=> \get_query_var( 'author_name' ),
		'tag' 				=> \get_query_var( 'tag', '' ),
		'posts_per_page' 	=> 6,
		'post_type' 		=> 'post',
		'taxonomies' 		=> 
			[ 
				'category',
				'post_tag' 
			], 
		'role__not_in' 		=> [ 'Administrator' ],
		'meta_key' 			=> false,
		'args'				=> false,
		'empty_args' 		=> 
							[
								'posts_per_page'        => 6,
								'post_type'             => 'post',
								'ignore_sticky_posts'   => false, // include sticky posts ##
								"post_status"           => "publish"
							],
		'default_args' 		=> 
							[
								'posts_per_page'        => 6,
								'post_type'             => 'post',
								'ignore_sticky_posts'   => false, // include sticky posts ##
								"post_status"           => "publish"
							],

	]	

]];

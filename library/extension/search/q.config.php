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

	'q_search' => [

		// config ##
		'application' 		=> 'posts',
		'device'			=> h::device(),
		'table'	 			=> 'posts',
		
		'control' 			=> 
							[ 
								'load' 		=> '0',  // run on load -- as hidden in UI ##
								'empty' 	=> '1'  // run on clear results -- show guide text ##
							],
		
		// markup ##
		'markup' 			=> '
								<div class="col-12 col-md-6 col-lg-4 ajax-loaded %class%">
									<a href="%post_permalink%" title="%post_title%">
										<div class="lazy card-img-top" data-src="%src%" alt="Open %post_title%" src="%src%"></div>
									</a>
									<div class="card-body">
										<h5 class="card-title"><a href="%permalink%" title="Read More">%post_title%</a></h5>
										<p class="card-text">%post_excerpt%</p>
										<p class="card-text">
											<small class="text-muted">%post_date_human%</small>
											<small class="text-muted">in <a href="%category_permalink%" title="%category_name%">%category_name%</a> </small>    
										</p>
									</div>
								</div>',
		
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
		'no_results' 		=> 'No Items Found',

		// UI ##
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

];

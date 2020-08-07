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
			'template'		=> '
								<div id="q-search" class="mt-3">
									<div class="row no-results">{{ no_posts }}</div>
									{{ filters }}
									<div id="ajax-content" class="col-12">
										<div id="q-search-results" class="row mb-1">
											{{ results }}
										</div>
    								</div>
								</div>',

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

			'feedback'		=> '<div class="{{ class }} text-center col-12 mt-0 mb-0">
									<span class="push-20 icon icon-{{ class }}"></span>
									<h5 class="push-20">{{ title }}</h5>
									{{ text }}
								</div>',
							
			'count_results'	=> '<h5 class="mb-5 col-12 q-search-count-results text-center" data-count="{{ count }}">{{ count }} {{ text }}</h5>',

			'pagination'	=> '<div class="col-12">
									<nav class="row row justify-content-center mt-3 mb-3">
										<ul class="pagination">
											{{ pagination }}
										</ul>
									</nav>
								</div>'
			
		],
		
		// text ##
		'results' 			=> [ 
								'Item Found', 
								'Items Found' 
							],

		'no_posts'			=> [
								'title'	=> __( "No Results Available", 'q-search' ),
								'text'	=> __( "<p>Sorry, currently there are no items to search.</p>"),
								'class'	=> 'no-posts'
							],

		'load_empty'		=> [
								'title'	=> __( "Search Tool", 'q-search' ),
								'text'	=> __( "<p>Use the search option and filters to find results</p>."),
								'class'	=> 'load-empty'
							],

		'no_results'		=> [
								'title'	=> __( "No Results Found", 'q-search' ),
								'text'	=> __( "<p>Sorry, that filter combination returned no results.</p>
												<p>Please try different criteria or <a href='#' class='qs-reset'>Clear all Filters</a></p>."),
								'class'	=> 'no-results'
							],

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
		'hide_titles' 		=> false,

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

		// taxonomies ##
		'taxonomies' 		=> 
			[ 
				'category',
				'post_tag' 
			], 
		'show_count' 		=> false, // # of terms in tax ##

		// user args ##
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

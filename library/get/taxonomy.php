<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
// use q\get;

// Q Theme ##
use q\theme;

class taxonomy extends \q\get {

	/**
	 * We need a generic get_taxonomy_terms method.. which distributes, based on post type and any passed tax / term ## 
	 */


	/**
	 * Helper Method to get category
	 */
	public static function category( $args = null ){

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ 
	   
		   // h::log( 'Bailing..' ); 
	   
		   return false; 
	   
	   }

	   // try and get_post_categories ##
	   if ( 
		   ! $get_the_category = \get_the_category( $the_post->ID )
	   ){

		   h::log( 'No categories found for Post: '.$the_post->post_title );

		   return false;

	   }

	   // we only want the first array item ##
	   $category = $get_the_category[0];

	   // test ##
	   // h::log( $category );

	   // categories ##
	   if (
		   ! is_object( $category )
		   || ! $category instanceof \WP_Term
	   ) {

		   h::log( 'Error in returned category' );

		   return false;

	   }

	   $array['permalink'] = \get_category_link( $category );
	   $array['slug'] = $category->slug;
	   $array['title'] = $category->cat_name;

	   // return ##
	   return ui\method::prepare_return( $args, $array );

   }


}
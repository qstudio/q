<?php

namespace q\context;

use q\core\helper as h;
use q\get;
use willow;

// register class to willow ##
// \q\context\post::__run();

class post {

	public static function __run( $args = null ) {

		// check for willow ##
		if( ! class_exists( 'willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			willow\context\extend::register([ 
				'context' 	=> str_replace( __NAMESPACE__.'\\', '', __CLASS__ ), 
				'class' 	=> __CLASS__,
				'methods' 	=> $public_methods // public only 
				// 'methods'	=> get_class_methods( __CLASS__ ) // all class methods ##
			]);
		}, 2 );

	}
	

	
    /**
    * Render WP_Query
    *
    * @since       1.0.2
    */
    public static function query( $args = [] )
    {

		// @todo -- add filter to return value and avoid Q check and get routine ##

		// Q needed to run get method ##
		if ( ! class_exists( 'Q' ) ){ return false; }

		// h::log( self::$markup );
		// h::log( self::$args );

		// build fields array with default values ##
		$return = ([
		// render\fields::define([
			'total' 		=> '0', // set to zero string value ##
			'pagination' 	=> null, // empty field.. ##
			'results' 		=> isset( self::$markup['default'] ) ? self::$markup['default'] : null // replace results with empty markup ##
		]);

        // pass to get_posts -- and validate that we get an array back ##
		if ( ! $array = \q\get\query::posts( $args ) ) {

			// log ##
			h::log( self::$args['task'].'~>n:query::posts did not return any data');

		}

		// validate what came back - it should include the WP Query, posts and totals ##
		if ( 
			! isset( $array['query'] ) 
			|| ! isset( $array['query']->posts ) 
			// || ! isset( $array['query']->posts ) 
		){

			// h::log( 'Error in data returned from query::posts' );

			// log ##
			h::log( self::$args['task'].'~>n:Error in data returned from query::posts');

		}
		
		// no posts.. so empty, set count to 0 and no pagination ##
		if ( 
			empty( $array['query']->posts )
			|| 0 == count( $array['query']->posts )
		){

			// h::log( 'No results returned from the_posts' );
			h::log( self::$args['task'].'~>n:No results returned from query::posts');

		// we have posts, so let's add some charm ##
		} else {

			// merge array into args ##
			$args = \q\core\method::parse_args( $array, $args );

			// h::log( $array['query']->found_posts );

			// define all required fields for markup ##
			// self::$fields = [
			$return = [
				'total' 		=> $array['query']->found_posts, // total posts ##
				'pagination'	=> \q\get\navigation::pagination( $args ), // get pagination, returns string ##
				'results'		=> $array['query']->posts // array of WP_Posts ##
			];

		}

		// ok ##
		return $return;

    }


}

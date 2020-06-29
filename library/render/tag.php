<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\view;
use q\get;
use q\render;

class tag extends \q\render {

	// properties ##
	protected static 
		$filtered_tags = false
		// $tag_map = []
	;

	private static function map( $tag = null ){

		// sanity ##
		if ( 
			is_null( $tag )
		){

			h::log( 'e:>No tag passed to map');

			return false;

		}

		// load tags ##
		self::tags();

		// check for class property ##
		if (
			! self::$filtered_tags
		){

			h::log( 'e:>filtered_tags are not loaded..');

			return false;

		}

		// build map ##
		$tag_map = [
			'vo' => self::$filtered_tags['variable']['open'],
			'vc' => self::$filtered_tags['variable']['close'],
		];

		// full back, in case not requested via shortcode ##
		if ( ! isset( $tag_map[$tag] ) ){

			// return isset @todo...

		}

		// search for and return matching key, if found ##
		return $tag_map[$tag] ?: false ;

	}

	protected static function tags(){

		// check if we have already filtered tags ##
		if ( self::$filtered_tags ){

			return self::$filtered_tags;

		}
		
		// per run filter on tags ##
		return self::$filtered_tags = \apply_filters( 'q/render/tags', self::$tags );

	}



	/**
	 * Wrap string in defined tags
	*/
	public static function wrap( $args = null ){

		// sanity ##
		if (
			! isset( $args )
			|| ! is_array( $args )
			|| ! isset( $args['open'] )
			|| ! isset( $args['value'] )
			|| ! isset( $args['close'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// check ##
		if (
			! self::map( $args['open'] )
			|| ! self::map( $args['close'] )
		){

			h::log( 'e:>Error collecting open or close tags' );

			return false;

		}

		// gather data ##
		$string = self::map( $args['open'] ).$args['value'].self::map( $args['close'] );
		
		// replace method, white space aware ##
		if ( 
			isset( $args['replace'] )
		){

			$array = [];
			$array[] = self::map( $args['open'] ).$args['value'].self::map( $args['close'] );
			$array[] = trim(self::map( $args['open'] )).$args['value'].trim(self::map( $args['close'] )); // trim all spaces in tags
			$array[] = rtrim(self::map( $args['open'] )).$args['value'].self::map( $args['close'] ); // trim right on open ##
			$array[] = self::map( $args['open'] ).$args['value'].ltrim(self::map( $args['close'] )); // trim left on close ##

			h::log( $array );
			// h::log( 'value: "'.$args['value'].'"' );

			return $array;

		}

		// test ##
		// h::log( 'd:>'.$string );

		// return ##
		return $string;

	}

	/**
     * shortcut to get
	 * 
	 * @since 4.1.0
     */
    public static function g( $args = null ) {

		return self::get( $args );
 
	}

	
	/**
     * Get a single tag
	 * 
	 * @since 4.1.0
     */
    public static function get( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
		){

			h::log('e:> No args passed to method');

			return false;

		}

		// sanity ##
		if (
			! self::tags()
		){

			h::log('e:>Error in stored $tags');

			return false;

		}

		// // get tags, with filter ##
		// $tags = self::tags();

		// looking for long form ##
		return self::tags()[ $args ] ?: false ;

	}



	/**
     * Get all tag definitions
	 * 
	 * @since 4.1.0
     */
    public static function get_all( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
		){

			h::log('e:> No args passed to method');

			return false;

		}

		// sanity ##
		if (
			! isset( self::$tags )
			|| ! is_array( self::$tags )
		){

			h::log('e:>Error in stored $tags');

			return false;

		}

		// get tags, with filter ##
		$tags = self::tags();

		// looking for long form ##
		$return = 
			isset( $tags ) ?
			$tags :
			false ;

		return $return;

	}


    /**
     * Define tags on a global or per process basis
	 * 
	 * @since 4.1.0
     */
    public static function set( $args = null ) {

       // @todo ##

    }


}

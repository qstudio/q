<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\view;

// Q Theme Config ##
// use q\theme as theme;

class config extends \Q {

	private static
		// loaded config ##
		$config = [],
		$cache = [],
		$args = [] // passed args ##
	;


	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $args = null ) {

		// without $context or $process, we can't get anything specific, so just run main filter ##
		// \apply_filters( 'q/config/get/all', self::$config, isset( $args['field'] ) ?: $args['field'] );

		self::$args = $args; // capture args ##

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['context'] ) 
			|| ! isset( $args['process'] )
		){

			// get caller ##
			$backtrace = core\method::backtrace([ 'level' => 2, 'return' => 'class_function' ]);

			// config is loaded by context or process, so we need one of those to continue ##
			h::log( 'e:>Q -> '.$backtrace.': config is loaded by context and process, so we need both of those to continue' );

			return false;

		}

		// start blank ##
		$return = false;

		// run filter passing lookup args to allow themes and plugins to control config ##
		self::filter();

		// define property ##
		$property =
			// isset( $v['process'] ) ? // lookup includes a process ##
			$args['context'].'__'.$args['process'] ;
			// $args['context'] ;

		// h::log('d:>Looking for $config property: '.$property );
		// h::log( self::$config );

		if ( 
			! isset( self::$config[ $property ] ) 
		){
	
			h::log( 'd:>config not available : "'.$property.'"' );

			// continue;

		} else {

			// assign return ##
			$return = self::$config[ $property ];

			// single loading -- good idea? ##
			// $found = true;

			// ok ##
			// h::log( 'd:>config set to : "'.$property.'"' );

		}

		return $return;

	}



	public static function filter() {

		// sanity ##
		if (
			is_null( self::$args )
			|| ! is_array( self::$args )
			|| ! isset( self::$args['context'] )
			|| ! isset( self::$args['process'] )
		){

			h::log('e:>Error in passed args');

			return false;

		}

		// h::log( $args );

		\apply_filters( 'q/config/get/all', self::$args );

	}





	/**
	 * Include configuration file, with local cache via handle
	 * 
	 * @since 4.1.0
	*/
	public static function load( $file = null, $handle = null )
	{

		// sanity ##
		if (
			is_null( $file )
			|| is_null( $handle )
			// || is_null( $args )
		){

			h::log( 'Error in passed params' );

			return self::$args;

		}

		// h::log( 'd:>Looking for handle: "'.$handle.'" in file: "'.$file.'"' );

		$backtrace = core\method::backtrace([ 'level' => 2, 'return' => 'class_function' ]);

		// use cached version ##
		if( isset( self::$cache[$handle] ) ){

			// h::log( 'd:>Returning cached version of config for handle: '.$handle.' from: '.$backtrace );
			// h::log( self::$cache[$handle] );
			return self::$args;

		}

		// check if file exists ##
		if (
			! file_exists( $file )
		){
			
			h::log( 'e:>Error, file does not exist: '.$file.' from: '.$backtrace );

			return self::$args; #self::$config;

		}

		// load config from JSON ##
		if (
			$array = include( $file )
		){

			// h::log( 'd:>Loading handle: "'.$handle.'" from file: "'.$file.'"' );

			// @todo - some cleaning and sanity ## strip functions etc ##

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>config handle: "'.$handle.'" loading: '.$file.' from: '.$backtrace );
				// h::log( $array );

				// set cache check ##
				self::$cache[$handle] = $array;

				// merge results into array ##
				self::$config = core\method::parse_args( $array, self::$config );

				// return ##
				return self::$args;

			} else {

				h::log( 'd:>config not an array -- handle: "'.$handle.' from: '.$backtrace );

			}

		}

		// empty array ##
		return self::$args;

	}

}

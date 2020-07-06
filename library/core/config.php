<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\view;

\q\core\config::run();

class config extends \Q {

	private static
		// loaded config ##
		$config = [],
		$cache = [],
		$args = [] // passed args ##
	;


	public static function run(){

		// filter Q Config -- ALL FIELDS [ $array "data" ]##
		// Priority -- Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		\add_filter( 'q/config/load', 
			function( $args ){
				$source = null; // context source ##
				return self::filter( $args, $source );
			}
		, 1, 1 );

		\add_filter( 'q/config/load', 
			function( $args ){
				$source = 'plugin'; // context source ##
				return self::filter( $args, $source );
			}
		, 10, 1 );
		
		\add_filter( 'q/config/load', 
			function( $args ){
				$source = 'parent'; // context source ##
				return self::filter( $args, $source );
			}
		, 100, 1 );

		\add_filter( 'q/config/load', 
			function( $args ){
				$source = 'parent'; // context source ##
				return self::filter( $args, $source );
			}
		, 1000, 1 );

	}



	
	/**
	 * Get configuration files
	 *
	 * @used by filter q/config/get/all
	 *
	 * @return		Array $array -- must return, but can be empty ##
	 */
	public static function filter( $args = null, $source = null ) {

		// h::log( $args );

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['context'] ) 
			|| ! isset( $args['task'] )
			// || is_null( $source )
		){

			// config is loaded by context or process, so we need one of those to continue ##
			h::log( 'e:>Error in passed args, $context and $task are required.' );

			// kick back args for future filters ##
			return $args;

		}

		// config file extension ##
		$extensions = \apply_filters( 'q/config/load/ext', [ 
			'.php', 
			// '.willow' 
		] );

		// config file path ( h::get will do fallback checks form child theme, parent theme, plugin + Q.. )
		$path = \apply_filters( 'q/config/load/path', 'view/context/' );

		// array of config files to load -- key ( for cache ) + value ##
		$array = [
			view\is::get().'__'.$args['context'].'__'.$args['task'] => view\is::get().'__'.$args['context'].'__'.$args['task'],
			$args['context'].'__'.$args['task'] => $args['context'].'__'.$args['task'],
			$args['context'] => $args['context'],
			'global' => 'global'
		];

		// filter options ##
		$array = \apply_filters( 'q/config/load/array', $array );

		// h::log( 'd:>looking for source: '.$source );

		// loop over options ##
		foreach( $array as $k => $v ){

			switch( $source ){

				// child context lookup ##
				case "child" :

					// check for look method ##
					if ( ! method_exists( 'q_theme', 'get_child_theme_path' ) ){ break; }
					foreach( $extensions as $ext ) {
						
						$file = \q_theme::get_child_theme_path( '/library/'.$path.$source.'/'.$v.$ext );
					
					}
					// h::log( 'd:>looking up file: '.$file );

				break  ;

				// parent lookup ## 
				case "parent" :

					// check for look method ##
					if ( ! method_exists( 'q_theme', 'get_parent_theme_path' ) ){ break; }
					foreach( $extensions as $ext ) {

						$file = \q_theme::get_parent_theme_path( '/library/'.$path.$source.'/'.$v.$ext );

					}
					// h::log( 'd:>looking up file: '.$file );

				break  ;

				// global lookup, so context/XX.php
				default :

					foreach( $extensions as $ext ) {
						
						$file = h::get( $path.$v.$ext, 'return', 'path' );

					}

				break ;

			}

			if ( 
				$file
				&& file_exists( $file ) // OR is_file ??
				&& is_file( $file )
			){

				// build cache key ##
				$cache_key = 
					! is_null( $source ) ? 
					$k.'_'.$source.'_'.core\method::file_extension( $file ) : 
					$k.'_'.core\method::file_extension( $file ) ;

				// h::log( 'd:>Loading config file: '.$file.' cache key: '.$cache_key );

				// send file to config loader ##
				core\config::load( $file, $cache_key );

			}

		}

		// kick back args for future filters ##
		return $args;

	}




	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $args = null ) {

		// without $context or $task, we can't get anything specific, so just run main filter ##
		// \apply_filters( 'q/config/get/all', self::$config, isset( $args['field'] ) ?: $args['field'] );

		self::$args = $args; // capture args ##

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['context'] ) 
			|| ! isset( $args['task'] )
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
		self::run_filter();

		// define property ##
		$property = $args['context'].'__'.$args['task'] ;

		// h::log('d:>Looking for $config property: '.$property );
		// h::log( self::$config );

		if ( 
			! isset( self::$config[ $property ] ) 
		){
	
			// h::log( 'd:>config not available : "'.$property.'"' );

			// continue;

		} else {

			// get property value ##
			$return = self::$config[ $property ];

			// filter single property values -- too slow ??
			// $return = \apply_filters( 'q/config/get/'.$args['context'].'/'.$args['task'], $return );

			// single loading -- good idea? ##
			// $found = true;

			// ok ##
			// h::log( 'd:>config set to : "'.$property.'"' );

		}

		return $return;

	}



	public static function run_filter() {

		// sanity ##
		if (
			is_null( self::$args )
			|| ! is_array( self::$args )
			|| ! isset( self::$args['context'] )
			|| ! isset( self::$args['task'] )
		){

			h::log('e:>Error in passed args');

			return false;

		}

		// h::log( $args );

		\apply_filters( 'q/config/load', self::$args );

	}





	/**
	 * Include configuration file, with local cache via handle
	 * 
	 * @since 4.1.0
	*/
	public static function load( $file = null, $handle = null )
	{

		// return args for other filters ### ?? ###
		$return = self::$args;

		// sanity ##
		if (
			is_null( $file )
			|| is_null( $handle )
			// || is_null( $args )
		){

			h::log( 'Error in passed params' );

			return $return;

		}

		// h::log( 'd:>Looking for handle: "'.$handle.'" in file: "'.$file.'"' );

		$backtrace = core\method::backtrace([ 'level' => 2, 'return' => 'class_function' ]);

		// use cached version ##
		if( isset( self::$cache[$handle] ) ){

			// h::log( 'd:>Returning cached version of config for handle: '.$handle.' from: '.$backtrace );
			// h::log( self::$cache[$handle] );
			return $return;

		}

		// check if file exists ##
		if (
			! file_exists( $file )
			|| ! is_file( $file )
		){
			
			h::log( 'e:>Error, file does not exist: '.$file.' from: '.$backtrace );

			return $return; #self::$config;

		}

		// h::log( 'dealing with file: '.$file. ' - ext: '.core\method::file_extension( $file ) );

		// get file extension ##
		switch( core\method::file_extension( $file ) ){

			case "willow" :

				// key ##
				$file_key = basename( $file, ".willow" );

				$contents = file_get_contents( $file );

				h::log( '$key: '.$file_key );
				h::log( $contents );

				$array[ $file_key ]['markup'] = $contents;

				// return $return;

			break ;

			default ;
			case "php" :

				$array = require_once( $file );
				// h::log( $array );

			break;

		}

		// load config from JSON ##
		if (
			$array
			// $array = include( $file )
		){

			// h::log( 'd:>Loading handle: "'.$handle.'" from file: "'.$file.'"' );

			// not an array, so take value and add to new array as $markup key ##
			if (  
				! is_array( $array )
			){

				$value = $array;
				// h::log( $value );
				$array = [];
				$array['markup'] = $value;

			}

			// @todo - some cleaning and sanity ## strip functions etc ##

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>config handle: "'.$handle.'" loading: '.$file.' from: '.$backtrace );
				// h::log( $array );

				// filter single property values -- too slow ??
				// perhaps this way is too open, and we should just run this at single property usage time... ##
				if ( isset( $array[ self::$args['context'].'__'.self::$args['task'] ] ) ) {

					$key = self::$args['context'].'__'.self::$args['task'];
					$property = $array[ $key ];

					$filter = 
						\apply_filters( 
							'q/config/load/'.self::$args['context'].'/'.self::$args['task'], 
							$property
					);

					if ( $filter ) {

						// how to validate ??
						// is this an array.. ?? ##
						$array[ $key ] = $filter;

					}

				}

				// set cache check ##
				self::$cache[$handle] = $array;

				// merge results into array ##
				self::$config = core\method::parse_args( $array, self::$config );

				// return ##
				return $return;

			} else {

				h::log( 'd:>config not an array -- handle: "'.$handle.' from: '.$backtrace );

			}

		}

		// return args for other filters ### ?? ###
		return $return;

	}

}

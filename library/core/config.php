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
		$config = []
	;


	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $field = null ) {

		// try to load config from Q core ##
		$config = self::load( self::get_plugin_path('q.config.php'), 'core' );

		// h::log( $config );
		// h::log( 'd:>'.$config[$field] );

		// filter all config early ##
		// Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		// $config = 
			// true === self::$filtered ? 
			// self::$filtered : // use stored filtered config ##
			// self::$filtered = \apply_filters( 'q/config/get/all', $config ) ; // filter in config and store to property ##

		$config = \apply_filters( 'q/config/get/all', $config ) ; // filter in config and store to property ##

		// perhaps the filters blitzed config... check for an empty array, if so reload ##
		if (
		 	! $config
		 	|| ! is_array( $config )
		 	|| empty( $config )
		){

			// try to load again config from Q core ##
			$config = self::load( self::get_plugin_path('q.config.php'), 'core' );

		}

		// now, check if we are looking for a specific field ##
		if (
			is_null( $field )
		) {

			// h::log( 'd:>Getting all config data' );

			// kick back ##
			return $config;

		}

		// h::log( 'Looking for specific Field: "'.$field.'"' );

		// check if field is set ##
		if (
			! isset( $config[$field] )
		){

			h::log( 'd:>No matching config found for Field: "'.$field.'"' );

			return false;

		}

		// kick back specific field ##
		return $config[$field];

	}




	/**
	 * Try to get configuration, based on normal requirements ##
	 * 
	 * @since 4.1.0
	*/
	public static function get_lookup( $args = null ){

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log('e:>Error in passed args');

			return false;

		}

		// get all config ##
		$config = self::get();

		// start blank ##
		$return = false;

		// load default config - the go specific > generic - stopping when a new config is found ##
		$lookups = [

			// i.e: frontpage_group_frontpage_work
			'template_type_process' => view\is::get().'_'.$args['controller'].'_'.$args['process'],

			// i.e: group_frontpage_work
			'type_process' => $args['controller'].'_'.$args['process'],

			// i.e: frontpage_work
			'process' => $args['process'],

			// i.e: group
			'config_load' => $args['controller']
			
		];

		// filter lookups, if users want to add more or re-order ##
		$lookups = \apply_filters( 'q/core/config/lookups', $lookups );

		// tracker ##
		$found = false;

		// loop options ##
		foreach( $lookups as $k => $v ) {

			// config load is defined in render/load - so most have it set ##
			// this will either be set to a class, like "group" or a class_method - like "post_title"
			if ( 
				! isset( $config[ $v ] ) 
			){
		
				// h::log( 'd:>config not available "'.$k.'": "'.$v.'"' );

				continue;

			} else {

				// assign return ##
				$return = $config[ $v ];

				// ok ##
				// h::log( 'd:>config set to "'.$k.'": "'.$v.'"' );

				// update tracker ##
				$found = true;

			}

			if ( $found ) { break; }

		}

		return $return;

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
		)

		// h::log( 'sfsdf' );
		// h::log( 'd:>Looking for handle: "'.$handle.'" in file: "'.$file.'"' );

		// use cached version ##
		if( isset( self::$config[$handle] ) ){

			// h::log( 'd:>Returning cached version of config for handle: '.$handle );

			return self::$config[$handle];

		}

		// check if file exists ##
		if (
			! file_exists( $file )
		){
			
			// h::log( 'e:>Error, file does not exist: '.$file );

			return false;

		}

		// load config from JSON ##
		if (
			$array = include( $file )
		){

			// h::log( 'd:>Loading handle: "'.$handle.'" from file: "'.$file.'"' );

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>config handle: "'.$handle.'" NOT, empty...loading' );

				// set property ##
				self::$config[$handle] = $array;

				// return ##
				return $array;

			} else {

				// h::log( 'd:>config not an array -- handle: "'.$handle.'"' );

			}

		}

		// bad news ##
		// h::log( 'e~>Config:>Error with data for handle: "'.$handle.'" from file: "'.$file.'"' );

		// empty array ##
		return [];

	}



	/**
     * Run defined callbacks on fields ##
     * 
     */
    public static function get_lookups()
    {

        return \apply_filters( 'q/core/config/lookups', self::$lookups );

	}
	
	

}

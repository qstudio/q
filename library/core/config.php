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
		$cache = []
	;


	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $field = null ) {

		// start with Q core ##
		// $config = self::load( self::get_plugin_path('q.config.php'), 'core' );

		// h::log( $config );
		// h::log( 'd:>'.$config[$field] );

		// filter all config early ##
		// Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		
		// filter in config and store to property ##
		// $config_merge = false;
		\apply_filters( 'q/config/get/all', self::$config, $field );
		// self::filter();

		// self::filter( self::$config, $field );

		// load default config - then go generic > specific ##
		$lookups = self::get_lookups();

		// // i.e: group, post, partial
		// $lookups['config_load'] = $args['controller'];

		// // normally a field, i.e: frontpage_work
		// $lookups['process'] = $args['process'];

		// // i.e: group_frontpage_work
		// $lookups['type_process'] = $args['controller'].'_'.$args['process'];

		// // i.e: frontpage_group_frontpage_work
		// $lookups['template_type_process'] = view\is::get().'_'.$args['controller'].'_'.$args['process'];

		// filter lookups, to add more keys, or re-order ##
		$lookups = \apply_filters( 'q/core/config/lookups/get', $lookups );

		// tracker ##
		// $found = false;

		// loop options ##
		foreach( $lookups as $k => $v ) {

			self::filter( $v );

		}

		// if ( 
		// 	isset( $config_merge )
		// 	&& $config_merge
		// 	&& $config_merge != false
		// 	&& $config != $config_merge
		// ){

		// 	h::log( 'Merging in config from: '.$field );
		// 	h::log( $config_merge );

		// 	$config = core\method::parse_args( $config_merge, $config );

		// }

		// perhaps the filters blitzed config... check for an empty array, if so reload ##
		// if (
		//  	! $config
		//  	|| ! is_array( $config )
		//  	|| empty( $config )
		// ){

		// 	// try to load again config from Q core ##
		// 	$config = self::load( self::get_plugin_path('q.config.php'), 'core' );

		// }

		// now, check if we are looking for a specific field ##
		if (
			is_null( $field )
		) {

			// h::log( 'd:>Getting all config data' );

			// kick back ##
			return self::$config;

		}

		// h::log( 'Looking for specific Field: "'.$field.'"' );

		// check if field is set ##
		if (
			! isset( self::$config[$field] )
		){

			// h::log( 'd:>No matching config found for Field: "'.$field.'"' );

			return false;

		}

		// kick back specific field ##
		return self::$config[$field];

	}



	public static function filter( $field = null ) {

		\apply_filters( 'q/config/get/all', self::$config, $field );

	}




	/**
	 * Try to get configuration, based on normal requirements ##
	 * 
	 * @since 4.1.0
	*/
	public static function lookup( $args = null ){

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log('e:>Error in passed args');

			return false;

		}

		// get all config -- might come form cache, or new lookups ##
		self::get();

		// start blank ##
		$return = false;

		// load default config - then go generic > specific ##
		$lookups = self::get_lookups();

		// i.e: group, post, partial
		$lookups['config_load'] = $args['controller'];

		// normally a field, i.e: frontpage_work
		$lookups['process'] = $args['process'];

		// i.e: group_frontpage_work
		$lookups['type_process'] = $args['controller'].'_'.$args['process'];

		// i.e: frontpage_group_frontpage_work
		$lookups['template_type_process'] = view\is::get().'_'.$args['controller'].'_'.$args['process'];

		// filter lookups, to add more keys, or re-order ##
		$lookups = \apply_filters( 'q/core/config/lookups/lookup', $lookups );

		// tracker ##
		// $found = false;

		// loop options ##
		foreach( $lookups as $k => $v ) {

			// // get lookup config, if exists ##
			// if ( 
				self::filter( $v ); 
			// ){

				// h::log( $more_config );

				// $config = $more_config;

			// }

			// config load is defined in render/load - so most have it set ##
			// this will either be set to a class, like "group" or a class_method - like "post_title"
			if ( 
				! isset( self::$config[ $v ] ) 
			){
		
				// h::log( 'd:>config not available "'.$k.'": "'.$v.'"' );

				continue;

			} else {

				// assign return ##
				$return = self::$config[ $v ];

				// ok ##
				// h::log( 'd:>config set to "'.$k.'": "'.$v.'"' );

				// update tracker ##
				// $found = true;

			}

			// if ( $found ) { break; }

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
		){

			h::log( 'Error in passed params' );

			return false;

		}

		// h::log( 'd:>Looking for handle: "'.$handle.'" in file: "'.$file.'"' );

		// use cached version ##
		if( isset( self::$cache[$handle] ) ){

			// h::log( 'd:>Returning cached version of config for handle: '.$handle );
			// h::log( self::$cache[$handle] );

			// return self::$cache[$handle];
			// return self::$config;
			return false;

		}

		// check if file exists ##
		if (
			! file_exists( $file )
		){
			
			// h::log( 'e:>Error, file does not exist: '.$file );

			return false; #self::$config;

		}

		// load config from JSON ##
		if (
			$array = include( $file )
		){

			// h::log( 'd:>Loading handle: "'.$handle.'" from file: "'.$file.'"' );

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>config handle: "'.$handle.'" NOT, empty...loading' );
				// h::log( $array );

				// set cache check ##
				self::$cache[$handle] = $array;

				// merge results into array ##
				self::$config = core\method::parse_args( $array, self::$config );

				// return ##
				return $array;

			} else {

				h::log( 'd:>config not an array -- handle: "'.$handle.'"' );

			}

		}

		// bad news ##
		// h::log( 'e~>Config:>Error with data for handle: "'.$handle.'" from file: "'.$file.'"' );

		// empty array ##
		return false;

	}



	/**
     * lookup methods
     * 
     */
    public static function get_lookups()
    {

		// load default config - then go generic > specific ##
		$array = [

			// theme/parent/q.config ##
			'parent' => 'parent',

			// theme/child/q.config ##
			'child' => 'child',

			// // i.e: group, post, partial
			// 'config_load' => $args['controller'],

			// // normally a field, i.e: frontpage_work
			// 'process' => $args['process'],

			// // i.e: group_frontpage_work
			// 'type_process' => $args['controller'].'_'.$args['process'],

			// // i.e: frontpage_group_frontpage_work
			// 'template_type_process' => view\is::get().'_'.$args['controller'].'_'.$args['process'],

		];

        return $array;

	}
	
	

}

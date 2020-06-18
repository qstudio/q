<?php

namespace q\core;

use q\core as core;
use q\core\helper as h;

// Q Theme Config ##
use q\theme as theme;

class config extends \Q {

	private static
		$core = false, // core config ##
		$filtered = false, // filtered config ##
		$lookup = [
			'shared'
		]
	;


	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $field = null ) {

		// try to load config from Q core ##
		$config = self::get_core();

		// h::log( $config );
		// h::log( 'd:>'.$config[$field] );

		// filter all config early ##
		// Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		$config = 
			true === self::$filtered ? 
			self::$filtered : // use stored filtered config ##
			self::$filtered = \apply_filters( 'q/config/get/all', $config ) ; // filter in config and store to property ##

		// perhaps the filters blitzed config... check for an empty array, if so reload ##
		if (
		 	! $config
		 	|| ! is_array( $config )
		 	|| empty( $config )
		){

			// try to load again config from Q core ##
			$config = self::get_core();

		}

		// now, check if we are looking for a specific field ##
		if (
			is_null( $field )
		) {

			h::log( 'Getting all config data' );

			// kick back ##
			return $config;

		}

		// h::log( 'Looking for specific Field: "'.$field.'"' );

		// check if field is set ##
		if (
			! isset( $config[$field] )
		){

			h::log( 'No matching config found for Field: "'.$field.'"' );

			return false;

		}

		// kick back specific field ##
		return $config[$field];

	}




	/**
	 * Try to get configuration, based on normal requirements ##
	 * 
	 * 
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

		// generic back up ##
		if ( 
			isset( $args['proces'] )
			&& isset( $config[ $args['proces'] ] ) 
		) {

			$return = $config[ $args['proces'] ];

			// h::log( 'd~>get_config:>return set to: '.$return );

		}

		// config load is defined in render/load - so most have it set ##
		// this will either be set to a class, like "group" or a class_method - like "post_title"
		if ( 
			isset( $args['config'] ) 
			&& isset( $args['config']['load'] ) 
			// && core\config::get( $args['config']['load'] )			
			&& isset( $config[ $args['config']['load'] ] ) 
		){
	
			$return = $config[ $args['config']['load'] ];
	
			// h::log( 'd~>get_config:>return set to: '.$args['config']['load'] );

			// look for predefined extensions of config->load ##
			$lookups = self::get_lookups();

			if ( 
				is_array( $lookups ) 
			){

				foreach( $lookups as $lookup ) {

					// h::log( 'd~>get_config:>looking for: '.$args['config']['load'].'_'.$lookup );

					if ( 
						// isset( $args['config'] ) 
						// && isset( $args['config']['load'] ) 
						// && core\config::get( $args['config']['load'] )			
						isset( $config[ $args['config']['load'].'_'.$lookup ] ) 
					){
				
						$return = $config[ $args['config']['load'].'_'.$lookup ];
				
						// h::log( 'd~>get_config:>return set to: '.$args['config']['load'].'_'.$lookup );

					}

				}

			}

		} 

		return $return;

	}




	private static function get_core()
	{

		// use cached version ##
		if( self::$core ){

			h::log( 'Returning cached version of core config' );

			return self::$core;

		}

		// load config from JSON ##
		if (
			$array = include( self::get_plugin_path('q.config.php') )
		){

			// h::log( 'd:>'.self::get_plugin_path('q.config.php') );

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>Q config NOT, empty...loading' );

				// set property ##
				self::$core = $array;

				// assign ##
				return $array;

			}

		}

		// bad news ##
		h::log( 'e~>Config:>Q config core empty, this should not happen, really..' );
		return [];

	}



	/**
     * Run defined callbacks on fields ##
     * 
     */
    public static function get_lookups()
    {

        return \apply_filters( 'q/core/config/lookup', self::$lookup );

	}
	
	

}

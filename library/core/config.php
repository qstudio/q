<?php

namespace q\core;

use q\core as core;
use q\core\helper as h;

// Q Theme Config ##
use q\theme as theme;

class config extends \Q {


	/**
	 * Get stored config setting, merging in any new of changed settings from extensions ##
	 */
	public static function get( $field = null ) {

		// starts with an empty array ##
		$config = [];

		// load config from JSON ##
		if (
			$array = include( self::get_plugin_path('q.config.php') )
		){

			// h::log( 'd:>'.self::get_plugin_path('q.config.php') );

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// h::log( 'd:>Q config NOT, empty...loading' );

				// assign ##
				$config = $array;

			}

		}

		// h::log( $config );
		// h::log( $config[$field] );

		// filter all config early ##
		// Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		$config = \apply_filters( 'q/config/get/all', $config );

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


}

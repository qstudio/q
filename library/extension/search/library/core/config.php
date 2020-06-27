<?php

namespace q\search\core;

// Q ##
use q\admin;
use q\core;

// Q Theme ##
use q\search\core\helper as h;

// load it up ##
\q\search\core\config::run();

class config extends \q_search {

    public static function run()
    {

		// filter Q Config -- ALL FIELDS [ $array "data" ]##
		// Priority -- Q = 1, Q Plugin = 10, Q Parent = 100, Q Child = 1000
		\add_filter( 'q/config/get/all', [ get_class(), 'get' ], 10, 1 );

    }



	/**
	 * Get configuration from /q.config.php
	 *
	 * @used by filter q/config/get/all
	 *
	 * @return		Array $array -- must return, but can be empty ##
	 */
	public static function get( $args = null ) {

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['context'] ) 
			|| ! isset( $args['task'] )
		){

			// h::log( $args );
			h::log( 'e:>Missing context and process extension->search' );

			return false;

		}

		// starts with an empty array ##
		$array = false;

		// load config from JSON ##
		if (
			// $array = include( self::get_plugin_path('q.config.php') )
			$array = core\config::load( self::get_plugin_path( 'q.config.php' ), 'q-search' )
		){

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// merge filtered data into default data ##
				// $config = core\method::parse_args( $array, $config );
				return $array;

			}

		}

		// h::log( $config );

		// kick back ##
		return false;

	}


}

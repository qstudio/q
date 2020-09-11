<?php

namespace q\module;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\module\search::__run();

class search extends \Q {
				
	// plugin properties ##
	public static $properties = false;

	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function __run() 
	{

		// load libraries ##
		core\load::libraries( self::load() );

	}


	/**
	 * Check for required breaking dependencies
	 *
	 * @return      Boolean
	 * @since       1.0.0
	 */
	public static function has_dependencies()
	{

		// check for what's needed ##
		if (
			! class_exists( 'willow' )
		) {

			h::log( 'e:>This module requires Q Willow to run correctly..' );

			return false;

		}

		// ok ##
		return true;

	}


	/**
	* Load Libraries
	*
	* @since        2.0
	*/
	private static function load()
	{

		// check for dependencies, required for UI components - admin will still run ##
		if ( ! self::has_dependencies() ) {

			return false;

		}

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> 'search',
			'name'		=> 'AJAX Search <a href="://qstudio.us/docs/q/modules/#search" target="_blank">Read More</a>',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->search )
			|| true !== core\option::get('module')->search 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}

		// h::log( 'd:>search is enabled.' );
		$array = [];
		$array['context'] = h::get( 'module/search/context/module.php', 'return', 'path' );
		$array['asset'] = h::get( 'module/search/asset/_load.php', 'return', 'path' );
		$array['method'] = h::get( 'module/search/core/method.php', 'return', 'path' );
		$array['admin'] = h::get( 'module/search/admin/_load.php', 'return', 'path' );
		$array['ui'] = h::get( 'module/search/view/_load.php', 'return', 'path' );

		return $array;

	}

}

<?php

namespace q\extension;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\search::run();

class search extends \Q {
				
	// plugin properties ##
	public static $properties = false;

	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function run() 
	{

		// load libraries ##
		core\load::libraries( self::load() );

	}


	/**
	* Load Libraries
	*
	* @since        2.0
	*/
	private static function load()
	{

		$array = [

			// core ##
			'option' => h::get( 'extension/search/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('extension') );
		if ( 
			! isset( core\option::get('extension')->nprogress )
			|| true !== core\option::get('extension')->nprogress 
		){

			// h::log( 'd:>search is not enabled.' );

			return $array;

		}

		// h::log( 'd:>search is enabled.' );

		$array['config'] = h::get( 'extension/search/core/config.php', 'return', 'path' );
		$array['method'] = h::get( 'extension/search/core/method.php', 'return', 'path' );
		$array['admin'] = h::get( 'extension/search/admin/_load.php', 'return', 'path' );
		$array['ui'] = h::get( 'extension/search/ui/_load.php', 'return', 'path' );

		return $array;

	}

}

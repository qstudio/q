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
         * Check for required breaking dependencies
         *
         * @return      Boolean
         * @since       1.0.0
         */
        public static function has_dependencies()
        {

            // check for what's needed ##
            if (
                ! class_exists( 'q_willow' )
            ) {

                h::log( 'e:>This extension requires Q Willow to run correctly..' );

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

		$array = [

			// core ##
			'option' => h::get( 'extension/search/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('extension') );
		if ( 
			! isset( core\option::get('extension')->search )
			|| true !== core\option::get('extension')->search 
		){

			// h::log( 'd:>search is not enabled.' );

			return $array;

		}

		// h::log( 'd:>search is enabled.' );

		$array['context'] = h::get( 'extension/search/context/extension.php', 'return', 'path' );
		$array['asset'] = h::get( 'extension/search/asset/_load.php', 'return', 'path' );
		$array['method'] = h::get( 'extension/search/core/method.php', 'return', 'path' );
		$array['admin'] = h::get( 'extension/search/admin/_load.php', 'return', 'path' );
		$array['ui'] = h::get( 'extension/search/view/_load.php', 'return', 'path' );

		return $array;

	}

}

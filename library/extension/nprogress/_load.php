<?php

/**
 * NProgress Credits:
 *
 * http://ricostacruz.com/nprogress/
 * https://github.com/rstacruz/nprogress
 */

namespace q\extension;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\nprogress::run();

class nprogress extends \Q {
                
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
			'option' => h::get( 'extension/nprogress/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('extension') );
		if ( 
			! isset( core\option::get('extension')->nprogress )
			|| true !== core\option::get('extension')->nprogress 
		){

			// h::log( 'd:>Nprogress is not enabled.' );

			return $array;

		}

		// h::log( 'd:>Nprogress is enabled.' );

		// frontend ##
		$array['render'] = h::get( 'extension/nprogress/view/render.php', 'return', 'path' );

		return $array;

	}

}

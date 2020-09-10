<?php

/**
 * NProgress Credits:
 *
 * http://ricostacruz.com/nprogress/
 * https://github.com/rstacruz/nprogress
 */

namespace q\module;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\module\nprogress::__run();

class nprogress extends \Q {
                
	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function __run() 
	{

		\q\module::filter([
			'module'	=> 'nprogress',
			'name'		=> 'Q ~ Progress Bar',
			'selected'	=> true,
		]);

		// h::log( core\option::get('extension') );
		if ( 
			! isset( core\option::get('module')->nprogress )
			|| true !== core\option::get('module')->nprogress 
		){

			// h::log( 'd:>Nprogress is not enabled.' );

			return false;

		}

	}

}

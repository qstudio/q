<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\bs_scrollspy::__run();

class bs_scrollspy extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Scrollspy',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		if ( 
			! isset( core\option::get('module')->bs_scrollspy )
			|| true !== core\option::get('module')->bs_scrollspy 
		){

			// h::log( 'd:>bs_scrollspy is not enabled.' );

			return false;

		}

    }

}

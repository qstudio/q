<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\javascript::__run();

class javascript extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Global Javascript',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('javascript') );
		if ( 
			! isset( core\option::get('module')->javascript )
			|| true !== core\option::get('module')->javascript 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}
		
    }

}

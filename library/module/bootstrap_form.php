<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\bootstrap_form::__run();

class bootstrap_form extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Form',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('bootstrap_form') );
		if ( 
			! isset( core\option::get('module')->bootstrap_form )
			|| true !== core\option::get('module')->bootstrap_form 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		
    }
	
}
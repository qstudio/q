<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\bootstrap_collapse::__run();

class bootstrap_collapse extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Collapse',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bootstrap_collapse )
			|| true !== core\option::get('module')->bootstrap_collapse 
		){

			// h::log( 'd:>collapse is not enabled.' );

			return false;

		}
		
    }


}

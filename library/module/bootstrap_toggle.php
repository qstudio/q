<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\bootstrap_toggle::__run();

class bootstrap_toggle extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Toggle',
			'selected'	=> true,
		]);

		// h::log( core\option::get('bootstrap_toggle') );
		if ( 
			! isset( core\option::get('module')->bootstrap_toggle )
			|| true !== core\option::get('module')->bootstrap_toggle 
		){

			h::log( 'd:>Toggle is not enabled.' );

			return false;

		}

    }

}

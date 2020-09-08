<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\plugin_anspress::__run();

class plugin_anspress extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Plugin ~ AnsPress',
			'selected'	=> false,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('anspress') );
		if ( 
			! isset( core\option::get('module')->anspress )
			|| true !== core\option::get('module')->anspress 
		){

			// h::log( 'd:>AnsPress is not enabled.' );

			return false;

		}
		
    }


}

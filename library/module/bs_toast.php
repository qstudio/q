<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_toast::__run();

class bs_toast extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		// \add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );
		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Toast',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('toast') );
		if ( 
			! isset( core\option::get('module')->bs_toast )
			|| true !== core\option::get('module')->bs_toast 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		
    }


}

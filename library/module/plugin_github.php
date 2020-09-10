<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\plugin_github::run();

class plugin_github extends \Q {

    public static function run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Plugin ~ GitHub Updater',
			// 'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->plugin_github )
			|| true !== core\option::get('module')->plugin_github 
		){

			// h::log( 'd:>Helper is not enabled.' );

			return false;

		}

        // no background processing for github updatre ##
        \add_filter( 'github_updater_disable_wpcron', '__return_true' );

    }

}

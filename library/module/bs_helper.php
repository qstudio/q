<?php

namespace q\module;

use q\core;
use q\core\helper as h;
use q\asset;

// load it up ##
\q\module\bs_helper::__run();

class bs_helper extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Helper',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bs_helper )
			|| true !== core\option::get('module')->bs_helper 
		){

			// h::log( 'd:>Helper is not enabled.' );

			return false;

		}

		// add html ##
		\add_action( 'wp_footer', [ get_class(), 'html' ], 100000 );

    }



	/**
	 * Add html element to page for debugging feedback
	 * 
	 */
	public static function html(){

		// we should never run this module if debugging is off ##
		if( 
			// false === self::$debug 
			false === \Q::$debug
		){

			h::log( 'BootStrap Helper is disabled when debugging is disabled...' );

			return false;

		}

?>
<span 
	id="bs_helper" 
	class="nodebug badge badge-warning" 
	data-toggle="tooltip" 
	data-placement="top"
	data-html="true"
	title="Tooltip on top"
	style="position: fixed; left: 0; bottom: 0; padding: 20px;"
	>~@~
</span>
<?php
			
	}

}

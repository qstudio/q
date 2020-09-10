<?php

namespace q\module;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\module\consent::__run();

class consent extends \Q {

	// plugin slug for translations ##
	static $slug = 'q-consent';

	// will contain the geotarget variables - https://wpengine.com/support/developers-guide-geotarget/ ## 
    static $geotarget = [
		'continent' => '',
		'country'   => '',
		// 'region'    => '',
		// 'city'      => ''
	];
	
	// default cookie values ##
	static $defaults = [
		'consent'       => 0, // tracking consent action ##
		'marketing'     => 1, // marketing permitted ##
		'analytics'     => 1, // analytics permitted ##
	];

	static $cookie = false;
	
	/**
	 * Instatiate Class
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function __run()
    {

		// @todo -- add admin controls to selected privacy page, block delete.. custom fields etc..

        core\load::libraries( self::load() );

    }



	/**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load()
    {

		$array = [

			// core ##
			'option' => h::get( 'module/consent/core/option.php', 'return', 'path' )

		];

		if ( 
			! isset( core\option::get('module')->consent )
			|| true !== core\option::get('module')->consent 
		){

			// h::log( 'd:>Consent is not enabled.' );

			return $array;

		}

		// h::log( 'd:>Loading rest of Consent System files...' );

		// $array['api'] = h::get( 'module/consent/core/api.php', 'return', 'path' );
		$array['geotarget'] = h::get( 'module/consent/core/geotarget.php', 'return', 'path' );
		$array['cookie'] = h::get( 'module/consent/core/cookie.php', 'return', 'path' );

		// backend ##
		$array['callback'] = h::get( 'module/consent/ajax/callback.php', 'return', 'path' );

		// frontend ##
		$array['theme'] = h::get( 'module/consent/ui/theme.php', 'return', 'path' );

		return $array;


	}


}

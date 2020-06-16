<?php

namespace q\extension;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\consent::run();

class consent extends \Q {

	// slug ??
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
	public static function run()
    {

		// h::log( core\option::get('extension') );
		
		if ( 
			! isset( core\option::get('extension')->consent )
			|| true !== core\option::get('extension')->consent 
		){

			h::log( 'd:>Consent is not enabled.' );

			return false;

		}

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

		return $array = [

			// core ##
			'api' => h::get( 'extension/consent/library/core/api.php', 'return', 'path' ),
			'geotarget' => h::get( 'extension/consent/library/core/geotarget.php', 'return', 'path' ),
			'cookie' => h::get( 'extension/consent/library/core/cookie.php', 'return', 'path' ),

			// backend ##
			'callback' => h::get( 'extension/consent/library/ajax/callback.php', 'return', 'path' ),

			// frontend ##
			'theme' => h::get( 'extension/consent/library/ui/theme.php', 'return', 'path' ),

		];


	}


}

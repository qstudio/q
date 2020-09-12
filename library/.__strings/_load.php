<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\strings::run();

class strings { // why not extend \Q ?? @todo ##

	
	/**
	 * Fire things up
	*/
	public static function run(){

		// load libraries ##
		core\load::libraries( self::load() );

	}
	

    /**
    * Load Libraries
    *
    * @since        4.1.0
    */
    public static function load()
    {

		return $array = [

			// methods ##
			'method' => h::get( 'strings/method.php', 'return', 'path' ),
			
		];

	}
	

}

<?php

namespace q\get;

use q\core;
use q\core\helper as h;

// load it up ##
\q\get\controller::run();

class controller extends \Q {

	public static function run(){

		core\load::libraries( self::load() );

	}

    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		return $array = [
			// wordpress functions ##
			'wp' => h::get( 'get/wp.php', 'return', 'path' ),

			// media objects ##
			'media' => h::get( 'get/media.php', 'return', 'path' ),
		];

    }

}
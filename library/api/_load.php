<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\api::run();

class api extends \Q {

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

			// // functions ##
			// 'method' => h::get( 'admin/method.php', 'return', 'path' ),

			// // filters ##
			// 'filter' => h::get( 'admin/filter.php', 'return', 'path' ),

			// plugin ##
			'plugin' => h::get( 'api/plugin/_load.php', 'return', 'path' ),

		];

    }

}

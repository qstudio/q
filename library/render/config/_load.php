<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
\q\render\config::run();

class config extends render {

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

			// options page ##
			// 'option' => h::get( 'admin/option.php', 'return', 'path' ),

			// options ##
			'option' => h::get( 'render/config/option.php', 'return', 'path' ),

			// see what data acf saves ##
			'save' => h::get( 'render/config/save.php', 'return', 'path' ),

			// defaults ##
			'get' => h::get( 'render/config/get.php', 'return', 'path' ),

			// context ##
			'context' => h::get( 'render/config/context/_load.php', 'return', 'path' )

		];

    }

}

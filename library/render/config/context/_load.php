<?php

namespace q\render\config;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
// \q\render\config::run();

class context extends render {

	// public static function run(){

	// 	self::load();

	// }

    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		// start empty ##
		$return = [];

		$array = [

			// options page ##
			// 'option' => h::get( 'admin/option.php', 'return', 'path' ),

			// ui ##
			'ui' => h::get( 'render/config/context/ui.php', 'return', 'path' ),

			// post ##
			'post' => h::get( 'render/config/context/post.php', 'return', 'path' ),

			// defaults ##
			// 'get' => h::get( 'render/config/get.php', 'return', 'path' )

		];

		// then load parent /_config.php ( h::get methods above will pull from child, parent or theme.. ) - and merge ##

		// then load child /_config.php ( which is saved from acf routing ) --- and merge ##

		// apply filter ##
		$array = \apply_filters( 'q/render/config/load', $array );

		foreach ( $array as $k => $file ) {

			// h::log( 'looking in file: '.$file );
			$return += require( $file );

		}

		return $return;

    }

}

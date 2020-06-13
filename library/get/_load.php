<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\get::run();

class get extends \Q {

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

			// taxonomy object ##
			'plugin' => h::get( 'get/plugin.php', 'return', 'path' ),

			// taxonomy object ##
			'theme' => h::get( 'get/theme.php', 'return', 'path' ),

			// WP_Post queries ##
			'query' => h::get( 'get/query.php', 'return', 'path' ),

			// post object ##
			'post' => h::get( 'get/post.php', 'return', 'path' ),

			// taxonomy object ##
			'taxonomy' => h::get( 'get/taxonomy.php', 'return', 'path' ),

			// modules ##
			'module' => h::get( 'get/module.php', 'return', 'path' ),

			// navigation items ##
			'navigation' => h::get( 'get/navigation.php', 'return', 'path' ),

			// media objects ##
			'media' => h::get( 'get/media.php', 'return', 'path' ),
			
		];

    }

}

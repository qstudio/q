<?php

namespace q;

use q\core;
use q\core\helper as h;
// use q\core\options as options;

// load it up ##
\q\hook::run();

class hook extends \Q {

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

			// admin hooks ##
			'switch_theme' => h::get( 'hook/switch_theme.php', 'return', 'path' ),
			'admin_init' => h::get( 'hook/admin_init.php', 'return', 'path' ),
			'after_switch_theme' => h::get( 'hook/after_switch_theme.php', 'return', 'path' ),
			'comment_post' => h::get( 'hook/comment_post.php', 'return', 'path' ),
			'save_post' => h::get( 'hook/save_post.php', 'return', 'path' ),

			// front-end hooks ##
			'wp_head' => h::get( 'hook/wp_head.php', 'return', 'path' ),
			'wp_footer' => h::get( 'hook/wp_footer.php', 'return', 'path' ),

			// global hooks ##
			'the_post' => h::get( 'hook/the_post.php', 'return', 'path' ),
			'plugins_loaded' => h::get( 'hook/plugins_loaded.php', 'return', 'path' ),
		];

    }


}

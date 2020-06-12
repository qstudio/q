<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\plugin::run();

class plugin extends \Q {

    public static function run()
    {

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

			// acf ##
			'acf' => h::get( 'plugin/acf.php', 'return', 'path' ),

			// github ##
			'github' => h::get( 'plugin/github.php', 'return', 'path' ),

			// gravityforms ##
			'gravityforms' => h::get( 'plugin/gravityforms.php', 'return', 'path' ),

			// google ##
			'google' => h::get( 'plugin/google.php', 'return', 'path' ),

			// facebook ##
			'facebook' => h::get( 'plugin/facebook.php', 'return', 'path' ),

			// linkedin ##
			'linkedin' => h::get( 'plugin/linkedin.php', 'return', 'path' ),

			// twitter ##
			'twitter' => h::get( 'plugin/twitter.php', 'return', 'path' ),

			// youtube ##
			'youtube' => h::get( 'plugin/youtube.php', 'return', 'path' ),

			// actions ##
			// 'action' => h::get( 'plugin/action.php', 'return', 'path' ),

			// plugins ##
			// require_once self::get_plugin_path( 'library/plugin/acf.php' );
			// require_once self::get_plugin_path( 'library/plugin/github.php' );
			// require_once self::get_plugin_path( 'library/plugin/gravityforms.php' );
			// require_once self::get_plugin_path( 'library/plugin/google.php' );
			// // require_once self::get_plugin_path( 'library/plugin/getresponse.php' );
			// require_once self::get_plugin_path( 'library/plugin/facebook.php' );
			// require_once self::get_plugin_path( 'library/plugin/linkedin.php' );
			// require_once self::get_plugin_path( 'library/plugin/twitter.php' );
			// require_once self::get_plugin_path( 'library/plugin/youtube.php' );
		
		];

    }

}

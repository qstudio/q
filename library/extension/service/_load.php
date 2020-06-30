<?php

namespace q\extension;

use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\service::run();

class service extends \Q {

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

			// asana ## @todo.. I like asana .. #
			'asana' => h::get( 'extension/service/asana.php', 'return', 'path' ),

			// github ##
			'github' => h::get( 'extension/service/github.php', 'return', 'path' ),

			// google ##
			'google' => h::get( 'extension/service/google.php', 'return', 'path' ),

			// facebook ##
			'facebook' => h::get( 'extension/service/facebook.php', 'return', 'path' ),

			// linkedin ##
			'linkedin' => h::get( 'extension/service/linkedin.php', 'return', 'path' ),

			// twitter ##
			'twitter' => h::get( 'extension/service/twitter.php', 'return', 'path' ),

			// youtube ##
			'youtube' => h::get( 'extension/service/youtube.php', 'return', 'path' ),

		];

    }

}

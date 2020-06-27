<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\extension::run();

class extension extends \Q {

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

			// brandbar ##
			// 'brand' => h::get( 'extension/brand.php', 'return', 'path' ), // @todo

			// banner / ticker ##
			// 'banner' => h::get( 'extension/banner.php', 'return', 'path' ), // @todo  ##

			// asana ## @todo.. I like asana .. #
			'asana' => h::get( 'extension/asana.php', 'return', 'path' ),

			// consent ##
			'consent' => h::get( 'extension/consent/_load.php', 'return', 'path' ),

			// device ##
			'device' => h::get( 'extension/device/_load.php', 'return', 'path' ),

			// nprogress ##
			'nprogress' => h::get( 'extension/nprogress/_load.php', 'return', 'path' ),

			// sticky ##
			'sticky' => h::get( 'extension/sticky/_load.php', 'return', 'path' ),

			// github ##
			'github' => h::get( 'extension/github.php', 'return', 'path' ),

			// google ##
			'google' => h::get( 'extension/google.php', 'return', 'path' ),

			// facebook ##
			'facebook' => h::get( 'extension/facebook.php', 'return', 'path' ),

			// linkedin ##
			'linkedin' => h::get( 'extension/linkedin.php', 'return', 'path' ),

			// twitter ##
			'twitter' => h::get( 'extension/twitter.php', 'return', 'path' ),

			// youtube ##
			'youtube' => h::get( 'extension/youtube.php', 'return', 'path' ),

		];

    }

}

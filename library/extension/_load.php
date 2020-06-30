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

			// consent ##
			'consent' => h::get( 'extension/consent/_load.php', 'return', 'path' ),

			// device ##
			'device' => h::get( 'extension/device/_load.php', 'return', 'path' ),

			// nprogress ##
			'nprogress' => h::get( 'extension/nprogress/_load.php', 'return', 'path' ),

			// sticky ##
			'sticky' => h::get( 'extension/sticky/_load.php', 'return', 'path' ),

			// services ##
			'service' => h::get( 'extension/service/_load.php', 'return', 'path' ),

		];

    }

}

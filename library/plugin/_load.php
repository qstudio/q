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

			// advanced forms ##
			// 'advanced_forms' => h::get( 'plugin/advanced_forms.php', 'return', 'path' ),

			// gravityforms ##
			// 'gravityforms' => h::get( 'plugin/gravityforms.php', 'return', 'path' ),

			// aesop ##
			// 'aesop' => h::get( 'plugin/aesop.php', 'return', 'path' ),

		];

    }

}

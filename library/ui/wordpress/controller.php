<?php

namespace q\ui;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
// use q\core\wordpress as wordpress;

// load it up ##
\q\ui\wordpress::run();

class wordpress extends \Q {

    public static function run()
    {

        // load templates ##
        self::load_libraries();

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // core functions ##
		require_once self::get_plugin_path( 'library/ui/wordpress/method.php' );
		
        // post data handlers ##
		require_once self::get_plugin_path( 'library/ui/wordpress/get.php' );
		
		// media handlers ##
        require_once self::get_plugin_path( 'library/ui/wordpress/media.php' );
        
    }

}
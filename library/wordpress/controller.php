<?php

namespace q\wordpress;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
// use q\core\wordpress as wordpress;

// load it up ##
\q\wordpress\controller::run();

class controller extends \Q {

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

        // core ##
        require_once self::get_plugin_path( 'library/wordpress/core.php' );

        // ui ##
        // require_once self::get_plugin_path( 'library/wordpress/ui.php' );

        // post data handlers ##
		require_once self::get_plugin_path( 'library/wordpress/post.php' );
		
		// media handlers ##
        require_once self::get_plugin_path( 'library/wordpress/media.php' );
        
    }

}
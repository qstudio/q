<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\core\controller::run();

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

		// render engines ##
		require_once self::get_plugin_path( 'library/core/helper.php' );
        require_once self::get_plugin_path( 'library/core/config.php' );
		require_once self::get_plugin_path( 'library/core/method.php' );
		require_once self::get_plugin_path( 'library/core/option.php' );
		require_once self::get_plugin_path( 'library/core/filter.php' );

    }

}
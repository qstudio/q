<?php

namespace q;

// use q\core\core as core;
use q\core\helper as h;

// load it up ##
\q\core::run();

class core extends \Q {

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

		// lirbaries ##
		require_once self::get_plugin_path( 'library/core/method.php' );
		require_once self::get_plugin_path( 'library/core/helper.php' );
		require_once self::get_plugin_path( 'library/core/log.php' );
		require_once self::get_plugin_path( 'library/core/filter.php' );
		require_once self::get_plugin_path( 'library/core/load.php' );
		require_once self::get_plugin_path( 'library/core/config.php' );
		require_once self::get_plugin_path( 'library/core/device.php' );
		require_once self::get_plugin_path( 'library/core/media.php' );
		require_once self::get_plugin_path( 'library/core/option.php' );
		require_once self::get_plugin_path( 'library/core/wpdb.php' );
		require_once self::get_plugin_path( 'library/core/filter.php' );

    }

}

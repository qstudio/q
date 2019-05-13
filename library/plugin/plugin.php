<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;

// load it up ##
\q\plugin\plugin::run();

class plugin extends \Q {

    public static function run()
    {

        // load templates ##
        self::load_libraries();

        // if ( ! \is_admin() ) {

        //     // load scripts early, so theme files can override ##
        //     \add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts' ), 2 );

        // }

    }


    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // plugins ##
        require_once self::get_plugin_path( 'library/plugin/acf.php' );
        require_once self::get_plugin_path( 'library/plugin/gravityforms.php' );
        require_once self::get_plugin_path( 'library/plugin/google.php' );
        require_once self::get_plugin_path( 'library/plugin/facebook.php' );
        require_once self::get_plugin_path( 'library/plugin/twitter.php' );
        require_once self::get_plugin_path( 'library/plugin/youtube.php' );

    }

}
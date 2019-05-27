<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
// use q\core\wordpress as wordpress;

// load it up ##
\q\controller\construct::run();

class construct extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $options;

    public static function run()
    {

        // load templates ##
        self::load_properties();

        // if ( ! \is_admin() ) {

        //     // plugin css / js -- includes defaults and resets and snippets from controllers ##
        //     \add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts_plugin' ), 1 );

        //     // plugins and enhanecments ##
        //     \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_general' ), 2 );

        //     // theme css / js -- theme assets loaded by q_theme ##

        // }

        // load templates ##
        self::load_libraries();

    }


    /**
    * Load Properties
    *
    * @since        2.0.0
    */
    private static function load_properties()
    {

        // assign values ##
        self::$plugin_version = self::version ;

        // grab the options ##
        self::$options = options::get();
        #helper::log( self::$options );

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // ui ##
        // require_once self::get_plugin_path( 'library/theme/ui.php' );

        // render engines ##
        require_once self::get_plugin_path( 'library/controller/javascript.php' );
        require_once self::get_plugin_path( 'library/controller/css.php' );

        // cookies ##
        require_once self::get_plugin_path( 'library/controller/cookie.php' );
        
        // minify ##
        require_once self::get_plugin_path( 'library/controller/minifier.php' );

        // UI controllers ##
        require_once self::get_plugin_path( 'library/controller/navigation.php' );
        require_once self::get_plugin_path( 'library/controller/generic.php' );

        // UI / JS / AJAX features ##
        require_once self::get_plugin_path( 'library/controller/modal.php' );
        require_once self::get_plugin_path( 'library/controller/tab.php' );
        require_once self::get_plugin_path( 'library/controller/select.php' );
        require_once self::get_plugin_path( 'library/controller/scroll.php' );
        require_once self::get_plugin_path( 'library/controller/push.php' );
        require_once self::get_plugin_path( 'library/controller/filter.php' );
        require_once self::get_plugin_path( 'library/controller/toggle.php' );
        require_once self::get_plugin_path( 'library/controller/load.php' );

    }

}
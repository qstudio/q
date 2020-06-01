<?php

namespace q\controller;

// use q\core\core as core;
use q\core;
use q\core\helper as h;

// load it up ##
\q\controller\controller::run();

class controller extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $options;

    public static function run()
    {

        // load templates ##
        self::load_properties();

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
        self::$options = core\option::get();
        #h::log( self::$options );

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // render engines ##
        require_once self::get_plugin_path( 'library/controller/javascript.php' );
        require_once self::get_plugin_path( 'library/controller/css.php' );

        // cookies ##
        require_once self::get_plugin_path( 'library/controller/cookie.php' );
        
        // minify ##
        require_once self::get_plugin_path( 'library/controller/minifier.php' );

        // UI controllers ##
        require_once self::get_plugin_path( 'library/controller/navigation.php' );
		// require_once self::get_plugin_path( 'library/controller/generic.php' );
		require_once self::get_plugin_path( 'library/controller/consent.php' );
		require_once self::get_plugin_path( 'library/controller/fields.php' );

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
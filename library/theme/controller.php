<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\theme\controller::run();

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

        // widgets == @TODO, should be filter for q_theme_x settings and global controller ##
        require_once self::get_plugin_path( 'library/theme/widget.php' );

        // meta controller ##
        require_once self::get_plugin_path( 'library/theme/meta.php' ); 
        // require_once self::get_plugin_path( 'library/theme/field.php' ); 

        // template controller, allows plugins to inject rules via filters ##
        require_once self::get_plugin_path( 'library/theme/template.php' ); 

        // add assets based on Q settings ##
        require_once self::get_plugin_path( 'library/theme/theme.php' );

    }



}
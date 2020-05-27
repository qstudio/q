<?php

namespace q\module;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
// use q\core\wordpress as wordpress;

// load it up ##
\q\module\construct::run();

class construct extends \Q {

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

        // field ##
        require_once self::get_plugin_path( 'library/module/field.php' );

    }

}
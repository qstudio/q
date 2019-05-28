<?php

namespace q\hook;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\hook\construct::run();

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

        // admin hooks ##
        // require_once self::get_plugin_path( 'library/hook/switch_theme.php' );
        require_once self::get_plugin_path( 'library/hook/admin_init.php' );
        require_once self::get_plugin_path( 'library/hook/after_switch_theme.php' );
        // require_once self::get_plugin_path( 'library/hook/comment_post.php' );
        // require_once self::get_plugin_path( 'library/hook/save_post.php' );

        // front-end hooks ##
        require_once self::get_plugin_path( 'library/hook/wp_head.php' );
        // require_once self::get_plugin_path( 'library/hook/wp_footer.php' );

        // global hooks ##
        require_once self::get_plugin_path( 'library/hook/the_post.php' );
        require_once self::get_plugin_path( 'library/hook/plugins_loaded.php' );

    }


}
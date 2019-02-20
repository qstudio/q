<?php

namespace q\plugins;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
// use q\controller\generic as generic;

// load it up ##
\q\plugins\facebook::run();

class facebook extends \Q {

    public static function run()
    {
        
        if ( ! \is_admin() ) {

            // define facebook pixel ##
            \add_action( 'wp_head', [ get_class(), 'pixel'], 10 );

        }

    }



    /**
     * Add FB Pixel <head>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function pixel()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { return false; }

        // @todo - add consent checks ##

        // check if we have tag_manager defined in config ##
        if ( ! self::$fb_pixel ) {

            helper::log( 'Facebook Pixel not defined in config' );

            return false;

        }

        // kick it back ##
        echo self::$fb_pixel;

    }


}
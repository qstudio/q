<?php

/**
 * Hook into plugins_loaded
 * 
 * clean up things we don't want
 * add things we do want
 * 
 * filters and actions ##
 *
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

namespace q\hook;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\hook\plugins_loaded::run();

class plugins_loaded extends \Q {

    public static function run()
    {

        // empty error log ##
        \add_action( 'plugins_loaded', array ( get_class(), 'empty_error_log' ), 5 );
            
    }
        
        
    /**
     * Empty error log
     */
    public static function empty_error_log() { 

        // only run when forced to via query arg ##
        if ( ! isset( $_GET['truncate_debug'] ) ) {

            return false;

        }

        if ( ! defined( 'WP_CONTENT_DIR' ) ) {

            return false;

        }

        $f = @fopen( WP_CONTENT_DIR."/debug.log", "r+" );
        
        if ( $f !== false ) {

            #wp_die('emptying error log...');
            ftruncate( $f, 0 );
            fclose( $f );

        }

    }

    
}
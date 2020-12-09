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
 */

namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class plugins_loaded {

    function __construct(){}
	
	function hooks() {

        // empty error log ##
        // \add_action( 'plugins_loaded', array ( get_class(), 'empty_error_log' ), 5 );

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

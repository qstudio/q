<?php

/**
 * Q_Hook_Plugins_Loaded Functions
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

use q\ui\core\options as options;

if ( ! class_exists ( 'Q_Hook_Plugins_Loaded' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'plugins_loaded', array ( 'Q_Hook_Plugins_Loaded', 'init' ), 4 );
    
    // Q_Hook_Plugins_Loaded Class
    class Q_Hook_Plugins_Loaded extends Q
    {

        
        /**
        * Creates a new instance.
        *
        * @wp-hook      init
        * @see          __construct()
        * @since        0.1
        * @return       void
        */
        public static function init() 
        {
            new self;
        }
        

        private function __construct()
        {
            
            // empty error log ##
            add_action( 'plugins_loaded', array ( $this, 'empty_error_log' ), 5 );
            
        }
        
        
        /**
         * Empty error log
         */
        public function empty_error_log() { 

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
    
}



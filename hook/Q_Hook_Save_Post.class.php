<?php

/**
 * Actions attached to API hook - save_post
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_Save_Post' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'save_post', array ( 'Q_Hook_Save_Post', 'init' ), 0 );
    
    // Q_Hook_Save_Post Class
    class Q_Hook_Save_Post extends Q
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
            
            if ( is_admin() ) { // make sure this is only loaded up in the admin ##
                
                add_action( 'save_post', array ( $this, 'save_post' ) );
                 
            }
            
        }
        
        /**
         * Actions to run when a post is saved 
         * 
         * @since       1.0
         */
        public function save_post() {
            
            // clear all transients to keep site content up-to-date ##
            add_action( 'save_post', 'q_transients_delete' );
            
        }
        
    }
    
}


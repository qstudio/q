<?php

/**
 * Actions to call on wp_footer() hook
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_WP_Footer' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'wp_footer', array ( 'Q_Hook_WP_Footer', 'init' ), 0 );
    
    // Q_Hook_WP_Footer Class
    class Q_Hook_WP_Footer extends Q
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
                
                add_action( 'wp_footer', array ( $this, 'q_wp_footer' ) );
                
            }
            
        }
        
        
        // function on swtich theme ##
        function q_wp_footer(){

            #wp_die( 'switched' );

        }
        
    }
    
}
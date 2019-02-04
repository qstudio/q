<?php

/**
 * Actions to call on switch_theme() hook
 *
 * @since 0.4
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_Switch_Theme' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'switch_theme', array ( 'Q_Hook_Switch_Theme', 'init' ), 0 );
    
    // Q_Hook_Switch_Theme Class
    class Q_Hook_Switch_Theme extends Q
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
                
                add_action( 'switch_theme', array ( $this, 'q_switch_theme' ), 1 );
                
            }
            
        }
        
        
        // function on swtich theme ##
        function q_switch_theme(){

            #wp_die( 'switched' );

        }
        
    }
    
}


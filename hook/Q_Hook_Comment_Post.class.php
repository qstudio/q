<?php

/**
 * Actions attached to API hook - comment_post
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_Comment_Post' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'comment_post', array ( 'Q_Hook_Comment_Post', 'init' ), 0 );
    
    // Q_Hook_Comment_Post Class
    class Q_Hook_Comment_Post extends Q
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
                
                
                add_action( 'transition_comment_status', array ( $this, 'approve_comment_callback' ), 10, 3 );
                
                #add_action( 'transition_comment_status', 'q_wp_die' );
                
            }
            
        }
        
        /**
         * Add Comment Approval callback function
         * 
         * @since       1.0
         */
        public function approve_comment_callback( $new_status, $old_status, $comment ) {
            
            if( $old_status != $new_status ) {
                
                if( $new_status == 'approved' ) {
                    
                    q_transients_delete_comments(); // TODO - only delete comment type transients ##
                    
                }
            }
            
        }
        
    }
    
}


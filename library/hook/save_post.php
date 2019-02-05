<?php

/**
 * Actions attached to API hook - save_post
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */
namespace q\hook;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\hook\save_post::run();

class save_post extends \Q {

    public static function run()
    {
            
        if ( \is_admin() ) { // make sure this is only loaded up in the admin ##
                
            // \add_action( 'save_post', array ( $this, 'save_post' ) );
                
        }
        
    }
        
    /**
     * Actions to run when a post is saved 
     * 
     * @since       1.0
     */
    public static function save_post() {
        
        // clear all transients to keep site content up-to-date ##
        \add_action( 'save_post', 'q_transients_delete' );
        
    }
    
}
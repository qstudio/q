<?php

/**
 * Hook into after_switch_theme
 *
 * @since       2.0.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */ 

namespace q\hook;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\hook\after_switch_theme::run();

class after_switch_theme extends \Q {

    public static function run()
    {
            
        if ( \is_admin() ) { // make sure this is only loaded up in the admin ##
            
            // delete all theme transient data from DB cache ##
            // \add_action( 'after_switch_theme', 'q_transients_delete', 1 );
            
        }
            
        // delete q_theme option - to allow next theme to select ##
        delete_site_option( 'q_theme' );
        
    }
    
}
<?php

/**
 * Actions to call on wp_footer() hook
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
\q\hook\wp_footer::run();

class wp_footer extends \Q {

    public static function run()
    {
            
        if ( is_admin() ) { // make sure this is only loaded up in the admin ##
            
            \add_action( 'wp_footer', array ( $this, 'wp_footer' ) );
            
        }
        
    }
    
    
    // function on swtich theme ##
    function wp_footer(){

        #wp_die( 'switched' );

    }
    
}
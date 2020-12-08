<?php

/**
 * Actions to call on wp_footer() hook
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 */

namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
// \q\hook\wp_footer::run();

class wp_footer {

    public static function run(){

        if ( is_admin() ) { // make sure this is only loaded up in the admin ##

            \add_action( 'wp_footer', array ( get_class(), 'wp_footer' ) );

        }

    }


    // function on swtich theme ##
    function wp_footer(){

        #wp_die( 'switched' );

    }

}

<?php

/**
 * Actions attached to API hook - save_post
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 */
namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
\q\hook\save_post::run();

class save_post {

    public static function run(){

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

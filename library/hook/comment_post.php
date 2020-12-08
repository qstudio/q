<?php

/**
 * Hook into comment_post
 *
 * @since       2.0.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 */

namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
\q\hook\comment_post::run();

class comment_post {

    public static function run(){

        if ( \is_admin() ) { // make sure this is only loaded up in the admin ##


            // \add_action( 'transition_comment_status', array ( $this, 'approve_comment_callback' ), 10, 3 );

            //\add_action( 'transition_comment_status', 'q_wp_die' );

        }

    }

    /**
     * Add Comment Approval callback function
     *
     * @since       1.0
     */
    public static function approve_comment_callback( $new_status, $old_status, $comment ) {

        if( $old_status != $new_status ) {

            if( $new_status == 'approved' ) {

                // q_transients_delete_comments();

            }
        }

    }

}

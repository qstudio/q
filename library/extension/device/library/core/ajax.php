<?php

namespace q\device\admin;

use q\device\core\helper as helper;
use q\device\core\core as core;

// load it up ##
// \q\device\admin\ajax::run();

class ajax extends \q_device {

    public static function run()
    {

        // ajax callback ##
        \add_action( 'wp_ajax_q_device', array( get_class(), 'callback' ) );

    }

    public static function callback()
    {

        // failed nonce validation ##
        if ( ! \wp_verify_nonce( $_POST['code'], 'q-stick-nonce' ) || ! isset( $_POST['id'] ) ) {
            
            #helper::log( 'failed nonce: '.$_POST['code'] ); 
            
            die();
        
        }

        $sticky_posts = core::get_sticky_posts();

        // convert ID to array ##
        $post_ids = array ( intval( $_POST['id'] ) );

        #Q_Control::log( $post_ids );

        // get post type ##
        $post_type = \get_post_type( $_POST['id'] ) ? \get_post_type( $_POST['id'] ) : 'post' ;

        // polylang active ##
        if ( \is_plugin_active("polylang/polylang.php") ) {

            // grab translated ID's from polylang ##
            global $polylang;
            $polylang_ids = array();

            foreach ( \pll_languages_list() as $slug ) {
                $polylang_ids[] = \pll_get_post( $_POST['id'], $slug );
            }

            if ( $polylang_ids ) {

                #Q_Control::log( $polylang_ids );

                foreach( $polylang_ids as $id ) {

                    // add to array of ID's ##
                    $post_ids[] = $id;

                }

            }

            #Q_Control::log( $post_ids );

            // remove duplicate values ##
            $post_ids = array_unique($post_ids);

            #Q_Control::log( $post_ids );

        }

        // loop over each ID ##
        $stickyResult = ''; // nada ##

        foreach ( $post_ids as $post_id ) {

            // unset ##
            if ( in_array( $post_id, $sticky_posts ) ) {

                $removeKey = array_search( $post_id, $sticky_posts );
                unset( $sticky_posts[$removeKey] );
                $stickyResult = "removed";
                #helper::log( 'Removed: '.$post_id );
                #$stickyResult = "removed {$post_id}/".var_export($post_ids, true );

            // set ##
            } else {

                array_unshift( $sticky_posts, $post_id );
                $stickyResult = "added";
                #helper::log( 'Added: '.$post_id );

            }

        }

        #helper::log( $sticky_posts );

        // remove duplicate values ##
        $sticky_posts = array_unique( $sticky_posts );

        // save sticky posts option - passed as a normal array, which WP serializes ##
        if( \update_option( 'sticky_posts', $sticky_posts ) ) {

            #helper::log( 'saved sticky...' );

            echo $stickyResult;

        } else {

            #helper::log( 'Error saving sticky..' );

            \_e( "An error occured", "q-sticky" );

        }

        // this is required to return a proper result ##
        die();

    }

}
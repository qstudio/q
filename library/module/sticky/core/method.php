<?php

namespace q\module\sticky;

use q\core\helper as h;
use q\module;

class method extends module\sticky {

    public static function get_defined_post_types(){

        // get defined post_types via filter ##
        $post_types = \apply_filters( "q/sticky/post_types", self::$post_types ); // unserialize ( Q_STICKY_POST_TYPE );
        
        if ( ! is_array ( $post_types ) ) { 
            
            (array) $post_types; 
        
        }

        // helper::log( $post_types );

        // check post_types defined are allow ##
        foreach ( $post_types as $post_type ) {

            if ( ! \post_type_exists( $post_type )) {

                // helper::log( 'Removing post_type: '.$post_type );

                unset( $post_types[$post_type] );

            }

        }

        // log ##
        // helper::log( $post_types );

        // kick it back ##
        return $post_types;

	}
	
	/**
    * gets the current post type in the WordPress Admin
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function get_current_post_type(){
        
        global $post, $typenow, $current_screen;

        //we have a post so we can just get the post type from that
        if ( $post && $post->post_type ) {
            
            return $post->post_type;

        //check the global $typenow - set in admin.php
        } elseif( $typenow ) {
            
            return $typenow;

        //check the global $current_screen object - set in sceen.php
        } elseif( $current_screen && $current_screen->post_type ) {

            return $current_screen->post_type;

        //lastly check the post_type querystring
        } elseif( isset( $_REQUEST['post_type'] ) ) {

            return \sanitize_key( $_REQUEST['post_type'] );

        }

        //we do not know the post type!
        return null;

    }

    public static function get_sticky_posts(){

        // start empty ##
        $sticky_posts = false;

        global $wpdb;
        
        $row = $wpdb->get_row( $wpdb->prepare( 
            "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", 
            'sticky_posts' 
        ) );

        // Has to be get_row instead of get_var because of funkiness with 0, false, null values
        if ( is_object( $row ) ) {

            $sticky_posts = \maybe_unserialize( $row->option_value );

        }

        // caste to array ##
        if ( ! is_array( $sticky_posts ) ) {

            (array) $sticky_posts;

        }

        // remove duplicate values ##
        $sticky_posts = array_unique( $sticky_posts );

        // log ##
        // helper::log( $sticky_posts );

        // kick it back ##
        return $sticky_posts;

    }

}

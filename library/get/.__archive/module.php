<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\get;

// Q Theme ##
use q\theme;

class module extends \q\get {


    /**
     * Get Sibling pages and return them in a flexible "landing" format
     *
     * @since       1.3.0
     * @return      string       HTML Menu
     * @todo        Add exception to block certain pages from showing - "Hide_landing = true"
     */
    public static function landing( $args = array() )
    {

        // get $the_post - allows for post_forcing ##
        // move global post to a new variable, for later use ##
        if ( ! $the_post = self::the_post() ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_landing );

        // find out "depth" of current page ##
        $depth = count( \get_post_ancestors( $the_post->ID ) );

        // work out who to list pages from ##
        $post_parent = $depth == 0 ? $the_post : \get_post( $the_post->post_parent );

        $args = array (
            'child_of'          => $post_parent->ID,
            'sort_column'       => 'menu_order',
            'sort_order'        => 'ASC',
        );
        $pages = \get_pages($args);

        if ( ! $pages ) { return false; }

        #pr( $pages );

        // remove pages with children ##
        foreach ( $pages as $key => $value ) {

            if ( self::has_children( $value->ID ) ) {

                // not needed ##
                unset( $pages[$key] );

            }

        }

        // kick 'em back ##
        return $pages;

    }


}
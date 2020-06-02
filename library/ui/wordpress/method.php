<?php

namespace q\ui\wordpress;

use q\core;
use q\core\helper as h;
use q\ui;

class method extends ui\wordpress {


    /**
     * Check if a plugin is active
     * 
     * @since       2.0.0
     * @return      Boolean
     */
    public static function plugin_is_active( $plugin ) 
    {
        
        return in_array( $plugin, (array) \get_site_option( 'active_plugins', [] ) );
    
    }


    /**
     * Save a value to the options table, either updating or creating a new key
     * 
     * @since       2.0.0
     * @return      Void
     */
    public static function add_update_option( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) 
    {
    
        if ( \get_site_option( $option_name ) != $new_value ) {

            \update_site_option( $option_name, $new_value );

        } else {

            \add_site_option( $option_name, $new_value, $deprecated, $autoload );

        }
    
    }


    /**
    * Get Q Plugin data
    *
    * @return   Object
    * @since    0.3
    */
    public static function plugin_data( $refresh = false ){

        if ( $refresh ) {

            #echo 'refrshing stored framework data<br />'; ##
            \delete_site_option( 'q_plugin_data' ); // delete option ##

        }

        if ( ! $array = \get_site_option( 'q_plugin_data' ) ) {

            $array = array (
                'version'       => self::version // \Q::version
            );

            if ( $array ) {

                self::add_update_option( 'q_plugin_data', $array, '', 'yes' );

            }

        }

        return core\method::array_to_object( $array );

    }



    /**
    * Get installed theme data
    *
    * @return  Object
    * @since   0.3
    */
    public static function theme_data( $refresh = false )
    {

       if ( $refresh ) {

           #echo 'refrshing stored theme data<br />'; ##
           \delete_site_option( 'q_theme_data' ); // delete option ##

       }

       // declare global variable ##
       global $q_theme_data;

       $array = \get_site_option( 'q_theme_data' );

       if ( ! \get_site_option( 'q_theme_data' ) ) {

           #echo 'stored theme option empty<br />';
           #$array = @file_get_contents( q_get_option("uri_parent")."library/version/");

           if( function_exists( 'wp_get_theme' ) ) {
               $array = \wp_get_theme( \get_site_option( 'template' ));
               #$theme_version = $theme_data->Version;
           } else {
               $array = \get_theme_data( \get_template_directory() . '/style.css');
               #$theme_version = $theme_data['Version'];
           }
           #$theme_base = get_option('template');

           if ( $array ) {

               self::add_update_option( 'q_theme_data', $array, '', 'yes' );
               #echo 'stored fresh theme data<br />';

           }

       }

       return core\method::array_to_object( $array );

    }






    // /**
    //  * Force Post ID based on pased arguments, or return false to keep property null
    //  *
    //  * @since       1.0.7
    //  * @return      Mixed       Int Post ID or void
    //  */
    // public static function set_force_post( $args = array() )
    // {

    //     // grab global post - or kick back ##
    //     if ( ! $the_post = self::the_post( $args ) ) { return false; }

    //     // Parse incoming $args into an array and merge it with $defaults - caste to object ##
    //     $args = ( object ) \wp_parse_args( $args, \q_theme::$set_force_post );

    //     // if we're requesting the parent - grab that ##
    //     if ( $args->post_parent === true && $the_post->post_parent ) {

    //         return self::$force_post = get_post( $the_post->post_parent );

    //     }

    //     // nothing cooking ##
    //     return false;

    // }



	/**
     * Check if a page has children
     *
     * @since       1.3.0
     * @param       integer         $post_id
     * @return      boolean
     */
    public static function has_children( $post_id = null )
    {

        // nothing to do here ##
        if ( is_null ( $post_id ) ) { return false; }

        // meta query to allow for inclusion and exclusion of certain posts / pages ##
        $meta_query =
                array(
                    array(
                        'key'       => 'program_sub_group',
                        'value'     => '',
                        'compare'   => '='
                    )
                );

        // query for child or sibling's post ##
        $wp_args = array(
            'post_type'         => 'page',
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'meta_query'        => $meta_query,
        );

        #pr( $wp_args );

        $object = new \WP_Query( $wp_args );

        // nothing found - why? ##
        if ( 0 === $object->post_count ) { return false; }

        // get children ##
        $children = \get_pages(
            array(
                'child_of'      => $post_id,
                'meta_key'      => '',
                'meta_value'    => '',
            )
        );

        // count 'em ##
        if( count( $children ) == 0 ) {

            // No children ##
            return false;

        } else {

            // Has Children ##
            return true;

        }

    }


    public static function list_image_sizes()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) h::log( $_wp_additional_image_sizes ); 

    }



}
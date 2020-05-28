<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\wordpress as wordpress;

// load it up ##
\q\plugin\acf::run();

class acf extends \Q {

    public static function run()
    {

        // add fields ##
        // \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );

        // // filter q/tab/special/script ##
        // \add_filter( 'q/tab/special/script', [ get_class(), 'tab_special_script' ], 10, 2 );

        // permalinks from post objects ##
        // \add_filter( 'q/meta/cta/generic_cta_url_1', array( get_class(), 'meta_post_object_permalink' ), 2, 10 );
        // \add_filter( 'q/meta/cta/generic_cta_url_2', array( get_class(), 'meta_post_object_permalink' ), 2, 10 );

    }



    /**
    * Add ACF Fields
    *
    * @since    2.0.0
    */
    public static function add_field_groups( Array $groups = null )
    {

        // get all field groups ##
        // $groups = self::get_fields();

        if ( 
            ! $groups 
            || ! is_array( $groups )
        ) {

            helper::log( 'No groups to load.' );

            return false;

        }

        // loop over gruops ##
        foreach( $groups as $group ) {

            // load them all up ##
            \acf_add_local_field_group( $group );

        }

    }


    /**
     * Get field group
     */
    public static function get_field_group( String $group = null ) {

        // @todo -- sanity ##
        if ( ! \function_exists('acf_get_field_group') ) {

            helper::log( 'function "acf_get_field_group" not found' );

            return false;

        }

        // @todo -- check if string passed ##

        // @todo -- look for field group and return boolen if fails ##

        return \acf_get_field_group( $group );

    }   



    /**
     * Get field group
     */
    public static function get_fields( String $group = null ) {

        // @todo -- sanity ##
        if ( ! \function_exists('acf_get_field_group') ) {

            helper::log( 'Error -> function "acf_get_field_group" not found' );

            return false;

        }

        // @todo -- check if string passed ##
        if ( is_null( $group ) ) {

            helper::log( 'Error -> No "group" string passed to method.' );

            return false;

        }

        // the $group string might be passed without the prefix "group_" - if it's missing, add it ##
        if ( 'group_' !== substr( $group, 0, 6 ) ) {

            // helper::log( 'Notice -> "group" string passed without "group_" prefix, so adding.' );

            $group = "group_".$group;

        }

        // @todo -- look for field group and return boolen if fails ##
        if ( ! $array = \acf_get_fields( $group ) ) {

            helper::log( 'Notice -> Group: "'.$group.'" not found.' );

            return false;

        }

        // filter ##
        $array = \apply_filters( 'q/plugin/acf/get_fields/'.$group, $array );

        // return ##
        return $array;

    }   


    


    /**
    * Handler for meta permalink
    *
    * @since    2.0.0
    */
    public static function meta_post_object_permalink( $value = null, $array = null, $args = null )
    {

        // helper::log( 'post_object_permalink' );
        
        if ( ! $value || is_null( $value ) ) {

            return false;

        }

        if ( is_numeric( $value ) ) {

            // helper::log( 'ID int passed: '.$value );

            if ( $permalink = \get_permalink( $value ) ) {

                // kick it back ##
                return $permalink;

            }

        } 

        if ( is_string( $value ) ) {

            // helper::log( 'Predefined string URL: '.$value );

            return $value;   

        }

        // or nada ##
        return false;

    }
    


}
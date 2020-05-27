<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\get as get;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class get extends field {

    /**
     * Get Defined Fields
     */
    public static function fields(){

        // sanity ##
        if ( 
            is_null( self::$args ) 
            || ! is_array( self::$args )
            || ! isset( self::$args['fields'] )
        ) {

            self::$log['error'][] = 'Error in passed parameter "fields"';

            return false;

        }

        // Get all ACF fields for this post ##
        if ( ! self::get_acf_fields() ) {

            return false;

       }

        // helper::log( $args[ 'fields' ] );

        // get field names from passes $args ##
        $array = array_column( self::$args[ 'fields' ], 'name' );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

            self::$log['error'][] = 'Error extracting field list from passed data';

            return false;

        }

        // helper::log( $array );

        // assign class property ##
        self::$fields = $array;

        // check if feature is enabled ##
        if ( ! self::is_enabled() ) {

            return false;

       }    

        // remove skipped fields, if defined ##
        self::skip();

        // if group specified, get only fields from this group ##
        self::get_group();

        // we should do a check if $fields is empty after all the filtering ##
        if ( 
            0 == count( self::$fields ) 
        ) {

            self::$log['notice'][] = 'Fields array is empty, so nothing to process...';

            return false;

        }

        // positive ##
        return true;

    }



    /**
     * Get ACF Fields
     */
    public static function get_acf_fields(){

        // option to pass post ID to function ##
        // this can be passed as an arg ##
        $post = 
            isset( $args['post'] ) ? 
            $args['post'] : 
            \get_the_ID() ;

        // get fields ##
        $array = \get_fields( $post );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

            self::$log['notice'][] = 'Post: '.$post.' has no ACF field data or corrupt data returned';

            return false;

        }

        // helper::log( $acf_fields );

        return self::$acf_fields = $array;

    }



    public static function skip(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

            self::$log['error'][] = 'Error in passed $args';

            return false;

        }

        if ( 
            isset( self::$args['skip'] ) 
            && is_array( self::$args['skip'] )
        ) {

            // helper::log( self::$args['skip'] );
            self::$fields = array_diff( self::$fields, self::$args['skip'] );

        }

    }



    public static function get_group(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
            || ! self::$fields
            || ! is_array( self::$fields )
        ) {

            self::$log['error'][] = 'Error in passed $args or $fields';

            return false;

        }

        if ( 
            isset( self::$args['group'] )
        ) {

            // helper::log( 'Removing fields from other groups... BEFORE: '.count( self::$fields ) );
            // helper::log( self::$fields );

            self::$fields = array_intersect_key( self::$acf_fields, array_flip( self::$fields ) );

            // helper::log( 'Removing fields from other groups... AFTER: '.count( self::$fields ) );

        }

        // kick back ##
        return true;

    }





    public static function is_enabled()
    {

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

            self::$log['error'][] = 'Error in passed self::$args';

            return false;

        }

        // check for enabled flag - if none, return true ##
        if ( 
            ! isset( self::$fields[self::$args['enable']] ) 
        ) {

            self::$log['notice'][] = 'No enable check defined in Group: '.self::$args['group'];

            return true;

        }

        // helper::log( 'We are looking for field: '.self::$args['enable'] );

        // kick back ##
        if ( 
            isset( self::$fields[self::$args['enable']] )
            && 1 == self::$fields[self::$args['enable']] 
        ) {

            self::$log['notice'][] = 'Field Group: '.self::$args['group'].' Enabled, continue';

            // helper::log( self::$args['enable'] .' == 1' );

            return true;

        }

        self::$log['notice'][] = 'Field Group: '.self::$args['group'].' NOT Enabled, stopping.';

        // helper::log( self::$args['enable'] .' != 1' );

        // negative ##
        return false;

    }





}
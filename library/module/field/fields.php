<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

// parent ##
use q\module\field as field;

use q\module\field\callback as callback;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\fields as fields;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class fields extends field {

    /**
     * Get Defined Fields
     */
    public static function get(){

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

        // helper::log( self::$args[ 'fields' ] );

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

        // remove skipped fields, if defined ##
        self::skip();

        // if group specified, get only fields from this group ##
        self::get_group();

        // check if feature is enabled ##
        if ( ! self::is_enabled() ) {

            return false;

       }    

        // helper::log( self::$fields );

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

        /*
        self::$fields => Array
        (
            [0] => frontpage_feature_enable
            [1] => frontpage_feature
        )
         */

        // helper::log( self::$fields );
        // helper::log( 'We are looking for field: '.self::$args['enable'] );

        // check for enabled flag - if none, return true ##
        if ( 
            ! isset( self::$fields[self::$args['enable']] )
        ) {

            self::$log['notice'][] = 'No enable check defined in Group: "'.self::$args['group'].'"';

            return true;

        }

        // kick back ##
        if ( 
            isset( self::$fields[self::$args['enable']] )
            && 1 == self::$fields[self::$args['enable']] 
        ) {

            self::$log['notice'][] = 'Field Group: "'.self::$args['group'].'" Enabled, continue';

            // helper::log( self::$args['enable'] .' == 1' );

            return true;

        }

        self::$log['notice'][] = 'Field Group: "'.self::$args['group'].'" NOT Enabled, stopping.';

        // helper::log( self::$args['enable'] .' != 1' );

        // negative ##
        return false;

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



    /**
     * Work passed field data into rendering format
     */
    public static function prepare(){

        // check we have fields to loop over ##
        if ( 
            ! self::$fields
            || ! is_array( self::$fields ) 
        ) {

            self::$log['error'][] = 'Error in $fields array';

            return false;

        }

        // filter $args now that we have fields data from ACF ##
        self::$args = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/before/args/'.self::$args['group'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/before/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

        // self::$log['debug'] = self::$fields;

        // start loop ##
        foreach ( self::$fields as $field => $value ) {

            // self::$log['debug'] = 'Working field: '.$field .' With Value:';
            // self::$log['debug'] = $value;

            // check field has a value ##
            if ( 
                ! $value 
                || is_null( $value )
            ) {

                self::$log['notice'][] = 'Field: '.$field.' has no value, check for data issues.';

                continue;

            }

            // Callback methods on specified fields ##
            // Note - field includes a list of standard callbacks, which can be extended via the filter q/field/callbacks/get ##
            $value = callback::field( $field, $value );

            // helper::log( 'After callback -- field: '.$field .' With Value:' );
            // helper::log( $value );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/field/format/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            format::field( $field, $value );

        }

        // filter all fields ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/after/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }


}
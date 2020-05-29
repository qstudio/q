<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
use q\plugin\acf as acf;
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
// use q\module\field\output as output;
// use q\module\field\ui as ui;

class fields extends field {

    /**
     * Get Defined Fields
     */
    public static function get(){

        // sanity ##
        if ( 
            is_null( self::$args ) 
            || ! is_array( self::$args )
            // || ! isset( self::$args['fields'] )
        ) {

            self::$log['error'][] = 'Error in passed parameter $args';

            return false;

        }

		// helper::log( self::$args['post'] );

        // Get all ACF fields for this post ##
        if ( ! self::get_acf_fields() ) {

            return false;

        }

        // get all fields defined in this group -- pass to $args['fields'] ##
        if ( ! self::get_group_fields() ) {

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



    public static function get_group_fields(){

        // assign variable ##
        $group = self::$args['group'];

        // try to get fields ##
        $array = acf::get_fields( $group );

        // helper::log( $array );

        if ( 
            ! $array
            || ! \is_array( $array )
        ) {

            self::$log['error'][] = 'No valid ACF field group returned for Group: "'.$group.'"';

            return false;

        }

        // filter ##
        // $array = \apply_filters( 'q/field/fields/get/'.$group, $array );

        $array = filter::apply([ 
            'parameters'    => [ 'fields' => $array ], // pass ( $fields ) as single array ##
            'filter'        => 'q/field/fields/get_group_fields/'.$group, // filter handle ##
            'return'        => $array
        ]); 

        // assign to class property ##
        self::$args['fields'] = $array;

        // helper::log( $array[0] );

        // return
        return true;

    }



    /**
     * Get ACF Fields
     */
    public static function get_acf_fields(){

        // option to pass post ID to function ##
        // this can be passed as an arg ##
        $post = 
            isset( self::$args['post'] ) ? 
            self::$args['post'] : 
			\get_the_ID() ;
			
		// helper::log( 'Post: '.$post );

        // get fields ##
		$array = \get_fields( $post );
		
		// helper::log( $array );

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
        // we also take one guess at the field name -- if it's not passed in config ##
        if ( 
            ! isset( self::$args['enable'] )
            && ! isset( self::$fields[self::$args['group'].'_enable'] )
        ) {

            self::$log['notice'][] = 'No enable defined in $args or enable field found for Group: "'.self::$args['group'].'"';

            return true;

        }

        // kick back ##
        if ( 
            // isset( self::$fields[self::$args['enable']] )
            // && 
            // 1 == self::$fields[self::$args['enable']] 
            (
                isset( self::$args['enable'] )
                && 1 == self::$fields[self::$args['enable']]
            )
            || 
            1 == self::$fields[self::$args['group'].'_enable']
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
            'filter'        => 'q/field/fields/prepare/before/args/'.self::$args['group'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/fields/prepare/before/fields/'.self::$args['group'], // filter handle ##
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

            // filter field before callback ##
            $field = filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/field/fields/prepare/before/callback/'.self::$args['group'].'/'.$field, // filter handle ##
                'return'        => $field
            ]); 

            // Callback methods on specified field ##
            // Note - field includes a list of standard callbacks, which can be extended via the filter q/field/callbacks/get ##
            $value = callback::field( $field, $value );

            // helper::log( 'After callback -- field: '.$field .' With Value:' );
            // helper::log( $value );

            // filter field before format ##
            $field = filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/field/fields/prepare/before/format/'.self::$args['group'].'/'.$field, // filter handle ##
                'return'        => $field
            ]); 

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/field/field/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            format::field( $field, $value );

        }

        // filter all fields ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/fields/prepare/after/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }



    
    /**
     * Add $field from self:$fields array
     * 
     */
    public static function set( string $field = null, string $value = null, string $message = null ) {

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $value ) 
        ) {

            self::$log['error'][] = 'No field or value passed to method.';

            return false;

        }

        // helper::log( 'Adding field: '.$field );

        // add field to array ##
        // @todo - perhaps more validation required ##
        self::$fields[$field] = $value;

        // track removal ##
        self::$log['fields']['added'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            log::backtrace() ;

        // positive ##
        return true;

    }



    /**
     * Remove $field from self:$fields array
     * 
     */
    public static function remove( string $field = null, string $message = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

            self::$log['error'][] = 'No field value passed to method.';

            return false;

        }

        // remove from array ##
        unset( self::$fields[$field] );

        // track removal ##
        self::$log['fields']['removed'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            log::backtrace() ;

        // positive ##
        return true;

    }



    
    /**
     * Try to get field type from passed key and field name
     * 
     * @return  boolean
     */
    public static function get_type( $field ){

        // helper::log( 'Checking Type of Field: "'.$field.'"' );

        if ( 
            $key = core::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

            // helper::log( self::$args['fields'][$key] );

            if ( 
                isset( self::$args['fields'][$key]['type'] )
            ) {

                // helper::log( 'Field: "'.$field.'" is Type: "'.self::$args['fields'][$key]['type'].'"' );

                self::$log['notice'][] = 'Field: "'.$field.'" is Type: "'.self::$args['fields'][$key]['type'].'"';

                return self::$args['fields'][$key]['type'];

            }

        }
        
        // kick it back ##
        return false;

    }



    /**
     * Try to get field type from passed key and field name
     * 
     * @return  boolean
     */
    public static function get_callback( $field ){

        // helper::log( 'Checking Type of Field: "'.$field.'"' );

        if ( 
            ! $key = core::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

            self::$log['error'][] = 'failed to find Field: "'.$field.'" data in controller self::$fields';

            // helper::log( 'Error - failed to find Field: "'.$field.'" data in controller self::$fields' );

            return false;

        }

        // helper::log( self::$args['fields'][$key] );

        if ( 
            ! isset( self::$args['fields'][$key]['callback'] )
        ) {

            // helper::log( 'Field: "'.$field.'" has no callback defined' );

            self::$log['notice'][] = 'Field: "'.$field.'" has no callback defined';

            return false;

        }

        // ok - we have a callback, let's check it's formatted correctly ##
        // we need a "method" ##
        // "args" are optional.. I guess, but surely we'd send the field value to the passed method.. or perhaps not ##
        if ( 
            ! is_array( self::$args['fields'][$key]['callback'] )
            || ! isset( self::$args['fields'][$key]['callback']['method'] )
        ) {

            // helper::log( 'Field: "'.$field.'" has a callback, but it is not correctly formatted - not an array or missing "method" key' );

            self::$log['error'][] = 'Field: "'.$field.'" has a callback, but it is not correctly formatted - not an array or missing "method" key';

            return false;

        }

        // ok - we must be good now ##

        // assign to var ##
        $callback = self::$args['fields'][$key]['callback'];

        // helper::log( 'Field: "'.$field.'" has a callback - sending back to caller' );

        self::$log['notice'][] = 'Field: "'.$field.'" has callback: "'.$callback['method'].'" sending back to caller';

        // filter ##
        $callback = filter::apply([ 
            'parameters'    => [ 'callback' => $callback, 'field' => $field, 'args' => self::$args, 'fiekds' => self::$fields ], // params ##
            'filter'        => 'q/field/fields/get_callback/'.self::$args['group'].'/'.$field, // filter handle ##
            'return'        => $callback
        ]); 

        // return ##
        return self::$args['fields'][$key]['callback'];

    }


}
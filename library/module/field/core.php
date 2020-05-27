<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\fields as fields;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class core extends field {

    public static function validate( Array $args ) {

        // checks on required fields in $args array ##
        if (
            ! isset( $args )
            || ! is_array( $args )
            || ! isset( $args['fields'] )
            || ! is_array( $args['fields'] )
            || ! isset( $args['group'] )
            || ! isset( $args['markup'] )
            || ! is_array( $args['markup'] )
            || ! isset( $args['markup']['template'] )
        ){

            self::$log['error'][] = 'Missing required args in Group, so stopping here.. ';

            return false;

        }

        // assign properties with initial filters ##
        $args = self::assign( $args );

        // check if module asked to run $args['config']['run']
        if ( 
            // isset( $args['config']['run'] )
            // && 
            false === $args['config']['run']
        ){

            self::$log['notice'][] = 'config->run defined as false for Group: '.$args['group'].', so stopping here.. ';

            return false;

        }

        // ok - should be good ##
        return true;

    }




    /**
     * Assign class properties with initial filters, merging in passed $args from calling method
     */
    public static function assign( Array $args = null ) {

        // apply global filter to $args - specific calls should be controlled by parameters included directly ##
        self::$args = filter::apply([
             'filter'        => 'q/field/args',
             'parameters'    => self::$args,
             'return'        => self::$args
        ]);

        // grab all passed args and merge with defaults ##
        self::$args = core::parse_args( $args, self::$args );
        
        // test ##
        // helper::log( self::$args );

        // grab args->markup ##
        self::$markup = $args['markup'];

        // return args for validation ##
        return self::$args;

    }



    /**
     * Recursive pass args 
     * 
     * @link    https://mekshq.com/recursive-wp-parse-args-wordpress-function/
     */
    public static function parse_args( &$a, $b ) {

        $a = (array) $a;
        $b = (array) $b;
        $result = $b;
        
        foreach ( $a as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::parse_args( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }

        return $result;

    }


    
    /**
     * Run defined callbacks on specific field ##
     * Retur alters the static class property $args
     * 
     */
    public static function callbacks( String $field = null, $value = null ){

        // sanity ##
        if ( is_null( $field ) ) {

            self::$log['error'][] = 'No field value passed to method.';

            return $value;

        }

        // sanity ##
        if ( is_null( $value ) ) {

            self::$log['error'][] = 'No value passed to method.';

            return $value;

        }

        // Check if there are any allowed callbacks ##
        // Also runs filters to add custom callbacks ##
        $callbacks = self::get_callbacks();

        if ( 
            ! $callbacks
            || ! \is_array( $callbacks ) 
        ) {

            self::$log['error'][] = 'No callbacks allowed in plugin';

            return $value;

        }

        // Check if we have any callbacks to run ## 
        if ( 
            ! isset ( self::$args['callback'] ) 
        ) {

            // helper::log( 'No callbacks registered for field group' );

            return $value;

        } 

        // Check if callbacks are formatted as an array ## 
        if ( 
            ! is_array ( self::$args['callback'] ) 
        ) {

            self::$log['error'][] = 'Error in callbacks format - not Array';

            return $value;

        } 

        // check if this field has any callbacks ##
        if ( 
            ! isset ( self::$args['callback'][$field] ) 
        ) {

            // helper::log( 'No callbacks registered for field: '. $field );

            return $value;

        } 

        // assign method to variable ##
        $method = self::$args['callback'][$field]['method'];
        $field_value = self::$fields[$field];

        // Check we have a real field value to work with ##
        if ( ! $field_value ) {

            self::$log['notice'][] = 'No field value found, stopping callback';

            return $value;

        }

        // Clean up args, with actual passed value ##
        self::$args['callback'][$field]['args'] = str_replace( 
            '%value%', 
            $field_value, 
            self::$args['callback'][$field]['args'] 
        );

        // assign args ##
        $args = self::$args['callback'][$field]['args'];

        // helper::log( $method );
        // helper::log( self::$args );

        // check if field callback is listed in the allowed array of callbacks ##
        if ( ! array_key_exists( $method, $callbacks ) ) {

            self::$log['notice'][] = 'Cannot find callback: '.$method;

            return $value;

        }

        // Check if the method is usable ##
        if (
            // ! method_exists( $args->view, $args->method )
            // || 
            ! is_callable( $method )
        ){

            self::$log['notice'][] = 'Method is not callable: '.$method;

            return $value;

        }

        // checks over ##
        // helper::log( 'Field: '.$field.' has a valid callback: '.$method);

        // filter callback specific to this field ##
        $callbacks = \apply_filters( 
            'q/field/callbacks/'.$method.'/'.$field, 
            $callbacks 
        );

        // run callback using original value of field ##
        $data = call_user_func (
            $method,
            $args
        );

        // Opps ##
        if ( ! $data ) {

            self::$log['notice'][] = 'Method returned bad data..';

            return $value;

        }

        // check ##
        // helper::log( $data );

        // now add new data to class property $fields ##
        self::$fields[$field] = $data;

        // done ##
        return $data;

    }



    /**
     * Run defined callbacks on fields ##
     * 
     */
    public static function get_callbacks()
    {

        return \apply_filters( 'q/field/callbacks/get', self::$callbacks );

    }



    public static function array_search( $field, $value, $array ) {

        foreach ( $array as $key => $val ) {
        
            if ( $val[$field] === $value ) {
        
                return $key;
        
            }
        
        }
        
        return null;

    }



    

    
    /**
     * Add $field from self:$fields array
     * 
     */
    public static function set_field( string $field = null, string $value = null, string $message = null ) {

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
    public static function remove_field( string $field = null, string $message = null ) {

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
    public static function field_type( $field ){

        // helper::log( 'Checking Type of Field: "'.$field.'"' );

        if ( 
            $key = self::array_search( 'key', 'field_'.$field, self::$args['fields'] )
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


     
}
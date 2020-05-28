<?php

namespace q\module\field;

use q\core\helper as helper;

use q\module\field as field;
use q\module\field\core as core;

class callback extends field {


    /**
     * Run defined callbacks on specific field ##
     * Retur alters the static class property $args
     * 
     */
    public static function field( String $field = null, $value = null ){

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

        // helper::log( 'Looking for callback for field: "'.$field.'" in self::$fields' );
        if ( ! $field_callback = fields::get_callback( $field ) ) {

            // self::$log['error'][] = 'No callbacks allowed in plugin';

            return $value;

        }

        // helper::log( $field_callback );

        // assign method to variable ##
        $method = $field_callback['method'];
        $field_value = self::$fields[$field];

        // Check we have a real field value to work with ##
        if ( ! $field_value ) {

            self::$log['notice'][] = 'No field value found, stopping callback';

            return $value;

        }

        // args is an optional param = we default to an array containing the field value ##
        $args = [ $field_value ];

        // if the callback passed args ( or if they were added by a filter ) - let's process them ##
        if ( 
            isset( $field_callback['args'] ) 
        ) {

            // Clean up args, with actual passed value ##
            $field_callback['args'] = str_replace( 
                '%value%', 
                $field_value, 
                $field_callback['args'] 
            );

            // assign args ##
            $args = $field_callback['args'];

        }

        // helper::log( $method );
        // helper::log( $args );

        // check if field callback is listed in the allowed array of callbacks ##
        if ( ! array_key_exists( $method, $callbacks ) ) {

            self::$log['notice'][] = 'Cannot find callback: "'.$method.'"';

            return $value;

        }

        // Check if the method is usable ##
        if (
            // ! method_exists( $args->view, $args->method )
            // || 
            ! is_callable( $method )
        ){

            self::$log['notice'][] = 'Method is not callable: "'.$method.'"';

            return $value;

        }

        // checks over ##
        // helper::log( 'Field: "'.$field.'" has a valid callback: "'.$method.'"');

        // $filter = 'q/field/callback/field/before/'.$method.'/'.$field;
        // helper::log( 'Filter: '.$filter );

        // filter field callback value ( $args ) before callback ##
        $args = filter::apply([ 
            'parameters'    => [ 'args' => $args, 'field' => $field, 'value' => $value, 'fields' => self::$fields ], // params ##
            'filter'        => 'q/field/callback/field/before/'.$method.'/'.$field, // filter handle ##
            'return'        => $args
        ]); 

        // helper::log( $args );

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

}
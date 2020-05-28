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

}
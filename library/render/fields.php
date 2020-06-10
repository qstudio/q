<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\plugin;
use q\render;

class fields extends \q\render {


    /**
     * Work passed field data into rendering format
     */
    public static function prepare(){

        // check we have fields to loop over ##
        if ( 
            ! self::$fields
            || ! is_array( self::$fields ) 
        ) {

			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in $fields array'
			]);

            return false;

        }

        // filter $args now that we have fields data from ACF ##
        self::$args = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/before/args/'.self::$args['group'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/before/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

        // start loop ##
        foreach ( self::$fields as $field => $value ) {

            // check field has a value ##
            if ( 
                ! $value 
                || is_null( $value )
            ) {

				// log ##
				render\log::add([
					'key' => 'notice', 
					'field'	=> __FUNCTION__,
					'value' =>  'Field: '.$field.' has no value, check for data issues.'
				]);

				// h::log( 'Field empty: '.$field );

                continue;

            }

            // filter field before callback ##
            $field = core\filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/render/fields/prepare/before/callback/'.self::$args['group'].'/'.$field, // filter handle ##
                'return'        => $field
            ]); 

            // Callback methods on specified field ##
            // Note - field includes a list of standard callbacks, which can be extended via the filter q/render/callbacks/get ##
            $value = render\callback::field( $field, $value );

            // helper::log( 'After callback -- field: '.$field .' With Value:' );
            // helper::log( $value );

            // filter field before format ##
            $field = core\filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/render/fields/prepare/before/format/'.self::$args['group'].'/'.$field, // filter handle ##
                'return'        => $field
			]); 
			
			// h::log( 'Field value: '.$value );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/render/field/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            render\format::field( $field, $value );

        }

        // filter all fields ##
        self::$fields = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/after/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }




    
    /**
     * Add $field from self:$fields array
     * 
     */
    public static function set( string $field = null, string $value = null ) {

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $value ) 
        ) {

			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' =>   'No field or value passed to method.'
			]);

            return false;

        }

        // helper::log( 'Adding field: '.$field );

        // add field to array ##
        self::$fields[$field] = $value;

		// log ##
		render\log::add([
			'key' => 'fields_added', 
			'field'	=> $field,
			'value' => log::backtrace()
		]);

        // positive ##
        return true;

    }



    /**
     * Remove $field from self:$fields array
     * 
     */
    public static function remove( string $field = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No field value passed to method.'
			]);

            return false;

        }

        // remove from array ##
        unset( self::$fields[$field] );

        // log ##
		render\log::add([
			'key' => 'field_removed', 
			'field'	=> $field,
			'value' => core\method::backtrace([ 'level' => 2, 'return' => 'function' ])
		]);

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
            $key = core\method::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

            // helper::log( self::$args['fields'][$key] );

            if ( 
                isset( self::$args['fields'][$key]['type'] )
            ) {

				// log ##
				render\log::add([
					'key' => 'notice', 
					'field'	=> __FUNCTION__,
					'value' => 'Field: "'.$field.'" is Type: "'.self::$args['fields'][$key]['type'].'"'
				]);

                return self::$args['fields'][$key]['type'];

            }

        }
        
        // kick it back ##
        return false;

    }



    /**
     * Get callbacks registered for $field
     * 
     * @return  boolean
     */
    public static function get_callback( $field ){

		// helper::log( 'Checking Type of Field: "'.$field.'"' );
		
		// sanity ##
		if ( ! isset( self::$args['fields'] ) ) {

			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => '"$args["fields"]" is not defined'
			]);

			return false;

		}

        if ( 
            ! $key = core\method::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'failed to find Field: "'.$field.'" data in $fields'
			]);

            return false;

        }

        // helper::log( self::$args['fields'][$key] );

        if ( 
            ! isset( self::$args['fields'][$key]['callback'] )
        ) {

			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Field: "'.$field.'" has no callback defined'
			]);

            return false;

        }

        // ok - we have a callback, let's check it's formatted correctly ##
        // we need a "method" ##
        // "args" are optional.. I guess, but surely we'd send the field value to the passed method.. or perhaps not ##
        if ( 
            ! is_array( self::$args['fields'][$key]['callback'] )
            || ! isset( self::$args['fields'][$key]['callback']['method'] )
        ) {

			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Field: "'.$field.'" has a callback, but it is not correctly formatted - not an array or missing "method" key'
			]);

            return false;

        }

        // ok - we must be good now ##

        // assign to var ##
        $callback = self::$args['fields'][$key]['callback'];

		// log ##
		render\log::add([
			'key' => 'notice', 
			'field'	=> __FUNCTION__,
			'value' => 'Field: "'.$field.'" has callback: "'.$callback['method'].'" sending back to caller'
		]);

        // filter ##
        $callback = core\filter::apply([ 
            'parameters'    => [ 'callback' => $callback, 'field' => $field, 'args' => self::$args, 'fiekds' => self::$fields ], // params ##
            'filter'        => 'q/render/fields/get_callback/'.self::$args['group'].'/'.$field, // filter handle ##
            'return'        => $callback
        ]); 

        // return ##
        return self::$args['fields'][$key]['callback'];

    }


}
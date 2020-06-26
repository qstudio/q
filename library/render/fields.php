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

			/// log ##
			h::log( self::$args['process'].'~>e:>Error in $fields array' );

            return false;

        }

        // filter $args now that we have fields data from ACF ##
        self::$args = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/before/args/'.self::$args['process'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/before/fields/'.self::$args['process'], // filter handle ##
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
				h::log( self::$args['process'].'~>n:>Field: "'.$field.'" has no value, check for data issues' );

				// h::log( 'Field empty: '.$field );

                continue;

            }

            // filter field before callback ##
            $field = core\filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/render/fields/prepare/before/callback/'.self::$args['process'].'/'.$field, // filter handle ##
                'return'        => $field
            ]); 

            // Callback methods on specified field ##
            // Note - field includes a list of standard callbacks, which can be extended via the filter q/render/callbacks/get ##
            $value = render\callback::field( $field, $value );

            // h::log( 'd:>After callback -- field: '.$field .' With Value:' );
            // h::log( $value );

            // filter field before format ##
            $field = core\filter::apply([ 
                'parameters'    => [ 'field' => $field, 'value' => $value, 'args' => self::$args, 'fields' => self::$fields ], // params
                'filter'        => 'q/render/fields/prepare/before/format/'.self::$args['process'].'/'.$field, // filter handle ##
                'return'        => $field
			]); 
			
			// h::log( 'd:>Field value: '.$value );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/render/field/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            render\format::field( $field, $value );

        }

        // filter all fields ##
        self::$fields = core\filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/render/fields/prepare/after/fields/'.self::$args['process'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }


	
	/**
	 * Define $fields args for render methods
	 * 
	 * @since 4.0.0
	*/
	public static function define( $array = null ){

		// sanity ##
		if (
			is_null( $array )
			|| ! is_array( $array )
		){

			h::log( 'e:>Error in pased $args' );

			return false;

		}

		// h::log( $array );

		foreach( $array as $key => $value ) {

			self::$fields[$key] = $value;

		}

		return true;

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
			h::log( self::$args['process'].'~>n:>No field or value passed to method.' );

            return false;

        }

        // helper::log( 'Adding field: '.$field );

        // add field to array ##
        self::$fields[$field] = $value;

		// log ##
		h::log( self::$args['process'].'~>fields_added:>"'.$field.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

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
			h::log( self::$args['process'].'~>n:>No field value passed to method.' );

            return false;

        }

        // remove from array ##
        unset( self::$fields[$field] );

        // log ##
		h::log( self::$args['process'].'~>fields_removed:>"'.$field.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return true;

    }


    
    /**
     * Try to get field type from passed key and field name
     * 
     * @return  boolean
     */
    public static function get_type( $field = null ){

		// sanity ##
		if(
			is_null( $field )
			|| ! isset( self::$args['fields'] )
		){

			h::log( 'd:>No $field passed to method or args->fields empty' );

			return false;

		}

		// h::log( 'd:>Checking Type of Field: "'.$field.'"' );
		
		// shortcut check for ui\method gather data ##
		if ( 
			isset( $args['type'] ) 
			&& array_key_exists( $args['type'], render\type::get_allowed() )
		){

			h::log( 'd:>Shortcut to type passed in args: '.$args['type'] );

			return $args['type'];

		}

        if ( 
			// self::is_array_of_arrays( $field )
			// || 
			$key = core\method::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

            // h::log( self::$args['fields'][$key] );

            if ( 
                isset( self::$args['fields'][$key]['type'] )
            ) {

				// log ##
				h::log( self::$args['process'].'~>n:>Field: "'.$field.'" is Type: "'.self::$args['fields'][$key]['type'].'"' );

                return self::$args['fields'][$key]['type'];

            }

        }
        
        // kick it back ##
        return false;

	}
	

	public static function is_array_of_arrays( $array = null ) {

		// sanity ##
		if(
			is_null( $array )
			|| ! is_array( $array )
		){

			h::log( 'e:>Error in passed args or not array' );

			return false;

		}

		foreach ( $array as $key => $value ) {

			if ( is_array( $value ) ) {

				h::log( 'd:>is_array' );

				return $key;

			}
			  
		}
		
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

			// get caller ##
			$backtrace = core\method::backtrace([ 'level' => 4, 'return' => 'class_function' ]);

			// log ##
			h::log( self::$args['process'].'~>n:>'.$backtrace.' -> "$args[fields]" is not defined' );

			return false;

		}

        if ( 
            ! $key = core\method::array_search( 'key', 'field_'.$field, self::$args['fields'] )
        ){

			// log ##
			h::log( self::$args['process'].'~>n:>failed to find Field: "'.$field.'" data in $fields' );

            return false;

        }

        // helper::log( self::$args['fields'][$key] );

        if ( 
            ! isset( self::$args['fields'][$key]['callback'] )
        ) {

			// log ##
			h::log( self::$args['process'].'~>n:>Field: "'.$field.'" has no callback defined' );

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
			h::log( self::$args['process'].'~>n:>Field: "'.$field.'" has a callback, but it is not correctly formatted - not an array or missing "method" key' );

            return false;

        }

        // ok - we must be good now ##

        // assign to var ##
        $callback = self::$args['fields'][$key]['callback'];

		// log ##
		h::log( self::$args['process'].'~>n:>Field: "'.$field.'" has callback: "'.$callback['method'].'" sending back to caller' );

        // filter ##
        $callback = core\filter::apply([ 
            'parameters'    => [ 'callback' => $callback, 'field' => $field, 'args' => self::$args, 'fiekds' => self::$fields ], // params ##
            'filter'        => 'q/render/fields/get_callback/'.self::$args['process'].'/'.$field, // filter handle ##
            'return'        => $callback
        ]); 

        // return ##
        return self::$args['fields'][$key]['callback'];

    }


}

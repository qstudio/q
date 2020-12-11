<?php

namespace q\core;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class wpdb {

    // store db query ##
	private $query = false;
	
	function __construct(){

	}

    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    function query( $string = 'options_q_option%' )    {

        if ( 
			$this->query
			&& is_array( $this->query )
			&& isset( $this->query[$string] ) 
		) {

            // h::log( 'e:>query already returned, so using stored values...' );

            return $this->query[$string];

        }

        // grab the global object ##
        global $wpdb;

        // run the query ##
        $query = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT option_name AS name, option_value AS value FROM $wpdb->options WHERE `option_name` LIKE %s limit 0, 1000",
				// 'options_q_option%'
				$string
            ),
            'ARRAY_A' // array ##
        );

        // test ##
        // h::log( $query );

        // validate ##
        if ( 
            ! $query  
            || ! is_array ( $query )
            || 0 == count ( $query ) 
        ) {

            // h::log( 'wpdb failure...' );

            return false;

        }

        // kick it back ##
        return $this->query[$string] = $query;

    }

    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    function prepare( Array $array = null, $string = 'options_q_option_' ){

        // sanity check ##
        if (
            is_null( $array )
            || ! is_array( $array )
        ) {

            h::log( 'e:>Passed Array is corrupt.' );

            return false;

        }

        // we will create a new array, with name and value ##
        $object = new \stdClass();

        // loop over each item and remove - some are strings, some are serliazed ##
        foreach ( $array as $item ) {

            // h::log( $item );

            // get key ##
			// $key = str_replace( 'options_q_option_', '', $item['name'] );
			$key = str_replace( $string, '', $item['name'] );

            // check if value is serlized, if so, break out as single items ##
            if ( is_serialized( $item['value'] ) ) {

                $option = unserialize( $item['value'] );

                // h::log( $option );
                // h::log( core::array_to_object( $option ) );

                // new sub object ##
                $option_object = new \stdClass();

                // we need these to be converted to an object ##
                foreach( $option as $option_key => $option_value ) {

                    // if ( 1 == $option_value ) {

                        // h::log( $option_value );
                 
                        $option_object->$option_value = true;

                    // }

                }

                $value = $option_object;

            } else {

                $value = ( 1 == $item['value'] ) ? true : $item['value'] ;

            }

            // add ##
            $object->$key = $value ;

        }

        // test ##
        // h::log( $array );

        // validate ##
        if ( 
            ! is_object ( $object )
            // || 0 == count ( $object ) 
        ) {

            h::log( 'e:>Prepared object is corrupt.' );

            return false;

        }

        // kick it back ##
        return $object;

    }
    
}

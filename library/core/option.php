<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\plugin; 
// use q\core\wordpress as wordpress;

// load it up ##
\q\core\option::run();

class option extends \Q {

    // store db query ##
    public static $query = false;

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {

        // set debug from Q settings page ---- very late ##
        \add_action( 'plugins_loaded', [ get_class(), 'debug' ], 10 );

    }



    
    /**
    * Get stored values of defined options
    * 
    * @since       1.0
    * @return      Object
    */
    public static function get( String $field = null ) 
    {
        
        // we need to get all stored options from WP ##
        if ( ! $array = self::wpdb() ) {

            h::log( 'e:>No stored values found.' );

            return false;

        }

        // now we need to format them into something which all existing theme controllers expect:
        // an array with "q_option_" removed and a value of 1 or 0 ##
        if ( ! $object = self::prepare( $array ) ) {

            h::log( 'e:>Error preparing stored values' );

            return false;

        }

        // h::log( $object );

        // check if we have an object ##
        if ( ! is_object( $object ) ) {

            h::log( 'e:>Error converting stored values to object' );    
            
            return false;

        }

        // test ##
        // h::log( $object->debug );

        // check if we return a single field or the entire array/object ##
        if ( is_null( $field ) ) {

            // h::log( 'Returning all options.' );

            return $object;

        } elseif ( 
            isset( $field )
            && isset( $object->$field )
        ) {

            // h::log( 'returning field: '.$field );

            return $object->$field;

        }

        // return ##
        return false;

    }



    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    public static function wpdb()
    {

        if ( self::$query ) {

            // h::log( 'query already returned, so using stored values...' );

            return self::$query;

        }

        // grab the global object ##
        global $wpdb;

        // run the query ##
        $query = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT option_name AS name, option_value AS value FROM $wpdb->options WHERE `option_name` LIKE %s limit 0, 1000",
                'options_q_option%'
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
        return self::$query = $query;

    }



    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    public static function prepare( Array $array = null )
    {

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
            $key = str_replace( 'options_q_option_', '', $item['name'] );

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
    
    

    /**
    * define debug setting from stored option
    *
    * @since 2.3.1   
    */
    public static function debug( $option = null )
    {

        // if debug set in code, use that setting first ##
        if ( self::$debug ) { 
        
            // h::log( 'Debug set to true in code, so respect that...' );

            return self::$debug; 
        
        }

        // h::log( 'debug set to: '.self::get('debug') );

        // get all stored options ##
        $debug = self::get('debug'); // \get_field( 'q_option_debug', 'option' ); 
        // \delete_site_option( 'options_q_option_debug', false );

        // check ##
        // h::log( \get_field( 'q_option_debug', 'option') );
        // h::log( 'd:>debug pulled from options table: '.json_encode( $debug ) );
        // h::log( 'debug pulled from options table: '. ( 1 == $debug ? 'True' : 'False' ) );

        // make a real boolean ##
        $debug = ( 
            ( 
                'on' == $debug
                || true === $debug 
            ) ? 
            true : 
            false 
        ) ;

        // check what we got ##
        // h::log( 'd:>debug set to: '. ( $debug ? 'True' : 'False' ) );

        // update property ##
        self::$debug = $debug;

        // kick back something ##
        return self::$debug;

    }



    /**
    * Delete Q Options - could be used to clear old settings
    */
    public static function delete( $option = null )
    {

        

    }



    public static function add_theme_support( $support )
    {

       h::log( 'd:>add_theme_support is deprecated, please use the new Q settings page and filters.' );

       return false;

    }
    
}

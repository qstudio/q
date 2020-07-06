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
    // public static $query = false;

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
		if ( ! $array = core\wpdb::query( 'options_q_option%' ) ) {

            h::log( 'e:>No stored values found.' );

            return false;

        }

        // now we need to format them into something which all existing theme controllers expect:
        // an array with "q_option_" removed and a value of 1 or 0 ##
		if ( ! $object = core\wpdb::prepare( $array, 'options_q_option_' ) ) {

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

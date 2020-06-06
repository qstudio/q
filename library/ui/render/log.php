<?php

namespace q\ui\render;

use q\core;
use q\core\helper as h;
use q\ui;

class log extends ui\render {


	/**
     * Logging function
     * 
     */
    public static function add( Array $args = null ){

		if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            // h::log( 'Debugging is turned off for Field Group: "'.self::$args['group'].'"' );

            return false;

        }   

        // h::log( $args );
		// sanity ##
		if (
			! isset( $args )
			|| is_null( $args )
			|| ! isset( $args['key'] )
			|| ! isset( $args['field'] )
			|| ! isset( $args['value'] )
		){

			h::log( 'Error in passed args' );

			return false;

		}

		// add ##
		self::$log[self::$args['group']][$args['key']][$args['field']] = $args['value'];

		// kick back ##
		return true;

    }


    
    /**
     * Logging function
     * 
     */
    public static function render( Array $args = null ){

        // h::log( $args );

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            // h::log( 'Debugging is turned off for Field Group: "'.$args['group'].'"' );

            return false;

        }   

		// we debug by group -- so, if the group is empty, bail ##
		if ( ! isset( self::$log[ $args['group'] ] ) ) {

			h::log( 'Log Group empty: "'.$args['group'].'"' );

			return false;

		}

		// h::log( self::$log );

        // option to debug only specific fields ##
        $return = 
            (
                isset( $args['debug'] )
                && isset( self::$log [$args['group'] ][ $args['debug'] ] ) 
            ) ?
            self::$log[ $args['group'] ][ $args['debug'] ] :
            self::$log[ $args['group'] ] ;

        // log to log ##
        h::log( $return );

    }


    public static function backtrace(){

        // h::log( \debug_backtrace() );

        return \debug_backtrace()[2]['function'];

    }

}
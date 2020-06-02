<?php

namespace q\ui\field;

use q\core;
use q\core\helper as h;
use q\ui;

class log extends \Q {
    
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

        // option to debug only specific fields ##
        $return = 
            (
                isset( $args['debug'] )
                && isset( self::$log[ $args['debug'] ] ) 
            ) ?
            self::$log[ $args['debug'] ] :
            self::$log ;

        // log to log ##
        h::log( $return );

    }


    public static function backtrace(){

        // h::log( \debug_backtrace() );

        return \debug_backtrace()[2]['function'];

    }

}
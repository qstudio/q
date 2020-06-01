<?php

namespace q\ui\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\ui\field as field;
use q\ui\field\core as core;
use q\ui\field\filter as filter;
use q\ui\field\format as format;
use q\ui\field\fields as fields;
use q\ui\field\log as log;
use q\ui\field\markup as markup;
use q\ui\field\output as output;
use q\ui\field\ui as ui;

class log extends field {
    
    /**
     * Logging function
     * 
     */
    public static function render( Array $args = null ){

        // helper::log( $args );

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            // helper::log( 'Debugging is turned off for Field Group: "'.$args['group'].'"' );

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
        helper::log( $return );

    }


    public static function backtrace(){

        // helper::log( \debug_backtrace() );

        return \debug_backtrace()[2]['function'];

    }

}
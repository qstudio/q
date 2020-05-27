<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\fields as fields;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class log extends field {
    
    /**
     * Logging function
     * 
     */
    public static function render( Array $args = null ){

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            helper::log( 'Debugging is turned off for Field Group: '.$args['group'] );

            return false;

        }   

        // option to debug only specific fields ##
        $return = 
            (
                isset( $args['field'] )
                && isset( self::$log[ $args['field'] ] ) 
            ) ?
            self::$log[ $args['field'] ] :
            self::$log ;

        // log to log ##
        helper::log( $return );

    }


    public static function backtrace(){

        // helper::log( \debug_backtrace() );

        return \debug_backtrace()[2]['function'];

    }

}
<?php

namespace q\ui\field;

use q\core;
use q\core\helper as h;
use q\ui;
// use q\core\config as config;

// use q\ui\field as field;
// // use q\ui\field\core as core;
// use q\ui\field\filter as filter;
// use q\ui\field\format as format;
// use q\ui\field\fields as fields;
// use q\ui\field\log as log;
// use q\ui\field\markup as markup;
// use q\ui\field\output as output;
// use q\ui\field\ui as ui;

// class method extends field {
class method extends ui\field {

	
    public static function validate( Array $args ) {

        // checks on required fields in $args array ##
        if (
            ! isset( $args )
            || ! is_array( $args )
            // || ! isset( $args['fields'] )
            // || ! is_array( $args['fields'] )
            || ! isset( $args['group'] )
            || ! isset( $args['markup'] )
            || ! is_array( $args['markup'] )
            || ! isset( $args['markup']['template'] )
        ){

            self::$log['error'][] = 'Error -> Missing required args, so stopping here.. ';

            return false;

        }

        // assign properties with initial filters ##
        $args = self::assign( $args );

        // check if module asked to run $args['config']['run']
        if ( 
            // isset( $args['config']['run'] )
            // && 
            false === $args['config']['run']
        ){

            self::$log['notice'][] = 'config->run defined as false for Group: '.$args['group'].', so stopping here.. ';

            return false;

        }

        // ok - should be good ##
        return true;

    }




    /**
     * Assign class properties with initial filters, merging in passed $args from calling method
     */
    public static function assign( Array $args = null ) {

        // apply global filter to $args - specific calls should be controlled by parameters included directly ##
        self::$args = core\filter::apply([
             'filter'        => 'q/field/args',
             'parameters'    => self::$args,
             'return'        => self::$args
        ]);

        // grab all passed args and merge with defaults ##
        self::$args = core\method::parse_args( $args, self::$args );
        
        // test ##
        // helper::log( self::$args );

        // grab args->markup ##
        self::$markup = $args['markup'];

        // return args for validation ##
        return self::$args;

    }


     
}
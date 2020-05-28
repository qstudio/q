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

class core extends field {

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
        self::$args = filter::apply([
             'filter'        => 'q/field/args',
             'parameters'    => self::$args,
             'return'        => self::$args
        ]);

        // grab all passed args and merge with defaults ##
        self::$args = core::parse_args( $args, self::$args );
        
        // test ##
        // helper::log( self::$args );

        // grab args->markup ##
        self::$markup = $args['markup'];

        // return args for validation ##
        return self::$args;

    }



    /**
     * Recursive pass args 
     * 
     * @link    https://mekshq.com/recursive-wp-parse-args-wordpress-function/
     */
    public static function parse_args( &$a, $b ) {

        $a = (array) $a;
        $b = (array) $b;
        $result = $b;
        
        foreach ( $a as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::parse_args( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }

        return $result;

    }
    



    public static function array_search( $field, $value, $array ) {

        foreach ( $array as $key => $val ) {
        
            if ( $val[$field] === $value ) {
        
                return $key;
        
            }
        
        }
        
        return null;

    }


     
}
<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\get as get;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class filter extends field {

    /**
     * Filter items at set points to allow for manipulation
     * 
     * 
     */
    public static function apply( Array $args = null ){

        // sanity ##
        if ( 
            ! $args 
            || ! is_array( $args )
            || ! isset( $args['filter'] )
            || ! isset( $args['parameters'] )
            || ! is_array( $args['parameters'] )
            || ! isset( $args['return'] )
        ) {

            self::$log['error'][] = 'Error in passed self::$args';

            return 'Error';

        }

        if( \has_filter( $args['filter'] ) ) {

            // helper::log( 'Running Filter: '.$args['filter'] );

            // run filter ##
            $return = \apply_filters( $args['filter'], $args['parameters'] );

            // check return ##
            // helper::log( $return );

        } else {

            // helper::log( 'No matching filter found: '.$args['filter'] );
            $return = $args['return']; 

        }

        // return true ##
        return $return;

    }

}
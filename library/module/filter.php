<?php

namespace q\module;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;

class filter extends \Q {

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
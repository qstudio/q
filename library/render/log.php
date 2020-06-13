<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
// use q\render;

class log extends \q\render {


	/**
     * Logging function
     * 
     */
    public static function add( Array $args = null ){

		// __deprecated ##
		return false;

    }


    
    /**
     * Logging function
     * 
     */
    public static function set( Array $args = null ){

        // h::debug( 'e:>'.$args['group'] );

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            h::log( 'd:>Debugging is turned off for Field Group: "'.$args['group'].'"' );

            return false;

        }   

		// debug the group ##
		return core\log::set( $args['group'] );

    }

}

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

        // h::log( 'e:>'.$args['group'] );

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            // h::log( 'd:>Debugging is turned OFF for : "'.$args['process'].'"' );

            return false;

        }   

		// h::log( 'd:>Debugging is turned ON for : "'.$args['process'].'"' );

		// filter in group to debug ##
		\add_filter( 'q/core/log/debug', function( $key ) use ( $args ){ 
			// h::log( $key );
			$return = is_array( $key ) ? array_merge( $key, [ $args['process'] ] ) : [ $key, $args['process'] ]; 
			// h::log( $return );
			return 
				$return;
			}
		);

		// return ##
		return true; 

		// debug the group ##
		// return core\log::write( $args['group'] );

    }

}

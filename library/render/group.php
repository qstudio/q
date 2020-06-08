<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
// use q\render;

class group extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::render( $args ); 
	
	}

	public static function render( $args = null ){

		// return h::log( 'hello here..' );
		// h::log( $args );

        // validate passed args ##
        if ( ! args::validate( $args ) ) {

            log::render( $args );

            return false;

		}
		
        // get field names from passed $args ##
        if ( ! get::fields() ) {

            log::render( $args );

            return false;

        }

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
        fields::prepare();

        // Prepare template markup ##
        markup::prepare();

        // optional logging to show removals and stats ##
        log::render( $args );

        // return or echo ##
        return output::return();

    }

}
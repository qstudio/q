<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\render;

class group extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::render( $args ); 
	
	}

	public static function render( $args = null ){

		// return h::log( 'hello here..' );
		// h::log( $args );

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

            render\log::set( $args );

            return false;

		}
		
        // get field names from passed $args ##
        if ( ! render\get::fields() ) {

            render\log::set( $args );

            return false;

        }

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
        render\fields::prepare();

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

    }

}

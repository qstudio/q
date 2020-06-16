<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\render;

class group extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = [], $process = null ){

		// assign "group" - this is used by group to pull acf fields, or to know the calling method for the_ calls ##
		// $args['group'] = isset( $args['method'] ) ? $args['method'] : core\method::backtrace([ 'level' => 2, 'return' => 'function' ]) ;
		// $args['group'] = $method;

		// h::log( $args );

        // validate passed args ##
        if ( ! render\args::validate( $args, $process ) ) {

            render\log::set( $args );

            return false;

		}
		
        // get field names from passed $args ##
        if ( ! render\get::fields() ) {

            render\log::set( $args );

            return false;

		}
		
		// h::log( self::$fields );

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
		render\fields::prepare();
		
		// h::log( self::$fields );

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

    }

}

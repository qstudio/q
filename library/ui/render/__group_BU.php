<?php

namespace q\ui\render;

use q\core\helper as h;
use q\ui;

// class ui extends field {
class group extends ui\render {

	public static function __callStatic( $function, $args ) {

        return self::render( $args ); 
	
	}

	public static function render( Array $args = null ){

		// return h::log( 'hello here..' );
		// h::log( $args );

        // validate passed args ##
        if ( ! method::validate( $args ) ) {

            log::render( $args );

            return false;

		}
		
        // get field names from passed $args ##
        if ( ! fields::get() ) {

            log::render( $args );

            return false;

        }

        // Now we can loop over each field, running callbacks, formatting, removing placeholders in markup
        fields::prepare();

        // Prepare template markup ##
        markup::prepare();

        // optional logging to show removals and stats ##
        log::render( $args );

        // return or echo ##
        return output::return();

    }

}
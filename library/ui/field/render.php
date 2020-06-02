<?php

namespace q\ui\field;

// use q\core\core;
use q\core\helper as h;
use q\ui;
use q\ui\field;

// class ui extends field {
class render extends ui\field {

	public static function __callStatic( $function, $args ) {

        return self::the_group( $args ); 
	
	}

	public static function the_group( Array $args = null ){

		// h:log( 'hello' ); return;

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
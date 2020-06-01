<?php

namespace q\ui\field;

// use q\core\core as core;
use q\core\helper as h;
use q\ui;
// use q\core\config as config;

// use q\ui\field as field;
// use q\ui\field\core as core;
// use q\ui\field\filter as filter;
// use q\ui\field\format as format;
// use q\ui\field\fields as fields;
// use q\ui\field\log as log;
// use q\ui\field\markup as markup;
// use q\ui\field\output as output;
// use q\ui\field\ui as ui;

// class ui extends field {
class render extends ui\field {

	public static function __callStatic( $function, $args ) {

        return self::group( $args ); 
	
	}

	public static function group( Array $args = null ){

        // validate passed args ##
        if ( ! ui\field\method::validate( $args ) ) {

            ui\field\log::render( $args );

            return false;

		}
		
        // get field names from passed $args ##
        if ( ! ui\field\fields::get() ) {

            ui\field\log::render( $args );

            return false;

        }

        // Now we can loop over each field, running callbacks, formatting, removing placeholders in markup
        ui\field\fields::prepare();

        // Prepare template markup ##
        ui\field\markup::prepare();

        // optional logging to show removals and stats ##
        ui\field\log::render( $args );

        // return or echo ##
        return ui\field\output::return();

    }

}
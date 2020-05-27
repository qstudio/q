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

class ui extends field {

    /**
     * Render fields based on passed $args
     * 
     */
    public static function render( Array $args = null ){

        // validate passed args ##
        if ( ! core::validate( $args ) ) {

            log::write();

            return false;

        }

        // get field names from passed $args ##
        if ( ! get::fields() ) {

            log::write();

            return false;

        }

        // Now we can loop over each field, running callbacks, formatting, removing placeholders in markup
        format::fields();

        // Apply Markup to all $fields -- based on generic or specific rules passed in the $args ##
        // returns value to self::$output ##
        markup::template();

        // optional logging to show removals and stats ##
        log::write();

        // return or echo ##
        return output::return();

    }

}
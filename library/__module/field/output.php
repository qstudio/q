<?php

namespace q\ui\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\ui\field as field;
use q\ui\field\core as core;
use q\ui\field\filter as filter;
use q\ui\field\format as format;
use q\ui\field\fields as fields;
use q\ui\field\log as log;
use q\ui\field\markup as markup;
use q\ui\field\output as output;
use q\ui\field\ui as ui;

class output extends field {

    
    public static function return() {

        // filter output ##
        self::$output = filter::apply([ 
            'parameters'    => [ // pass ( $fields, $args, $output ) as single array ##
                'fields'    => self::$fields, 
                'args'      => self::$args, 
                'output'    => self::$output ], 
            'filter'        => 'q/field/output/'.self::$args['group'], // filter handle ##
            'return'        => self::$output
        ]); 

        // helper::log( self::$output );

        // either return or echo ##
        if ( 'echo' === self::$args['config']['return'] ) {

            echo self::$output;

            return true;

        } else {

            return self::$output;

        }

    }

}
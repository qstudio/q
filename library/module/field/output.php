<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\fields as fields;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

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
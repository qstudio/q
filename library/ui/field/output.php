<?php

namespace q\ui\field;

use q\core;
use q\core\helper as h;
use q\ui;

class output extends ui\field {

    
    public static function return() {

        // filter output ##
        self::$output = core\filter::apply([ 
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
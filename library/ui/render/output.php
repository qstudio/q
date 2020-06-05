<?php

namespace q\ui\render;

use q\core;
use q\core\helper as h;
use q\ui;

class output extends ui\render {

    
    public static function return() {

		// sanity ##
		if ( 
			! isset( self::$output )
			|| is_null( self::$output )
		){

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => '$output is empty, so nothing to render.. stopping here.'
			]);

			// kick out ##
			return false;

		}

        // filter output ##
        self::$output = core\filter::apply([ 
            'parameters'    => [ // pass ( $fields, $args, $output ) as single array ##
                'fields'    => self::$fields, 
                'args'      => self::$args, 
				'output'    => self::$output 
			], 
            'filter'        => 'q/render/output/'.self::$args['group'], // filter handle ##
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
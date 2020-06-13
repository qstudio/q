<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class output extends \q\render {

    
    public static function return() {

		// sanity ##
		if ( 
			! isset( self::$output )
			|| is_null( self::$output )
		){

			// log ##
			h::log( self::$args['group'].'~>e:>$output is empty, so nothing to render.. stopping here.');

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

			// reset all args ##
			render\args::reset();

			// stop here ##
            return true;

        } else {

			// grab ##
			$return = self::$output;

			// reset all args ##
			render\args::reset();

			// return ##
            return $return;

        }

    }

}

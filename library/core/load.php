<?php

namespace q\core;

use q\core;
use q\plugin as q;
use q\core\helper as h;

class load {
    
	/**
	 * Load defined assets ##
	 * 
	 * @todo		Move to UI methods 
	 * 
	 */
	public static function libraries( $array = null ){

		// sanity ##
		if (
			is_null( $array )
			|| ! is_array( $array )
		) {

			// h::log( 'e:>Error in passed params' );

			return false;

		}

		// filter, so other plugins can control - inject, remove ##
		$array = core\filter::apply([
			'parameters'    => [ 'array' => $array ], // pass ( $string ) as single array ##
            'filter'        => 'q/core/load/'.core\method::backtrace([ 'return' => 'class' ]), // filter handle ##
            'return'        => $array
		]);

		if ( 
			! $array
		){

			h::log( 'e:>No libraries to load from: "'.core\method::backtrace([ 'return' => 'class_function', 'level' => 2 ]).'"' );

		}

		// h::log( $array );

		// load ##
		foreach( $array as $key => $value ) {

			// value might be empty, if file is missing ##
			if (
				! $value
				|| is_null( $value )
			) {

				h::log( 'e:>Error loading library "'.$key.'" from: "'.core\method::backtrace([ 'return' => 'class_function', 'level' => 2 ]).'"' );

				continue;

			}

			// h::log( 'd:>Loading: '.$key.' from: '.$value );

			require_once( $value );

		}

	}


}

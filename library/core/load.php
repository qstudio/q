<?php

namespace q\core;

use q\core;
use q\core\helper as h;

class load extends \Q {

    
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

			h::log( 'Error in passed params' );

			return false;

		}

		// filter, so other plugins can control - inject, remove ##
		$array = core\filter::apply([
			'parameters'    => [ 'array' => $array ], // pass ( $string ) as single array ##
            'filter'        => 'q/ui/module/load/'.core\method::backtrace([ 'return' => 'class' ]), // filter handle ##
            'return'        => $array
		]);

		if ( 
			! $array
		){

			h::log( 'No libraries to load from: "'.core\method::backtrace([ 'return' => 'class_function' ]).'"' );

		}

		// h::log( $array );

		// load ##
		foreach( $array as $key => $value ) {

			// value might be empty, if file is missing ##
			if (
				! $value
				|| is_null( $value )
			) {

				h::log( 'Error loading library: '.$key );

				continue;

			}

			// h::log( 'Loading: '.$key.' from: '.$value );

			require_once( $value );

		}

	}


}
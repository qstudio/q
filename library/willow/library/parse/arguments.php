<?php

namespace q\willow;

// use q\core;
use q\willow;
use q\willow\core;
use q\core\helper as h;
// use q\ui;
use q\render; // @TODO ##

class arguments extends willow\parse {
	
	/*
	Decode arguments passed in string

	Requirements: 

	>> new = test & config = debug:true, run:true <<
	>> config->debug = true & config->handle = sm:medium, lg:large <<
	*/
	public static function decode( $args = null ){

		// h::log( $args );

		// @todo - sanity ##
		if(
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['string'] )
			// || ! isset( $args['field'] )
			|| ! isset( $args['value'] )
			|| ! isset( $args['tag'] )
		){

			h::log( 'e:>Error in passed arguments' );

			return false;

		}
		
		// assign variables ##
		$string = $args['string'];
		$field = isset( $args['field'] ) ? $args['field'] : null ;
		$value = $args['value'];
		$tag = $args['tag'];

		// trim string ##
		$string = trim( $string );

		// check for "<" at start and ">" at end ##
		if( 
			! core\method::starts_with( $string, '@' )
			// ||
			// ! render\method::ends_with( $string, ']' ) 
		){

			// h::log( 'd:>Argument string does not start with "@" so not treated as an array' );

			return false;

		}

		// check for "=" delimiter ##
		if( false === strpos( $string, '=' ) ){

			h::log( 'e:>Error in passed string format, missing delimiter "="' );

			return false;

		}

		// ok, strip off leading @ ##
		// $string = render\method::string_between( $string, '[', ']' );
		$string = str_replace( '@', '', $string );

		// clean up string -- remove all white space ##
		// $string = trim( $string );
		$string = str_replace( ' ', '', $string );
		// h::log( 'd:> '.$string );

		// extract data from string ##
		$array = core\method::parse_str( $string );

		// h::log( $array );

		// return false;
		/*

		// // check for ":"
		// if( false === strpos( $string, '&' ) ){

		// 	h::log( 'e:>Error in passed string format, missing ";"' );

		// 	return false;

		// }

		// h::log( 'd:>'.$string );
		// format should be key:value; key: value; key : value ;-- white space agnostic ##
		$explode = explode( ';', $string );
		
		// validate ##
		if ( 
			! isset( $explode[0] )
			|| ! isset( $explode[1] )
		){

			h::log( 'e:>Error in passed string format, missing ":"' );

			return false;

		}

		// trim again ##
		$explode = array_map( 'trim', $explode );

		$array = [];
		// h::log( $explode );

		// make simple key->value array ##
		$array_base = [];
		$array_base[ $explode[0] ] = $explode[1];

		// REALLY UGLY ### to improve tomorrow ##
		foreach( $array_base as $key => $value ){

			// check if key is an array "->" ##
			if( false !== strpos( $key, '->' ) ) {

				$key_array = explode( '->', $key );
				$array[ $key_array[0] ][ $key_array[1] ] = $value;

			} else {

				$array[ $key ] = $value;

			}

		}

		// return $array;

		// h::log( $config_array );
		*/

		// sanity ##
		if ( 
			// ! $config_string
			! $array
			|| ! is_array( $array )
			// || ! isset( $matches[0] ) 
			// || ! $matches[0]
		){

			h::log( render::$args['task'].'~>e:>No config found in value: '.$value ); // @todo -- add "loose" lookups, for white space '@s
			h::log( 'd:>No config found in value: '.$value ); // @todo -- add "loose" lookups, for white space '@s''

			return false;

		}

		// What about affecting markup via config ?? 
		// Check if config or markup key exists in $array ?? -- NOPE - because we might pass both in a single [[ xx ]]
		// we need to build 2 seperate arrays, or split at this point
		// LIKE ---> if ( array_key_exists( 'config', $array ) ) // make new args array ##
		// LIKE ---> if ( array_key_exists( 'markup', $array ) ) // make new markup array ##

		// h::log( $array );

		switch( $tag ) {

			case "function" :

				// kick back to function handler ##
				return $array;

			break ;

			case "variable" :

				// h::log( $array );
				// h::log( render::$args[$field] );
				// h::log( 'field: '.$field );

				// merge in new args to args->field ##
				if ( ! isset( render::$args[$field] ) ) render::$args[$field] = [];
				render::$args[$field] = \q\core\method::parse_args( $array, render::$args[$field] );

			break;

		}

		// done ##
		return true;

		// h::log( render::$args[$field] );

		/*
		foreach( $array as $k => $v ) {

			// h::log( "d:>config_setting: ".$k );
			// h::log( "d:>config_value: ".$v );

			h::log( 't:> - add config handlers... based on field type ##');
			// config::handle() ##
			switch ( $field_type ) {

				case "src" :
					
					// assign new $args[FIELDNAME]['src'] with value of config --
					render::$args[$field_name]['config'][$k] = is_array( $v ) ? (array) $v : $v; // note, $v may be an array of values

				break ;

			}

		}
		*/
		
	}




	public static function cleanup( $args = null ){

		$open = trim( willow\tags::g( 'arg_o' ) );
		$close = trim( willow\tags::g( 'arg_c' ) );

		// h::log( self::$markup['template'] );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/render/parse/argument/cleanup/regex', 
			 // "/$open.*?$close/ms" 
			//  "/$open\s+.*?\s+$close/s"
			"~\\$open\s+(.*?)\s+\\$close~"
		);

		// use callback to allow for feedback ##
		self::$markup['template'] = preg_replace_callback(
			$regex, 
			function($matches) {
				
				// h::log( $matches );
				if ( 
					! $matches 
					|| ! is_array( $matches )
					|| ! isset( $matches[1] )
				){

					return false;

				}

				// h::log( $matches );

				// get count ##
				$count = strlen($matches[1]);

				if ( $count > 0 ) {

					h::log( $count .' argument tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			self::$markup['template'] 
		);
		
		// self::$markup['template'] = preg_replace( $regex, "", self::$markup['template'] ); 

	}


}

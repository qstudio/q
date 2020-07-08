<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class arguments extends \q\render {


	
	/*
	Decode arguments passed in string

	Requirements: 

	[[ new = test & config = debug:true, run:true ]]
	[[ config->debug = true & config->handle = sm:medium, lg:large ]]
	*/
	public static function decode( $args = null ){

		// @todo - sanity ##
		if(
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['string'] )
			|| ! isset( $args['field'] )
			|| ! isset( $args['variable'] )
			|| ! isset( $args['tag'] )
		){

			h::log( 'e:>Error in passed arguments' );

			return false;

		}
		
		// assign variables ##
		$string = $args['string'];
		$field = $args['field'];
		$variable = $args['variable'];
		$tag = $args['tag'];

		// clean up string -- remove all white space ##
		// $string = trim( $string );
		$string = str_replace(' ', '', $string);
		// h::log( 'd:> '.$string );

		// check for ":"
		if( false === strpos( $string, '=' ) ){

			h::log( 'e:>Error in passed string format, missing "="' );

			return false;

		}

		$array = render\method::parse_str( $string );

		// h::log( $array );

		return false;

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

		/*
		' handle: square-sm; '
		*/
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

		// sanity ##
		if ( 
			// ! $config_string
			! is_array( $array )
			// || ! isset( $matches[0] ) 
			// || ! $matches[0]
		){

			h::log( self::$args['task'].'~>e:>No config in variable: '.$variable ); // @todo -- add "loose" lookups, for white space '@s
			h::log( 'd:>No config in variable: '.$variable ); // @todo -- add "loose" lookups, for white space '@s''

			return false;

		}

		// What about affecting markup via config ?? 
		// Check if config or markup key exists in $array ?? -- NOPE - because we might pass both in a single [[ xx ]]
		// we need to build 2 seperate arrays, or split at this point
		// LIKE ---> if ( array_key_exists( 'config', $array ) ) // make new args array ##
		// LIKE ---> if ( array_key_exists( 'markup', $array ) ) // make new markup array ##

		// merge in new args to args->field ##
		self::$args[$field_name] = core\method::parse_args( self::$args[$field_name], $array );

		/*
		foreach( $array as $k => $v ) {

			// h::log( "d:>config_setting: ".$k );
			// h::log( "d:>config_value: ".$v );

			h::log( 't:> - add config handlers... based on field type ##');
			// config::handle() ##
			switch ( $field_type ) {

				case "src" :
					
					// assign new $args[FIELDNAME]['src'] with value of config --
					self::$args[$field_name]['config'][$k] = is_array( $v ) ? (array) $v : $v; // note, $v may be an array of values

				break ;

			}

		}
		*/
		
	}




	public static function cleanup(){

		$open = trim( tags::g( 'arg_o' ) );
		$close = trim( tags::g( 'arg_c' ) );

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

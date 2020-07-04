<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class argument extends \q\render {


	/**
	 * Scan for arguments in variables and convert to $config->data
	 * 
	 * @since 4.1.0
	*/
	public static function prepare( $args = null ){

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// h::log( $args['key'] );

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all variable variables from markup string ##
        if ( 
            ! $variables = render\variable::get( $string, 'variable' ) 
        ) {

			// h::log( self::$args['task'].'~>d:>No variables found in $markup');
			// h::log( 'd:>No variables found in $markup: '.self::$args['task']);

			return false;

		}

		// log ##
		h::log( self::$args['task'].'~>d:>"'.count( $variables ) .'" variables found in string');
		// h::log( 'd:>"'.count( $variables ) .'" variables found in string');

		// remove any leftover variables in string ##
		foreach( $variables as $key => $value ) {

			// h::log( self::$args['task'].'~>d:>'.$value );

			// now, we need to look for the config pattern, defined as field(setting:value;) and try to handle any data found ##
			// $regex_find = \apply_filters( 'q/render/markup/config/regex/find', '/[[(.*?)]]/s' );
			
			// if ( 
			// 	preg_match( $regex_find, $value, $matches ) 
			// ){

			if ( 
				// $config_string = method::string_between( $value, '[[', ']]' )
				$config_string = method::string_between( $value, trim( tag::g( 'arg_o' )), trim( tag::g( 'arg_c' )) )
			){

				// store variable ##
				$variable = $value;

				// $config_string = json_encode( $config_string );
				// h::log( $config_string );

				// // grab config JSON ##
				// $config_string = '{ "handle":{ "all":"square-sm", "lg":"vertical-lg" }, "string": "value" }';
				$config_object = json_decode( $config_string );
				// $config_object = isset( $config_json[0] ) ? $config_json[0] : false ;

				// h::log( 'd:>config: '.$config_string );
				// h::log( $config_json );
				// h::log( $config_object );

				// sanity ##
				if ( 
					! $config_string
					|| ! is_object( $config_object )
					// || ! isset( $matches[0] ) 
					// || ! $matches[0]
				){

					h::log( self::$args['task'].'~>e:>No config in variable: '.$variable ); // @todo -- add "loose" lookups, for white space '@s
					// h::log( 'd:>No config in variable: '.$variable ); // @todo -- add "loose" lookups, for white space '@s''

					continue;

				}

				// h::log( $matches[0] );

				// get field ##
				// h::log( 'value: '.$value );
				
				// $field = trim( method::string_between( $value, '{{ ', '[[' ) );
				$field = str_replace( $config_string, '', $value );

				// clean up field data ##
				$field = preg_replace( "/[^A-Za-z0-9_]/", '', $field );

				// h::log( 'field: '.$field );

				// check if field is sub field i.e: "post__title" ##
				if ( false !== strpos( $field, '__' ) ) {

					$field_array = explode( '__', $field );

					$field_name = $field_array[0]; // take first part ##
					$field_type = $field_array[1]; // take second part ##

				} else {

					$field_name = $field; // take first part ##
					$field_type = $field; // take second part ##

				}

				// we need field_name, so validate ##
				if (
					! $field_name
					|| ! $field_type
				){

					h::log( self::$args['task'].'~>e:>Error extracting $field_name or $field_type from variable: '.$variable );

					continue;

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_variable value
				// $variable = $value;
				// $new_variable = explode( '(', $variable )[0].' }}';
				// $new_variable = '{{ '.$field.' }}';
				$new_variable = tag::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);

				// test what we have ##
				// h::log( "d:>variable: ".$value );
				// h::log( "d:>new_variable: ".$new_variable);
				// h::log( "d:>field_name: ".$field_name );
				// h::log( "d:>field_type: ".$field_type );

				foreach( $config_object as $k => $v ) {

					// h::log( "d:>config_setting: ".$k );
					// h::log( "d:>config_value: ".$v );

					h::log( 't:> - add config handlers... based on field type ##');
					// config::handle() ##
					switch ( $field_type ) {

						case "src" :
							
							// assign new $args[FIELDNAME]['src'] with value of config --
							self::$args[$field_name]['config'][$k] = is_object( $v ) ? (array) $v : $v; // note, $v may be an array of values

						break ;

					}

				}

				// h::log( self::$args[$field_name] );

				// now, edit the variable, to remove the config ##
				render\variable::edit( $variable, $new_variable );

			}
		
        }

	}


	public static function cleanup(){

		$open = trim( tag::g( 'arg_o' ) );
		$close = trim( tag::g( 'arg_c' ) );

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

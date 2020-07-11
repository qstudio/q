<?php

namespace q\willow;

use q\willow\core;
use q\core\helper as h;
use q\willow;

use q\render; // TODO

class variables extends willow\parse {

	/**
	 * Scan for arguments in variables and convert to $config->data
	 * 
	 * @since 4.1.0
	*/
	public static function prepare( $args = null ){

		h::log( 't:>Problem with willow<>render cross-over.. remember.. Q can run without Willow, so willow needs to run without Q..' );

		// sanity -- this requires ##
		if ( 
			! isset( render::$markup )
			|| ! is_array( render::$markup )
			|| ! isset( render::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = render::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( render::$args['task'].'~>e:>Error in $markup' );
			// h::log( 'd:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all variable variables from markup string ##
        if ( 
            ! $variables = willow\markup::get( $string, 'variable' ) 
        ) {

			// h::log( render::$args['task'].'~>d:>No variables found in $markup');
			// h::log( 'd:>No variables found in $markup: '.render::$args['task']);

			return false;

		}

		// log ##
		h::log( render::$args['task'].'~>d:>"'.count( $variables ) .'" variables found in string');
		// h::log( 'd:>"'.count( $variables ) .'" variables found in string');

		// remove any leftover variables in string ##
		foreach( $variables as $key => $value ) {

			h::log( render::$args['task'].'~>d:>'.$value );
			// h::log( 'd:>variable: "'.$value.'"' );

			// now, we need to look for the config pattern, defined as field(setting:value;) and try to handle any data found ##
			// $regex_find = \apply_filters( 'q/render/markup/config/regex/find', '/[[(.*?)]]/s' );
			
			// if ( 
			// 	preg_match( $regex_find, $value, $matches ) 
			// ){

			if ( 
				// $config_string = method::string_between( $value, '[[', ']]' )
				$config_string = core\method::string_between( $value, trim( tags::g( 'arg_o' )), trim( tags::g( 'arg_c' )) )
			){

				// store variable ##
				$variable = $value;

				// h::log( $matches[0] );

				// get field ##
				// h::log( 'value: '.$value );
				
				// $field = trim( method::string_between( $value, '{{ ', '[[' ) );
				$field = str_replace( $config_string, '', $value );

				// clean up field data ## -- @TODO, move to \Q::sanitize();
				$field = preg_replace( "/[^A-Za-z0-9._]/", '', $field );

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

					h::log( render::$args['task'].'~>e:>Error extracting $field_name or $field_type from variable: '.$variable );

					continue;

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_variable value
				// $variable = $value;
				// $new_variable = explode( '(', $variable )[0].' }}';
				// $new_variable = '{{ '.$field.' }}';
				$new_variable = willow\tags::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);

				// test what we have ##
				// h::log( 'd:>variable: "'.$value.'"' );
				// h::log( 'd:>new_variable: "'.$new_variable.'"' );
				// h::log( 'd:>field_name: "'.$field_name.'"' );
				// h::log( 'd:>field_type: "'.$field_type.'"' );

				// pass to argument handler ##
				willow\arguments::decode([ 
					'string' 	=> $config_string, // string containing arguments ##
					'field' 	=> $field_name, 
					'value' 	=> $variable, // variable value being worked from loop ##
					'tag'		=> 'variable'	
				]);

				// h::log( render::$args[$field_name] );

				// now, edit the variable, to remove the config ##
				willow\markup::swap( $variable, $new_variable, 'variable', 'variable' );

			}
		
        }

	}



	public static function cleanup( $args = null ){

		$open = trim( willow\tags::g( 'var_o' ) );
		$close = trim( willow\tags::g( 'var_c' ) );

		// h::log( render::$markup['template'] );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/render/parse/variable/cleanup/regex', 
			 // "/$open.*?$close/ms" 
			//  "/$open\s+.*?\s+$close/s"
			"~\\$open\s+(.*?)\s+\\$close~"
		);

		// use callback to allow for feedback ##
		render::$markup['template'] = preg_replace_callback(
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

					// h::log( $count .' variable tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			render::$markup['template'] 
		);

		// h::log( render::$markup['template'] );
		
		// render::$markup['template'] = preg_replace( $regex, "", render::$markup['template'] ); 

	}



}

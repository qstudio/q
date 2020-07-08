<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class functions extends \q\render {


    /**
	 * Scan for functions in markup and convert to variables and $fields
	 * 
	 * @since 4.1.0
	*/
    public static function prepare( $args = null ){

		// h::log( $args['key'] );
		h::log( 't:>TODO -- arguments and parameters passed from willow tags...' );

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tags::g( 'fun_o' ) );
		$close = trim( tags::g( 'fun_c' ) );
		// $end = trim( tags::g( 'sec_e' ) );
		// $end_preg = str_replace( '/', '\/', ( trim( tags::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/function/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// // strip all function blocks, we don't need them now ##
			// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			// $regex_remove = \apply_filters( 
			// 	'q/render/markup/function/regex/remove', 
			// 	"/$open.*?$close/ms" 
			// 	// "/{{#.*?\/#}}/ms"
			// );
			// self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::log( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for function name and arguments...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// h::log( $matches[0][$match][0] );

				$function = method::string_between( $matches[0][$match][0], $open, $close );

				// return entire function string, including tags for tag swap ##
				$function_match = method::string_between( $matches[0][$match][0], $open, $close, true );
				// h::log( '$function_match: '.$function_match );
				// $markup = method::string_between( $matches[0][$match][0], $close, $end );

				// clean up ##
				$function = trim($function);

				// h::log( 'function: '.$function );

				// sanity ##
				if ( 
					! isset( $function ) 
					// || ! strstr( $function, '__' )
					// || ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// default args ##
				// $hash = 'function__'.mt_rand();
				$hash = $function; // set hash to entrire function, in case this has no config and is not class_method format ##
				// h::log( 'hash set to: '.$hash );
				// $function_args = [ 'config' => [ 'hash' => $hash ] ];
				$function_args = [];
				$class = false;
				$method = false;
				$function_array = false;
				$config_string = false;
				
				if ( 
					// $config_string = method::string_between( $value, '[[', ']]' )
					$config_string = method::string_between( $function, trim( tags::g( 'arg_o' )), trim( tags::g( 'arg_c' )) )
				){
	
					// $config_string = json_encode( $config_string );
					// h::log( $config_string );

					if ( 
						is_object( json_decode( $config_string ))
					){

						// decode to JSON object ##
						$config_object = json_decode( $config_string );

						h::log( 'is JSON..' );

					} else {

						$config_object = false;

					}
	
					// h::log( 'd:>config: '.$config_string );
					// h::log( $config_json );
					// h::log( $config_array );
	
					// sanity ##
					if ( 
						! $config_string
						// || ! is_array( $config_array )
						// || ! isset( $matches[0] ) 
						// || ! $matches[0]
					){
	
						h::log( self::$args['task'].'~>e:>No valid config found in function: '.$function ); // @todo -- add "loose" lookups, for white space '@s
						// h::log( 'd:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s''
	
						continue;
	
					}
	
					// h::log( $matches[0] );
	
					// $field = trim( method::string_between( $value, '{{ ', '[[' ) );
					// $function = str_replace( trim( tags::g( 'arg_o' )).$config_string.trim( tags::g( 'arg_c' )), '', $function );
					// h::log( 'function: '.$function );
					// $function = method::string_between( $function, trim( tags::g( 'fun_o' )), trim( tags::g( 'arg_o' )) );
					$function_explode = explode( trim( tags::g( 'arg_o' )), $function );
					// h::log( $function_explode );
					$function = $function_explode[0];
					// $class = false;
					// $method = false;

					$hash = $function; // update hash to take simpler function name.. ##
					// h::log( 'hash updated to: '.$hash );

					// $function_args = [ 'config' => [ 'hash' => $hash ] ];
	
					// h::log( 'function: '.$function );

					if ( $config_object && is_object( $config_object ) ) {

						foreach( $config_object as $k => $v ) {

							// $function_args = [];
		
							// h::log( "d:>config_setting: ".$k );
							// h::log( $v ); // may be an array ##
							
							$function_args[$k] = $v;
		
						}

					// single arguments are always assigned as markup->template ##
					} else {

						// call_user_func_array requires an array, so casting here ##
						$function_args['markup']['template'] = $config_string;

					}
	
				}

				// h::log( 'function: '.$function );

				// $hash = 'function__'.$function; /// BIG PROBLEM ##

				// sort of unique hash reference for placeholder and field key ##
				// $hash = bin2hex( random_bytes(16) );
				// $hash = 'function__'.preg_replace( "/[^A-Za-z0-9_]/", '', $function );
				// $hash = 'function__'.\mt_rand();
				// $hash = 'get_the_title';

				// Q function correction ##
				if( render\method::starts_with( $function, '~' ) ) {

					// h::log( 'Function starts with ~: '.$function );

					$function = str_replace( '~', '', $function );
					
					// format to q::function ##
					$function = 'q::'.$function;

					// h::log( 'Function now: '.$function );

				}

				// function might be passed in class::method format, let's check ##
				if (
					strstr( $function, '::' )
				){

					// h::log( 'function is class::method' );

					list( $class, $method ) = explode( '::', $function );

					$hash = $method; //  PROBLEM ##
					// h::log( 'hash updated again to: '.$hash );

					// h::log( 'class: '.$class.' - method: '.$method );

					if ( 
						! $class 
						|| ! $method 
					){

						h::log( 'e:>Error in passed function name, stopping here' );

						continue;

					}

					// function correction ##
					if( 'q' == $class ) $class = '\\q\\render';

					if ( 
						! class_exists( $class )
						|| ! is_callable( $class, $method )
						// we cannot validate the method, as it's MAGIC ## ??
					){

						h::log( 'Cannot find - class: '.$class.' - method: '.$method );

						continue;

					}	

					// if we are calling q\render.. we need to wrap up the current process or append args.. how ??

					// h::log( 'd:>Function array created' );

					// make function an array ##
					$function_array = [ $class, $method ];

				} elseif ( ! function_exists( $function ) ) {

					h::log( 'Cannot find function: '.$function );

					continue;

				}

				// test what we have ##
				// h::log( 'd:>function: "'.$function.'"' );
				// h::log( 'hash at end is...: '.$hash );

				// if ( function_exists( $function ) ) build list of functions to call later at end of current process.. pre-output.. placeholder can be added now.. fields added to list... how??

				// class and method set ## 

				if ( $class && $method ) {

					// collect current process state ##
					render\args::collect();

					// h::log( 'd:>Calling class_method: '.$class.'::'.$method );

					// pass args, if set ##
					if( $function_args ){

						// h::log( 'passing args array:' );
						// h::log( $function_args );

						render\fields::define([
							$hash => call_user_func_array( $function_array, [ 0 => $function_args ] )
						]);

					} else { 

						// h::log( 'NOT passing args:' );

						render\fields::define([
							$hash => call_user_func_array( $function_array, [] )
						]);

					}

					// restore previous process state ##
					render\args::set();

				} else {

					// h::log( 'd:>Calling function' );

					// pass args, if set ##
					if( $function_args ){

						// h::log( 'passing args array:' );
						// h::log( $function_args );

						render\fields::define([
							$hash => call_user_func_array( $function, [ 0 => $function_args ] )
						]);

					} else {

						// h::log( 'NOT passing args:' );

						render\fields::define([
							$hash => call_user_func( $function )
						]);

					}

				}

				// finally -- add a variable "{{ $field }}" before this comment block at $position to markup->template ##
				$variable = render\tags::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				// variable::set( $variable, $position, 'variable' ); // '{{ '.$field.' }}'
				render\tag::swap( $function_match, $variable ); // '{{ '.$field.' }}'

			}

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		// $regex_remove = \apply_filters( 
		// 	'q/render/markup/function/regex/remove', 
		// 	"/$open.*?$close/ms" 
		// 	// "/{{#.*?\/#}}/ms"
		// );
		// self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 

		}

	}


	public static function cleanup(){

		$open = trim( tags::g( 'fun_o' ) );
		$close = trim( tags::g( 'fun_c' ) );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/render/parse/function/cleanup/regex', 
		 	"/$open.*?$close/ms" 
		// 	// "/{{#.*?\/#}}/ms"
		);
		
		// self::$markup['template'] = preg_replace( $regex, "", self::$markup['template'] ); 

		// use callback to allow for feedback ##
		self::$markup['template'] = preg_replace_callback(
			$regex, 
			function($matches) {
				
				// h::log( $matches );

				// get count ##
				$count = strlen($matches[1]);

				if ( $count > 0 ) {

					h::log( $count .' function tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			self::$markup['template'] 
		);

	}


}

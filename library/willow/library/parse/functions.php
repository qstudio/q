<?php

namespace q\willow;

use q\willow;
use q\willow\core;
use q\core\helper as h;

// render -- @TODO ##
use q\render;

class functions extends willow\parse {

    /**
	 * Scan for functions in markup and convert to variables and $fields
	 * 
	 * @since 4.1.0
	*/
    public static function prepare( $args = null ){

		// h::log( $args['key'] );
		// h::log( 't:>TODO -- arguments and parameters passed from willow tags...' );

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
		){

			h::log( render::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( willow\tags::g( 'fun_o' ) );
		$close = trim( willow\tags::g( 'fun_c' ) );
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
			// render::$markup['template'] = preg_replace( $regex_remove, "", render::$markup['template'] ); 
		
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

				$function = core\method::string_between( $matches[0][$match][0], $open, $close );

				// return entire function string, including tags for tag swap ##
				$function_match = core\method::string_between( $matches[0][$match][0], $open, $close, true );
				// h::log( '$function_match: '.$function_match );
				// $markup = core\method::string_between( $matches[0][$match][0], $close, $end );

				// clean up ##
				$function = trim( $function );

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
				$hash = $function; // set hash to entire function, in case this has no config and is not class_method format ##
				// h::log( 'hash set to: '.$hash );
				// $function_args = [ 'config' => [ 'hash' => $hash ] ];
				// $function_args = [];

				$function_args = '';

				$class = false;
				$method = false;
				$function_array = false;
				$config_string = false;

				// $config_string = core\method::string_between( $value, '[[', ']]' )
				$config_string = core\method::string_between( 
					$function, 
					trim( willow\tags::g( 'arg_o' )), 
					trim( willow\tags::g( 'arg_c' )) 
				);

				// args set to >> strip << - this should remove any markup set ##
				/*
				if ( 'strip' == $config_string ){

					h::log( 'unsetting markup for function: '.$function );

					// $function_hash_args['markup'] = [];
					// h::log( render::$args );

					// add config ##
					$function_args = \q\core\method::parse_args( 
						$function_args, 
						[ 
							'config' => [ 
								'strip' => true 
							] 
						]
					);

					// update function ##
					$function = str_replace( [ $config_string, tags::g( 'arg_o' ), tags::g( 'arg_c' ) ], '', $function );

					// falsey ##
					$config_string = '';

					h::log( 'function after strip: "'.$function.'"' );

				}
				*/

				// go with it ##
				if ( 
					$config_string 
				){
	
					// clean up string -- remove all white space ##
					// $string = trim( $string );
					// $config_string = str_replace( ' ', '', $config_string );
					// h::log( 'd:> '.$string );
	
					// pass to argument handler ##
					$function_args = 
						willow\arguments::decode([ 
							'string' 	=> $config_string, 
							// 'field' 	=> $field_name, 
							'value' 	=> $function,
							'tag'		=> 'function'	
						]);
	

					// h::log( $matches[0] );
	
					// $field = trim( core\method::string_between( $value, '{{ ', '[[' ) );
					// $function = str_replace( trim( tags::g( 'arg_o' )).$config_string.trim( tags::g( 'arg_c' )), '', $function );
					// h::log( 'function: '.$function );
					// $function = core\method::string_between( $function, trim( tags::g( 'fun_o' )), trim( tags::g( 'arg_o' )) );
					$function_explode = explode( trim( willow\tags::g( 'arg_o' )), $function );
					// h::log( $function_explode );
					$function = trim( $function_explode[0] );
					// $class = false;
					// $method = false;

					$hash = $function; // update hash to take simpler function name.. ##
					// h::log( 'hash updated to: '.$hash );
					h::log( 'function: "'.$function.'"' );

					// single arguments are always assigned as markup->template ##
					// } else {
					if ( 
						! $function_args
						|| ! is_array( $function_args ) 
					) {

						// create required array structure + value
						$function_args['markup']['template'] = $config_string;

					}
	
				// perhaps function passed with ( ) and arguments -- check ##
				} elseif( 
					strstr( $function, '(' ) 
					&& strstr( $function, ')' )
				){

					// try to get function args
					$function_args = core\method::string_between( $function, '(', ')' );

					// clean up ##
					$function_args = trim( $function_args );

					$function_explode = explode( '(', $function );

					$function = trim( $function_explode[0] );

					// h::log( 'd:>function_args: '.$function_args );

				}

				h::log( 'd:>function: '.$function );

				// starts with "~" so, ALL return values should be escaped #
				if( core\method::starts_with( $function, '~' ) ) {

					// h::log( 'Function starts with "~": '.$function );
					$function = str_replace( '~', '', $function );

					// update hash ##
					$hash = $function;
					
					// h::log( 'Function now: '.$function );

					// add config ##
					$function_args = \q\core\method::parse_args( 
						$function_args, 
						[ 
							'config' => [ 
								'escape' => true 
							] 
						]
					);

				}

				// starts with "~" so, ALL return values should be escaped #
				if( core\method::starts_with( $function, '-' ) ) {

					h::log( 'Function starts with "-": '.$function );
					$function = str_replace( '-', '', $function );

					// update hash ##
					$hash = $function;
					
					// h::log( 'Function now: '.$function );

					// add config ##
					$function_args = \q\core\method::parse_args( 
						$function_args, 
						[ 
							'config' => [ 
								'strip' => true 
							] 
						]
					);

				}

				// function scope "\" indicates global, outsode of context scope ##
				if( core\method::starts_with( $function, '\\' ) ) {

					// h::log( 'Function starts with "\": '.$function );
					// global functions are escaped with "\" ##
					$function = str_replace( '\\', '', $function );

					// update hash ##
					$hash = $function;
					
					// h::log( 'Function now: '.$function );

				} else {

					// h::log( 'Function starts with ~: '.$function );
					// Q functions are namespaced with "~" ##
					// $function = str_replace( '~', '', $function );
					$function = str_replace( '::', '__', $function );
					
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

					$hash = $method; 
					// h::log( 'hash updated again to: '.$hash );

					// h::log( 'class: '.$class.' - method: '.$method );

					if ( 
						! $class 
						|| ! $method 
					){

						h::log( 'e:>Error in passed function name, stopping here' );

						continue;

					}

					// clean up class name @todo -- 
					// $class = core\method::sanitize( $class, 'php_class' );
					// $class = preg_replace( '/[^a-zA-Z0-9-_]/', '', (string) $class );

					// clean up method name @todo --
					// $method = core\method::sanitize( $method, 'php_function' );
					// $method = preg_replace( '/[^a-zA-Z0-9-_]/', '', (string) $method );

					// function correction ##
					if( 'q' == $class ) $class = '\\q\\context';

					if ( 
						! class_exists( $class )
						|| ! is_callable( $class, $method )
						// we cannot validate the method, as it's MAGIC ## ??
					){

						h::log( 'Cannot find - class: '.$class.' - method: '.$method );

						continue;

					}	

					// h::log( 'd:>Function array created' );

					// make class__method an array ##
					$function_array = [ $class, $method ];

				// try to locate function directly in global scope ##
				} elseif ( ! function_exists( $function ) ) {

					h::log( 'd:>Cannot find function: '.$function );

					continue;

				}

				// test what we have ##
				// h::log( 'd:>function: "'.$function.'"' );
				$hash = $hash.'.'.rand();
				// h::log( 'hash at end is...: '.$hash );

				// if ( function_exists( $function ) ) build list of functions to call later at end of current process.. pre-output.. placeholder can be added now.. fields added to list... how??

				// h::log( $function_args );

				// class and method set ## 

				if ( $class && $method ) {

					// correcting args ##
					$function_hash_args = [];

					// buffer hash ##
					// $function_hash_args['config']['hash'] = $hash ;

					// merge in new args ##
					// $function_args = \q\core\method::parse_args( $function_args, $function_hash_args );
					$function_args = \q\core\method::parse_args( 
						$function_args, 
						[ 
							'config' => [ 
								'hash' => $hash 
							] 
						]
					);

					// collect current process state ##
					render\args::collect();

					// h::log( 'd:>Calling class_method: '.$class.'::'.$method );

					// pass args, if set ##
					if( $function_args ){

						// h::log( 'passing args array:' );
						// h::log( $function_args );

						render\fields::define([
							$hash => call_user_func_array( $function_array, [ 0 => $function_args ] ) // 0 index is for static class args gatherer ##
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

					// h::log( 'd:>Calling function: '.$function );

					// clean up function name -- @todo ##
					// $function = preg_replace( '/[^a-zA-Z0-9-_]/', '', (string) $function );
					// $function = core\method::sanitize( $function, 'php_function' );

					// pass args, if set ##
					if( $function_args ){

						// h::log( 'passing args array:' );
						// h::log( $function_args );

						render\fields::define([
							$hash => call_user_func( $function, $function_args )
						]);

					} else {

						// h::log( 'NOT passing args:' );

						render\fields::define([
							$hash => call_user_func( $function )
						]);

					}

				}

				// finally -- add a variable "{{ $field }}" where the function tag block was in markup->template ##
				$variable = willow\tags::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				// variable::set( $variable, $position, 'variable' ); // '{{ '.$field.' }}'
				willow\markup::swap( $function_match, $variable, 'function', 'variable' ); // '{{ '.$field.' }}'

			}

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		// $regex_remove = \apply_filters( 
		// 	'q/render/markup/function/regex/remove', 
		// 	"/$open.*?$close/ms" 
		// 	// "/{{#.*?\/#}}/ms"
		// );
		// render::$markup['template'] = preg_replace( $regex_remove, "", render::$markup['template'] ); 

		}

	}


	public static function cleanup( $args = null ){

		$open = trim( willow\tags::g( 'fun_o' ) );
		$close = trim( willow\tags::g( 'fun_c' ) );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/render/parse/function/cleanup/regex', 
		 	"/$open.*?$close/ms" 
		// 	// "/{{#.*?\/#}}/ms"
		);
		
		// render::$markup['template'] = preg_replace( $regex, "", render::$markup['template'] ); 

		// use callback to allow for feedback ##
		render::$markup['template'] = preg_replace_callback(
			$regex, 
			function($matches) {
				
				if( ! isset( $matches[1] )) {

					return "";

				}

				// h::log( $matches );

				// get count ##
				$count = strlen($matches[1]);

				if ( $count > 0 ) {

					h::log( $count .' function tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			render::$markup['template'] 
		);

	}


}

<?php

namespace q\willow;

use q\willow;
use q\willow\core;
use q\core\helper as h;

// render -- @TODO ##
use q\render;

class functions extends willow\parse {

	private static 

		$hash, 
		$flags,
		$function,
		$function_match, // full string matched ##
		$function_args,
		$class,
		$method,
		$function_array,
		$config_string
		// $is_global
	
	;


	private static function reset(){

		self::$hash = false; 
		self::$flags = false;
		self::$function = false;
		self::$function_args = false;
		self::$class = false;
		self::$method = false;
		self::$function_array = false;
		self::$config_string = false;
		// self::$is_global = false;

	}


	public static function flags(){

		if(
			// true === strpos( self::$function, trim( willow\tags::g( 'fla_o' ) ) )
			// && true === strpos( self::$function, trim( willow\tags::g( 'fla_c' ) ) )
			core\method::starts_with( self::$function, trim( willow\tags::g( 'fla_o' ) ) ) 
			&& $flags = core\method::string_between( self::$function, trim( willow\tags::g( 'fla_o' ) ), trim( willow\tags::g( 'fla_c' ) ) )
		){

			// $flags = core\method::string_between( self::$function, trim( willow\tags::g( 'fla_o' ) ), trim( willow\tags::g( 'fla_c' ) ) );
			h::log( self::$function. ' - flags - '.$flags );

			self::$flags = str_split( $flags );
			self::$flags = array_fill_keys( self::$flags, true );
			// h::log( self::$flags );

			$flags_all = core\method::string_between( self::$function, trim( willow\tags::g( 'fla_o' ) ), trim( willow\tags::g( 'fla_c' ) ), true );

			// remove flags ##
			self::$function = str_replace( $flags_all, '', self::$function );

			// h::log( 'function after flag removal: '.self::$function );

		}

	}
	

    /**
	 * Scan for functions in markup and convert to variables and $fields
	 * 
	 * @since 4.1.0
	*/
    public static function prepare( $args = null ){

		// h::log( $args['key'] );

		// sanity -- method requires requires ##
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

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/willow/parse/functions/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

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

				// clear slate ##
				self::reset();

				// h::log( 'd:>Searching for function name and arguments...' );

				// $position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// h::log( $matches[0][$match][0] );

				self::$function = core\method::string_between( $matches[0][$match][0], $open, $close );

				// return entire function string, including tags for tag swap ##
				self::$function_match = core\method::string_between( $matches[0][$match][0], $open, $close, true );
				// h::log( '$function_match: '.$function_match );

				// look for flags ##
				self::flags();

				// clean up ##
				self::$function = trim( self::$function );

				// h::log( 'function: '.self::$function );

				// sanity ##
				if ( 
					! self::$function
					|| ! isset( self::$function ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// default args ##
				self::$hash = self::$function; // set hash to entire function, in case this has no config and is not class_method format ##
				// h::log( 'hash set to: '.$hash );

				// $function_args = '';

				// $class = false;
				// $method = false;
				// $function_array = false;
				// $config_string = false;

				// $config_string = core\method::string_between( $value, '[[', ']]' )
				self::$config_string = core\method::string_between( 
					self::$function, 
					trim( willow\tags::g( 'arg_o' )), 
					trim( willow\tags::g( 'arg_c' )) 
				);

				// go with it ##
				if ( 
					self::$config_string 
				){
	
					// clean up string -- remove all white space ##
					// $string = trim( $string );
					// $config_string = str_replace( ' ', '', $config_string );
					// h::log( 'd:> '.$string );
	
					// pass to argument handler ##
					self::$function_args = 
						willow\arguments::decode([ 
							'string' 	=> self::$config_string, 
							// 'field' 	=> $field_name, 
							'value' 	=> self::$function,
							'tag'		=> 'function'	
						]);
	

					// h::log( $matches[0] );
	
					// $field = trim( core\method::string_between( $value, '{{ ', '[[' ) );
					// $function = str_replace( trim( tags::g( 'arg_o' )).$config_string.trim( tags::g( 'arg_c' )), '', $function );
					// h::log( 'function: '.$function );
					// $function = core\method::string_between( $function, trim( tags::g( 'fun_o' )), trim( tags::g( 'arg_o' )) );
					$function_explode = explode( trim( willow\tags::g( 'arg_o' )), self::$function );
					// h::log( $function_explode );
					self::$function = trim( $function_explode[0] );
					// $class = false;
					// $method = false;

					self::$hash = self::$function; // update hash to take simpler function name.. ##
					// h::log( 'hash updated to: '.$hash );
					// h::log( 'function: "'.$function.'"' );

					// perhaps args are listed a csv - so check and if so, split to an array ##
					/*
					if ( 
						strstr( self::$config_string, ',' )
						&& $config_string_explode = explode( ',', self::$config_string )
					){

						if( is_array( $config_string_explode ) ) {

							// test ##
							h::log( $config_string_explode );

							//

						}

					}
					*/

					// single arguments are always assigned as markup->template ##
					// } else {
					if ( 
						! self::$function_args
						|| ! is_array( self::$function_args ) 
					) {

						// create required array structure + value
						// self::$function_args['markup']['template'] = self::$config_string;
						self::$function_args = self::$config_string;

					}
					
				}

				// @TODO -- make patterns and improve flag system ( --f --c -- d etc.. )
				$function = self::flags(); 

				// function name might still contain opening and closing args brakets, which were empty - so remove them ##
				self::$function = str_replace( [
						trim( willow\tags::g( 'arg_o' )), 
						trim( willow\tags::g( 'arg_c' )) 
					], '',
					self::$function 
				);

				// check if we are being passed a simple string function, or a class::method
				if(
					strpos( self::$function, '::' )
				){

					// global ##
					if( 
						// core\method::starts_with( self::$function, '+' ) 
						! isset( self::$flags['g'] )
					) {
	
						// h::log( 'CLASS::Function starts with "+": '.self::$function );
						// global functions are escaped with "+" ##
						// self::$function = str_replace( '+', '', self::$function );
	
						// update hash ##
						// self::$hash = self::$function;

						// scope tracker ##
						// self::$is_global = true;
	
						// h::log( 'Function now: '.self::$function );
						// h::log( 'Hash now: '.self::$hash );

					// Q/context ##
					// } else {

						self::$function = str_replace( '::', '__', self::$function );
						
						// function correction ##
						// if( 'q' == self::$class ) self::$class = '\\q\\context';

						// format to q::function ##
						self::$function = '\\q\\context::'.self::$function;

						// update hash? ##
						self::$hash = self::$function;
						// h::log( 'Function now: '.$function );

					}

					// h::log( 'function is class::method' );
					// break function into class::method parts ##
					list( self::$class, self::$method ) = explode( '::', self::$function );

					// update hash ##
					self::$hash = self::$method; 
					// h::log( 'hash updated again to: '.self::$hash );

					if ( 
						! self::$class 
						|| ! self::$method 
					){

						h::log( 'e:>Error in passed function name, stopping here' );

						continue;

					}

					// clean up class name @todo -- 
					self::$class = \q\core\method::sanitize( self::$class, 'php_class' );

					// clean up method name --
					self::$method = \q\core\method::sanitize( self::$method, 'php_function' );

					// h::log( 'class: '.self::$class.' - method: '.self::$method );

					// // function correction ##
					// if( 'q' == self::$class ) self::$class = '\\q\\context';

					if ( 
						! class_exists( self::$class )
						// || ! method_exists( self::$class, self::$method ) // internal methods are found via callstatic lookup ##
						|| ! is_callable( self::$class, self::$method )
					){

						h::log( 'Cannot find - class: '.self::$class.' - method: '.self::$method );

						continue;

					}	

					// h::log( 'd:>Function array created' );

					// make class__method an array ##
					self::$function_array = [ self::$class, self::$method ];

				// simple function string ##
				} else {

					if( 
						// core\method::starts_with( self::$function, '+' ) 
						isset( self::$flags['g'] )
					) {
	
						// h::log( 'Function starts with "+": '.$function );
						// global functions are escaped with "+" ##
						// self::$function = str_replace( '+', '', self::$function );
	
						// update hash ##
						// self::$hash = self::$function;

						// scope tracker ##
						// self::$is_global = true;
	
						// clean up function name ##
						self::$function = \q\core\method::sanitize( self::$function, 'php_function' );

						// try to locate function directly in global scope ##
						if ( ! function_exists( self::$function ) ) {
	
							h::log( 'd:>Cannot find global function: '.self::$function );
	
							continue;
	
						}

					} else {

						// clean up function name -- @todo ##
						self::$function = \q\core\method::sanitize( self::$function, 'php_function' );

						// try to locate function directly in global scope ##
						if ( ! function_exists( self::$function ) ) {
							
							h::log( 'd:>Cannot find Q scope function: '.self::$function );

							continue;

						}

					}


				}

				// test what we have ##
				// h::log( 'd:>function: "'.$function.'"' );
				self::$hash = self::$hash.'.'.rand();
				// h::log( 'hash at end is...: '.self::$hash );

				// h::log( $function_args );

				// class and method set -- so call ## 
				if ( self::$class && self::$method ) {

					// correcting args ##
					// $function_hash_args = [];

					// buffer hash ##
					// $function_hash_args['config']['hash'] = $hash ;

					// merge in new args ##
					// if( ! self::$is_global ) { // unless in the global scope ##
					if( ! isset( self::$flags['g'] ) ) { // unless in the global scope ##

						self::$function_args = \q\core\method::parse_args( 
							self::$function_args, 
							[ 
								'config' => [ 
									'hash' => self::$hash 
								] 
							]
						);
						
					}

					if( ! isset( self::$flags['g'] ) ) { // unless in the global scope ##

						// e = escape --- escape html ##
						if( isset( self::$flags['e'] ) ) { // unless in the global scope ##

							self::$function_args = \q\core\method::parse_args( 
								self::$function_args, 
								[ 
									'config' => [ 
										'escape' => true 
									] 
								]
							);
						}

						// r = raw --- strip html / php tags ##
						if( isset( self::$flags['s'] ) ) {

							self::$function_args = \q\core\method::parse_args( 
								self::$function_args, 
								[ 
									'config' => [ 
										'strip' => true 
									] 
								]
							);
						}

					}

					// collect current process state ##
					render\args::collect();

					// h::log( 'd:>Calling class_method: '.self::$class.'::'.self::$method );

					// pass args, if set ##
					if( self::$function_args ){

						// h::log( 'passing args array:' );
						// h::log( self::$function_args );

						render\fields::define([
							self::$hash => call_user_func_array( 
								self::$function_array, [ 0 => self::$function_args ] ) // 0 index is for static class args gatherer ##
						]);

					} else { 

						// h::log( 'NOT passing args:' );
						// $return = self::$class::{ self::$method }();
						// if( is_array( $get ) ){

							// h::log( 'is_array...' );
							// h::log( $get );

						// } else {

							// h::log( 'NOT array: '.$get );

						// } 

						// render\fields::define([
						// 	self::$hash => $return
						// ]);

						// global function returns can be pushed directly into buffer ##
						// NOT sure, basically, this skips all internal processing for external functions, which sounds right ??
						render::$buffer[ self::$hash ] = self::$class::{ self::$method }();

					}

					// restore previous process state ##
					render\args::set();

				} else {

					// h::log( 'd:>Calling function: '.self::$function );

					// pass args, if set ##
					if( self::$function_args ){

						// h::log( 'passing args:' );
						// h::log( self::$function_args );

						render\fields::define([
							self::$hash => call_user_func( self::$function, self::$function_args )
						]);

					} else {

						// h::log( 'NOT passing args:' );

						/*
						render\fields::define([
							self::$hash => call_user_func( self::$function )
						]);
						*/

						// external functions skip internal processing and return their results directly to the buffer ##
						render::$buffer[ self::$hash ] = self::$function;

					}

				}

				// finally -- add a variable "{{ $field }}" where the function tag block was in markup->template ##
				$variable = willow\tags::wrap([ 'open' => 'var_o', 'value' => self::$hash, 'close' => 'var_c' ]);
				// variable::set( $variable, $position, 'variable' ); // '{{ '.$field.' }}'
				willow\markup::swap( self::$function_match, $variable, 'function', 'variable' ); // '{{ '.$field.' }}'

				// clear slate ##
				self::reset();

			}

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

<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class parser extends \q\render {

	protected static $regex = [
		'clean'	=>"/[^A-Za-z0-9_]/" // clean up string to alphanumeric + _
	];

	// track wrapping ##
	// protected static $wrapped = false;

    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
     * 
     */
    public static function prepare( $args = null ){

		// h::log( self::$args['markup'] );

		// post-format markup to extract markup keys collected by config ##
		// markup::merge();

		// if buffering, we need to collect the return from each parser ##

		// pre-format markup to extract functions ##
		parser::function();

		// pre-format markup to extract sections ##
		parser::section();

		// pre-format markup to extract partials ##
		parser::partial();

		// pre-format markup to extract comments and place in html ##
		// parser::comment();

		// search for config settings in markup, such as "src" handle ##
		parser::argument();


	}

	

	/**
	 * Scan for functions in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function function(){

		// h::log( $args['key'] );

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
		$open = trim( tag::g( 'fun_o' ) );
		$close = trim( tag::g( 'fun_c' ) );
		// $end = trim( tag::g( 'sec_e' ) );
		// $end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
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

			// strip all function blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/function/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
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
				$hash = $function; /// BIG PROBLEM ##
				// $function_args = [ 'config' => [ 'hash' => $hash ] ];
				$function_args = [];
				$class = false;
				$method = false;
				$function_array = false;
				$config_string = false;
				
				if ( 
					// $config_string = method::string_between( $value, '[[', ']]' )
					$config_string = method::string_between( $function, trim( tag::g( 'arg_o' )), trim( tag::g( 'arg_c' )) )
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
					// $function = str_replace( trim( tag::g( 'arg_o' )).$config_string.trim( tag::g( 'arg_c' )), '', $function );
					// h::log( 'function: '.$function );
					// $function = method::string_between( $function, trim( tag::g( 'fun_o' )), trim( tag::g( 'arg_o' )) );
					$function_explode = explode( trim( tag::g( 'arg_o' )), $function );
					// h::log( $function_explode );
					$function = $function_explode[0];
					// $class = false;
					// $method = false;

					$hash = $function; /// BIG PROBLEM ##
					// $function_args = [ 'config' => [ 'hash' => $hash ] ];
	
					// h::log( 'function: '.$function );

					if ( $config_object && is_object( $config_object ) ) {

						foreach( $config_object as $k => $v ) {

							// $function_args = [];
		
							// h::log( "d:>config_setting: ".$k );
							// h::log( $v ); // may be an array ##
							
							$function_args[$k] = $v;
		
						}

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

				// function might be passed in class::method format, let's check ##
				if (
					strstr( $function, '::' )
				){

					// h::log( 'function is class::method' );

					list( $class, $method ) = explode( '::', $function );

					// h::log( 'class: '.$class.' - method: '.$method );

					if ( 
						! $class 
						|| ! $method 
					){

						h::log( 'e:>Error in passed function name, stopping here' );

						continue;

					}

					// function correction for q render methods... ##
					if( 'render' == $class ) $class = '\\q\\'.$class;

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

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	/**
	 * Scan for sections in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function section(){

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
		$open = trim( tag::g( 'sec_o' ) );
		$close = trim( tag::g( 'sec_c' ) );
		$end = trim( tag::g( 'sec_e' ) );
		$end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/section/regex/find', 
			"/$open\s+(.*?)\s+$end_preg/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/section/regex/remove', 
				"/$open.*?$end_preg/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::debug( $matches[1] );

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

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// foreach( $matches[1][0][0] as $k => $v ){
				// $delimiter = \apply_filters( 'q/render/markup/comments/delimiter', "::" );
				// list( $field, $markup ) = explode( $delimiter, $value[0] );
				// $field = method::string_between( $matches[0][$match][0], '{{#', '}}' );
				// $markup = method::string_between( $matches[0][$match][0], '{{# '.$field.' }}', '{{/#}}' );

				$field = method::string_between( $matches[0][$match][0], $open, $close );
				$markup = method::string_between( $matches[0][$match][0], $close, $end );

				// sanity ##
				if ( 
					! isset( $field ) 
					|| ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match key or value' );

					continue; 

				}

				// clean up ##
				$field = trim($field);
				$markup = trim($markup);

				// $hash = 'section__'.\mt_rand();
				$hash = $field;

				// test what we have ##
				// h::log( 'd:>field: "'.$field.'"' );
				// h::log( "d:>markup: $markup" );

				// so, we can add a new field value to $args array based on the field name - with the markup as value
				// self::$args[$field] = $markup;
				self::$markup[$hash] = $markup;

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				// placeholder::set( "{{ $field }}", $position ); // , $markup
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	
	/**
	 * Scan for functions in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function partial(){

		// h::log( $args['key'] );

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
		$open = trim( tag::g( 'par_o' ) );
		$close = trim( tag::g( 'par_c' ) );
		// $end = trim( tag::g( 'sec_e' ) );
		// $end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/partial/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/partial/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
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

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// get partial data ##
				$partial = method::string_between( $matches[0][$match][0], $open, $close );
				// $markup = method::string_between( $matches[0][$match][0], $close, $end );

				// sanity ##
				if ( 
					! isset( $partial ) 
					// || ! strstr( $partial, '__' )
					// || ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// clean up ##
				$partial = trim($partial);
				$context = 'partial';
				$task = $partial;
				// list( $context, $task ) = explode( '__', $partial );

				// test what we have ##
				// h::log( 'd:>context: "'.$context.'"' );
				// h::log( 'd:>task: "'.$task.'"' );

				// hash ##
				// $hash = bin2hex( random_bytes(16) );
				$hash = 'partial__'.\mt_rand();

				// @todo -- currently only partials are handled... ##
				switch( $context ) {

					case 'partial' :

						// so, we can add a new field value to $args array based on the field name - with the markup as value
						render\fields::define([
							// $function => render::{$function}()
							$hash => core\config::get([ 'context' => $context, 'task' => $task ])
						]);

					break ;

					default :

						h::log( 'e:>Currently, only partial partials are supported' );

					break ;

				}


				// and now we need to add a placeholder "{{ $field }}" before this block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}




	/**
	 * Scan for comments in markup and convert to placeholders and $fields and also to error log ##
	 * 
	 * @since 4.1.0
	*/
	public static function comment(){

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

		// get all comments, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tag::g( 'com_o' ) );
		$close = trim( tag::g( 'com_c' ) );

		// h::log( 'open: '.$open. ' - close: '.$close );

		$regex_find = \apply_filters( 
			'q/render/markup/comment/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/comment/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
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

				// h::log( 'd:>Searching for comments data...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 
				
				// get a single comment ##
				$comment = method::string_between( $matches[0][$match][0], $open, $close );

				// sanity ##
				if ( 
					! isset( $comment ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// clean up ##
				$comment = trim($comment);

				// test what we have ##
				// h::log( 'd:>comment: "'.$comment.'"' );

				// hash ##
				// $hash = bin2hex( random_bytes(16) );
				$hash = 'comment__'.\mt_rand();

				// so, we can add a new field value to $args array based on the field name - with the comment as value
				render\fields::define([
					$hash => '<!-- '.$comment.' -->'
				]);
				
				// also, add a log entry ##
				h::log( 'd:>'.$comment );

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	/**
	 * Scan for arguments in variables and convert to $data
	 * 
	 * @since 4.1.0
	*/
	public static function argument(){

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

		// get all variable placeholders from markup string ##
        if ( 
            ! $placeholders = placeholder::get( $string, 'variable' ) 
        ) {

			// h::log( self::$args['task'].'~>d:>No placeholders found in $markup');
			// h::log( 'd:>No placeholders found in $markup: '.self::$args['task']);

			return false;

		}

		// log ##
		h::log( self::$args['task'].'~>d:>"'.count( $placeholders ) .'" placeholders found in string');
		// h::log( 'd:>"'.count( $placeholders ) .'" placeholders found in string');

		// remove any leftover placeholders in string ##
		foreach( $placeholders as $key => $value ) {

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

				// store placeholder ##
				$placeholder = $value;

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

					h::log( self::$args['task'].'~>e:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s
					// h::log( 'd:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s''

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

					h::log( self::$args['task'].'~>e:>Error extracting $field_name or $field_type from placeholder: '.$placeholder );

					continue;

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_placeholder value
				// $placeholder = $value;
				// $new_placeholder = explode( '(', $placeholder )[0].' }}';
				// $new_placeholder = '{{ '.$field.' }}';
				$new_placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);

				// test what we have ##
				// h::log( "d:>placeholder: ".$value );
				// h::log( "d:>new_placeholder: ".$new_placeholder);
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

				// now, edit the placeholder, to remove the config ##
				placeholder::edit( $placeholder, $new_placeholder );

			}
		
        }

	}


}

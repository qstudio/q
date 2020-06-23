<?php

namespace q\core;

use q\core;
use q\render;
use q\core\helper as h;

// run ##
core\log::run();

class log extends \Q {

	// track who called what ##
	public static 
		$file				= \WP_CONTENT_DIR."/debug.log",
		$empty 				= false, // track emptied ##
		$backtrace 			= false,
		$backtrace_key 		= false,
		$delimiters 		= [
			'array' 		=> '~>',
			'value' 		=> ':>'
		],
		$special_keys 		= [
			'd' 			=> 'debug', // shown by default
			'e' 			=> 'error',
			'n' 			=> 'notice',
			// 'l' 			=> 'log',
			't'				=> 'todo'
		],
		$key_array 			= [],
		$on_run 			= true,
		$on_shutdown 		= true,
		$shutdown_key 		= [ 'error' ], // control log keys ##
		$shutdown_key_debug = [ 'debug', 'todo' ] // control debug keys ##
	;


	public static function run(){

		// filter pre-defined actions ##
		$on_run 			= \apply_filters( 'q/core/log/on_run', self::$on_run );
		$on_shutdown 		= \apply_filters( 'q/core/log/on_shutdown', self::$on_shutdown );

		// on_run set to true ##
		if ( $on_run ) {

			// earliest possible action.. empty log ##
			self::empty();  

			// also, pre-ajax ##
			if( 
				defined('DOING_AJAX') 
				&& DOING_AJAX
			) {

				// core\helper::debug( 'DOING AJAX...' );
				self::empty();

			}

		}

		if ( $on_shutdown ) {

			// latest possible action, write to error_log ##
			register_shutdown_function( [ get_class(), 'shutdown' ] );

		}

	}



	private static function get_backtrace( $args = null ){

		// called directly, else called from h::log() ##
		// $level_function = apply_filters( 'q/core/log/traceback/function', 3 );
		// $level_file = apply_filters( 'q/core/log/traceback/file', 2 );

		$backtrace_1 = core\method::backtrace([ 
			'level' 	=> \apply_filters( 'q/core/log/traceback/function', 3 ), 
			'return' 	=> 'class_function' 
		]);

		// format for key usage ##
		$backtrace_1 = strtolower( str_replace( [ '()' ], [ '' ], $backtrace_1 ) );
			
		$backtrace_2 = core\method::backtrace([ 
			'level' 	=> \apply_filters( 'q/core/log/traceback/file', 2 ), 
			'return' 	=> 'file_line' 
		]);

		// self::$backtrace = ' -> '.$backtrace_1.' - '.$backtrace_2;
		self::$backtrace = ' -> '.$backtrace_2;
		self::$backtrace_key = $backtrace_1;
		// core\helper::debug( $backtrace );
		// $log = $log.' - '.$backtrace;

	}



	/**
     * Store logs, to render at end of process
     * 
     */
    public static function set( $args = null ){

		// test ##
		// core\helper::debug( $args );

		// core\helper::debug( 'd:>Problem with passed args' );

		// add info about calling function ##
		self::get_backtrace( $args );
		
		// sanity ##
		if (
			! isset( $args )
			|| is_null( $args )
			// || ! isset( $args['log'] )
		){

			// core\helper::debug( 'd:>Problem with passed args' );

			return false;

		}

		// translate passed log
		// check we have what we need to set a new log point ##
		if ( 
			! $log = self::translate( $args )
		){

			// core\helper::debug( 'Error in passed log data..' );

			return false;

		}

		// kick back ##
		return true;

	}



	/**
	 * Hardcore way to directly set a log key and value.. no safety here..
	*/
	public static function set_to( $key = null, $value = null ){

		// sanity @todo ##
		self::$log[$key] = $value;

	}
	
	

	/**
     * Translate Log Message
	 * 
	 * Possible formats
	 * - array, object, int - direct dump
	 * - string - "hello"
	 * - $log[] = value - "l:>hello"
     * - $notice[] = value - "n:>hello"
	 * - $error[] = value - "e:>hello"
	 * - $group[] = value - "group:>hello"
	 * - $key[$key][$key] = value - "n~>group~>-problem:>this is a string"
     */
    private static function translate( $args = null ){

		// arrays and objects are dumped directly ##
		if ( 
			// is_array( $args )
			// || is_object( $args )
			is_int( $args )
			|| is_numeric( $args )
		){

			// core\helper::debug( 'is_array OR is_object or is_int' );
			// return self::$log['log'][] = var_export( $args, true ).self::$backtrace;
			return self::push( 'debug', var_export( $args, true ).self::$backtrace, self::$backtrace_key );
			
		}

		// arrays and objects are dumped directly ##
		if ( 
			is_array( $args )
			|| is_object( $args )
			// || is_int( $args )
			// || is_numeric( $args )
		){

			// core\helper::debug( 'is_array OR is_object or is_int' );
			// return self::$log['log'][] = var_export( $args, true ).self::$backtrace;
			self::push( 'debug', 'Array or Object below from -> '.self::$backtrace, self::$backtrace_key );
			return self::push( 'debug', var_export( $args, true ), self::$backtrace_key );
			
		}

		// bool ##
		if (
			is_bool( $args )
		){

			// core\helper::debug( 'is_bool' );
			// return self::$log['log'][] = ( true === $args ? 'boolean:true' : 'boolean:false' ).self::$backtrace ;
			return self::push( 'debug', ( true === $args ? 'boolean:true' : 'boolean:false' ).self::$backtrace, self::$backtrace_key );

		}

		// filter delimters ##
		self::$delimiters = \apply_filters( 'q/core/log/delimit', self::$delimiters );

		// string ##
		if ( 
			is_string( $args ) 
		) {

			// core\helper::debug( 'is_string' );

			// string might be a normal string, or contain markdown to represent an array of data ##

			// no fixed pattern ##
			if ( ! core\method::strposa( $args, self::$delimiters ) ) {

				// core\helper::debug( 'string has no known delimit, so treat as log:>value' );
				// return $args['log'][] = $args.self::$backtrace; 
				return self::push( 'debug', $args.self::$backtrace, self::$backtrace_key );

			}

			// string ##
			if ( 
				false === strpos( $args, self::$delimiters['array'] ) 
				&& false !== strpos( $args, self::$delimiters['value'] ) 
			) {
			
				// core\helper::debug( 'only key:value delimiters found..' );

				// get some ##
				$key_value = explode( self::$delimiters['value'], $args );
				// core\helper::debug( $key_value );

				$key = $key_value[0];
				$value = $key_value[1];

				// core\helper::debug( "d:>key: $key + value: $value" );

				// return with special key replacement check ##
				return self::push( self::key_replace( $key ), $value.self::$backtrace, self::$backtrace_key );

			}

			// array ##
			if ( 
				false !== strpos( $args, self::$delimiters['array'] ) 
				&& false !== strpos( $args, self::$delimiters['value'] ) 
			) {
			
				// core\helper::debug( 'both array and value delimiters found..' );

				// get some ##
				$array_key_value = explode( self::$delimiters['value'], $args );
				// core\helper::debug( $array_key_value );

				$value_keys = $array_key_value[0];
				$value = $array_key_value[1];

				$keys = explode( self::$delimiters['array'], $value_keys );
				
				// core\helper::debug( $keys );
				// core\helper::debug( "l:>$value" );

				return self::push( $keys, $value.self::$backtrace, self::$backtrace_key );

			}

		}

        return false;

	}
	


	/**
	 * Push item into the array, checking if selected key already exists 
	 */
	private static function push( $key = null, $value = null, $new_key = '' ){

		// @todo - sanity ##

		// grab reference of self::$log ##
		$log = self::$log;

		// check if $key already exists ##
		if ( 
			is_string( $key )
			|| (
				is_array( $key )
				&& 1 == count( $key )
			)
		){

			// take first array value, if array, else key.. ##
			$key = is_array( $key ) ? $key[0] : $key ;

			// h::debug( $log );

			if ( 
				! isset( $log[$key] )
				// && empty( $log[$key] )
				// && ! $log[$key]
				// && ! is_array( $log[$key] )
				// && array_filter( $log[$key] ) == []
			){

				$log[$key] = [];
				// h::debug( 'd:> create new empty array for "'.$key.'"' );

			}

			// new key is based on the class_method called when the log was set ##
			// this key might already exist, if so, add as a new array key + value ##
			$new_key = isset( $new_key ) ? $new_key : self::get_acronym( $value ) ;

			// make new key safe ??
			// $new_key = str_replace( [ '\\','::' ], '_', $new_key );

			// new key ??
			// h::debug( 'd:>new_key: "'.$new_key.'"' );

			// key already exists ##
			if ( 
				! isset( $log[$key][$new_key] ) 
				// && empty( $log[$key][$new_key] )
				// && ! $log[$key][$new_key]
				// && ! is_array( $log[$key] )
				// && array_filter( $log[$key][$new_key] ) == []
			){

				// h::debug( 'd:>create new empty array in: "'.$key.'->'.$new_key.'"' );

				// create new key as empty array ##
				$log[$key][$new_key] = [];

			}

			// check if the value has been added already ##
			if ( in_array( $value, $log[$key][$new_key], true ) ) {

				// h::debug( 'd:> "'.$key.'->'.$new_key.'" value already exists, so skip' );

				return false;

			}

			// h::debug( 'd:> add value to: '.$key.'->'.$new_key.'"' );

			// add value to array ##
			$log[$key][$new_key][] = $value;

			// kick back ##
			return self::$log = $log;

		}

		if(
			is_array( $key )
			&& count( $key ) > 1
		){

			/*
			// old way, with fancy loop ##
			// create array
			self::$key_array = 
				array_merge_recursive ( 
					self::create_multidimensional_array( [], $key, $value ), 
					self::$key_array 
				);
			// h::log( self::$key_array );

			// merge created keys with log ##
			return self::$log = array_merge( $log, self::$key_array );
			*/

			//


			// $key_array = [];

			// we only go up to 3 levels.. oddly, so give a warning ##
			// if ( count( $key ) > 3 ) { core\helper::debug( 'The max key depth is 3..' ); }

			// special keys ##
			// foreach( $key as $k => $v ) {

			// 	// check for special keys ##
			// 	if ( 1 == strlen( $v ) ) {

			// 		$key[$k] = self::key_replace( $v );

			// 	}

			// }	

			// h::log( $log );

			
			// self::$log = array_merge( self::$log, self::$key_array );

			// self::$log = array_merge_recursive( self::$key_array, self::$log );

			// duplicates gone.. hacky ##
			// self::$log = self::array_unique_multidimensional( self::$log );

			// return self::$log = array_merge_recursive( self::$key_array, self::$log );
			// h::log( self::$log );

			// self::$log = $merge;

			// check if key exists ##

			// manually ## 
			if (
				isset( $key[2] )
			){
				if ( ! isset( self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ][ self::key_replace($key[2]) ] ) ) {
					
					self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ][ self::key_replace($key[2]) ] = [];
				
				}

				// value exists ##
				if ( 
					in_array( $value, self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ][ self::key_replace($key[2]) ], true ) 
				){ 
					return false;
				};

				// add value and return ##
				return self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ][ self::key_replace($key[2]) ][] = $value;

			}

			if (
				isset( $key[1] )
			){

				if ( ! isset( self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ] ) ) {

					self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ] = [];

				}

				// value exists ##
				if ( 
					in_array( $value, self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ], true ) 
				){ 
					return false;
				};

				// add value and return ##
				return self::$log[ self::key_replace($key[0]) ][ self::key_replace($key[1]) ][] = $value;

			}

			if (
				isset( $key[0] )
			){
				if ( ! isset( self::$log[self::key_replace($key[0])] ) ) {

					self::$log[ self::key_replace($key[0]) ] = [];

				}

				// value exists ##
				if ( 
					in_array( $value, self::$log[ self::key_replace($key[0]) ], true ) 
				){ 
					return false;
				};

				// add value and return ##
				return self::$log[ self::key_replace($key[0]) ][] = $value;

			}
			
			/*
			$md_array = [];
			$md_array = self::create_multidimensional_array( self::$log, $key, $value ) ;
			// h::log( $md_array );
			// h::log( self::$log );

			// merge array into self::$log ##
			// self::$log = core\method::parse_args( $md_array, self::$log );
			$merge = array_replace_recursive( $md_array, self::$log );
			h::log( $merge );

			self::$log = $merge
			*/

		}

		// negative #
		return false;

	}

	

	public static function get_acronym( $string = null, $length = 10 ) {

		// sanity ##
		if ( is_null( $string ) ) { return false; }

		return 
			render\method::chop( 
				str_replace(
					[ '-', '_' ], "", // replace ##
					strtolower( 
						array_reduce( 
							str_word_count( $string, 1), function($res , $w){ 
								return $res . $w[0]; 
							} 
						)
					)
				),
				$length, '' // chop ##
			);

	}



	public static function in_multidimensional_array( $needle, $haystack ) {

		foreach( $haystack as $key => $value ) {

		   $current_key = $key;

		   if( 
			   $needle === $value 
			   || ( 
				   is_array( $value ) 
				   && self::in_multidimensional_array( $needle, $value ) !== false 
				)
			) {

				return $current_key;

			}
		}

		return false;

	}

	

	/**
	 * Create Multidimensional array from keys ##
	 * 
	 * @link 	https://eval.in/828697 
	 */
	public static function create_multidimensional_array( $array = [], $keys, $value ){    

		$tmp_array = &$array;

		while( count( $keys ) > 0 ){     

			$k = array_shift( $keys );     

			if( ! is_array( $tmp_array ) ) {

				$tmp_array = [];

			}
			$tmp_array = &$tmp_array[self::key_replace( $k )];

		}

		$tmp_array = $value;

		return $array;

	}



	/**
	 * Special Key replacement 
	 *
	 * - e = error
	 * - n = notice
	 * - l = log ( default ) 
	 */
	private static function key_replace( $key = null ){
		
		// @todo -- sanity ##

		// filter special keys ##
		self::$special_keys = \apply_filters( 'q/core/log/special_keys', self::$special_keys );

		// lookfor key
		if ( 
			isset( self::$special_keys[$key] )
		){

			// core\helper::debug( "key is special: $key" );
			return self::$special_keys[$key];

		}

		// core\helper::debug( "key is NOT special: $key" );
		return $key;

	}


		
    /**
     * Logging function
     * 
     */
    public static function write( $key = null ){

		// test ##
		// self::set( 'write: '.$key );
		// core\helper::debug( self::$log );

		// if key set, check if exists, else bale ##
		if ( 
			! is_null( $key )
			&& ! isset( self::$log[ $key ] ) 
		) {

			// self::set( '"'.$key.'" Log is empty.' );

			return false;

		}

		// option to debug only specific key ##
		if ( isset( $key ) ) {
			
			$return = self::$log[ $key ];  // key group ##

			// empty log key ##
			unset( self::$log[ $key ] );

        } else {

			$return = self::$log ; // all

			// empty log ##
			// unset( self::$log );
			self::$log = [];

		}
			
		// create named array key, based on passed key, so easier to read log ##
		if ( ! is_null( $key ) ) { $return = [ $key => $return ]; }

		// keys are added sequentially, so we need to reverse to see the actual flow ##
		if ( is_array( $return ) ) { $return = array_reverse( $return ); }

		// clean up ##
		// $return = self::array_unique_multidimensional( $return );

		// debugging is on in WP, so write to error_log ##
        if ( true === WP_DEBUG ) {

			if ( 
				is_array( $return ) 
				|| is_object( $return ) 
			) {
				// error_log( print_r( $return, true ) );
				self::error_log( print_r( $return, true ) );
            } else {
				// error_log( $return );
				// trigger_error( $return );
				self::error_log( $return );
            }

		}

		// done ##
		return true;

	}


	public static function array_unique_multidimensional($input)
	{

		$serialized = array_map('serialize', $input);

		$unique = array_unique($serialized);

		return array_intersect_key($input, $unique);
	
	}



	/**
	 * Replacement error_log function, with custom return format 
	 * 
	 * @since 4.1.0
	 */ 
	public static function error_log( $log )
	{
		
		// $displayErrors 	= ini_get( 'display_errors' );
		$log_errors     = ini_get( 'log_errors' );
		$error_log      = ini_get( 'error_log' );

		// if( $displayErrors ) echo $errStr.PHP_EOL;

		if( $log_errors )
		{
			$message = sprintf( 
				// '[%s] %s (%s, %s)', 
				'%s', 
				// date('d-m H:i'), 
				// date('H:i'), 
				$log, 
				// $errFile, 
				// $errLine 
			);
			// file_put_contents( $error_log, $message.PHP_EOL, FILE_APPEND );
			file_put_contents( self::$file, $message.PHP_EOL, FILE_APPEND );
		}

		// ok ##
		return true;

	}



	/**
     * Clear Temp Log
     * 
     */
    private static function clear( $args = null ){

		// test ##
        // core\helper::debug( $args );

		// sanity ##
		// ...

		// if key set, check if exists, else bale ##
		if ( 
			isset( $args['key'] )
			&& ! isset( self::$log[ $args['key'] ] ) 
		) {

			core\helper::debug( 'Log key empty: "'.$args['key'].'"' );

			return false;

		}

		// core\helper::debug( self::$log );

        // option to debug only specific fields ##
        if ( isset( $args['key'] ) ) {

			unset( self::$log[ $args['key'] ] );

			core\helper::debug( 'n>Emptied log key: "'.$args['key'].'"' );

			return true;

		}

		unset( self::$log );

		core\helper::debug( 'n>Emptied all log keys' );
		
		return true;

	}
	


	/**
     * Empty Log
     * 
     */
    private static function empty( $args = null ){

		// empty once -- commented out.. ##
		if( self::$empty ) { return false; }

		// $f = @fopen( WP_CONTENT_DIR."/debug.log", "r+" );
		$f = @fopen( self::$file, "r+" );
		if ( $f !== false ) {
			
			ftruncate($f, 0);
			fclose($f);

			// log to log ##
			// core\helper::debug( 'Log Emptied: '.date('l jS \of F Y h:i:s A') );

			// track ##
			self::$empty == true;

		}

	}
	


	private static function php_error(){

		// h::debug( 'fatal...' );

		$error = error_get_last();

		if(
			$error !== NULL 
		){

			self::set_to( 'php', self::php_error_decode( $error['type'], $error['message'], $error['file'], $error['line'] ) );

			self::write();

			return true;

		}

		return false;
		
    }



	private static function php_error_decode( $errno, $errstr, $errfile, $errline ) {

        switch ($errno){

            case E_ERROR: // 1 //
                $typestr = 'E_ERROR'; break;
            case E_WARNING: // 2 //
                $typestr = 'E_WARNING'; break;
            case E_PARSE: // 4 //
                $typestr = 'E_PARSE'; break;
            case E_NOTICE: // 8 //
                $typestr = 'E_NOTICE'; break;
            case E_CORE_ERROR: // 16 //
                $typestr = 'E_CORE_ERROR'; break;
            case E_CORE_WARNING: // 32 //
                $typestr = 'E_CORE_WARNING'; break;
            case E_COMPILE_ERROR: // 64 //
                $typestr = 'E_COMPILE_ERROR'; break;
            case E_CORE_WARNING: // 128 //
                $typestr = 'E_COMPILE_WARNING'; break;
            case E_USER_ERROR: // 256 //
                $typestr = 'E_USER_ERROR'; break;
            case E_USER_WARNING: // 512 //
                $typestr = 'E_USER_WARNING'; break;
            case E_USER_NOTICE: // 1024 //
                $typestr = 'E_USER_NOTICE'; break;
            case E_STRICT: // 2048 //
                $typestr = 'E_STRICT'; break;
            case E_RECOVERABLE_ERROR: // 4096 //
                $typestr = 'E_RECOVERABLE_ERROR'; break;
            case E_DEPRECATED: // 8192 //
                $typestr = 'E_DEPRECATED'; break;
            case E_USER_DEPRECATED: // 16384 //
                $typestr = 'E_USER_DEPRECATED'; break;
        }

        $message =
            $typestr .
            ': ' . $errstr .
            ' in ' . $errfile .
            ' on line ' . $errline .
            PHP_EOL;

        // if(($errno & E_FATAL) && ENV === 'production'){

        //     header('Location: 500.html');
        //     header('Status: 500 Internal Server Error');

        // }

        // if ( 
		// 	! ( $errno && ERROR_REPORTING ) 
		// ){
		
		// 	return;
			
		// }

    	return sprintf( '%s', $message );

    }
	

	/**
     * Shutdown method
     * 
     */
    public static function shutdown(){

		// empty log ##
		// self::empty();

		// check for fatal error ##
		// self::php_error();

		// filter what to write to log - defaults to "error" key ##
		$key = \apply_filters( 'q/core/log/default', self::$shutdown_key );
		$key_debug = \apply_filters( 'q/core/log/debug', self::$shutdown_key_debug );

		// core\helper::debug( $key );
		// core\helper::debug( $key_debug );

		// write specific key, as filter might return false / null ##
		if( 
			! $key 
			|| is_null( $key ) 
			|| empty( $key ) 
			// || ! isset( self::$log[ $key ] )
		){

			core\helper::debug( 'd:>shutdown -- no key, so write all..' );

			// log all ##
			return self::write();

		}

		$log = [];
		
		// log key ##
		foreach( ( array )$key as $k => $v ) {

			// h::debug( 'd:>key is: '.$v );
			
			// skip missing keys ##
			if ( ! isset( self::$log[$v] ) ) { continue; }

			// log specific key ##
			// self::write( $v );
			$log[$v] = self::$log[$v];

		}

		// debugging so log more keys... ##
		if ( 
			\Q::$debug // debugging ##
			&& $key_debug
		) {

			foreach( ( array )$key_debug as $k => $v ) {

				// skip keys already written above ##
				if ( is_array( $key ) && array_key_exists( $v, $key ) ) { continue; }

				// skip missing keys ##
				if ( ! isset( self::$log[$v] ) ) { continue; }

				// h::debug( 'd:>debug key is: '.$v );

				// log specific key ##
				// self::write( $v );
				$log[$v] = self::$log[$v];

			}

		}

		// assign to new key ##
		self::$log['shutdown'] = $log;

		// write new key to log ##
		self::write( 'shutdown' );

		// done ##
		return true;

	}

}

<?php

namespace q\core;

use q\core;
// use q\core\helper as h;

// run ##
core\log::run();

class log extends \Q {

	// track who called what ##
	public static 
		$backtrace 		= false,
		$delimit 	= [
			'array' 	=> '~>',
			'value' 	=> ':>'
		],
		$special_keys = [
			'e' 		=> 'error',
			'n' 		=> 'notice',
			'l' 		=> 'log'
		]
	;


	public static function run(){

		// earliest possible action.. empty log
		self::empty();

		// latest possible action, write to error_log ##
		register_shutdown_function( [ get_class(), 'shutdown' ] );

	}



	private static function get_backtrace(){

		$backtrace_1 = core\method::backtrace([ 'level' => 3, 'return' => 'class_function' ]);
		$backtrace_2 = core\method::backtrace([ 'level' => 2, 'return' => 'file_line' ]);

		self::$backtrace = ' -> '.$backtrace_1.' - '.$backtrace_2;
		// self::set( $backtrace );
		// $log = $log.' - '.$backtrace;

	}



	/**
     * Store logs, to render at end of process
     * 
     */
    public static function set( $args = null ){

		// test ##
		// self::set( $args );

		// add info about calling function ##
		self::get_backtrace();
		
		// sanity ##
		if (
			! isset( $args )
			|| is_null( $args )
			// || ! isset( $args['log'] )
		){

			self::set( 'n:Problem with passed args' );

			return false;

		}

		// translate pass log
		// check we have what we need to set a new log point ##
		if ( 
			! $log  = self::translate( $args )
		){

			// self::set( 'Error in passed log data..' );

			return false;

		}

		// kick back ##
		return true;

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
			is_array( $args )
			|| is_object( $args )
			|| is_int( $args )
			|| is_numeric( $args )
		){

			// self::set( 'n:>is_array OR is_object or is_int' );
			// return self::$log['log'][] = var_export( $args, true ).self::$backtrace;
			return self::push( 'log', var_export( $args, true ).self::$backtrace );
			
		}

		// bool ##
		if (
			is_bool( $args )
		){

			// self::set( 'n:>is_bool' );
			// return self::$log['log'][] = ( true === $args ? 'boolean:true' : 'boolean:false' ).self::$backtrace ;
			return self::push( 'log', ( true === $args ? 'boolean:true' : 'boolean:false' ).self::$backtrace );

		}

		// string ##
		if ( 
			is_string( $args ) 
		) {

			// self::set( 'n:>is_string' );

			// string might be a normal string, or contain markdown to represent an array of data ##
			// check for fixed pattern 
			if ( ! core\method::strposa( $args, self::$delimit ) ) {

				// self::set( 'n:>string has no known delimit, so treat as log:>value' );
				// return $args['log'][] = $args.self::$backtrace; 
				return self::push( 'log', $args.self::$backtrace );

			}

			// string ##
			if ( 
				false === strpos( $args, self::$delimit['array'] ) 
				&& false !== strpos( $args, self::$delimit['value'] ) 
			) {
			
				// self::set( 'only key:value delimiters found..' );

				// get some ##
				$key_value = explode( self::$delimit['value'], $args );
				// self::set( $key_value );

				$key = $key_value[0];
				$value = $key_value[1];

				// self::set( "l:>key: $key + value: $value" );

				// return with special key replacement check ##
				return self::push( self::key_replace( $key ), $value.self::$backtrace );

			}

			// array ##
			if ( 
				false !== strpos( $args, self::$delimit['array'] ) 
				&& false !== strpos( $args, self::$delimit['value'] ) 
			) {
			
				// self::set( 'both array and value delimiters found..' );

				// get some ##
				$array_key_value = explode( self::$delimit['value'], $args );
				// self::set( $array_key_value );

				$value_keys = $array_key_value[0];
				$value = $array_key_value[1];

				$keys = explode( self::$delimit['array'], $value_keys );
				
				// self::set( $keys );
				// self::set( "l:>$value" );

				return self::push( $keys, $value.self::$backtrace );

			}

		}

        return false;

	}
	


	/**
	 * Push item into the array, checking if selected key already exists 
	 */
	private static function push( $key = null, $value = null ){

		// @todo - sanity ##

		// @todo -- how to add deep keys self::$log[$key][$key] = value ##
		// pass key as an array ??
		// loop over each key, chec if set.. if not set ###
		// then add value ##

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
		
			if ( 
				! isset( self::$log[$key] )
			){

				self::$log[$key] = [];
				// self::set( "n:>create new empty array for '{$key}'" );

				return self::$log[$key][] = $value;
				// self::set( "n:>added {value} to '{$key}'" );

			} else {

				// else, add ##
				return self::$log[$key][] = $value;

			}

		}

		if(
			is_array( $key )
			&& count( $key ) > 1
		){

			// self::set( 'Dealing with multi-dimensional array keys' );
			// self::set( $key );

			$md_array = [];
			$md_array = self::create_multidimensional_array( $md_array, $key, $value ) ;
			// self::set( $md_array );

			// merge array into self::$log ##
			self::$log = core\method::parse_args( $md_array, self::$log );

		}

		// negative #
		return false;

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

		// lookfor key
		if ( 
			isset( self::$special_keys[$key] )
		){

			// self::set( "n:>key is special: $key" );
			return self::$special_keys[$key];

		}

		// self::set( "n:>key is NOT special: $key" );
		return $key;

	}



	/**
	 * Create Multidimensional array from keys ##
	 * 
	 * @link 	https://eval.in/828697 
	 * 
	 * $keys = array("aggs","name","aggs","name","aggs");
	 * $aggs = add_keys_dynamic( [],$keys,"test");
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
     * Logging function
     * 
     */
    private static function write( $key = null ){

		// test ##
        // self::set( self::$log );

		// sanity ##
		// ...

		// if key set, check if exists, else bale ##
		if ( 
			isset( $key )
			&& ! isset( self::$log[ $key ] ) 
		) {

			self::set( 'Log key empty: "'.$key.'"' );

			return false;

		}

		// self::set( self::$log );

        // option to debug only specific key ##
        $return = 
            isset( $key ) ?
            self::$log[ $key ] : // key group ##
            self::$log ; // all

        // log to log ##
		// self::set( $return );

		// debugging is on in WP, so write to error_log ##
        if ( true === WP_DEBUG ) {

			if ( 
				is_array( $return ) 
				|| is_object( $return ) 
			) {
                error_log( print_r( $return, true ) );
            } else {
                error_log( $return );
            }

		}
		
		// done ##
		return true;

	}

	

	/**
     * Clear Temp Log
     * 
     */
    private static function clear( $args = null ){

		// test ##
        // self::set( $args );

		// sanity ##
		// ...

		// if key set, check if exists, else bale ##
		if ( 
			isset( $args['key'] )
			&& ! isset( self::$log[ $args['key'] ] ) 
		) {

			self::set( 'n:>Log key empty: "'.$args['key'].'"' );

			return false;

		}

		// self::set( self::$log );

        // option to debug only specific fields ##
        if ( isset( $args['key'] ) ) {

			unset( self::$log[ $args['key'] ] );

			self::set( 'n>Emptied log key: "'.$args['key'].'"' );

			return true;

		}

		unset( self::$log );

		self::set( 'n>Emptied all log keys' );
		
		return true;

	}
	


	/**
     * Empty Log
     * 
     */
    private static function empty( $args = null ){

        $f = @fopen( WP_CONTENT_DIR."/debug.log", "r+" );
		if ( $f !== false ) {
			
			ftruncate($f, 0);
			fclose($f);

			// log to log ##
			self::set( 'n:>Log Emptied: '.date('l jS \of F Y h:i:s A') );

		}

    }

	

	/**
     * Shutdown method
     * 
     */
    public static function shutdown(){

		// filter what to write to log - defaults to "error" key ##
		$key = \apply_filters( 'q/core/log/default', 'notice' );
		// $key = \apply_filters( 'q/core/log/default', null );

		// write specific key, as filter might return false / null ##
		if( 
			! $key 
			|| is_null( $key ) 
			|| ! isset( self::$log[ $key ] )
		){

			self::set( 'write all..' );

			// log all ##
			return self::write();

		}

		// log specific key ##
		return self::write( $key );

	}


}
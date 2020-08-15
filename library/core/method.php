<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\render;
// use q\theme;

class method extends \Q {


    /**
     * Request data safely using $_GET, $_POST & $_REQUEST
     * 
     * @since   0.1
     * @param   string      $key:           Key to search for
     * @param   string      $sanitize       Sanatize method to apply to the data
     * @param   Boolean     $debug          Allows for individual debugging of keys
     * @return  mixed       boolean | string         
     */
    public static function request_safe( $key = null, $sanitize = null, $debug = false, $methods = array( 'post' => true ) )
    {
        
        // quick check ##
        if ( ! $key ) { return false; }
        
        // debugging on - so allow broader range of request methods ##
        if ( self::$debug ) {
            
            $methods['get'] = true; // allow $_GET method ##
            #$methods['request'] = true; // allow $_REQUEST method ##
            
        }
        
        // check for key in allowed superglobals ##
        foreach( $methods as $method => $value ) {
            
            if ( $value === true ) { // method allowed ##
                
                switch ( $method ) {
                    
                    case 'get':
                        
                        if ( isset( $_GET[ $key ] ) ) {  
                            
                            // if ( $debug === true ) { pr($_GET[ $key ]); } // debug ##
                            
                            return self::sanitize( $_GET[ $key ], \sanitize_text_field ( $sanitize ) );
                            
                        }
                            
                        break;
                    
                    case 'post':
                    default:
                        
                        if ( isset( $_POST[ $key ] ) ) {  
                            
                            // if ( $debug === true ) { wp_die($_POST[ $key ]); } // debug ##
                            
                            return self::sanitize( $_POST[ $key ], \sanitize_text_field ( $sanitize ) );
                            
                        }
                        
                        break;
                    
                }
                
            }
            
        }
        
        // nothing happening ##
        return false;
        
    }
    
    
    /**
     * Sanitize user input data using WordPress functions
     * 
     * @since       0.1
     * @param       string      $value      Value to sanitize
     * @param       string      $type       Type of value ( email, user, int, key, text[default] )
     * @link        http://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
     * @link        http://wp.tutsplus.com/tutorials/creative-coding/data-sanitization-and-validation-with-wordpress/
     * @return      string      HTML output
     */
    public static function sanitize( $value = null, $type = 'text' )
    {
        
        // check submitted data ##
        if ( is_null( $value ) ) {
            
            return false;
            
        }
        
        switch ( $type ) {
            
            case( 'email' ):
            
                return \sanitize_email( $value );
                break;
            
            case( 'user' ):
            
                return \sanitize_user( $value );
                break;
            
            case( 'integer' ):
            
                return intval( $value );
                break;
            
            case( 'filename' ):
            
                return \sanitize_file_name( $value );
                break;
            
            case( 'key' ):
            
                return self::sanitize_key( $value ); // altered version of wp sanitize_key
                break;
			
			case( 'php_class' ):

				return self::php_class( $value );
				break;

			case( 'php_namespace' ):

				return self::php_namespace( $value );
				break;

			case( 'php_function' ):

				return self::php_function( $value );
				break;

            case( 'sql' ):
                
                return \esc_sql( $value );
                break;
            
            case( 'stripslashes' ):
                
                return preg_replace("~\\\\+([\"\'\\x00\\\\])~", "$1", $value);
                #stripslashes( $value );
                break;
            
            case( 'none' ):
                
                return $value;
                break;
            
            case( 'text' ):
            default;
                    
                // text validation
                return \sanitize_text_field( $value );
                break;
                
        }
        
	}


	/**
    * Sanitizes a php namespace
    *
    * @since 1.3.0
    * @param string $key String key
    * @return string Sanitized key
    */
    public static function php_namespace( $key = null ) 
    {
        
        // sanity check ##
        if ( ! $key ) { return false; }
        
        // scan the key for allowed characters ##
        $key = preg_replace( '^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*(\\\\[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)*$', '', $key );
        
        // return the key ##
        return $key;
        
	}


	
	/**
    * Sanitizes a php function name
    *
    * @since 1.3.0
    * @param string $key String key
    * @return string Sanitized key
    */
    public static function php_function( $key = null ) 
    {
        
        // sanity check ##
        if ( ! $key ) { return false; }
        
        // scan the key for allowed characters ##
        $key = preg_replace( '/[^A-Za-z0-9-_]+/', '', $key );
        
        // return the key ##
        return $key;
        
	}



    /**
    * Sanitizes a php class name
    *
    * @since 1.3.0
    * @param string $key String key
    * @return string Sanitized key
    */
    public static function php_class( $key = null ) 
    {
        
        // sanity check ##
        if ( ! $key ) { return false; }
        
        // scan the key for allowed characters ##
        $key = preg_replace( '/[^A-Za-z0-9-\\\\_]+/', '', $key );
        
        // return the key ##
        return $key;
        
	}

    
    
    /**
    * Sanitizes a string key.
    *
    * @since 1.3.0
    * @param string $key String key
    * @return string Sanitized key
    */
    public static function sanitize_key( $key = null ) 
    {
        
        // sanity check ##
        if ( ! $key ) { return false; }
        
        // scan the key for allowed characters ##
        $key = preg_replace( '/[^a-zA-Z0-9_\-~!$^+]/', '', $key );
        
        // return the key ##
        return $key;
        
	}
	
	


	/**
	 * Debug Calling class + method / function 
	 * 
	 * @since 	4.0.0
	 */
	public static function backtrace( $args = null ) {

		// default args ##
		$level = isset( $args['level'] ) ? $args['level'] : 1 ; // direct caller ##

		// check we have a result ##
		$backtrace = debug_backtrace();

		if (
			! isset( $backtrace[$level] )
			// || ! isset( $backtrace[$level]['class'] )
			// || ! isset( $backtrace[$level]['function'] )
		) {

			return false;

		}

		// get defined level of data ##
		$caller = $backtrace[$level];

		// class::function() ##
		if ( 
			isset( $args['return'] ) 
			&& 'class_function' == $args['return'] 
			// && isset( $caller['class'] )
			// && isset( $caller['function'] )
		) {

			return sprintf(
				__( '%s%s()', 'Q' )
				,  	isset($caller['class']) ? $caller['class'].'::' : null
				,   $caller['function']
			);

		}

		// config class_function() ##
		if ( 
			isset( $args['return'] ) 
			&& 'config' == $args['return'] 
			// && isset( $caller['class'] )
			// && isset( $caller['function'] )
		) {

			return sprintf(
				__( '%s%s()', 'Q' )
				,  	isset($caller['class']) ? $caller['class'].'_' : null
				,   $caller['function']
			);

		}

		// file::line() ##
		if ( 
			isset( $args['return'] ) 
			&& 'file_line' == $args['return'] 
			&& isset( $caller['file'] )
			&& isset( $caller['line'] )
		) {

			return sprintf(
				__( '%s:%d', 'Q' )
				,   $caller['file']
				,   $caller['line']
			);

		}

		// specific value ##
		if ( 
			isset( $args['return'] ) 
			&& isset( $caller[$args['return']] )
		) {

			return sprintf(
				__( '%s', 'Q' )
				,  $caller[$args['return']] 
			);

		}

		// default - everything ##
		return sprintf(
			__( '%s%s() %s:%d', 'Q' )
			,   isset($caller['class']) ? $caller['class'].'::' : ''
			,   $caller['function']
			,   isset( $caller['file'] ) ? $caller['file'] : 'n'
			,   isset( $caller['line'] ) ? $caller['line'] : 'x'
		);

	}



	

    public static function array_to_object( $array ) {
        
        #h::log( 'here..' );
        if ( ! is_array( $array ) ) {

            return $array;

        }
    
        $object = new \stdClass();

        if ( is_array( $array ) && count( $array ) > 0 ) {

            foreach ( $array as $name => $value ) {

                $name = strtolower( trim( $name ) );

                if ( ! empty( $name ) ) {

                    $object->$name = self::array_to_object( $value );

                }

            }

            return $object;

        } else {
          
            return false;
        
        }

	}
	


	public static function array_unique_multidimensional( $input )
	{

		$serialized = array_map('serialize', $input);

		$unique = array_unique($serialized);

		return array_intersect_key($input, $unique);
	
	}

	

    /**
     * Recursive pass args 
     * 
     * @link    https://mekshq.com/recursive-wp-parse-args-wordpress-function/
     */
    public static function parse_args( &$args, $defaults ) {

        $args = (array) $args;
        $defaults = (array) $defaults;
        $result = $defaults;
        
        foreach ( $args as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::parse_args( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }

        return $result;

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
    



    public static function array_search( $field = null, $value = null, $array = null ) {

		// sanity ##
		if (
			is_null( $field )
			|| is_null( $value )
			|| is_null( $array )
			|| ! is_array( $array )
		){

			h::log( 'e:>Error in passed params' );

			return false;

		}

        foreach ( $array as $key => $val ) {
        
            if ( $val[$field] === $value ) {
        
                return $key;
        
            }
        
        }
        
        return null;

	}
	

	/**
	 * search string by array
	 * 
	 * @link	https://stackoverflow.com/questions/6284553/using-an-array-as-needles-in-strpos
	 */
	public static function strposa($haystack, $needle, $offset=0) 
	{
		if( ! is_array( $needle ) ) {
			
			$needle = array($needle);

		}

		foreach( $needle as $query ) {

			// stop on first true result ##
			if( strpos( $haystack, $query, $offset ) !== false) return true;
		
		}

		return false;

	}


    /**
     * Save a value to the options table, either updating or creating a new key
     * 
     * @since       2.0.0
     * @return      Void
     */
    public static function add_update_option( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) 
    {
    
        if ( \get_site_option( $option_name ) != $new_value ) {

            \update_site_option( $option_name, $new_value );

        } else {

            \add_site_option( $option_name, $new_value, $deprecated, $autoload );

        }
    
    }



	public static function file_extension( $string = null ) {

		// sanity ##
		if( is_null( $string ) ){

			h::log( 'e:>No string passed to method' );

			return false;

		}

		$n = strrpos( $string, "." );
		return ( $n === false ) ? "" : substr( $string, $n+1 );
		
	}



	public static function file_put_array( $path, $array )
	{

		if ( is_array( $array ) ){

			$contents = self::var_export_short( $array, true );
			// $contents = var_export( $array, true );

			// stripslashes ## .. hmmm ##
			$contents = str_replace( '\\', '', $contents );

			// h::log( 'd:>Array data good, saving to file' );

			// save in php as an array, ready to return ##
			file_put_contents( $path, "<?php\n return {$contents};\n") ;
			
			// done ##
			return true;

		}

		h::log( 'e:>Error with data format, config file NOT saved' );
		
		// failed ##
		return false;

	}


	public static function var_export_short( $data, $return = true ){

		$dump = var_export($data, true);

		$dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
		$dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
		$dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties

		if (gettype($data) == 'object') { // Deal with object states
			$dump = str_replace('__set_state(array(', '__set_state([', $dump);
			$dump = preg_replace('#\)\)$#', "])", $dump);
		} else { 
			$dump = preg_replace('#\)$#', "]", $dump);
		}

		if ($return===true) {
			return $dump;
		} else {
			echo $dump;
		}

	}


	/*
	public static function var_export( $var, $indent ="" ) {
		switch (gettype($var)) {
			case 'integer':         
			case 'double':             
				return $var;
			case "string":
				return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
			case "array":
				$indexed = array_keys($var) === range(0, count($var) - 1);
				$r = [];
				foreach ($var as $key => $value) {
					$r[] = "$indent    "
						 . ($indexed ? "" : self::var_export54($key) . " => " )
						 . self::var_export54( $value, "$indent    " );
				}
				return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
			case "boolean":
				return $var ? "TRUE" : "FALSE";
			default:
				return var_export($var, TRUE);
		}
	}
	*/
    
}

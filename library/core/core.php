<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
use q\theme\template as template;

class core extends \Q {


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
                            
                            if ( $debug === true ) { pr($_GET[ $key ]); } // debug ##
                            
                            return self::sanitize( $_GET[ $key ], \sanitize_text_field ( $sanitize ) );
                            
                        }
                            
                        break;
                    
                    case 'post':
                    default:
                        
                        if ( isset( $_POST[ $key ] ) ) {  
                            
                            if ( $debug === true ) { wp_die($_POST[ $key ]); } // debug ##
                            
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




    public static function array_to_object( $array ) {
        
        #wp_die( 'here..' );
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
    



    public static function array_search( $field, $value, $array ) {

        foreach ( $array as $key => $val ) {
        
            if ( $val[$field] === $value ) {
        
                return $key;
        
            }
        
        }
        
        return null;

    }

    
}
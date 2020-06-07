<?php

namespace q\core;

use q\core;
use q\core\helper as h;
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

		// class::method() ##
		if ( 
			isset( $args['return'] ) 
			&& 'class_function' == $args['return'] 
			&& isset( $caller['class'] )
			&& isset( $caller['function'] )
		) {

			return sprintf(
				__( '%s::%s()', 'Q' )
				,  	$caller['class'] 
				,   $caller['function']
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
		$return = sprintf(
			__( '%s%s() %s:%d', 'Q' )
			,   isset($caller['class']) ? $caller['class'].'::' : ''
			,   $caller['function']
			,   isset( $caller['file'] ) ? $caller['file'] : 'n'
			,   isset( $caller['line'] ) ? $caller['line'] : 'x'
		);

		// kick it back ##
		return $return;

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
	

	
    /**
     * Check if a plugin is active
     * 
     * @since       2.0.0
     * @return      Boolean
     */
    public static function plugin_is_active( $plugin ) 
    {
        
        return in_array( $plugin, (array) \get_site_option( 'active_plugins', [] ) );
    
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


    /**
    * Get Q Plugin data
    *
    * @return   Object
    * @since    0.3
    */
    public static function plugin_data( $refresh = false ){

        if ( $refresh ) {

            #echo 'refrshing stored framework data<br />'; ##
            \delete_site_option( 'q_plugin_data' ); // delete option ##

        }

        if ( ! $array = \get_site_option( 'q_plugin_data' ) ) {

            $array = array (
                'version'       => self::version // \Q::version
            );

            if ( $array ) {

                self::add_update_option( 'q_plugin_data', $array, '', 'yes' );

            }

        }

        return core\method::array_to_object( $array );

    }



    /**
    * Get installed theme data
    *
    * @return  Object
    * @since   0.3
    */
    public static function theme_data( $refresh = false )
    {

       if ( $refresh ) {

           #echo 'refrshing stored theme data<br />'; ##
           \delete_site_option( 'q_theme_data' ); // delete option ##

       }

       // declare global variable ##
       global $q_theme_data;

       $array = \get_site_option( 'q_theme_data' );

       if ( ! \get_site_option( 'q_theme_data' ) ) {

           #echo 'stored theme option empty<br />';
           #$array = @file_get_contents( q_get_option("uri_parent")."library/version/");

           if( function_exists( 'wp_get_theme' ) ) {
               $array = \wp_get_theme( \get_site_option( 'template' ));
               #$theme_version = $theme_data->Version;
           } else {
               $array = \get_theme_data( \get_template_directory() . '/style.css');
               #$theme_version = $theme_data['Version'];
           }
           #$theme_base = get_option('template');

           if ( $array ) {

               self::add_update_option( 'q_theme_data', $array, '', 'yes' );
               #echo 'stored fresh theme data<br />';

           }

       }

       return core\method::array_to_object( $array );

    }




	/**
     * Check if a page has children
     *
     * @since       1.3.0
     * @param       integer         $post_id
     * @return      boolean
     */
    public static function has_children( $post_id = null )
    {

        // nothing to do here ##
        if ( is_null ( $post_id ) ) { return false; }

        // meta query to allow for inclusion and exclusion of certain posts / pages ##
        $meta_query =
                array(
                    array(
                        'key'       => 'program_sub_group',
                        'value'     => '',
                        'compare'   => '='
                    )
                );

        // query for child or sibling's post ##
        $wp_args = array(
            'post_type'         => 'page',
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'meta_query'        => $meta_query,
        );

        #pr( $wp_args );

        $object = new \WP_Query( $wp_args );

        // nothing found - why? ##
        if ( 0 === $object->post_count ) { return false; }

        // get children ##
        $children = \get_pages(
            array(
                'child_of'      => $post_id,
                'meta_key'      => '',
                'meta_value'    => '',
            )
        );

        // count 'em ##
        if( count( $children ) == 0 ) {

            // No children ##
            return false;

        } else {

            // Has Children ##
            return true;

        }

    }


    public static function list_image_sizes()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) h::log( $_wp_additional_image_sizes ); 

	}
	

	/**
	 * Get information about available image sizes
	 * 
	 * @link		https://developer.wordpress.org/reference/functions/get_intermediate_image_sizes/
	 */
	function get_image_sizes( $size = '' ) {

		$wp_additional_image_sizes = wp_get_additional_image_sizes();
	
		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
	
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array( 
					'width' => $wp_additional_image_sizes[ $_size ]['width'],
					'height' => $wp_additional_image_sizes[ $_size ]['height'],
					'crop' =>  $wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
	
		// Get only 1 size if found
		if ( $size ) {
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
		return $sizes;

	}

    
}
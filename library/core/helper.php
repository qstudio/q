<?php

namespace q\core;

use q\plugin as q;
use q\core;
use q\extension;
use q\core\helper as h;

class helper {

    /**
	 * Detect if this is a development site running on a private/loopback IP
	 *
	 * @return bool
	 */
	public static function is_localhost(){
        
        $loopbacks = array( '127.0.0.1', '::1' );
        
        if ( in_array( $_SERVER['REMOTE_ADDR'], $loopbacks ) ) {

            return true;
            
		}

		if ( ! filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE ) ) {

            return true;
            
		}

        return false;

    }

    /**
	 * Detect if this is a development site running on a staging URL ".staging" 
	 *
     * @todo        move to Q
	 * @return      Boolean
	 */
	public static function is_staging(){

        $needle = '.staging'; # '.qlocal.com';
        
        $urlparts = parse_url( \network_site_url() );
        // h::log( $urlparts );
        $domain = $urlparts['host'];
        
        // h::log( 'network_site_url: '.\network_site_url() );
        // h::log( 'domain: '.$domain );

        // if ( in_array( $domain, $loopbacks ) ) {
        if ( strpos( $domain, $needle ) !== false ) {

            // h::log( 'On staging..' );

            return true;
            
		}

        // h::log( 'Not on staging...' );

        return false;
        
	}

    /**
     * Check for a connection to the intraweb
     * 
     * @since   2.0.1
     * @return  Boolean
     */
    public static function is_connected(){
        
        // connect to Google ##
        $connected = @fsockopen( "www.google.com", "80" ); // domain and port

        if ( $connected ){

            fclose( $connected );

            return true; //action when connected ##

        } else {

            return false; //action in connection failure

        }

    }


    /**
    * check if a file exists with environmental fallback
    * first check the active theme, then the plugin
    *
    * @param    $include        string      Include file with path ( from library/  ) to include. i.e. - templates/loop-nothing.php
    * @param    $return         string      return method ( echo, return, require )
    * @param    $type           string      type of return string ( url, path )
    * @param    $path           string      path prefix
    * @param    $class          string      parent class to reference for location of assets
    *
    * @since 0.1
    */
    public static function get( $include = null, $return = 'echo', $type = 'url', $path = "library/", $class = null ){

        // nothing passed ##
        if ( is_null( $include ) ) { 

            return false;            

        }

        // nada ##
        $template = false; 
        
        #if ( ! defined( 'TEMPLATEPATH' ) ) {

        #    h::log( 'MISSING for: '.$include.' - AJAX = '.( \wp_doing_ajax() ? 'true' : 'false' ) );

		#}
		
		// h::log( 'd:>h::get class/include: '.$class.'/'.$include );

        // perhaps this is a child theme ##
        if ( 
            // defined( 'Q_CHILD_THEME' )
            // && Q_CHILD_THEME
			\get_template_directory() !== \get_stylesheet_directory()
            && file_exists( \get_stylesheet_directory().'/'.$path.$include )
        ) {

			// h::log( 'CHILD: '.$path.$include );

            $template = \get_stylesheet_directory_uri().'/'.$path.$include; // template URL ##
            
            if ( 'path' === $type ) { 

                $template = \get_stylesheet_directory().'/'.$path.$include;  // template path ##

            }

        }

        // load active theme over plugin ##
        elseif ( 
            file_exists( \get_template_directory().'/'.$path.$include ) 
        ) { 

            $template = \get_template_directory_uri().'/'.$path.$include; // template URL ##
            
            if ( 'path' === $type ) { 

                $template = \get_template_directory().'/'.$path.$include;  // template path ##

            }

            // if ( self::$debug ) self::log( 'parent theme: '.$template );

        // load from extended Plugin ##
        } elseif ( 
			! is_null( $class )
			&& method_exists( $class, 'get_plugin_path' )
            && file_exists( call_user_func( array( $class, 'get_plugin_path' ), $path.$include ) )
            // file_exists( self::get_plugin_path( $path.$include ) )
        ) {

            // h::log( 'd:>h::get class: '.$class );

            // $template = self::get_plugin_url( $path.$include ); // plugin URL ##
            $template = call_user_func( array( $class, 'get_plugin_url' ), $path.$include );

            if ( 'path' === $type ) {
                
                // $template = self::get_plugin_path( $path.$include ); // plugin path ##
                $template = call_user_func( array( $class, 'get_plugin_path' ), $path.$include );
                
            } 

            // h::log( 'extended plugin: '.$template );

        }

        // load from Plugin ##
        elseif ( 
            file_exists( q::get_plugin_path( $path.$include ) )
        ) {

            $template = q::get_plugin_url( $path.$include ); // plugin URL ##

            if ( 'path' === $type ) {
                
                $template = q::get_plugin_path( $path.$include ); // plugin path ##
                
            } 

            #if ( self::$debug ) self::log( 'plugin: '.$template );

        }

        if ( $template ) { // continue ##

            // apply filters ##
            $template = \apply_filters( __NAMESPACE__.'_helper_get', $template );

            // echo or return string ##
            if ( 'return' === $return ) {

                #if ( self::$debug ) h::log( 'returned' );

                return $template;

            } elseif ( 'require' === $return ) {

                #if ( self::$debug ) h::log( 'required' );

                return require_once( $template );

            } else {

                #if ( self::$debug ) h::log( 'echoed..' );

                echo $template;

            }

        }

        // nothing cooking ##
        return false;

    }

    /**
     * Write to WP Error Log
     *
     * @since       1.5.0
     * @return      void
     */
    public static function log( $args = null ){
		
		// shift callback level, as we added another level.. ##
		\add_filter( 
			'q/core/log/traceback/function', function () {
			return 4;
		});
		\add_filter( 
			'q/core/log/traceback/file', function () {
			return 3;
		});
		
		// pass to core\log::set();
		return core\log::set( $args );

	}
	
	/**
     * Write to WP Error Log directly, not via core\log
     *
     * @since       4.1.0
     * @return      void
     */
    public static function hard_log( $args = null ){
		
		// error_log( $args );

		// sanity ##
		if ( is_null( $args ) ) { 
			
			error_log( 'Nothing passed to log(), so bailing..' );

			return false; 
		
		}

		// $args can be a string or an array - so fund out ##
		if (  
			is_string( $args )
		) {

			// default ##
			$log = $args;

		} elseif ( 
			is_array( $args ) 
			&& isset( $args['log_string'] )	
		) {

			error_log( 'log_string => from $args..' );
			$log = $args['string'];

		} else {
			
			$log = $args;

		} 

		// debugging is on in WP, so write to error_log ##
        if ( true === WP_DEBUG ) {

			// get caller ##
			$backtrace = core\method::backtrace();

            if ( is_array( $log ) || is_object( $log ) ) {
                core\log::error_log( print_r( $log, true ).' -> '.$backtrace );
            } else {
                core\log::error_log( $log.' -> '.$backtrace );
            }

		}
		
		// done ##
		return true;

    }


    /**
     * Pretty print_r / var_dump
     *
     * @since       0.1
     * @param       Mixed       $var        PHP variable name to dump
     * @param       string      $title      Optional title for the dump
     * @return      String      HTML output
     */
    public static function pr( $var, $title = null )
    {

        if ( $title ) $title = '<h2>'.$title.'</h2>';
        print '<pre class="var_dump">'; echo $title; var_dump($var); print '</pre>';

    }


    /**
     * Pretty print_r / var_dump with wp_die
     *
     * @since       0.1
     * @param       Mixed       $var        PHP variable name to dump
     * @param       string      $title      Optional title for the dump
     * @return      String      HTML output
     */
    public static function pr_die( $var, $title = null )
    {

        \wp_die( self::pr( $var, $title ) );

    }




    /**
    * Get current device type
    *
    * @since       0.1
	* @return      string      Device slug
    */
    public static function device(){

        // property already loaded ##
        if ( q::$_device ) { 
        
            // h::log( '$device set to: '.q::$_device );
            
            // filter ##
            $string = \apply_filters( 'q/device/handle', q::$_device );

            // log ##
            // h::log( '$device filtered to: '.q::$_device );

            // kick it back ##
            return $string; 
        
        }

        // we have a new simpler device handler extension - check if the class is available ##
        if ( 
			class_exists( '\q\extension\device\method' ) 
			&& isset( core\option::get('extension')->device )
			&& true === core\option::get('extension')->device 
        ) {
			
			// self::log( 'd:>Device Extension Found..' );
            $handle = \q\extension\device\method::handle();

        // backup to mobile ##
        } else {

			// self::log( 'd:>assign default handle' );

            $handle = 'handheld'; // defaults to mobile ##

        }

        // check what we have ##
        // self::log( 'd:>handle: '.$handle );

        // set and return the property value ##
        // return q::$_device = $handle;

        // filter ##
        $string = \apply_filters( 'q/device/handle', $handle );

        // log ##
        // h::log( '$device filtered to: '.$string );

        // kick it back, setting property ##
        return q::$_device = $string;

    }




	/** 
     * Check which consent is given by the user via a stored cookie
     * 
     * @since 4.0.0
     * */
    public static function consent( $setting = null ): bool
    {

        if ( is_null( $setting ) ) {

            // h::log( 'd:>No setting passed, default to true.' );

            return true;

        }

        if ( 
			! class_exists( '\q\extension\consent' )
			|| ! isset( core\option::get('extension')->consent )
			|| true !== core\option::get('extension')->consent 
        ) {

            // self::log( 'd:>Consent Class not found, default to true' );

            // no ##
            return true;

        }

        if (
            ! \q\extension\consent\cookie::is_active( $setting ) 
        ) {

            // self::log( 'd:>Setting not allowed: '.$setting );

            // no ##
            return false;

        }

        // self::log( 'd:>Setting allowed: '.$setting );

        // ok ##
        return true;

    }


	
    /**
    * Get current browser client
    *
    * @since       0.1
	* @return      string      Device slug
	*/
	/*
    public static function client()
    {

        if ( 
            class_exists( '\q\extension\device\method' ) 
        ) {

			return extension\device\method::client();
			
		} else {

			// unknown ##
			return false;

		}

	}
	*/




    /**
     * Return data image element to use for holding images
     * 
     * @todo        Review
     */
    public static function holder( $string = null ) 
    {

        if ( is_null( $string ) ) {

            return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        }

    }



    /**
	 * Remove Class Action Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_action() on an action added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove actions with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 *
	 * @param string $tag         Action to remove
	 * @param string $class_name  Class name for the action's callback
	 * @param string $method_name Method name for the action's callback
	 * @param int    $priority    Priority of the action (default 10)
	 *
	 * @return bool               Whether the function is removed.
	 */
    public static function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) 
    {
    
        global $wp_filter;

        // Check that filter actually exists first
        if ( ! isset( $wp_filter[ $tag ] ) ) return FALSE;
    
        /**
         * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
         * a simple array, rather it is an object that implements the ArrayAccess interface.
         *
         * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
         *
         * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
         */
        if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
            // Create $fob object from filter tag, to use below
            $fob = $wp_filter[ $tag ];
            $callbacks = &$wp_filter[ $tag ]->callbacks;
        } else {
            $callbacks = &$wp_filter[ $tag ];
        }
    
        // Exit if there aren't any callbacks for specified priority
        if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) return FALSE;
    
        // Loop through each filter for the specified priority, looking for our class & method
        foreach( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
    
            // Filter should always be an array - array( $this, 'method' ), if not goto next
            if ( ! isset( $filter[ 'function' ] ) || ! is_array( $filter[ 'function' ] ) ) continue;
    
            // If first value in array is not an object, it can't be a class
            if ( ! is_object( $filter[ 'function' ][ 0 ] ) ) continue;
    
            // Method doesn't match the one we're looking for, goto next
            if ( $filter[ 'function' ][ 1 ] !== $method_name ) continue;
    
            // Method matched, now let's check the Class
            if ( get_class( $filter[ 'function' ][ 0 ] ) === $class_name ) {
    
                // WordPress 4.7+ use core remove_filter() since we found the class object
                if( isset( $fob ) ){
                    // Handles removing filter, reseting callback priority keys mid-iteration, etc.
                    $fob->remove_filter( $tag, $filter['function'], $priority );
    
                } else {
                    // Use legacy removal process (pre 4.7)
                    unset( $callbacks[ $priority ][ $filter_id ] );
                    // and if it was the only filter in that priority, unset that priority
                    if ( empty( $callbacks[ $priority ] ) ) {
                        unset( $callbacks[ $priority ] );
                    }
                    // and if the only filter for that tag, set the tag to an empty array
                    if ( empty( $callbacks ) ) {
                        $callbacks = array();
                    }
                    // Remove this filter from merged_filters, which specifies if filters have been sorted
                    unset( $GLOBALS['merged_filters'][ $tag ] );
                }
    
                return TRUE;
            }
        }
    
        return FALSE;
    
    }


}

<?php

/* 
 * NProgress Bar
 *
 * @package         q-nprogress
 * @author          Q Studio <social@qstudio.us>
 * @license         GPL-2.0+
 * @link            https://qstudio.us/
 * @copyright       2019 Q Studio
 *
 * @wordpress-plugin
 * Plugin Name:     Q NProgress
 * Plugin URI:      https://www.qstudio.us
 * Description:     Include NProgress in WordPress: http://ricostacruz.com/nprogress/
 * Version:         1.1.0
 * Author:          Q Studio
 * Author URI:      https://www.qstudio.us
 * License:         GPL
 * Copyright:       Q Studio
 * Class:           q_nprogress
 * Text Domain:     q-nprogress
 * Domain Path:     /theme/language
 * GitHub Plugin URI: qstudio/q-nprogress
*/

/**
 * NProgress Credits:
 *
 * http://ricostacruz.com/nprogress/
 * https://github.com/rstacruz/nprogress
 */

use q\nprogress\core\helper as h;

defined( 'ABSPATH' ) OR exit;

if ( ! class_exists( 'q_nprogress' ) ) {
    
    // instatiate plugin via WP plugins_loaded - init is too late for CPT ##
    add_action( 'after_setup_theme', array ( 'q_nprogress', 'get_instance' ), 1 );
    
    class q_nprogress {
                
        // Refers to a single instance of this class. ##
        private static $instance = null;

        // Plugin Settings
        const version = '1.1.0';
        // static $device = ''; // start false ##
        static $debug = false;
        // static $load_count = 0;
        const text_domain = 'q-nprogress'; // for translation ##

        // plugin properties ##
        public static $properties = false;

        /**
         * Creates or returns an instance of this class.
         *
         * @return  Foo     A single instance of this class.
         */
        public static function get_instance() 
        {

            if ( 
                null == self::$instance 
            ) {

                self::$instance = new self;

            }

            return self::$instance;

        }
        
        
        /**
         * Instatiate Class
         * 
         * @since       0.2
         * @return      void
         */
        private function __construct() 
        {
			
            // activation ##
            register_activation_hook( __FILE__, array ( $this, 'register_activation_hook' ) );

            // deactvation ##
            register_deactivation_hook( __FILE__, array ( $this, 'register_deactivation_hook' ) );

            // set text domain ##
            add_action( 'init', array( $this, 'load_plugin_textdomain' ), 1 );

            // load libraries ##
			self::load_libraries();
			
            // check debug settings ##
            add_action( 'plugins_loaded', array( get_class(), 'debug' ), 11 );

        }



        /**
         * We want the debugging to be controlled in global and local steps
         * If Q debug is true -- all debugging is true
         * else follow settings in Q, or this plugin $debug variable
         */
        public static function debug()
        {

            // define debug ##
            self::$debug = 
                ( 
                    class_exists( 'Q' )
                    && true === \Q::$debug
                ) ?
                true :
                self::$debug ;

            // test ##
            // h::log( 'Q exists: '.json_encode( class_exists( 'Q' ) ) );
            // h::log( 'Q debug: '.json_encode( \Q::$debug ) );
            // h::log( json_encode( self::$debug ) );

            return self::$debug;

        }


        // the form for sites have to be 1-column-layout
        public function register_activation_hook() {

            #add_option( 'q_nprogress_configured' );

            // flush rewrites ##
            #global $wp_rewrite;
            #$wp_rewrite->flush_rules();

        }


        public function register_deactivation_hook() {

            #delete_option( 'q_nprogress_configured' );

        }


        
        /**
         * Load Text Domain for translations
         * 
         * @since       1.7.0
         * 
         */
        public function load_plugin_textdomain() 
        {
            
            // set text-domain ##
            $domain = self::text_domain;
            
            // The "plugin_locale" filter is also used in load_plugin_textdomain()
            $locale = apply_filters('plugin_locale', get_locale(), $domain);

            // try from global WP location first ##
            load_textdomain( $domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo' );
            
            // try from plugin last ##
            load_plugin_textdomain( $domain, FALSE, plugin_dir_path( __FILE__ ).'library/language/' );
            
        }
        
        
        
        /**
         * Get Plugin URL
         * 
         * @since       0.1
         * @param       string      $path   Path to plugin directory
         * @return      string      Absoulte URL to plugin directory
         */
        public static function get_plugin_url( $path = '' ) 
        {

            return plugins_url( $path, __FILE__ );

        }
        
        
        /**
         * Get Plugin Path
         * 
         * @since       0.1
         * @param       string      $path   Path to plugin directory
         * @return      string      Absoulte URL to plugin directory
         */
        public static function get_plugin_path( $path = '' ) 
        {

            return plugin_dir_path( __FILE__ ).$path;

        }
        


         /**
         * Check for required classes to build UI features
         * 
         * @return      Boolean 
         * @since       0.1.0
         */
        public static function has_dependencies()
        {

            // check for what's needed ##
            if (
                ! class_exists( 'Q' )
            ) {

                h::log( 'e:>Q classes are required, install required plugin.' );

                return false;

            }

            // ok ##
            return true;

        }
        
        

        /**
        * Load Libraries
        *
        * @since        2.0
        */
		private static function load_libraries()
        {

            // check for dependencies, required for UI components - admin will still run ##
            if ( ! self::has_dependencies() ) {

                return false;

            }

            // methods ##
            require_once self::get_plugin_path( 'library/core/helper.php' );
            require_once self::get_plugin_path( 'library/core/core.php' );

            // frontend ##
            require_once self::get_plugin_path( 'library/ui/theme.php' );

        }

    }

}
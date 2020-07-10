<?php

/*
 * logic-less, procedural semantic markup language 
 *
 * @package         q-willow
 * @author          Q Studio <social@qstudio.us>
 * @license         GPL-2.0+
 * @link            http://qstudio.us/
 * @copyright       2010 Q Studio
 *
 * @wordpress-plugin
 * Plugin Name:     Q Willow
 * Plugin URI:      https://www.qstudio.us
 * Description:     Willow is a Simple, logic-less, procedural semantic markup language 
 * Version:         0.0.1
 * Author:          Q Studio
 * Author URI:      https://www.qstudio.us
 * License:         GPL
 * Copyright:       Q Studio
 * Class:           q_willow
 * Text Domain:     q-willow
 * Domain Path:     /languages
 * GitHub Plugin URI: qstudio/q-willow
*/

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

/* Check for Class */
if ( ! class_exists( 'q_willow' ) ) {

    // instatiate plugin via WP plugins_loaded ##
    add_action( 'plugins_loaded', array ( 'q_willow', 'get_instance' ), 1 );

    // Q Class ##
    class q_willow {

        // Refers to a single instance of this class. ##
        private static $instance = null;

        // Plugin Settings
        const version = '0.0.1';
        const text_domain = 'q-willow'; // for translation ##
		// static $debug = false; // global debugging, normally false, as individual plugins can control local level debugging ##
		
		protected static 

			/* define template delimiters */
			// based on Mustache, but not the same... https://github.com/bobthecow/mustache.php/wiki/Mustache-Tags
			$tags = [

				// variables ##
				'variable'		=> [
					'open' 		=> '{{ ', // open ## 
					'close' 	=> ' }}', // close ##
				],

				// parameters / arguments ##
				'argument'		=> [
					'open' 		=> '>> ', // open ## 
					'close' 	=> ' <<', // close ##
				],
				
				// section ##
				'section'		=> [
					'open' 		=> '{{# ', // open ##
					'close' 	=> ' }}', // close ##
					'end'		=> '{{/#}}' // end statement ##
				],

				// inversion ##  // else, no results ##
				// @todo.... this proably will only work when pared with a section.. so, if the section returned false, render the inversion ## 
				'inversion'		=> [
					'open'		=> '{{^ ',
					'close'		=> ' }}', 
					'end'		=> '{{/}}'
				],

				// function -- also, an unescaped variable -- @todo --- ##
				'function'		=> [
					'open' 		=> '{{{ ', // open ## 
					'close' 	=> ' }}}', // close ##
				],

				// partial ##
				'partial'		=> [
					'open' 		=> '{{> ', // open ## 
					'close' 	=> ' }}', // close ##
				],

				// comment ##
				'comment'		=> [
					'open' 		=> '{{! ', // open ## 
					'close' 	=> ' }}', // close ##
				],

			]

		;

        /**
         * Creates or returns an instance of this class.
         *
         * @return  Foo     A single instance of this class.
         */
        public static function get_instance()
        {

            if ( null == self::$instance ) {
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

        }




        /**
         * plugin activation
         *
         * @since   0.2
         */
        public function register_activation_hook()
        {

            $q_options = array(
                'configured'    => true
                ,'version'      => self::version
            );

            // init running, so update configuration flag ##
            add_option( 'q_willow', $q_options, '', true );

        }


        /**
         * plugin deactivation
         *
         * @since   0.2
         */
        public function register_deactivation_hook()
        {

            // de-configure plugin ##
            delete_option('q_willow');

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
            $locale = apply_filters('plugin_locale', get_locale(), $domain );

            // try from global WP location first ##
            load_textdomain( $domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo' );

            // try from plugin last ##
            load_plugin_textdomain( $domain, FALSE, plugin_dir_path( __FILE__ ).'library/languages/' );

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

            #return plugins_url( ltrim( $path, '/' ), __FILE__ );
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
         * Check for required breaking dependencies
         *
         * @return      Boolean
         * @since       1.0.0
         */
        public static function has_dependencies()
        {

            // check for what's needed ##
            if (
                ! class_exists( 'Q' )
            ) {

                helper::log( 'e:>Q Willow requires Q to run correctly..' );

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
			require_once self::get_plugin_path( 'library/core/_load.php' );

			// parsers ##
			require_once self::get_plugin_path( 'library/parse/_load.php' );

			// output buffer ##
			require_once self::get_plugin_path( 'library/buffer/_load.php' );
			
			// tags ##
			require_once self::get_plugin_path( 'library/tags/_load.php' );

			// view ##
			// require_once self::get_plugin_path( 'library/view/_load.php' );

			// assets ##
			// require_once self::get_plugin_path( 'library/asset/_load.php' );

			// ui modules ##
			// require_once self::get_plugin_path( 'library/module/_load.php' );

			// extensions ##
			// require_once self::get_plugin_path( 'library/extension/_load.php' );

            // admin ##
			// require_once self::get_plugin_path( 'library/admin/_load.php' );

			// test suite ##
            // require_once self::get_plugin_path( 'library/test/_load.php' );

            // hooks ##
            // require_once self::get_plugin_path( 'library/hook/_load.php' );

            // plugins ##
            #require_once self::get_plugin_path( 'library/plugin/_load.php' );

        }


    }

}

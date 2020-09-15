<?php

namespace q\asset;

use q\core;
use q\core\helper as h;
use q\asset;
use q\strings;

// fire up ##
\q\asset\js::__run();

class js extends \Q {

	protected static 

		$q_modules = []

	;

	public static function __run(){

		// load early ##
		\add_action( 'init', [ get_class(), 'load' ], 10 );

		// delete ##
		// \add_action( 'init', [ get_class(), 'delete' ], 9 );

		// save late ##
		// \add_action( 'shutdown', [ get_class(), 'save' ], 1000 );

	}
	
	
	public static function load(){

		// h::log( \q\core\option::get('module') );

		// load list of modules, stored in site_option "q_modules" - includes list of parameters to localize ##
		// h::log( \get_option( "q_modules" ) );
		if ( ! self::$q_modules = \get_option( "q_modules" ) ){

			// return false;

			h::log( 'e:>No modules option found: "q_modules", creating now.' );

			$q_modules = [];
			$q_modules['scss'] = [];
			$q_modules['js'] = [];
			$q_modules['localize'] = [];

			self::$q_modules = $q_modules;

		}

		// h::log( self::$q_modules );

	}


	public static function delete(){

		\delete_option( "q_modules" );

	}


	public static function save(){

		// store active modules list ##
		core\method::add_update_option( 'q_modules', self::$q_modules, '', 'yes' );

	}


	/**
	 * Add scripts from modules
	 * 
	 * - Q Setting save, get active theme, copy active module js files over from plugins/Q/../_source/js/module/*.js - empty folder first..
	 * - If watching, files will be compilled from themes/q_parent/../_source/js/module/*.js to asset/js/module/module.js
	 * - on Deploy, files will be compilled from themes/q_parent/../_source/js/module/*.js to asset/js/module/module.min.js
	 * 
	 * This method allows modules to register scripts to enqueue
	 * also, pass params to make available to JS, via wp_localize_script - they would be available at "q_module.MODULENAME__PARAM"
	 */
	public static function set( $args = null ){

		// sanity ##
		if(
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['module'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// check if module in array, if not add ##
		if( ! in_array( $args['module'], self::$q_modules['javascript'] ) ){

			self::$q_modules['javascript'][] = $args['module'];

		}

		// @TODO ---> how to skip including a JS file in the compilled output and load it directly, for quicker debugging ??
		// DELETE from QParent/_source/js/module/$file.js
		// ADD flag to enqueue to load the file directly from Q:/_source/js/module/$file.js
		// Re-compile - only works if Grunt watcher is running...

		// h::log( $args );

		// check if module being added want to localize variables ##
		$args['localize'] = array_filter( $args['localize'], 'strlen' );
		if( 
			isset( $args['localize'] ) 
			&& is_array( $args['localize'] )
		){

			// append module name to each passed key=>value, so "module->post_id = 123" becomes module_post_id = 123  ##
			$args['localize'] = array_combine(
				array_map(function($k) use( $args ){ return $args['module'].'_'.$k; }, array_keys($args['localize'])),
				$args['localize']
			);
			// h::log( $args['localize'] );

			if ( ! isset( self::$q_modules['localize'] ) ){ self::$q_modules['localize'] = []; }

			// $q_modules['']
			foreach( $args['localize'] as $key => $value ){

				// check if localize key already exists, else add it ##
				if( ! array_key_exists( $key, self::$q_modules['localize'] ) ){

					// note that localized keys are "namespaced" from the sending module ##
					self::$q_modules['localize'][$key] = $value;

				}

			}

			self::$q_modules['localize'] = array_merge( self::$q_modules['localize'], $args['localize'] );

		}

		// clean up empty items ##
		self::$q_modules['localize'] = array_filter( self::$q_modules['localize'], 'strlen' );

		// test ##
		// h::log( self::$q_modules );

		// update class property ##
		// self::$q_modules['localize'];

		// store active modules list ##
		// core\method::add_update_option( 'q_modules', self::$q_modules, '', 'yes' );

	}


	public static function get(){

		return self::$q_modules;

	}


	public static function localize(){

		if ( 
			! self::$q_modules['localize']
			|| ! is_array( self::$q_modules['localize'] )
		){

			h::log( 'e:>No modules provided arguments to localize.' );

			return []; // return empty array, for merging ##

		}

		// h::log( self::$q_modules );
		return self::$q_modules['localize'];

	}


}

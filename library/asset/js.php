<?php

namespace q\asset;

use q\plugin as q;
use q\core;
use q\core\helper as h;
use q\asset;
use q\strings;

class js {

	private $q;

	function __construct(){

		// we need the current $q instance ##
		$this->q = \q\plugin::get_instance();

	}

	function hooks(){

		// load early ##
		\add_action( 'init', [ $this, 'load' ], 1 );

		// delete ##
		// \add_action( 'init', [ get_class(), 'delete' ], 9 );

		// save late ##
		// \add_action( 'shutdown', [ get_class(), 'save' ], 1000 );

		// add values to js localize ##
		\add_filter( 'q/asset/localize', [ $this, 'localize' ], 10, 1 );

	}
	
	function load(){

		// h::log( \q\theme\core\option::get('module') );
		// $_q_modules = $this->q->get( '_q_modules' );

		// load list of modules, stored in site_option "q_modules" - includes list of parameters to localize ##
		// h::log( \get_option( "q_modules" ) );
		if ( false === $_q_modules = \get_option( "q_modules" ) ){

			// return false;

			h::log( 'e:>No modules option found: "q_modules", creating now.' );

			$_q_modules = [];
			$_q_modules['scss'] = [];
			$_q_modules['javascript'] = [];
			$_q_modules['localize'] = [];

		}

		// self::$q_modules = $q_modules;
		$this->q->set( '_q_modules', $_q_modules );

		// h::log( $_q_modules );

	}

	function delete(){

		\delete_option( "q_modules" );

	}

	function save(){

		// store active modules list ##
		core\method::add_update_option( 'q_modules', $this->q->get( '_q_modules' ), '', 'yes' );

	}

	/**
	 * Add scripts from modules
	 * 
	 * - Q Setting save, get active theme, copy active module js files over from plugins/Q/../_source/js/module/*.js - empty folder first..
	 * - If watching, files will be compilled from themes/q_parent/../_source/js/module/*.js to asset/js/module/module.js
	 * - on Deploy, files will be compilled from themes/q_parent/../_source/js/module/*.js to asset/js/module/module.min.js
	 * 
	 * This method allows modules to register scripts to enqueue
	 * also, pass params to make available to JS, via wp_localize_script - they would be available at "q_data.MODULENAME__PARAM"
	 */
	function set( $args = null ){

		// sanity ##
		if(
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['module'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// h::log( $args );

		$_q_modules = $this->q->get( '_q_modules' );
		// h::log( $_q_modules );

		// check if module in array, if not add ##
		if( ! in_array( $args['module'], $_q_modules['javascript'] ) ){

			$_q_modules['javascript'][] = $args['module'];

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

			if ( ! isset( $_q_modules['localize'] ) ){ $_q_modules['localize'] = []; }

			// $q_modules['']
			foreach( $args['localize'] as $key => $value ){

				// check if localize key already exists, else add it ##
				if( ! array_key_exists( $key, $_q_modules['localize'] ) ){

					// note that localized keys are "namespaced" from the sending module ##
					$_q_modules['localize'][$key] = $value;

				}

			}

			$_q_modules['localize'] = array_merge( $_q_modules['localize'], $args['localize'] );

		}

		// clean up empty items ##
		$_q_modules['localize'] = array_filter( $_q_modules['localize'], 'strlen' );

		// store ##
		$this->q->set( '_q_modules', $_q_modules );

		// test ##
		// h::log( $_q_modules );

		// update class property ##
		// $_q_modules['localize'];

		// store active modules list ##
		// core\method::add_update_option( 'q_modules', $_q_modules, '', 'yes' );

	}


	function get(){

		// return self::$q_modules;
		return $this->q->get( '_q_modules' );

	}


	/**
	 * Merge generated localize values into array passed to wp_enqueue_script
	*/
	function localize( $array ){

		$_q_modules = $this->get();
		// h::log( $_q_modules['localize'] );

		if ( 
			! isset( $_q_modules['localize'] )
			|| ! is_array( $_q_modules['localize'] )
		){

			h::log( 'e:>q_modules localize is empty or not an array.' );

			// return $array; // return passed array ##
			$_q_modules['localize'] = [];

		}

		// h::log( $_q_modules );
		return array_merge( $array, $_q_modules['localize'] );

	}


}

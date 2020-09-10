<?php

namespace q\module\search;

// Q ##
use q\core\helper as h;

// willow ##
use q\willow; // to extend ##
// use q\module;

// register class to render ##
\q\module\search\module::__run();

class module {

	public static function __run( $args = null ) {

		// check for willow ##
		if( ! class_exists( 'q_willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			willow\context\extend::register([ 
				'context' 	=> 'module', 
				'class' 	=> __CLASS__,
				'methods' 	=> $public_methods // public only 
				// 'methods'	=> get_class_methods( __CLASS__ ) // all class methods ##
			]);
		}, 2 );

	}



	/**
     * search module render
     *
     * @param       Array       $args
     * @since       4.1.0
     * @return      Array
     */
    public static function search( $args = null ) {

		// add assets ##
		// \add_action( 'get_header', [ '\\q\\module\\search\\enqueue', 'wp_enqueue_scripts' ], 10 );

		// JS callback ##
		\add_action( 'wp_footer', [ '\\q\\module\\search\\render', 'q_search_callback' ], 10 );

		// JS init ##
		\add_action( 'wp_footer', [ '\\q\\module\\search\\render', 'scripts' ], 1 );

		// return search UI ##
		return \q\module\search\render::ui( $args );

	}

}

<?php

namespace q\context;

use q\core\helper as h;
use willow;
use q\get;
// use q\module as modules;

// register class to willow ##
\q\context\navigation::__run();

class navigation {

	public static function __run( $args = null ) {

		// check for willow ##
		if( ! class_exists( 'willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			willow\context\extend::register([ 
				'context' 	=> str_replace( __NAMESPACE__.'\\', '', __CLASS__ ), 
				'class' 	=> __CLASS__,
				'methods' 	=> $public_methods // public only 
				// 'methods'	=> get_class_methods( __CLASS__ ) // all class methods ##
			]);
		}, 2 );

	}
	

	/**
    * Render nav menu
    *
    * @since       4.1.0
    */
    public static function menu( $args = null ){

		return [ 'menu' => get\navigation::menu( $args ) ];

	}
	

	/**
    * Render pagination
    *
    * @since       4.1.0
    */
    public static function pagination( $args = null ){

		return [ 'pagination' => get\navigation::pagination( $args ) ];

	}
	

	/**
    * Render siblings
    *
    * @since       4.1.0
    */
    public static function siblings( $args = null ){

		return [ 'siblings' => get\navigation::siblings( $args ) ];

	}


	/**
    * Render children
    *
    * @since       4.1.0
    */
    public static function children( $args = null ){

		return [ 'children' => get\navigation::children( $args ) ];

	}



	/**
    * Render back_home_next
    *
    * @since       4.1.0
    */
    public static function relative( $args = null ){

		return [ 'relative'	=> get\navigation::relative( $args ) ];

    }

}

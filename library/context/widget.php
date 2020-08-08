<?php

namespace q\context;

use q\core\helper as h;
// use q\ui;
// use q\get;
use q\willow;
// use q\willow\context;
// use q\willow\render; 
use q\widget as widgets;

// register class to willow ##
\q\context\widget::__run();

class widget {

	public static function __run( $args = null ) {

		// check for willow ##
		if( ! class_exists( 'q_willow' ) ){ return false; }

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
    * Render sharelines
    */
    public static function sharelines( $args = null ){

		// $get = \q\widget\sharelines::get( $args );
		// h::log( $get );
		return \q\widget\sharelines::get( $args );

	}


	// @todo - instagram ##


}

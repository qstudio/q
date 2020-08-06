<?php

namespace q\extension\search;

// Q ##
use q\core\helper as h;

// willow ##
use q\willow; // to extend ##
use q\extension as extensions;

// register class to render ##
\q\extension\search\extension::__run();

class extension {

	public static function __run( $args = null ) {

		// check for willow ##
		if( ! class_exists( 'q_willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			willow\context\extend::register([ 
				'context' 	=> 'extension', 
				'class' 	=> __CLASS__,
				'methods' 	=> $public_methods // public only 
				// 'methods'	=> get_class_methods( __CLASS__ ) // all class methods ##
			]);
		}, 2 );

	}



	/**
     * search extension render
     *
     * @param       Array       $args
     * @since       4.1.0
     * @return      Array
     */
    public static function search( $args = null ) {

		return extensions\search\render::ui( $args );

	}

}

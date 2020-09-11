<?php

namespace q\context;

use q\core\helper as h;
// use q\ui;
// use q\get;
use q\willow;
// use q\willow\context;
// use q\willow\render; 
use q\module as modules;

// register class to willow ##
\q\context\module::__run();

class module {

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
    * lightbox @@TODO - look for gallery images, if found, return gallery markup
    */
    public static function lightbox( $args = null ){

		return \q\module\bs_lightbox::get( $args );

	}


	/**
    * Sharelines
    */
    public static function sharelines( $args = null ){

		return \q\module\sharelines::get( $args );

	}


	/**
     * comment_template
     *
	 * @todo 		allow for passing markup
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function comment( $args = null )
    {

		return \q\module\comment::get( $args );

	}




}

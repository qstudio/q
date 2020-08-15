<?php

namespace q\context;

use q\core\helper as h;
use q\get;
use q\willow;

// register class to willow ##
\q\context\ui::__run();

class ui {

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
     * head
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function head( $args = null )
    {

		if ( ! class_exists( 'q_theme' ) ){ return false; }

		// include <html>, wp_head() until.. <body> ##
		return \q\theme\view\ui\header::head( $args );
			
	}
	

	/**
     * get_header
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function header( $args = null )
    {

		$name = null;
		if ( isset( $args['name'] ) ) {
			$name = $args['name'];
		}
		\do_action( 'get_header', $name );

		// look for config ##
		$config = \q\willow\core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ]);
		// $config = false;

		// look for property "args->task" in config ##
		if ( 
			$config
		){

			// h::log( 'Running from config' );

			// check ##
			// h::log( $config );
			
			// define "fields", passing returned data ##
			// render\fields::define(
			return $config;
			// );

		} else {

			// h::log( 'Running from method' );

			if ( ! class_exists( 'q_theme' ) ){ return false; }

			// we can call the footer::render() method
			return \q\theme\view\ui\header::header( $args );

		}

		// done ##
		return true;

	}



	/**
     * get_footer
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function footer( $args = null )
    {

		$name = null;
		if ( isset( $args['name'] ) ) {
			$name = $args['name'];
		}
		\do_action( 'get_footer', $name );

		// required hook included in ui_footer ##
		// \wp_footer();

		$config = \q\willow\core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ]);
		// h::log( $config );

		// return core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ]);

		// look for property "args->task" in config ##
		if ( 
			$config
		){

			// check ##
			// h::log( 'e:>UI FOOTER' );
			// h::log( $config );
			
			// define "fields", passing returned data ##
			// render\fields::define(
			return $config;
			// );

		} else {

			// h::log( 'e:>RENDER FOOTER' );

			if ( ! class_exists( 'q_theme' ) ){ return false; }

			// we can call the footer::render() method
			return theme\view\ui\footer::return( $args );

		}

		// done ##
		return true;

	}



	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function open( $args = null )
    {

		return [ 'classes' => \q\get\theme::body_class( $args ) ];

	}

	

	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function close( $args = null )
    {

		return \q\willow\core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ]);

	}

}

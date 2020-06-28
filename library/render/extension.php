<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\view;
use q\get;
use q\render;

// load it up ##
\q\render\extension::run();

class extension extends \q\render {

	/**
	 * Fire things up
	*/
	public static function run(){

		// allow for class extensions ##
		\do_action( 'q/render/register', [ get_class(), 'register' ] );

	}


	public static function register( $args = null ){

		// h::log( $args );

		// @todo -- lots of logic ###

		// store ##
		self::set( $args );

	}


	
    public static function set( $args = null ) {

		// @todo -- sanity ##

		// we only want to get "public" methods -- in this case, listed without __FUNCTION at start ##
		$methods = [];
		foreach( $args['methods'] as $method ){
			// h::log( 'd:>checking method: '.$method );
			if ( false !== strpos( $method, '__' ) ){ continue; } // skip __METHOD ##
			$methods[] = $method;
		};

		// $array = [
		// 	'context' 	=> $args['context'],
		// 	'class' 	=> $args['class'],
		// 	'methods' 	=> $methods
		// ];

		// h::log( $array );

		self::$extensions[$args['class']] = [
			'context' 	=> $args['context'],
			'class' 	=> $args['class'],
			'methods' 	=> $methods
		];

		// h::log( 'set: '.$args['class'] );

	}


	/**
	 * Prepare passed args ##
	 *
	 */
	public static function get( $context = null, $task = null ) {

		// @todo -- sanity ##

		// check ##
		// h::log( 'd:>Looking for extension: '.$context );

		// is_array ##
		// h::log( self::$extensions );

		// check ##
		// $found = false;

		foreach( self::$extensions as $k => $v ){

			// h::log( 'checking class: '.$k );

			// check if $context match ##
			if ( $v['context'] == $context ){

				// now check if we have a matching method ##
				// if ( in_array( $task, $v['methods'] ) ) {
				if (false !== $key = array_search( $task, $v['methods'] ) ) {

					// h::log( 'found context: '.$v['class'] );

					return [ 'class' => $v['class'], 'method' => $v['methods'][$key] ];

				}

			}

		}

		// nada
		return false;

	}

     
}

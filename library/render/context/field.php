<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class field extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

		// run method to populate field data ##
		// $method = $args['task'];
		
		// build $args['field'] ##
		$args['field'] = $args['task'];

		// check for extensions ##
		$extension = render\extension::get( $args['context'], $args['task'] );

		if (
			! \method_exists( get_class(), 'get' ) // base method is get\meta ##
			&& ! $extension // look for extensions ##
		) {

			render\log::set( $args );

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$args['task'] );

            return false;

		}

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			// h::log( 'd:>Bunked here..' );

            return false;

		}

		// base class ##
		if ( 
			\method_exists( get_class(), 'get' ) 
		){

			// 	h::log( 'load base method: '.$extension['class'].'::'.$extension['method'] );

			// call render method ##
			self::get( self::$args );

		// extended class ##
		} elseif (
			$extension
		){

			// 	h::log( 'load extended method: '.$extension['class'].'::'.$extension['method'] );

			// h::log( 'd:>render extension..' );
			$extension['class']::{ $extension['method'] }( self::$args );

		}


        // if ( ! render\args::validate( $args ) ) {

        //     render\log::set( $args );

        //     return false;

		// }

		// // build $args['field'] -- 
		// $args['field'] = $args['task'];

		// // h::log( 'd:>markup: '.$args['markup'] );
		// // h::log( 'd:>field: '.$args['field'] );

		// // build fields array with default values ##
		// render\fields::define([
		// 	$args['task'] => get\meta::field( $args )
		// ]);

		// h::log( self::$fields );

		// check each field data and apply numerous filters ##
		render\fields::prepare();

		// h::log( self::$fields );

		// Prepare template markup ##
		render\markup::prepare();

		// h::log( 'd:>markup: '.$args['markup'] );

        // optional logging to show removals and stats ##
        // render\log::set( $args );

        // return or echo ##
        return render\output::return();

	}
	

	// ---------- methods ##


	/**
     * Get field data via meta handler
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function get( $args = null ) {

		// get title - returns array with key 'title' ##
		render\fields::define([
			$args['task'] => get\meta::field( $args )
		]);

	}
	

}

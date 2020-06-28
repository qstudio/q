<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class navigation extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

		// run method to populate field data ##
		$method = $args['task'];
		$extension = render\extension::get( $args['context'], $args['task'] );

		if (
			! \method_exists( get_class(), $method ) // && exists ##
			&& ! $extension // look for extensions ##
		) {

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$method );

			render\log::set( $args );

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
			\method_exists( get_class(), $method ) 
		){

			// 	h::log( 'load base method: '.$extension['class'].'::'.$extension['method'] );

			// call render method ##
			self::{ $method }( self::$args );

		// extended class ##
		} elseif (
			$extension
		){

			// 	h::log( 'load extended method: '.$extension['class'].'::'.$extension['method'] );

			// h::log( 'd:>render extension..' );
			$extension['class']::{ $extension['method'] }( self::$args );

		}
		// h::log( self::$fields );

		// check each field data and apply numerous filters ##
		render\fields::prepare();

		// h::log( self::$fields );

		// Prepare template markup ##
		render\markup::prepare();

		// h::log( 'd:>markup: '.$args['markup'] );

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

    }


}

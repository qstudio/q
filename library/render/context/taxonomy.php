<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class taxonomy extends \q\render {

	/** MAGIC */
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

			render\log::set( $args );

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$method );

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

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
		render\fields::prepare();
		
		// h::log( self::$fields );

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

	}
	


	// ---------- methods ##




	/**
     * Post Category
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function terms( $args = null ) {

		h::log( $args );

		// get term - returns array with keys 'title', 'permalink', 'slug', 'active' ##
		render\fields::define(
			get\taxonomy::terms( $args )
		);

	}


	// categories ##


	// tag ##


	// tags ##


	// etc ##
	


}

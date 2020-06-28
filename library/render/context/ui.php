<?php

namespace q\render;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
// use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
use q\render; // self ##

// Q Theme ##
use q\theme;

class ui extends \q\render {

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
		
		// h::log( self::$markup );

        // return or echo ##
        return render\output::return();

	}
	


	// ---------- methods ##



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

		return theme\furniture\header::render( $args );

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

		return theme\furniture\footer::render( $args );

	}



	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function open( $args = null )
    {

		return render\fields::define([
			'classes' => get\theme::body_class( $args ) // grab classes ##
		]);

	}

	

	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function close( $args = null )
    {

        // set-up new array -- nothing really to do ##
		// grab classes ##
		return render\fields::define([
			'oh' => '' // hack.. nothing to pass here ##
		]);

	}


}

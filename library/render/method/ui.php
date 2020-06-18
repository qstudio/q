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

	public static function run( $args = null, $method = null ){

		// h::log( 'd:>hello here..' );
		// $args['config']['load'] = 'ui_'.$method;

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			h::log( 'd:>Bunked here..' );

            return false;

		}

		// h::log( $args );

		// run method to populate field data ##
		// $method = $args['config']['method'];
		if (
			! \method_exists( get_class(), $method ) // && exists ##
		) {

			h::log( 'd:>Cannot locate method: '.$method );

		}

		// call render method ##
		self::{ $method }( self::$args );
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
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function open( $args = null )
    {

		render\fields::define([
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
		render\fields::define([
			'oh' => '' // hack.. nothing to pass here ##
		]);

	}


}

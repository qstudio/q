<?php

namespace q\render;

use q\core;
use q\core\helper as h;
// use q\ui;
use q\render;

// \q\render\buffer::__run();

class buffer extends \q\render {

	/**
	 * Move some / all of buffer logic in here ##
	*/

    public static function prepare( $string = null ) {

		// h::log( $args );

		// sanity ##
		if ( 
			is_null( $string )
		){

			// log ##
			h::log( 'e:>$buffer is empty, so nothing to render.. stopping here.');

			// kick out ##
			return false;

		}

		// we are passed an html string, captured from output buffering, which we need to parse for tags and process ##
		// h::log( $string );

		// build required args ##
		$args = [
			'config'		=> [
				'return' 	=> 'return',
				'debug'		=> true
			],
			'markup'		=> [
				'template'	=> $string
			],
			'context'		=> 'buffer',
			'task'			=> 'prepare',
		];

		// force methods to return... but to where??? ##
		self::$args_default['config']['return'] = 'return';

		// reset args ##
		render\args::reset();

		// extract markup from passed args ##
		render\markup::pre_validate( $args );

		// validate passed args ##
		if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			h::log( 'e:>Args validation failed' );

			// reset all args ##
			render\args::reset();

			return false;

		}

		// prepare markup, fields and handlers based on passed configuration ##
		// so.. let's parser prepare an array in $buffer of hash + value.. then pass this to fields::define ??
		render\parse::prepare( $args );

		// h::log( self::$markup );
		// h::log( self::$buffer );

		// prepare field data ##
		// render\fields::prepare();
		render\fields::define( self::$buffer );

		// Prepare template markup ##
		render\markup::prepare();

		// // Prepare template markup ##
		// render\parse::cleanup();

		// optional logging to show removals and stats ##
		render\log::set( $args );

		// assign output to markup->template ##
		self::$markup['template'] = render\output::prepare();

		// clean up left over tags ##
		render\parse::cleanup();

		// check what we have ##
		// h::log( self::$markup['template'] );

		// return to OB ##
		return self::$markup['template'];

    }

}

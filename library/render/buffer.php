<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\render;

\q\render\buffer::run();

class buffer extends \q\render {

	/**
	 * Check for view template and start OB, if correct
	*/
	public static function run(){

		// not on admin ##
		if ( \is_admin() ) return false;

		// \add_action( 'get_header',  [ get_class(), 'ob_start' ], 0 ); // try -- template_redirect.. was init
		\add_action( 'get_header',  function(){ 
			
			if ( 'willow' == \q\view\is::format() ){

				// h::log( 'd:>starting OB, as on a willow template: "'.\q\view\is::format().'"' );

				// set buffer ##
				self::$buffering = true;

				return ob_start();

			}

			// h::log( 'd:>not a willow template, so no ob: "'.\q\view\is::format().'"' );

			return false; 
		}
		, 0 ); 

		\add_action( 'shutdown', function() {

			if ( 'willow' != \q\view\is::format() ){

				// h::log( 'e:>No buffer.. so no go' );
				
				return false; 
			
			}

			// h::log( 'e:>Doing shutdown buffer' );

			$final = '';
		
			// We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
			// that buffer's output into the final output.
			$levels = ob_get_level();
		
			for ($i = 0; $i < $levels; $i++) {
				$final .= ob_get_clean();
			}

			// @TODO... this needs to be more graceful, and render needs to have "blocks", which can be passed / set
			// echo theme\view\ui\header::render();
			\q\render::ui__header();
		
			// Apply any filters to the final output
			// echo \apply_filters( 'ob_output', $final );
			echo \q\render\buffer::prepare( $final );

			// @TODO... this needs to be more graceful, and render needs to have "blocks", which can be passed / set
			// echo theme\view\ui\footer::render();
			\q\render::ui__footer();

		}, 0);

	}


	/**
	 * Prepare output for Buffer
	 * 
	 * @since 4.1.0
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

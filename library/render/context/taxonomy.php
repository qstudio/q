<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class taxonomy extends \q\render {

	/**
     * Post Category
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function terms( $args = null ) {

		// h::log( $args );
		// h::log( self::$markup );

		// get term - returns array with keys 'title', 'permalink', 'slug', 'active' ##
		render\fields::define(
			// return an array of term items, in the array "terms" ##
			get\taxonomy::terms( $args )
		);

	}


	// categories ##


	// tag ##


	// tags ##


	// etc ##
	


}

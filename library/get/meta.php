<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

// Q Theme ##
use q\theme;

class meta extends \q\get {

	
    /**
     * Get Post meta field from acf, format if required and markup
     *
     * @since       4.1.0
     */
    public static function field( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['field'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// pst ID ##
		$post_id = isset( $args['config']['post'] ) ? $args['config']['post']->ID : null ;

		// get field ##
		if ( $value = \get_field( $args['field'], $post_id ) ) {

			// h::log( 't:>we need to pass this thru filters - but return expects an array');

			return $value;

		}

		h::log( 'e:>get_field retuned no data - field: "'.$args['field'].'"');
		
		// return ##
		return false;

	}


}

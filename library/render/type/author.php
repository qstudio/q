<?php

namespace q\render\type;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class author extends render\type {

	/**
     * Author handler
     *  
     * 
     **/ 
    public static function format( \WP_Post $value = null, String $field = null ): string {

		// start with default passed value ##
		$string = $value->$field;

		// get author ##
		$author = $value->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			h::log( 'Error in returned author data' );

			return $string;

		}

		// special fields first ?? ##
		switch( $field ) {

			// human readable date ##
			case 'author_permalink' :

				$string = \esc_url( \get_author_posts_url( $author ) );

			break ;

			// formatted date ##
			case 'author_name' :

				$string = isset( $authordata->display_name ) ? $authordata->display_name : $authordata->user_login ;
				
			break ;

		}

		// check ##
		if ( is_null( $string ) ) {

			h::log( 'String is empty.. so return passed value' );

			$string = $value->$field;

		}

        // kick back ##
        return $string;

    }



}
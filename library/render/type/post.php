<?php

namespace q\render\type;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class post extends render\type {

	/**
     * WP Post handler
     *  
     * 
     **/ 
    public static function format( \WP_Post $value = null, String $field = null ): string {

		// start with default passed value ##
		$string = $value->$field;

		// special fields first ?? ##
		switch( $field ) {

			// human readable date ##
			case 'post_date_human' :

				$string = \human_time_diff( 
					\get_the_date( 
						'U', // standard ##
						$value->ID 
					), \current_time('timestamp') );

			break ;

			// formatted date ##
			case 'post_date' :

				$string = 
					\get_the_date( 
						isset( self::$args['date_format'] ) ? 
						self::$args['date_format'] : // take from value passed by caller ##
							core\config::get( 'date_format' ) ?
							core\config::get( 'date_format' ) : // take from global config ##
							'F j, Y', // standard ##
						// $value->post_date, 
						$value->ID
					);

				h::log( 'post_date: '.$string );
				
			break ;

			case 'post_permalink' :

				$string = \get_permalink( $value->ID );

			break ;

			case 'post_excerpt' :

				$string = $value->post_excerpt;

				// if is_search - highlight ##
				if ( \is_search() ) {

					$string = 
						ui\method::search_the_content([
							'string' 	=> \apply_filters( 'q/get/wp/post_content', $value->post_content ),
							'limit'		=> self::$args['length']
						]) ? 
						ui\method::search_the_content([
							'string' 	=> \strip_shortcodes(\apply_filters( 'q/get/wp/post_content', $value->post_content )),
							'limit'		=> self::$args['length']
						]) : 
						$value->post_excerpt ;

				}

			break ;

		}

		// __magic__ fields ##
		if ( is_null( $string ) && $value->$field ) {

			// h::log( 'Field: "'.$field.'" value already set: '.$value->$field );

			// filter magic post fields -- global ##
			$string = \apply_filters( 
				'q/render/format/wp_post/field/'.$field, $value->$field 
			);

			// h::log( 'Filter: q/render/format/wp_post/field/'.$field );

			// filter magic post fields -- field specific ##
			$string = \apply_filters( 
				'q/render/format/wp_post/field/'.self::$args['group'].'/'.$field, $value->$field 
			);

			// kick back already ##
			// return $string;

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
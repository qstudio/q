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

			case 'post_ID' :

				$string = $value->ID;

			break ;

			// case 'post_title' :

			// 	$string = null;

			// break ;

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

				// h::log( 'post_date: '.$string );
				
			break ;

			case 'post_permalink' :

				$string = \get_permalink( $value->ID );

			break ;

			case 'post_is_sticky' :

				$string = \is_sticky( $value->ID ) ? 'sticky' : 'not_sticky' ;

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
		if ( 
			$value->$field
			&& ( 
				empty( $string ) 
				|| is_null( $string ) 
			) 
		) {

			// h::log( 'Field: "'.$field.'" value magically set to: '.ui\method::chop( $value->$field, 50 ) );

			$string = $value->$field;

			// // filter magic post fields -- global ##
			// $string = \apply_filters( 
			// 	'q/render/type/wp_post/'.$field, $value->$field 
			// );

			// h::log( 'Filter: q/render/type/wp_post/'.$field );

			// // filter magic post fields -- field specific ##
			// $string = \apply_filters( 
			// 	'q/render/type/wp_post/'.self::$args['group'].'/'.$field, $value->$field 
			// );

			// kick back already ##
			// return $string;

		}

        // kick back ##
        return $string;

    }



}
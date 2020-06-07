<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
// use q\render;

class type extends \q\render {


	/**
     * WP Post handler
     *  
     * 
     **/ 
    public static function post( $value = null, $wp_post_field = null ){

		// check this type is allowed ##
		if ( ! array_key_exists( __FUNCTION__, self::get_allowed() ) ) {

			h::log( 'Value Type not allowed: '.__FUNCTION__ );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Value Type not allowed: '.__FUNCTION__
			]);

			return $value->$wp_post_field;

		}

 		// @todo -- validate what is $value - should be int or WP_Post object ##

		// start empty ##
		$string = '';

		// special fields first ?? ##
		switch( $wp_post_field ) {

			// human readable date ##
			case 'post_date_human' :

				// h::log( self::$args['date_format'] );

				$string = \human_time_diff( 
					\get_the_date( 
						isset( self::$args['date_format'] ) ? 
						self::$args['date_format'] : // take from value passed by caller ##
							core\config::get( 'date_format' ) ?
							core\config::get( 'date_format' ) : // take from global config ##
							'U', // standard ##
						$value->ID 
					), \current_time('timestamp') );

				return $string;

			break ;

			// formatted date ##
			case 'post_date' :

				// h::log( self::$args['date_format'] );

				$string = 
					\get_the_date( 
						isset( self::$args['date_format'] ) ? 
						self::$args['date_format'] : // take from value passed by caller ##
							core\config::get( 'date_format' ) ?
							core\config::get( 'date_format' ) : // take from global config ##
							'U', // standard ##
						$value->post_date 
					);

				return $string;
				
			break ;

			case 'post_permalink' :

				$string = \get_permalink( $value->ID );

				return $string;

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

				return $string;

			break ;

		}

		// __magic__ fields ##
		if ( $value->$wp_post_field ) {

			// h::log( 'Field: "'.$wp_post_field.'" value already set: '.$value->$wp_post_field );

			// filter magic post fields -- global ##
			$string = \apply_filters( 
				'q/render/format/wp_post/field/'.$wp_post_field, $value->$wp_post_field 
			);

			// h::log( 'Filter: q/render/format/wp_post/field/'.$wp_post_field );

			// filter magic post fields -- field specific ##
			$string = \apply_filters( 
				'q/render/format/wp_post/field/'.self::$args['group'].'/'.$wp_post_field, $value->$wp_post_field 
			);

			// kick back already ##
			return $string;

		}

		// check ##
		h::log( 'String is empty.. so return passed value' );

		$string = $value->$wp_post_field;

        // kick back ##
        return $string;

    }


    
    /**
     * Image type handler 
     *  
     * 
     * @todo - add srcset check
     * @todo - placeholder fallback
     * @todo - what about different image methods ??
     **/ 
    public static function src( $value = null, $wp_post_field = null ){

		// check this type is allowed ##
		if ( ! array_key_exists( __FUNCTION__, self::get_allowed() ) ) {

			h::log( 'Value Type not allowed: '.__FUNCTION__ );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Value Type not allowed: '.__FUNCTION__
			]);

			return $value->$wp_post_field;

		}

		// @todo - validate passed $value ##

		// attachment ID ##
		$attachment_id = \get_post_thumbnail_id( $value );
		// $post = \get_post_thumbnail_id( $value->ID );
		// h::log( 'Field: '.$wp_post_field.' - Post ID: '.$value->ID.' - Src ID: '.$attachment_id );

        // check and assign ##
        $handle = 
            isset( self::$args['src']['handle'][$wp_post_field] ) ? // @todo -- this is probably wrong ##
            self::$args['src']['handle'][$wp_post_field] :
            \apply_filters( 'q/render/type/src/handle', 'medium' ); // filterable default ##

        // h::log( 'Image handle: '.$handle );

		// start empty ##
        $string = '';

        // h::log( 'Image ID: '.$value );

        // get image ##
		$src = \wp_get_attachment_image_src( $attachment_id, $handle );
		// h::log( $src );

		// validate ##
		if ( 
			! $src
			|| ! is_array( $src ) 
		) {

			// h::log( $src );
			h::log( 'wp_get_attachment_image_src returned bad data' );

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'wp_get_attachment_image_src returned bad data'
			]);

			return $value->$wp_post_field;

		}

		// assign to string ##
        $string = $src[0];

		// conditional -- add img meta values and srcset ##
        if ( 
            isset( self::$args['config']['srcset'] )
            && true == self::$args['config']['srcset'] 
        ) {

            // $id = \get_post_thumbnail_id( $value );
            $srcset = \wp_get_attachment_image_srcset( $value->ID, $handle );
            $sizes = \wp_get_attachment_image_sizes( $value->ID, $handle );
            $alt = 
                \get_post_meta( $value->ID, '_wp_attachment_image_alt', true ) ?
                \get_post_meta( $value->ID, '_wp_attachment_image_alt', true ) :
                get\wp::the_excerpt_from_id( $value->ID, 100 );

            // markup tag attributes ##
            $srcset = '" srcset="'.\esc_attr($srcset).'"'; 
            $sizes = ' sizes="'.\esc_attr($sizes).'"'; 
            $alt = ' alt="'.\esc_attr($alt).'"'; 

            $string = $src[0].$srcset.$sizes.$alt;

        }

        // h::log( 'Image string: '.$string );

        // kick back ##
        return $string;

	}
	


	/**
     * Category handler
     *  
     * 
     **/ 
    public static function category( $value = null, $wp_post_field = null ){

		// check this type is allowed ##
		if ( ! array_key_exists( __FUNCTION__, self::get_allowed() ) ) {

			h::log( 'Value Type not allowed: '.__FUNCTION__ );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Value Type not allowed: '.__FUNCTION__
			]);

			return $value->$wp_post_field;

		}

 		// @todo -- validate what is $value - should be int or WP_Post object ##

		// start empty ##
		$string = '';

		// get category ##
		$category = \get_the_category( $value->ID );
		// h::log( $category );

		// get category ##
		if ( 
			! $category
			|| ! is_array( $category )
		) {

			h::log( 'No category or corrupt data returned' );

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'No category data returned'
			]);

			return false;

		}

		// h::log( 'Working: '.$wp_post_field );

		switch( $wp_post_field ) {

			case 'category_name' :

				// h::log( 'Working: '.$wp_post_field );

				$string = isset( $category[0] ) ? $category[0]->name : null ; // category missing ##

			break ;

			case 'category_permalink' :

				// h::log( 'Working: '.$wp_post_field );

				$string = isset( $category[0] ) ? \get_category_link( $category[0] ) : null ; // category missing ##

			break ;

		}

		// check ##
		// h::log( '$string: '.$string );

        // kick back ##
        return $string;

    }



    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_allowed()
    {

        return \apply_filters( 'q/render/type/get', self::$type );

    }



}
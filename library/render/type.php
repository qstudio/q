<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
// use q\render;

class type extends \q\render {

    
    /**
     * Image type handler 
     *  
     * 
     * @todo - add srcset check
     * @todo - placeholder fallback
     * @todo - what about different image methods ??
     **/ 
    public static function src( $value = null, $field = null ){

		$type = 'src';

		// check this type is allowed ##
		if ( ! array_key_exists( $type, self::get_allowed() ) ) {

			h::log( 'Value Type not allowed: '.$type );

			return $value;

		}

		// attachment ID ##
		$attachment_id = \get_post_thumbnail_id( $value );

		// $post = \get_post_thumbnail_id( $value->ID );
		// h::log( 'Field: '.$field.' - Post ID: '.$value.' - Src ID: '.$attachment_id );

        // check and assign ##
        $handle = 
            isset( self::$args['src']['handle'][$field] ) ?
            self::$args['src']['handle'][$field] :
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

			h::log( $src );
			h::log( 'wp_get_attachment_image_src returned bad data' );

			return $value;

		}

		// assign to string ##
        $string = $src[0];

		// conditional -- add img meta values and srcset ##
		/*
        if ( 
            isset( self::$args['config']['srcset'] )
            && true == self::$args['config']['srcset'] 
        ) {

            // $id = \get_post_thumbnail_id( $value );
            $srcset = \wp_get_attachment_image_srcset( $value, $handle );
            $sizes = \wp_get_attachment_image_sizes( $value, $handle );
            $alt = 
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) ?
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) :
                get\wp::the_excerpt_from_id( $value, 100 );

            // markup tag attributes ##
            $srcset = '" srcset="'.\esc_attr($srcset).'"'; 
            $sizes = ' sizes="'.\esc_attr($sizes).'"'; 
            $alt = ' alt="'.\esc_attr($alt).'"'; 

            $string = $src[0].$srcset.$sizes.$alt;

        }
		*/

        // h::log( 'Image string: '.$string );

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
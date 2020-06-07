<?php

namespace q\render\type;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class src extends render\type {

    /**
     * Image type handler 
     *  
     * 
     * @todo - add srcset check
     * @todo - placeholder fallback
     * @todo - what about different image methods ??
     **/ 
    public static function format( \WP_Post $value = null, String $field = null ): string {

		// attachment ID ##
		$attachment_id = \get_post_thumbnail_id( $value );
		// $post = \get_post_thumbnail_id( $value->ID );
		// h::log( 'Field: '.$field.' - Post ID: '.$value->ID.' - Src ID: '.$attachment_id );

        // check and assign ##
        $handle = 
            isset( self::$args['src']['handle'][$field] ) ? // @todo -- this is probably wrong ##
            self::$args['src']['handle'][$field] :
            \apply_filters( 'q/render/type/src/handle', 'medium' ); // filterable default ##

        // h::log( 'Image handle: '.$handle );

		// start empty ##
        $string = $value->$field;

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

			return $string;

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

		// check ##
		if ( is_null( $string ) ) {

			h::log( 'String is empty.. so return passed value' );

			$string = $value->$field;

		}

        // kick back ##
        return $string;

	}
	


}
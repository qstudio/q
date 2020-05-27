<?php

namespace q\module\field;

use q\core\helper as helper;
use q\core\wordpress as q_wordpress;

// Q Field Classes ##
use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\fields as fields;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class type extends field {

    
    /**
     * Image type handler 
     *  
     * 
     * @todo - add srcset check
     * @todo - placeholder fallback
     * @todo - what about different image methods ??
     **/ 
    public static function img( $value = null, $field = null ){

        // check and assign ##
        $handle = 
            isset( self::$args['img']['handle'][$field] ) ?
            self::$args['img']['handle'][$field] :
            \apply_filters( 'q/field/format/img/handle', 'medium' ); ;

        // helper::log( 'Image handle: '.$handle );

        $string = '';

        // helper::log( 'Image ID: '.$value );

        // get image ##
        $src =  \get_the_post_thumbnail_url( $value, $handle );

        $string = $src;

        // conditional -- add img meta values and srcset ##
        if ( 
            isset( self::$args['filter']['img'] )
            && 'srcset' == self::$args['filter']['img'] 
        ) {

            // $id = \get_post_thumbnail_id( $value );
            $srcset =  \wp_get_attachment_image_srcset( $value, $handle );
            $sizes =  \wp_get_attachment_image_sizes( $value, $handle );
            $alt = 
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) ?
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) :
                q_wordpress::excerpt_from_id( $value, 100 );

            // markup tag attributes ##
            $srcset = '" srcset="'.\esc_attr($srcset).'"'; 
            $sizes = ' sizes="'.\esc_attr($sizes).'"'; 
            $alt = ' alt="'.\esc_attr($alt).'"'; 

            $string = $src.$srcset.$sizes.$alt;

        }

        // helper::log( 'Image string: '.$string );

        // kick back ##
        return $string;

    }



    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_allowed()
    {

        return \apply_filters( 'q/field/formats/get', self::$formats );

    }



}
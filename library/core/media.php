<?php

namespace q\core;

use q\core as core;
use q\willow;
use q\core\helper as h;

// Q Theme Config ##
use q\theme as theme;

// load it up ##
\q\core\media::run();

class media extends \Q {

    public static function run()
    {

        // filter intermediate image sizes ##
        // \add_filter( 'intermediate_image_sizes_advanced', [ get_class(), 'intermediate_image_sizes_advanced' ] );

	}



    /**
     * Remove standard image sizes so that these sizes are not
     * created during the Media Upload process
     *
     * Tested with WP 3.2.1
     *
     * Hooked to intermediate_image_sizes_advanced filter
     * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
     *
     * @param $sizes, array of default and added image sizes
     * @return $sizes, modified array of image sizes
     * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
     */
    public static function intermediate_image_sizes_advanced( $sizes)
    {

        unset( $sizes['hero']);

        return $sizes;

    }



	
    public static function image_sizes_list()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) h::log( $_wp_additional_image_sizes ); 

	}
	

	/**
	 * Get information about available image sizes
	 * 
	 * @link		https://developer.wordpress.org/reference/functions/get_intermediate_image_sizes/
	 */
	function image_sizes( $size = '' ) {

		$wp_additional_image_sizes = \wp_get_additional_image_sizes();
	
		$sizes = array();
		$get_intermediate_image_sizes = \get_intermediate_image_sizes();
	
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = \get_site_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = \get_site_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) \get_site_option( $_size . '_crop' );
			} elseif ( isset( $wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array( 
					'width' => $wp_additional_image_sizes[ $_size ]['width'],
					'height' => $wp_additional_image_sizes[ $_size ]['height'],
					'crop' =>  $wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
	
		// Get only 1 size if found
		if ( $size ) {
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
		return $sizes;

	}



	/**
     * Check if an attached file exists
     *
     * @since       1.6.3
     * @return      boolean
     */
    public static function attachment_exists( $id = null )
    {

        // sanity ##
        if ( is_null ( $id ) ) {

            return false;

        }

        // get attachment path ##
        if ( $file = \get_attached_file( $id ) ) {

            if ( file_exists( $file ) ) {

                return true;

            }

        }

        // nothng cooking ##
        return false;

	}


}

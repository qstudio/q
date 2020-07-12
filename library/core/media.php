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

        // add_image_sizes for all themes ##
        \add_action( 'init', [ get_class(), 'add_image_sizes' ], 1000 );

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



    /**
     * Add image sizes for all devices - so that all device images sizes are prepared when files are uploaded
     * Note: Tablet uses desktop sized images
     *
     * @since       0.1
     * @return      void
     */
    public static function add_image_sizes()
    {

		if ( $handles = self::calculate_image_sizes() ) {

			foreach( $handles as $handle ) {

				\add_image_size( $handle['handle'], $handle['width'], $handle['height'], $handle['crop'] ); // icon ##

			}

		}

        // generic ##
        \add_image_size( 'icon', 80, 80, true ); // icon ##
        \add_image_size( 'thumb', 250, 250, true ); // small thumb ##

    }

	

	/**
	 * Generates list of image sizes, with width, height and crop from Q config ## 
	 */
	public static function calculate_image_sizes()
	{

		// core\config::get();
		// h::log( core\config::get( 'src' ) );
		// $global = core\config::get([ 'context' => 'media', 'task' => 'src' ]);
		$config = willow\core\config::get([ 'context' => 'media', 'task' => 'config' ]);
		if ( ! $config ) {

			h::log( 'e:>Error in stored src config' );

			return false;

		}

		if ( 
			false === $config['generate'] 
		) {

			h::log( 'd:>Image size generation disabled' );

			return false;

		}

		// h::log( $config );

		$ratio = $config['ratio'];
		$handles = $config['handles'];
		$sizes = $config['sizes'];
		$open = $config['open'];
		$pixel = $config['pixel'];
		$scale = $config['scale'];

		// get hight value ##
		$sizes_last_key = array_key_last($sizes);
		$big = $sizes[ $sizes_last_key ];
		// h::log( 'BIG: '.$sizes_biggest );

		// new array ##
		$array = [];

		// loop over handles are do some maths ##
		foreach( $handles as $handle => $handle_value ) {

			// h::log( $handle );

			// loop over all sizes ##
			foreach( $sizes as $size => $size_value ) {

				if ( 
					'all' == $handle_value['sizes']
					|| (
						is_array( $handle['sizes'] )
						&& in_array( $size, $handle_value['sizes'] )
					)
				){

					// work out handle ##
					$src_handle = 'xs' == $size ? $handle : $handle.'-'.$size ;

					// h::log( 'Generate image size: '.$size ); 

					// new array key ##
					$array[ $src_handle ] = [
						'handle' 	=> $src_handle,
						'width'		=> self::calculate_image_size( $handle_value['width'], $size_value, $ratio, $scale ),
						'height'	=> self::calculate_image_size( $handle_value['height'], $size_value, $ratio, $scale ),
						'crop'		=> isset( $handle_value['crop'] ) && true == $handle_value['crop'] ? true : false
					];

				}

			}

			// open ##
			if ( 
				isset( $handle_value['open'] ) && true == $handle_value['open']
			){

				// h::log( 'create open scaled image' );

				$array[ $handle.'-open' ] = [
					'handle' 	=> $handle.'-open',
					'width'		=> 
									'width' == $handle_value['open'] ? 
									$open : // set to open width ##
									( $big * $scale )
								, 
					'height'	=> 	
									'height' == $handle_value['open'] ? 
									$open :// set to open height ##
									( $big / 2 * $scale )
								, 
					'crop'		=> false // no hard crop ##
				];

			} 
			
			// pixel ##
			if ( 
				isset( $handle_value['pixel'] ) && true == $handle_value['pixel']
			) {

				// h::log( 'create pixel scaled image' );
				// size = 200
				// pixel = 2
				// square: open = false.. width = size * pixel = 400, height = size x pixel = 400
				// horizontal: open = width.. width = size * pixel = 400, height = size x pixel = 400

				$array[ $handle.'-pixel' ] = [
					'handle' 	=> $handle.'-pixel',
					'width'		=> self::calculate_image_size( $handle_value['width'], ( $big * $pixel ), $ratio, $scale ),
					'height'	=> self::calculate_image_size( $handle_value['height'], ( $big * $pixel ), $ratio, $scale ),
					'crop'		=> true // hard crop ##
				];

			}

		}

		// h::log( $array );

		return $array;

	}


	public static function calculate_image_size( $method = 'equal', $size = null, $ratio = 1.618, $scale = 1 ){

		// @todo - sanity ##
		// h::log( 'Calculate size by: '.$method.' from: '.$size );

		// default ##
		$value = $size;

		switch ( $method ) {

			case "multiply" :

				$value = $size * $ratio;

			break;

			case "divide" :

				$value = $size / $ratio;

			break;

			default :
			case "equal" :

				$value = $size;

			break;	


		}

		// scale ##
		$value = ( $value * $scale );

		// kick back rounded whole number ##
		return round( $value );

	}


}
